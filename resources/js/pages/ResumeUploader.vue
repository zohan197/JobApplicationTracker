<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="Resumes" />

    <div class="px-4 py-6 space-y-6">
      <HeadingSmall
        title="Resumes"
        description="Upload and manage your resumes and cover letters here."
      />

      <!-- Upload Form -->
      <form @submit.prevent="uploadResume" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="grid gap-2">
            <Label for="title">Title</Label>
            <Input id="title" v-model="title" required placeholder="e.g. Senior Dev Resume" />
          </div>

          <div class="grid gap-2">
            <Label for="type">Type</Label>
            <select
              id="type"
              v-model="type"
              required
              class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground shadow-sm focus:border-primary focus:ring focus:ring-primary/20"
            >
              <option disabled value="">Select Type</option>
              <option value="resume">Resume</option>
              <option value="cover_letter">Cover Letter</option>
            </select>
          </div>

          <div class="grid gap-2 md:col-span-2">
            <Label for="file">File (PDF only)</Label>
            <Input id="file" type="file" accept="application/pdf" @change="handleFileUpload" required />
          </div>
        </div>

        <div class="flex items-center gap-4">
          <Button type="submit">Upload</Button>
        </div>
      </form>

      <!-- Uploaded Resumes -->
      <div v-if="resumes" class="space-y-4">
        <div
          v-for="item in resumes"
          :key="item.id"
          class="rounded-lg border border-border bg-muted p-4 flex justify-between items-center"
        >
          <div>
            <h4 class="font-medium text-sm text-foreground">
              {{ item.title }}
            </h4>
            <p class="text-sm text-muted-foreground capitalize">
              {{ item.type.replace('_', ' ') }}
            </p>
          </div>
          <div class="flex gap-2">
            <Button size="sm" variant="secondary" @click="download(item.id)">Download</Button>
            <Button size="sm" variant="destructive" @click="remove(item.id)">Delete</Button>
          </div>
        </div>
      </div>

      <p v-else class="text-sm text-muted-foreground">No resumes uploaded yet.</p>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';
import { router } from '@inertiajs/vue3'

defineProps<{
  resumes: any[];
  errors: Record<string, string>;
}>();


const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Resumes',
    href: '/resumes',
  },
];

const title = ref('');
const type = ref('');
const file = ref<File | null>(null);

const handleFileUpload = (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    file.value = target.files[0];
  }
};

const uploadResume = async () => {
  const formData = new FormData();
  formData.append('title', title.value);
  formData.append('type', type.value);
  if (file.value) {
    formData.append('file', file.value);
  }

  await axios.post('/resumes', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });

  title.value = '';
  type.value = '';
  file.value = null;

  // Reload page or emit event if needed
  await router.reload({ only: ['resumes'] });
};

const remove = async (id: number) => {
  await axios.delete(`/resumes/${id}`);
  await router.reload({ only: ['resumes'] });
};

const download = async (id: number) => {
  const response = await axios.get(`/resumes/${id}/download`, {
    responseType: 'blob',
  });

  const url = window.URL.createObjectURL(new Blob([response.data]));
  const link = document.createElement('a');
  link.href = url;
  link.setAttribute('download', 'resume.pdf');
  document.body.appendChild(link);
  link.click();
  link.remove();
};
</script>
