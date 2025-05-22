<template>
  <Dialog v-model:open="isOpen" class="z-50">
    <DialogContent class="max-w-2xl">
      <DialogHeader>
        <DialogTitle>Reminders for: {{ jobTitle }}</DialogTitle>
        <DialogDescription class="text-sm text-gray-400">
          Set and manage follow-up reminders for this job application.
        </DialogDescription>
      </DialogHeader>

      <div class="space-y-4 mt-4">
        <!-- Form to add a new reminder -->
        <form @submit.prevent="submitReminder">
          <div class="mb-2">
            <label for="reminderDate" class="block text-sm font-medium text-gray-700">
            Date</label>
            <input
              type="date"
              v-model="reminderDate"
              class="w-full border rounded p-2"
              required
            />
          </div>
          <div class="mb-2">
            <label for="reminderNote" class="block text-sm font-medium text-gray-700">
            Note</label>
            <input
            type="text"
            v-model="reminderNote"
            class="w-full border rounded p-2"
            required
            />
          </div>
          <Button type="submit" class="mt-2" :disabled="submitting">
            <Loader v-if="submitting" class="mr-2 h-4 w-4 animate-spin" />
            Set Reminder
          </Button>
        </form>

        <!-- Existing reminders -->
        <div v-if="reminders.length" class="mt-6 space-y-2">
        <div
            v-for="reminder in reminders"
            :key="reminder.id"
            class="p-3 border rounded-xl bg-gray-50 dark:bg-gray-800 flex justify-between items-center"
        >
            <div class="flex items-center gap-3">
            <!-- Checkbox to mark as notified -->
            <input
                type="checkbox"
                :checked="reminder.notified"
                :disabled="reminder.notified"
                @change="markAsNotified(reminder)"
                title="Mark as notified"
                class="accent-green-500 h-4 w-4"
            />

            <div class="text-sm" :class="{ 'line-through text-gray-400': reminder.notified }">
                <span class="font-semibold">Date:</span>
                {{ formatDate(reminder.remind_at) }}
                <div>
                <span class="font-semibold">Note:</span>
                {{ reminder.note }}
                </div>
            </div>
            </div>

            <Button variant="ghost" size="icon" @click="deleteReminder(reminder)">
            <Trash2 class="w-4 h-4 text-red-500" />
            </Button>
        </div>
        </div>

      </div>

      <DialogFooter>
        <Button variant="outline" @click="closeModal">Close</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import axios from 'axios'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Loader, Trash2 } from 'lucide-vue-next'

const props = defineProps<{
  open: boolean;
  jobId: number | null;
  jobTitle: string;
}>();
const emit = defineEmits(['close', 'update:open'])

const isOpen = ref(props.open)
const reminders = ref([])
const reminderDate = ref('')
const reminderNote = ref('')
const submitting = ref(false)

// Watch for modal open state from parent
watch(() => props.open, (val) => {
  isOpen.value = val
  if (val) fetchReminders()
})

// Sync open state back to parent
watch(isOpen, (val) => {
  if (val !== props.open) {
    emit('update:open', val)
  }
})

// Emit to update parent when modal is closed
function closeModal() {
  emit('update:open', false);
  emit('close');
}

async function fetchReminders() {
  try {
    const res = await axios.get(`/job-listings/${props.jobId}/reminders`)
    reminders.value = res.data
  } catch (err) {
    console.error('Failed to load reminders', err)
  }
}

async function submitReminder() {
  if (!reminderDate.value) {
    alert('Please select a date.')
    return
  }

  submitting.value = true
  try {
    const res = await axios.post(`/job-listings/${props.jobId}/reminders`, {
      remind_at: reminderDate.value,
      note: reminderNote.value,
    })
    reminders.value.unshift(res.data)
    reminderDate.value = ''
    reminderNote.value = ''
  } catch (err) {
    console.error('Failed to add reminder', err)
  } finally {
    submitting.value = false
  }
}

async function deleteReminder(reminder: { id: number }) {
  if (!confirm('Are you sure you want to delete this reminder?')) return
  try {
    await axios.delete(`/reminders/${reminder.id}`)
    reminders.value = reminders.value.filter((r) => r.id !== reminder.id)
  } catch (err) {
    console.error('Failed to delete reminder', err)
  }
}

function formatDate(dateStr: string) {
  const d = new Date(dateStr)
  return d.toLocaleDateString() + ' ' + d.toLocaleTimeString()
}


async function markAsNotified(reminder: any) {
  try {
    const res = await axios.patch(`/job-listings/${props.jobId}/reminders/${reminder.id}/mark-as-notified`)
    Object.assign(reminder, res.data)
    reminder.notified = true // Update local reminder with fresh data from backend
  } catch (err) {
    console.error('Failed to mark as notified', err)
  }
}



</script>
