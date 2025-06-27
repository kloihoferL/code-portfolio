import { ref } from "vue";
import { pb } from "@/pocketbase.js";
import {useLogin} from "@/useLogin.js";

const tournaments = ref([]);
const { currentUser } = useLogin()
const userTournaments = ref([]);
const record = ref(null);

export function useTournaments() {
  async function fetchTournaments() {
    try {
      tournaments.value = await pb.collection('tournaments').getFullList({
        expand: 'players',
      });

    } catch (error) {
      console.error("Error fetching tournaments:", error);
    }
  }

  async function fetchMatches(){
    try{
      const record = await pb.collection('matches').getOne('RECORD_ID', {
        expand: 'relField1,relField2.subRelField',
      });
      console.log(record);
    }catch (error) {
        console.error("Error fetching matches:", error);
    }
  }

  async function getallTournamentsFromUserWithId(userId) {
    try {
      userTournaments.value = await pb.collection('tournaments').getFullList({
        filter: `players ~ "${userId}"`,
        expand: 'players',
      });
      return userTournaments;
    } catch (error) {
      console.error("Error fetching user tournaments:", error);
      return [];
    }
  }

  async function subscribeToTournament(tournament) {
    const currentUserID = currentUser.value?.id;

    const tournamentPlayers = (tournament.players || []).map(p =>
      typeof p === 'string' ? p : p.id
    );

    if (tournamentPlayers.includes(currentUserID)) {
      alert('Du bist bereits angemeldet.');
      return;
    }

    const updatedPlayers = [...tournamentPlayers];

    if (!updatedPlayers.includes(currentUserID)) {
      updatedPlayers.push(currentUserID);
    }


    try {
      await pb.collection('tournaments').update(tournament.id, {
        players: updatedPlayers,
      });

      alert('Erfolgreich angemeldet!');
      await fetchTournaments();
    } catch (error) {
      console.error("Fehler beim Anmelden:", error);
      alert('Fehler beim Anmelden: ' + error.message);
    }
  }

  async function createTournament(data) {
    try {
      record.value = await pb.collection('tournaments').create(data);
      return record.value;
    } catch (err) {
      console.error("Fehler beim Erstellen des Turniers:", err);
      throw err;
    }
  }



  return {
    tournaments,
    fetchTournaments,
    subscribeToTournament,
    fetchMatches,
    getallTournamentsFromUserWithId,
    createTournament,
    userTournaments
  };
}
