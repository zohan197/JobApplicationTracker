<!-- components/StatCard.vue -->
<template>
  <div
    class="flex flex-col justify-center rounded-xl border border-sidebar-border/70 bg-white/50 p-6 text-center dark:bg-gray-900 dark:text-gray-100"
  >
    <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ title }}</h3>
    <p class="text-3xl font-bold">{{ count }}</p>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title: string;
  count: number | string;
}>();
</script>
