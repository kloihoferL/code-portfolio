<script setup>

import {onMounted, ref} from "vue";
import {useMatches} from "@/useMatches.js";
import {useLogin} from "@/useLogin.js";
import {useTournaments} from "@/useTournaments.js";
import {useUsers} from "@/useUsers.js";
const { currentUser } = useLogin();
const {userTournaments, getallTournamentsFromUserWithId} = useTournaments();
const { userMatches,winningMatches, getAllMatchesFromUserWithId, getAllWinningMatchesFromUser } = useMatches();
const allMatches = ref([]);




const UserId = currentUser.value.id;

onMounted(async() =>{
  if (currentUser.value?.id) {
    await getAllMatchesFromUserWithId(UserId);
    await getAllWinningMatchesFromUser(UserId);
    await getallTournamentsFromUserWithId(UserId);
  }
  console.log('user matches in komponente' + userMatches.value);
  console.log('usertournaments' + userTournaments.value);
})

</script>

<template>
  <div class="p-6 text-white">
    <div class="mb-6">
      <p class="text-2xl font-semibold">Guten Tag {{ currentUser.name }} 🎾</p>
    </div>

    <div class="bg-gray-800 rounded-2xl p-6 shadow-lg mb-6">
      <p class="text-xl font-bold mb-4">Meine Spielstatistik</p>
      <div class="flex gap-4">
        <div class= "border border-green-500 text-green-500  rounded-xl p-4 w-32 text-center shadow-inner">
          <p class="font-semibold">Gewonnen</p>
          <p class="text-2xl">{{ winningMatches.length }}</p>
        </div>

        <div class="border border-red-500 text-red-500  rounded-xl p-4 w-32 text-center shadow-inner">
          <p class="font-semibold">Verloren</p>
          <p class="text-2xl">{{ userMatches.length - winningMatches.length }}</p>
        </div>

        <div class=" border border-gray-400 text-gray-400 rounded-xl p-4 w-32 text-center shadow-inner">
          <p class="font-semibold">Insgesamt</p>
          <p class="text-2xl">{{ userMatches.length }}</p>
        </div>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <p class="text-xl font-bold mb-4">Meine Spiele</p>

        <div v-if="userMatches.length === 0" class="text-gray-400 italic">
          Keine Matches gefunden.
        </div>

        <div
          v-for="match in userMatches"
          :key="match.id"
          :class="[
            'rounded-2xl border p-4 mb-4 transition-all shadow-md',
            match.expand.winner.id === currentUser.id
              ? 'border-green-500 '
              : 'border-red-500 '
          ]"
        >
          <div class="flex justify-between items-center mb-1 text-sm text-gray-300">
            <p>Runde: <span class="font-semibold text-white">{{ match.round }}</span></p>
          </div>

          <p class="text-lg font-semibold text-white">
            {{ match.expand.player1_id.name }} <span class="text-gray-400">vs</span> {{ match.expand.player2_id.name }}
          </p>

          <p class="text-sm text-gray-300 mb-2">
            Turnier: <span class="text-blue-400 font-medium">{{ match.expand.tournament_id.name }}</span>
          </p>

          <div class="flex justify-between items-center text-sm mt-2">
            <p class="text-gray-300">
              Ergebnis: <span class="font-semibold text-white">{{ match.score }}</span>
            </p>
            <p
              class="font-semibold"
              :class="match.expand.winner.id === currentUser.id ? 'text-green-400' : 'text-red-400'"
            >
              🏆 Sieger: {{ match.expand.winner.name }}
            </p>
          </div>
        </div>
      </div>

      <div>
        <p class="text-xl font-bold mb-4">Meine Turniere</p>

        <div
          v-for="tournament in userTournaments"
          :key="tournament.id"
          class="rounded-2xl shadow-md border border-gray-600 p-4 mb-4 bg-gray-800 hover:shadow-lg transition"
        >
          <h3 class="text-lg font-semibold text-white mb-1">{{ tournament.name }}</h3>
        </div>
      </div>
    </div>
  </div>
</template>


<style scoped>

</style>
