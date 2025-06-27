<script setup>
import {ref, reactive, computed, onMounted, watch} from 'vue'
import { useRoute } from 'vue-router'
import { useTournaments } from '@/useTournaments.js'
import { useUsers } from "@/useUsers.js";
import { useMatches } from "@/useMatches.js"
import { useShare } from '@vueuse/core'

const route = useRoute()
const { tournaments, fetchTournaments, subscribeToTournament } = useTournaments()
const { allUsers, fetchUsers } = useUsers()
const { createMatch, tournamentMatches, getMatchesByTournamentId } = useMatches()

const { share, isSupported } = useShare({
  title: 'Mein cooles Turnier',
  text: 'Schau dir dieses Turnier an!',
  url: window.location.href,
})

const modalRef = ref(null)

function closeModal() {
  modalRef.value?.close()
}

const form = reactive({
  tournament: '',
  score: '',
  round: '',
  date: '',
  player1: '',
  player2: '',
  winner: ''
})

const tournament = computed(() =>
  tournaments.value.find(t => t.id === route.params.id)
)


onMounted(async () => {
  await fetchTournaments()
  await fetchUsers()

  const tid = route.params.id
  form.tournament = tid
  await getMatchesByTournamentId(tid)
})


const isRegistrationOpen = computed(() => {
  if (!tournament.value || !tournament.value.registration_deadline) return false

  const deadline = new Date(tournament.value.registration_deadline)
  const today = new Date()

  deadline.setHours(0, 0, 0, 0)
  today.setHours(0, 0, 0, 0)

  return today <= deadline
})

async function handleSubmit() {
  const { player1, player2, score, winner, round, date } = form
  const tid = form.tournament

  if (!tid || !player1 || !player2 || !winner || !score || !round || !date) {
    alert("Bitte fülle alle Felder aus.")
    return
  }

  try {
    const isoDate = new Date(date).toISOString()
    const record = await createMatch(tid, player1, player2, score, winner, round, isoDate)

    console.log("Match erstellt:", record)
    await getMatchesByTournamentId(tid)

    modalRef.value?.close()
    form.score = ''
    form.round = ''
    form.date = ''
    form.player1 = ''
    form.player2 = ''
    form.winner = ''

  } catch (err) {
    console.error("Fehler beim Speichern:", err)
    alert("Speichern fehlgeschlagen.")
  }
}
</script>



<template>

  <div class="p-4">
    <RouterLink to="/tournaments">
      <button class="mb-4  text-white px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-500 transition">
        Zurück zur Übersicht
      </button>
    </RouterLink>

    <div class="bg-gray-800 text-white p-6 rounded-lg shadow-lg space-y-4">
      <h1 v-if="tournament" class="text-3xl font-bold">{{ tournament.name }}</h1>
      <p v-if="tournament" v-html="tournament.description" class="text-gray-300"></p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <p><strong>Startgeld:</strong> {{ tournament.entryfee }}</p>
        <p><strong>Gewinn:</strong> {{ tournament.pricewinner }}</p>
        <p><strong>Teilnehmer:</strong> {{ tournament.players.length }}</p>
        <p>
          <strong>Datum:</strong>
          {{ new Date(tournament.fromdate).toLocaleDateString('de-DE') }} -
          {{ new Date(tournament.todate).toLocaleDateString('de-DE') }}
        </p>
        <p v-if="isRegistrationOpen">
          <strong>Anmeldefrist:</strong>
          {{ new Date(tournament.registration_deadline).toLocaleDateString('de-DE') }}
        </p>
        <p v-else class="text-red-400 font-semibold col-span-2">Anmeldefrist vorbei</p>
      </div>

      <div>
        <h2 class="text-xl font-semibold mt-6 mb-2">Teilnehmerliste</h2>
        <ul v-if="tournament?.expand?.players?.length" class="list-disc list-inside text-gray-200">
          <li
            v-for="player in tournament.expand.players"
            :key="player.id"
          >
            {{ player.name || player.email }}
          </li>
        </ul>
        <p v-else class="text-gray-400 italic">Noch keine Teilnehmer</p>
      </div>

      <button
        @click="share"
        :disabled="!isSupported"
        class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded shadow transition disabled:opacity-50"
      > Turnier teilen <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
      </svg>

      </button>

      <div v-if="isRegistrationOpen">
        <button
          @click="subscribeToTournament(tournament)"
          class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition"
        >
          Jetzt anmelden
        </button>
      </div>
    </div>

    <div class="mt-8 bg-gray-800 text-white p-6 rounded-lg shadow space-y-4">
      <h2 class="text-xl font-semibold">Ergebnisse</h2>
      <ul v-if="tournamentMatches.length" class="space-y-2">
        <li v-for="match in tournamentMatches" :key="match.id" class="text-gray-100">
          <span class="font-semibold">{{ match.expand.player1_id.name }}</span>
          vs.
          <span class="font-semibold">{{ match.expand.player2_id.name }}</span> –
          <span>Ergebnis: {{ match.score }}</span> – <span>Runde: {{match.round}} | </span>
          <span class="text-green-400">🏆 {{ match.expand.winner.name }}</span>
        </li>
      </ul>
      <p v-else class="text-gray-400 italic">Noch keine Ergebnisse</p>
      <div class="mt-6">
        <button @click="modalRef?.showModal()" class="btn  text-white px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-500 transition">+ Ergebnis</button>
      </div>
    </div>



    <!-- Modal -->
    <dialog ref="modalRef" id="my_modal_3" class="modal">
      <div class="modal-box bg-white text-black rounded-xl shadow-xl">
        <button @click="closeModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">
          ✕
        </button>
        <form method="dialog" @submit.prevent="handleSubmit" class="space-y-4">

          <h3 class="font-bold text-2xl text-gray-800">Neues Ergebnis hinzufügen</h3>

          <!-- hidden field für die turnier id, weil sie gleich gesetzt wird -->
          <input type="hidden" v-model="form.tournament" />


          <div>
            <label class="label"><span class="label-text text-gray-700">Ergebnis</span></label>
            <input v-model="form.score" type="text" placeholder="Ergebnis eingeben"
                   class="w-full border border-gray-300 rounded-md p-2 bg-white text-black" />
          </div>

          <div>
            <label class="label"><span class="label-text text-gray-700">Runde</span></label>
            <input v-model="form.round" type="text" placeholder="Runde vom Turnier"
                   class="w-full border border-gray-300 rounded-md p-2 bg-white text-black" />
          </div>

          <div>
            <label class="label"><span class="label-text text-gray-700">Datum</span></label>
            <input v-model="form.date" type="date"
                   class="w-full border border-gray-300 rounded-md p-2 bg-white text-black" />
          </div>

          <div class="flex gap-4">
            <div class="flex-1">
              <label class="label"><span class="label-text text-gray-700">Spieler 1</span></label>
              <select v-model="form.player1" class="w-full border border-gray-300 rounded-md p-2 bg-white text-black">
                <option disabled value="">Spieler wählen</option>
                <option v-for="player in tournament.expand.players" :key="player.id" :value="player.id">
                  {{ player.name }}
                </option>
              </select>
            </div>

            <div class="flex-1">
              <label class="label"><span class="label-text text-gray-700">Spieler 2</span></label>
              <select v-model="form.player2" class="w-full border border-gray-300 rounded-md p-2 bg-white text-black">
                <option disabled value="">Spieler wählen</option>
                <option v-for="player in tournament.expand.players" :key="player.id" :value="player.id">
                  {{ player.name }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="label"><span class="label-text text-gray-700">🏆 Gewinner</span></label>
            <select v-model="form.winner" class="w-full border border-gray-300 rounded-md p-2 bg-white text-black">
              <option disabled value="">Spieler wählen</option>
              <option v-for="player in tournament.expand.players" :key="player.id" :value="player.id">
                {{ player.name }}
              </option>
            </select>
          </div>

          <div class="modal-action">
            <button type="submit" class="btn bg-indigo-600 hover:bg-indigo-500 text-white transition rounded-md px-6 py-2">
              Speichern
            </button>
          </div>
        </form>
      </div>
    </dialog>
  </div>

</template>

