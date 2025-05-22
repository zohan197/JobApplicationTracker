<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Job Listings" />

    <div class="px-4 py-6">
      <HeadingSmall
        title="Job Listings"
        description="Track your job applications here. You can add, view, edit, and remove your job applications."
      />

      <!-- Loading Indicator -->
      <div v-if="store.loading" class="text-sm text-muted-foreground">Loading...</div>

      <!-- Job List -->
      <div v-else class="space-y-4 py-4">
        <div
          v-for="job in listings"
          :key="job.id"
          class="rounded-lg border border-border bg-muted p-4"
        >
          <div class="flex justify-between items-center">
            <div>
              <h3 class="font-semibold text-sm text-foreground">{{ job.position }}</h3>
              <p class="text-sm text-muted-foreground">
                @ {{ job.company }} ({{ job.status }})
              </p>
              <p class="text-xs text-muted-foreground">
                Added on: {{ formatDate(job.created_at) }}
              </p>
            </div>
            <div class="flex gap-2">
              <Button size="sm" variant="outline" @click="editJob(job)">Edit</Button>
              <Button size="sm" variant="destructive" @click="store.deleteListing(job.id)">Delete</Button>
              <Button size="sm" variant="outline" @click="openNotesModal(job)">Notes</Button>
              <Button size="sm" variant="outline" @click="openReminderModal(job)">Reminder</Button>
            </div>
          </div>
        </div>

        <!-- Notes Modal -->
        <JobNotesModal
          :open="showNotesModal"
          :job-id="selectedJob?.id ?? null"
          :job-title="selectedJob?.position ?? ''"
          @close="showNotesModal = false"
          @update:open="val => showNotesModal = val"
        />

        <!-- Reminder Modal -->
        <ReminderModal
          :open="showReminderModal"
          :job-id="selectedJob?.id ?? null"
          :job-title="selectedJob?.position ?? ''"
          @close="showReminderModal = false"
          @update:open="val => showReminderModal = val"
        />

      </div>

      <!-- Job Form -->
      <form
        @submit.prevent="submitForm"
        class="space-y-6 pt-6"
        :key="isEditing ? editingId : 'new'" 
      >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-for="(label, key) in fields" :key="key" class="grid gap-2">
            <Label :for="key">{{ label }}</Label>
            <Input
              :id="key"
              v-model="form[key]"
              :placeholder="label"
              :type="key === 'applied_at' ? 'date' : 'text'"
            />
            <p v-if="formErrors[key]" class="text-sm text-red-500">{{ formErrors[key] }}</p>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <Button type="submit" :disabled="submitting">
            <Loader v-if="submitting" class="mr-2 h-4 w-4 animate-spin" />
            {{ isEditing ? 'Update Job' : 'Add Job' }}
          </Button>
          <Button v-if="isEditing" type="button" @click="cancelEdit" variant="outline">
            Cancel
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useJobListingStore } from '@/stores/jobListing';
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItem } from '@/types';
import JobNotesModal from '@/components/JobNotesModal.vue';
import { Loader } from 'lucide-vue-next';
import ReminderModal from '@/components/ReminderModal.vue';

defineProps<{
  listings: any[]
}>();

const breadcrumbItems: BreadcrumbItem[] = [
  { title: 'Job Listings', href: '/job-listings' }
];

const submitting = ref(false);
const fields: Record<string, string> = {
  company: 'Company',
  position: 'Position',
  status: 'Status',
  url: 'URL',
  applied_at: 'Applied Date'
};

const store = useJobListingStore();

const form = reactive({
  company: '',
  position: '',
  status: '',
  url: '',
  applied_at: '',
});

const formErrors = reactive<Record<string, string>>({});
const isEditing = ref(false);
const editingId = ref<number | null>(null);

function clearErrors() {
  Object.keys(formErrors).forEach(key => delete formErrors[key]);
}

function resetForm() {
  Object.assign(form, {
    company: '',
    position: '',
    status: '',
    url: '',
    applied_at: '',
  });
  clearErrors();
}

function editJob(job: any) {
  Object.assign(form, { ...job });
  editingId.value = job.id;
  isEditing.value = true;
  clearErrors();
}

function cancelEdit() {
  resetForm();
  isEditing.value = false;
  editingId.value = null;
}

function submitForm() {
  submitting.value = true;

  const action = isEditing.value && editingId.value
    ? store.updateListing(editingId.value, form)
    : store.createListing(form);

  action
    .then(() => {
      resetForm();
      isEditing.value = false;
      editingId.value = null;
    })
    .catch(error => {
      if (error.response?.status === 422) {
        const errors = error.response.data.errors;
        Object.assign(formErrors, errors);
      } else {
        console.error("Unexpected error:", error);
      }
    })
    .finally(() => {
      submitting.value = false;
    });
}

// Notes modal state
const showNotesModal = ref(false);
const selectedJob = ref<{ id: number | null; position: string }>({ id: null, position: '' });

function openNotesModal(job: any) {
  selectedJob.value = job;
  showNotesModal.value = true;
}

// Reminder modal state
const showReminderModal = ref(false);

function openReminderModal(job: any) {
  selectedJob.value = job;
  showReminderModal.value = true;
}

function formatDate(dateStr: string | null): string {
  if (!dateStr) return '';
  const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateStr).toLocaleDateString(undefined, options);
}
</script>
