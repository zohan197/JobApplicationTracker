<template>
  <div class="p-4">
    <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200">
      Upcoming Reminders (Next 7 Days)
    </h2>
    <ul class="space-y-3 max-h-80 overflow-y-auto">
      <li
        v-for="reminder in reminders"
        :key="reminder.id"
        class="rounded border border-sidebar-border/70 p-3 dark:border-sidebar-border bg-white/50 dark:bg-gray-900"
      >
        <div class="flex justify-between items-center">
          <p class="font-medium text-gray-900 dark:text-gray-100">{{ reminder.note }}</p>
          <small class="text-sm text-gray-500 dark:text-gray-400">
            {{ formatDate(reminder.remind_at) }}
          </small>
        </div>
        <p class="text-sm text-gray-600 font-bold dark:text-gray-300 mt-1">
          {{ reminder.company }} — <em>{{ reminder.position }}</em>
        </p>

        <div v-if="reminder.notes?.length" class="mt-2 text-sm text-gray-700 dark:text-gray-300">
          <p class="font-semibold">Related Notes:</p>
          <ul class="list-disc list-inside pl-2">
            <li v-for="(note, idx) in reminder.notes" :key="idx">{{ note }}</li>
          </ul>
        </div>
      </li>

      <li v-if="reminders.length === 0" class="text-center text-gray-500 dark:text-gray-400">
        No reminders coming up!
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

interface Reminder {
  id: number
  note: string
  remind_at: string
  company: string
  position: string
  notes: string[]
}

const reminders = ref<Reminder[]>([])

function formatDate(dateStr: string) {
  const options: Intl.DateTimeFormatOptions = {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }
  return new Date(dateStr).toLocaleDateString(undefined, options)
}

async function fetchReminders() {
  try {
    const res = await axios.get('/upcoming-reminders')
    reminders.value = res.data
  } catch (err) {
    console.error('Failed to load reminders', err)
  }
}

onMounted(() => {
  fetchReminders()
})
</script>
