<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import StatCard from '@/components/StatCard.vue'
import { ref, onMounted } from 'vue'
import axios from 'axios'
import UpcomingReminders from '@/components/UpcomingReminders.vue'
import CalendarWidget from '@/components/CalendarWidget.vue'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
]

const totalApplications = ref(0)
const statusCounts = ref<{ [key: string]: number }>({})
const upcomingReminders = ref(0)

async function fetchDashboardStats() {
  try {
    const res = await axios.get('/dashboard-stats')
    totalApplications.value = res.data.totalApplications
    statusCounts.value = res.data.statusCounts
    upcomingReminders.value = res.data.upcomingReminders
  } catch (error) {
    console.error('Failed to load dashboard stats', error)
  }
}

onMounted(() => {
  fetchDashboardStats()
})
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <StatCard title="Total Applications" :count="totalApplications" />
        <StatCard
          v-for="(count, status) in statusCounts"
          :key="status"
          :title="`Applications: ${status}`"
          :count="count"
        />
        <StatCard title="Upcoming Reminders" :count="upcomingReminders" />
      </div>
      <div class="grid gap-4 mt-6 lg:grid-cols-6">
        <!-- Calendar Widget - 2/3 width -->
        <div class="relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border lg:col-span-3">
            <CalendarWidget />
        </div>

        <!-- Upcoming Reminders - 1/3 width -->
        <div class="relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border lg:col-span-3">
            <UpcomingReminders />
        </div>
        </div>
    </div>
  </AppLayout>
</template>
