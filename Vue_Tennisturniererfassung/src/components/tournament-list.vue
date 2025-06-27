<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useTournaments } from '@/useTournaments.js'

const { tournaments, fetchTournaments, createTournament } = useTournaments()

const modalRef = ref(null)

function closeModal() {
  modalRef.value?.close()
}

const form = reactive({
  name: '',
  description: '',
  fromdate: '',
  todate: '',
  registration_deadline: '',
  entryfee: '',
  pricewinner: ''
})

onMounted(() => {
  fetchTournaments()
})

async function handleSubmit() {
  const {
    name,
    description,
    fromdate,
    todate,
    registration_deadline,
    entryfee,
    pricewinner
  } = form;

  if (
    !name || !description || !fromdate || !todate ||
    !registration_deadline || !entryfee || !pricewinner
  ) {
    alert("Bitte fülle alle Felder aus.");
    return;
  }

  try {
    // Validierung: sind die Dates wirklich gültig?
    const parsedFrom = new Date(`${fromdate}T00:00:00`);
    const parsedTo = new Date(`${todate}T00:00:00`);
    const parsedDeadline = new Date(`${registration_deadline}T00:00:00`);

    if (
      isNaN(parsedFrom.getTime()) ||
      isNaN(parsedTo.getTime()) ||
      isNaN(parsedDeadline.getTime())
    ) {
      alert("Mindestens ein Datum ist ungültig.");
      return;
    }

    const data = {
      name,
      description,
      fromdate: parsedFrom.toISOString(),
      todate: parsedTo.toISOString(),
      registration_deadline: parsedDeadline.toISOString(),
      entryfee,
      pricewinner: parseFloat(pricewinner),
      players: []
    };

    await createTournament(data);
    await fetchTournaments();

    modalRef.value?.close();

    // Reset
    form.name = '';
    form.description = '';
    form.fromdate = '';
    form.todate = '';
    form.registration_deadline = '';
    form.entryfee = '';
    form.pricewinner = '';

  } catch (err) {
    console.error("Fehler beim Erstellen des Turniers:", err);
    alert("Speichern fehlgeschlagen.");
  }
}


</script>

<template>
  <div class="p-6">
    <h2 class="text-3xl font-bold text-white mb-6">
      Alle Turniere
      <button @click="modalRef?.showModal()" class="btn bg-indigo-600 hover:bg-indigo-500 text-white transition rounded-md px-6 py-2 mx-3">
        +
      </button>
    </h2>

    <div class="grid gap-6 md:grid-cols-2">
      <div
        v-for="tournament in tournaments"
        :key="tournament.id"
        class="bg-gray-800 text-white rounded-2xl p-6 shadow-lg space-y-4 transition hover:shadow-xl"
      >
        <h3 class="text-2xl font-semibold">{{ tournament.name }}</h3>
        <p v-html="tournament.description" class="text-gray-300"></p>
        <p>
          <strong>Datum:</strong>
          {{ new Date(tournament.fromdate).toLocaleDateString('de-DE') }} -
          {{ new Date(tournament.todate).toLocaleDateString('de-DE') }}
        </p>
        <div class="flex justify-end">
          <router-link
            :to="`/tournaments/${tournament.id}`"
            class="bg-gradient-to-r from-indigo-700 to-orange-500 text-white rounded-xl px-6 py-2 text-base font-medium hover:opacity-90 transition"
          >
            Mehr erfahren
          </router-link>
        </div>
      </div>
    </div>

    <dialog ref="modalRef" class="modal">
      <div class="modal-box bg-white text-black rounded-xl shadow-xl">
        <button @click="closeModal" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <h3 class="font-bold text-2xl text-gray-800">Neues Turnier erstellen</h3>

          <input v-model="form.name" type="text" placeholder="Name" class="w-full border border-gray-300 rounded-md p-2" />
          <textarea v-model="form.description" placeholder="Beschreibung" class="w-full border border-gray-300 rounded-md p-2"></textarea>

          <label>Startdatum</label>
          <input v-model="form.fromdate" type="date" placeholder="Startdatum" class="w-full border border-gray-300 rounded-md p-2" />
          <label>Enddatum</label>
          <input v-model="form.todate" type="date" placeholder="Enddatum" class="w-full border border-gray-300 rounded-md p-2" />
          <label>Anmeldefrist bis: </label>
          <input v-model="form.registration_deadline" type="date" placeholder="Anmeldefrist" class="w-full border border-gray-300 rounded-md p-2" />

          <input v-model="form.entryfee" type="number" placeholder="Startgeld" class="w-full border border-gray-300 rounded-md p-2" />
          <input v-model="form.pricewinner" type="number" placeholder="Preis für Gewinner" class="w-full border border-gray-300 rounded-md p-2" />

          <div class="modal-action">
            <button type="submit" class="btn bg-indigo-600 hover:bg-indigo-500 text-white rounded-md px-6 py-2">
              Speichern
            </button>
          </div>
        </form>
      </div>
    </dialog>
  </div>
</template>
