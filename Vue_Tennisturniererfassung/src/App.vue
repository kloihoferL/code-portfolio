<script setup>
import { useLogin } from "@/useLogin.js";

import {useRoute} from "vue-router";
import { HomeIcon, UserCircleIcon, Cog6ToothIcon } from '@heroicons/vue/24/outline'
const { currentUser, logout } = useLogin();
const route = useRoute();
import { useRouter } from "vue-router";
const router = useRouter();

const handleLogout = () => {
  logout();
  router.push('/');
};

</script>

<template>
  <aside
    v-if="currentUser && route.name !== 'login'"
    class="fixed top-0 left-0 z-40 h-screen bg-gray-100 dark:bg-gray-800 shadow w-16 md:w-64 transition-all duration-300"
  >
    <div class="h-full px-2 md:px-3 py-4 overflow-y-auto flex flex-col">
      <ul class="space-y-2 font-medium">
        <li>
          <RouterLink
            to="/Tournaments"
            class="flex items-center p-2 text-white rounded-lg dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700"
          >
            <HomeIcon class="h-6 w-6" />
            <span class="ml-3">Startseite</span>
          </RouterLink>
        </li>
        <li>
          <RouterLink
            to="/Mein-Profil"
            class="flex items-center p-2 text-white rounded-lg dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700"
          >
            <UserCircleIcon class="h-6 w-6" />
            <span class="ml-3">Mein Profil</span>
          </RouterLink>
        </li>
      </ul>

      <button
        @click="handleLogout"
        class="mt-auto flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 px-4 rounded transition text-sm md:block"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
        </svg>
        <span>Logout</span>
      </button>

    </div>

  </aside>

  <div :class="currentUser && route.name !== 'login' ? 'md:ml-64 ml-16 p-4' : 'p-4'">
    <RouterView />
  </div>
</template>

<style scoped>
@media (max-width: 767px) {
  span {
    display: none;
  }
}



</style>



