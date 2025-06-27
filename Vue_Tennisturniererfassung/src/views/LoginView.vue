<script setup>
import {ref} from "vue";
import {useLogin} from "@/useLogin.js";
import { useRouter } from "vue-router";
const router = useRouter();

const {login} = useLogin();

const email = ref('');
const password = ref('');

async function handleLogin() {
  try {
    await login(email.value, password.value);
    await router.push('/tournaments'); // Weiterleitung nach erfolgreichem Login
  } catch (error) {
    console.error("Login fehlgeschlagen:", error);
  }
}

</script>

<template>
  <div class="min-h-screen bg-gray-900 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md space-y-8">
      <div class="text-center">
        <h2 class="mt-6 text-3xl font-bold text-white">Willkommen zurück 🎾</h2>
        <p class="mt-2 text-sm text-gray-400">Melde dich an, um fortzufahren</p>
      </div>

      <form class="bg-gray-800 p-6 rounded-xl shadow space-y-6" @submit.prevent="handleLogin">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-300">E-Mail</label>
          <input
            v-model="email"
            id="email"
            name="email"
            type="email"
            required
            placeholder="E-Mail"
            class="mt-1 w-full rounded-md bg-gray-700 border border-gray-600 px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-300">Passwort</label>
          <input
            v-model="password"
            id="password"
            name="password"
            type="password"
            required
            placeholder="Passwort"
            @keydown.enter="handleLogin"
            class="mt-1 w-full rounded-md bg-gray-700 border border-gray-600 px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>


        <div>
          <button
            type="submit"
            :disabled="!email || !password"
            class="w-full rounded-md bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2 px-4 transition disabled:opacity-50"
          >
            Login
          </button>
        </div>
      </form>

    </div>
  </div>
</template>

<style>

</style>
