<template>
  <Dialog v-model:open="isOpen" class="z-50">
    <DialogContent class="max-w-2xl">
      <DialogHeader>
        <DialogTitle>Notes for: {{ jobTitle }}</DialogTitle>
        <DialogDescription class="text-sm text-gray-400">
          Add or manage your notes related to this job application.
        </DialogDescription>
      </DialogHeader>

      <div class="space-y-4 mt-4">
        <!-- Form to add a new note -->
        <form @submit.prevent="submitNote">
          <Textarea
            v-model="noteInput"
            placeholder="Write a new note..."
            label="New Note"
            id="note"
            :rows="4"
            class="w-full"
          />
          <Button type="submit" class="mt-2" :disabled="submitting">
            <Loader v-if="submitting" class="mr-2 h-4 w-4 animate-spin" />
            Add Note
          </Button>
        </form>

        <!-- Existing notes -->
        <div v-if="notes.length" class="mt-6 space-y-2">
          <div
            v-for="note in notes"
            :key="note.id"
            class="p-3 border rounded-xl bg-gray-50 dark:bg-gray-800 flex justify-between items-start"
          >
            <div class="w-full">
              <Textarea
                v-model="note.content"
                placeholder="Edit your note..."
                label="Note"
                :rows="2"
                class="w-full bg-transparent resize-none border-none p-0 focus:ring-0"
                @blur="updateNote(note)"
              />
              <div class="text-xs text-gray-500 mt-1">
                Updated: {{ formatDate(note.updated_at) }}
              </div>
            </div>
            <Button variant="ghost" size="icon" @click="deleteNote(note)">
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
import { Textarea } from '@/components/ui/Textarea'

const props = defineProps({
  open: Boolean,
  jobId: Number,
  jobTitle: String,
})
const emit = defineEmits(['close', 'update:open'])

const isOpen = ref(props.open)
const notes = ref([])               // List of notes
const noteInput = ref('')           // New note input
const submitting = ref(false)       // Loading state

// Watch for modal open state from parent
watch(() => props.open, (val) => {
  isOpen.value = val
  if (val) fetchNotes()
})

// Sync open state back to parent
watch(isOpen, (val) => {
  if (val !== props.open) {
    emit('update:open', val)
  }
})

function closeModal() {
  isOpen.value = false
  emit('close')
}

async function fetchNotes() {
  try {
    const res = await axios.get(`/job-listings/${props.jobId}/notes`)
    notes.value = res.data
  } catch (err) {
    console.error('Failed to load notes', err)
  }
}

async function submitNote() {
  if (!noteInput.value.trim()) {
    alert('Please enter some note content before adding.')
    return
  }

  submitting.value = true

  try {
    const res = await axios.post(`/job-listings/${props.jobId}/notes`, {
      content: noteInput.value,
    })
    notes.value.unshift(res.data)
    noteInput.value = ''
  } catch (err) {
    console.error('Failed to add note', err)
  } finally {
    submitting.value = false
  }
}

async function updateNote(note: { id: number; content: string }) {
  try {
    await axios.put(`/notes/${note.id}`, { content: note.content })
  } catch (err) {
    console.error('Failed to update note', err)
  }
}

async function deleteNote(note: { id: number }) {
  if (!confirm('Are you sure you want to delete this note?')) return
  try {
    await axios.delete(`/notes/${note.id}`)
    notes.value = notes.value.filter((n) => n.id !== note.id)
  } catch (err) {
    console.error('Failed to delete note', err)
  }
}

function formatDate(dateStr: string) {
  const d = new Date(dateStr)
  return d.toLocaleDateString() + ' ' + d.toLocaleTimeString()
}
</script>

