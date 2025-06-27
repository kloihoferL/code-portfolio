<template>
    <tournament-list></tournament-list>
</template>

<script setup >
import TournamentList from "@/components/tournament-list.vue"
import {useLogin} from "@/useLogin.js";
import {useTournaments} from "@/useTournaments.js";
import {onMounted} from "vue";
import Matches from "@/components/matches.vue";

const {currentUser, logout} = useLogin();
const {fetchTournaments} = useTournaments();


onMounted(() => {
  fetchTournaments();
});




</script>


