<template>
<tournament-details></tournament-details>
  <div>
    <RouterLink to="/">
      <button @click="logout()" class="btn btn-primary">
        Logout
      </button>
    </RouterLink>
  </div>
</template>

<script setup >
import TournamentList from "@/components/tournament-list.vue"
import {useLogin} from "@/useLogin.js";
import {useTournaments} from "@/useTournaments.js";
import {onMounted} from "vue";


const {currentUser, logout} = useLogin();
const {fetchTournaments} = useTournaments();


onMounted(() => {
  fetchTournaments();
});




</script>


