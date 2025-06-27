import express from "express";
const app = express();
import { LMStudioClient, Chat } from "@lmstudio/sdk";
const client = new LMStudioClient();
const model = await client.llm.model("mistral-nemo-instruct-2407");
import pdfParse from "pdf-parse";
import cors from "cors";
import path, {dirname} from "path";
import util from "util";
import {exec} from "child_process";
import {fileURLToPath} from "url";
const execAsync = util.promisify(exec);
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
import * as os from "node:os";
import * as fs from "node:fs";
import multer from "multer";

const chatState = {
  chat: Chat.empty(),
  job: "",
  numberOfQuestions: 0,
  documentApplication: "",
  documentFiles: [],
};

const uploadFile = multer();

const whisperCmd = `${path.resolve()}/whisper.cpp/build/bin/whisper-cli \
  -m ${path.resolve()}/whisper.cpp/models/ggml-large-v2.bin \
  -l de \
  ${path.resolve()}/uploads/audio.wav \
  > ${path.resolve()}/uploads/audio.wav.txt`;

const upload = multer({
  storage: multer.diskStorage({
    destination: (req, file, cb) => {
      cb(null, path.join(__dirname, "uploads"));
    },
    filename: (req, file, cb) => {
      cb(null, "audio.wav");
    },
  }),
});

app.use(
  cors({
    origin: "*",
    methods: ["GET", "POST"],
    allowedHeaders: ["Content-Type"],
  })
);
app.use(express.json());

app.post("/uploadCV", uploadFile.single("cv"), async (req, res) => {
  try {
    if (!req.file) { return res.status(400).json({ error: "Keine Datei hochgeladen." }); }

    const tmpPath = path.join(os.tmpdir(), req.file.originalname);
    fs.writeFileSync(tmpPath, req.file.buffer);

    // pdf-text-reader erwartet den Pfad der Datei und liefert ein Array von Seiten zurück
    const data = await pdfParse(req.file.buffer);
    const text = data.text;

    // Speichere den Text im chatState
    chatState.documentApplication = text;

    // Lösche die temporäre Datei wieder
    fs.unlinkSync(tmpPath);

    res.status(200).json({ message: "Lebenslauf erfolgreich gelesen.", text });
  } catch (error) {
    res.status(500).json({ error: "Fehler beim Verarbeiten der Datei." });
  }
});

app.post("/uploadFiles", uploadFile.single("document"), async (req, res) => {
  try {
    if (!req.file) { return res.status(400).json({ error: "Keine Datei hochgeladen." }); }

    const tmpPath = path.join(os.tmpdir(), req.file.originalname);
    fs.writeFileSync(tmpPath, req.file.buffer);

    // pdf-text-reader erwartet den Pfad der Datei und liefert ein Array von Seiten zurück
    const data = await pdfParse(req.file.buffer);
    const text = data.text;

    // Speichere den Text im chatState
    chatState.documentFiles.push(text);

    // Lösche die temporäre Datei wieder
    fs.unlinkSync(tmpPath);

    res.status(200).json({ message: "Datei erfolgreich gelesen.", text });
  } catch (error) {
    res.status(500).json({ error: "Fehler beim Verarbeiten der Datei." });
  }
});

app.post("/initChat", async (req, res) => {
  try {
    const { job, numberOfQuestions } = req.body;

    if (!job) {
      return res.status(400).json({ error: "Jobtitel fehlt." });
    }

    // Chatverlauf global neu starten
    chatState.chat = Chat.empty();
    chatState.job = job;
    chatState.numberOfQuestions = numberOfQuestions;

    const systemPrompt = `
      Du bist ein deutscher Recruiter und ich bin ein Bewerbungskandidat.
      Wir befinden uns in einem Bewerbungsgespräch für den Job als ${job}.
      Falls "jobspezifische Fragen" als Job angegeben wurde, stelle
      allgemeine Fragen zu den Stärken und Schwächen des Kandidatens.
      Reagiere auf meine Antworten so, wie es ein echter Recruiter tun würde.
      Starte mit einer passenden Einstiegsfrage zum Gesprächsbeginn.
    `.trim();

    chatState.chat.append("system", systemPrompt);
    chatState.chat.append("user", "Hallo, ich freue mich auf das Gespräch.");

    const prediction = model.respond(chatState.chat, {
      onMessage: (msg) => chatState.chat.append(msg),
    });

    let responseText = "";
    for await (const { content } of prediction) {
      responseText += content;
    }

    res.status(200).json({ response: responseText });
  } catch (error) {
    console.error("Fehler in /initChat:", error);
    res.status(500).json({ error: "Serverfehler beim Initialisieren des Chats" });
  }
});

app.post("/chat", async (req, res) => {
  try {
    const { message } = req.body;

    if (!message) {
      return res.status(400).json({ error: "Keine Nachricht erhalten." });
    }

    // Aktuelle Frage anhängen
    chatState.chat.append("user", message);

    // Zähle runter
    chatState.numberOfQuestions--;

    // Wenn keine Fragen mehr offen sind -> Abschlussantwort generieren
    if (chatState.numberOfQuestions <= 0) {
      const closingPrompt = `
        Das Bewerbungsgespräch ist nun abgeschlossen.
        Bitte fasse meine Antworten kurz zusammen und gib mir ein kurzes Feedback,
        wie du als Recruiter meine Leistung einschätzt.
        Verwende dabei ein professionelles, aber freundliches Abschlussstatement.
        Hebe unbedingt positive sowohl als auch negative Punkte hervor.
      `.trim();

      chatState.chat.append("system", closingPrompt);

      const closingPrediction = model.respond(chatState.chat, {
        onMessage: (msg) => chatState.chat.append(msg),
      });

      let closingText = "";
      for await (const { content } of closingPrediction) {
        closingText += content;
      }

      return res.status(200).json({ response: closingText });
    }

    // Ansonsten: normale nächste Frage stellen
    const prediction = model.respond(chatState.chat, {
      onMessage: (msg) => chatState.chat.append(msg),
    });

    let responseText = "";
    for await (const { content } of prediction) {
      responseText += content;
    }

    res.status(200).json({ response: responseText });
  } catch (error) {
    console.error("Fehler in /chat:", error);
    res.status(500).json({ error: "Fehler beim Verarbeiten der Nachricht" });
  }
});



app.post("/transcribe", upload.single("audio"), async (req, res) => {
  try {
    if (!req.file) {
      return res.status(400).json({ error: "Keine Datei hochgeladen." });
    }

    await execAsync(whisperCmd);

    const transcriptPath = req.file.path + ".txt";
    const transcript = fs.readFileSync(transcriptPath, "utf-8");

    let cleanText = transcript
      .replace(/\[.*? --> .*?\]/g, "") // Entferne Zeitstempel
      .replace(/\s+/g, " ") // Reduziere Whitespace auf Einzel-Leerzeichen
      .trim(); // Entferne führende/trailende Leerzeichen

    res.setHeader("Content-Type", "application/json");
    res.json({ transcript: cleanText });
  } catch (error) {
    console.error("Fehler bei der Transkription:", error);
    res.status(500).json({ error: error.message });
  }
});

const PORT = process.env.PORT || 3001;

app.listen(PORT, () => console.log(`Server running on port ${PORT}`));
