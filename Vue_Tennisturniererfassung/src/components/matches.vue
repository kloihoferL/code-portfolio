<script setup>


import {useMatches} from "@/useMatches.js";

const { matches, fetchMatches} = useMatches();
import {onMounted} from "vue";

onMounted(() => {
  fetchMatches();

});

</script>

<template>
  <h2>Alle Matches</h2>

  <div v-for="match in matches" :key="match.id">
    <p> {{ match.expand.player1_id.name }} vs {{ match.expand.player2_id.name }} </p>
    <p> Ergebnis: {{ match.score }} </p>
    <p> 🏆 Winner: {{ match.expand.winner.name }} </p>
    <p> Runde: {{match.round}} </p>
    <p> Datum: {{match.matchdate}}</p>
  </div>


</template>

<style scoped>

</style>
