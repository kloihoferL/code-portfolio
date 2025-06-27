import { ref, onMounted } from "vue";
import { pb } from "@/pocketbase.js";
import { useLogin } from "@/useLogin.js";
import { useWebNotification } from "@vueuse/core";

const matches = ref([]);
const userMatches = ref([]);
const tournamentMatches = ref([]);
const record = ref(null);
const winningMatches = ref([]);

const { currentUser } = useLogin();

export function useMatches() {
  async function fetchMatches() {
    try {
      matches.value = await pb.collection('matches').getFullList({
        expand: 'player1_id,player2_id,tournament,winner'
      });
      console.log(matches.value);
    } catch (error) {
      console.error("Error fetching matches:", error);
    }
  }

  async function getAllMatchesFromUserWithId(userId) {
    try {
      userMatches.value = await pb.collection('matches').getFullList({
        filter: `player2_id = "${userId}" || player1_id = "${userId}"`,
        expand: 'player2_id,tournament_id,winner,player1_id',
      });
    } catch (error) {
      console.error("Error fetching user matches:", error);
      return [];
    }
  }

  async function getAllWinningMatchesFromUser(userId) {
    try {
      winningMatches.value = await pb.collection('matches').getFullList({
        filter: `winner = "${userId}"`,
        expand: 'player1_id,player2_id,tournament',
      });
      return winningMatches;
    } catch (error) {
      console.error("Error fetching winning matches:", error);
      return [];
    }
  }

  async function getMatchesByTournamentId(tournamentId) {
    try {
      tournamentMatches.value = await pb.collection('matches').getFullList({
        filter: `tournament_id = "${tournamentId}"`,
        expand: 'player1_id,player2_id,winner'
      });
      return tournamentMatches;
    } catch (error) {
      console.error("Fehler beim Laden der Matches für das Turnier:", error);
      return [];
    }
  }

  async function createMatch(tournamentId, player1Id, player2Id, score, winnerId, round, date) {
    const data = {
      tournament_id: tournamentId,
      round: round,
      player1_id: player1Id,
      player2_id: player2Id,
      score: score,
      winner: winnerId,
      matchdate: date
    };

    record.value = await pb.collection('matches').create(data);
    return record;
  }

  onMounted(async () => {
    await pb.collection("matches").unsubscribe("*");
    await pb.collection("matches").subscribe("*", async (matchEvent) => {
      if (matchEvent.action === 'create') {
        const match = matchEvent.record;

        const expandedMatch = await pb.collection('matches').getOne(match.id, {
          expand: 'player1_id,player2_id,winner'
        });

        const p1 = expandedMatch.expand?.player1_id?.username ?? "Spieler 1";
        const p2 = expandedMatch.expand?.player2_id?.username ?? "Spieler 2";
        const winner = expandedMatch.expand?.winner?.username ?? "Unbekannt";
        const score = expandedMatch.score ?? "";

        const { show } = useWebNotification({
          title: `Neues Match-Ergebnis`,
          body: `${p1} vs ${p2}\nGewinner: ${winner}\nErgebnis: ${score}`,
          lang: 'de',
          renotify: true,
          tag: `match-${match.id}`,
          requireInteraction: false,
        });

        show();

      }
    });
  });

  return {
    fetchMatches,
    getAllMatchesFromUserWithId,
    getAllWinningMatchesFromUser,
    createMatch,
    getMatchesByTournamentId,
    matches,
    record,
    userMatches,
    winningMatches,
    tournamentMatches
  };
}
