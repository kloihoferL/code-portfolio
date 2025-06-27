import {Component, OnInit, signal} from '@angular/core';
import { RouterLink } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { ConfigSignalService } from '../services/config-signal.service';
import {FormsModule} from '@angular/forms';
import { CommonModule } from '@angular/common';
import {ChatService} from '../shared/chat.service';
import RecordRTC from 'recordrtc';

interface FrageAntwortPaar {
  frage: string;
  antwort?: string;
}

interface ServerResponse {
  response: string;
}


@Component({
  selector: 'app-step3',
  standalone: true,
  imports: [RouterLink, FormsModule, CommonModule],
  templateUrl: './step3.component.html',
  styles: ``
})
export class Step3Component implements OnInit {
  selectedCategory = '';
  selectedCount = 0;
  currentCount = 0;

  chatHistory: FrageAntwortPaar[] = [];
  currentInput = '';
  isFinished = false;
  isLoading = false;
  isTranscribing = false;


  /*currentIndex = 0;
  isFinished = false;
  userAnswers: string[] = [];*/

  recorder : RecordRTC | null = null;
  isRecording = signal(false);

  constructor(
    public config: ConfigSignalService,
    private chatService: ChatService
  ) {
  }


  ngOnInit(): void {
    //selected category und selectedCount aus dem ConfigSignalService abrufen an server schicken
    /*this.selectedCategory = this.config.selectedCategory();*/
    this.selectedCount = this.config.counterQuestion();

    const jobToSend = this.config.selectedCategory() === 'jobspezifische fragen'
      ? this.config.jobTitle()
      : this.config.selectedCategory();

    this.selectedCategory = jobToSend;

    this.isLoading = true;

    console.log(this.selectedCount, this.selectedCategory);

    this.chatService.initChat(jobToSend, this.selectedCount)
      .subscribe({
        next: (res) => {
          this.chatHistory.push({frage: res.response});
          this.isLoading = false;
        },
        error: (err) => console.error('Fehler beim Start:', err)
      });

  }

  /*sendeAntwort(): void {
    const antwort = this.currentInput.trim();
    if (!antwort || this.isFinished) return;

    // Antwort speichern
    this.chatHistory[this.chatHistory.length - 1].antwort = antwort;
    this.currentInput = '';
    this.currentCount++;

    if (this.currentCount >= this.selectedCount) {
      this.isFinished = true;
      return;
    }

    // Nächste Frage vom Server holen
    this.chatService.sendAnswer(antwort)
      .subscribe({
        next: (res) => {
          this.chatHistory.push({ frage: res.frage });
        },
        error: (err) => console.error('Fehler beim Abrufen der nächsten Frage:', err)
      });
  }*/

  sendeAntwort(): void {
    const antwort = this.currentInput.trim();
    if (!antwort || this.isFinished) return;

    // Aktuelle Frage mit Antwort ergänzen
    this.chatHistory[this.chatHistory.length - 1].antwort = antwort;
    this.currentInput = '';
    this.currentCount++;
    this.isLoading = true;

    // Nächste Frage holen
    this.chatService.sendAnswer(antwort).subscribe({
      next: (res) => {
        this.chatHistory.push({frage: res.response});
        this.isLoading = false;

        // Interview fertig?
        if (this.currentCount >= this.selectedCount) {
          this.isFinished = true;
          return;
        }
      },
      error: (err) => console.error('Fehler beim Abrufen der nächsten Frage:', err)
    });
  }


  handleRecording(): void {
    this.isRecording.update(v => !v);
    if (this.isRecording()) {
      this.startRecording();
    } else {
      this.stopRecording();
    }
  }

  async startRecording() {
    const stream = await navigator.mediaDevices.getUserMedia({audio: true});
    this.recorder = new RecordRTC(stream, {
      type: "audio",
      mimeType: "audio/wav",
      recorderType: RecordRTC.StereoAudioRecorder,
      desiredSampRate: 16000,
    });
    this.recorder.startRecording();
  }

  stopRecording(): void {
    if (!this.recorder || !this.isRecording) return;

    this.recorder.stopRecording(() => {
      const audioBlob = this.recorder?.getBlob();
      this.uploadAudioBlob(audioBlob);
    });
  }

  adjustTextareaHeight(textarea: HTMLTextAreaElement) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
  }

  async uploadAudioBlob(blob: any) {
    this.isTranscribing = true;

    const formData = new FormData();
    formData.append("audio", blob, "audio.wav");

    try {
      const res = await fetch("http://localhost:3001/transcribe", {
        method: "POST",
        body: formData,
      });

      const data = await res.json();

      if (data.transcript) {
        this.currentInput = data.transcript;
      }

    } catch (err : any) {
      console.error("Fehler beim Upload:", err.message);
      alert(`Fehler: ${err.message}`);
    } finally {
      this.isTranscribing = false;
    }
  }
}
