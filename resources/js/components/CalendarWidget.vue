<template>
  <div class="p-6 max-w-full max-h-full bg-white rounded-xl">
    <h2 class="text-2xl font-semibold mb-6 text-gray-900">Reminder Calendar</h2>
    <VueCal
      class="modern-calendar"
      :events="events"
      :time="false"
      :on-event-click="onEventClick"
      :default-view="defaultView"
      hide-title-bar
      style="height: 650px; width: 100%;"
      :day-format="{ weekday: 'short', day: 'numeric' }"
    />
      <div class="mt-4">
        <p class="text-sm text-gray-500">
          Click on an event to view details.
        </p>
      </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import axios from 'axios';
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';

const events = ref([]);

async function fetchReminders() {
  try {
    const res = await axios.get('/dashboard/reminders');
    events.value = res.data.map((reminder: any) => ({
      start: reminder.remind_at,
      end: reminder.remind_at,
      title: reminder.position ?? 'Reminder',
      content: `Company: ${reminder.company} \n\nNote: ${reminder.note}`,
    }));
  } catch (err) {
    console.error('Error fetching reminders:', err);
  }
}

const defaultView = ref('month');

onMounted(() => {
  if (window.innerWidth < 600) {
    defaultView.value = 'agenda'; // or 'week'
  }
});

function onEventClick(event: any) {
  alert(`${event.title}\n\n${event.content}`);
}

onMounted(fetchReminders);
</script>

<style scoped>
.modern-calendar {
  font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  border-radius: 14px;
  background-color: #fff;
  box-shadow: 0 10px 20px rgb(0 0 0 / 0.07);
  color: #111827;
  height: 100% !important;
  width: 100% !important;
  user-select: none;
  height: 400px !important;
}

/* Weekday header */
.modern-calendar .vuecal__weekdays {
  display: flex;
  justify-content: space-between;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #e5e7eb;
  text-transform: uppercase;
  font-weight: 700;
  font-size: 0.5rem !important;
  color: #6b7280;
  letter-spacing: 0.1em;
  user-select: none;
}

/* Day cells */
.modern-calendar .vuecal__cell {
  border-top: 1px solid #f3f4f6;
  border-left: none !important;
  border-right: none !important;
  padding: 0.75rem 1rem;
  min-height: 7rem;
  font-size: 1rem;
  font-weight: 500;
  position: relative;
  transition: background-color 0.15s ease;
  color: #374151;
}

.modern-calendar .vuecal__cell:hover {
  background-color: #f9fafb;
  cursor: default;
}

/* Event styling */
.modern-calendar .vuecal__event {
  background-color: #2563eb;
  color: white !important;
  padding: 0.4rem 0.8rem !important;
  border-radius: 0.5rem !important;
  font-size: 0.85rem !important;
  font-weight: 600;
  box-shadow: 0 4px 12px rgb(37 99 235 / 0.35);
  margin-bottom: 0.3rem !important;
  cursor: pointer;
  white-space: normal !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  user-select: none;
}

.modern-calendar .vuecal__event:hover {
  background-color: #1e40af;
  box-shadow: 0 6px 16px rgb(30 64 175 / 0.5);
}

/* Remove weekday vertical borders */
.modern-calendar .vuecal__weekdays span {
  border: none !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .modern-calendar {
    font-size: 0.85rem;
  }
  .modern-calendar .vuecal__cell {
    min-height: 5.5rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
  }
  .modern-calendar .vuecal__event {
    font-size: 0.75rem !important;
    padding: 0.3rem 0.5rem !important;
  }
  .modern-calendar .vuecal__weekdays {
    font-size: 0.7rem;
  }
}

.modern-calendar .vuecal__header {
  font-size: 0.5rem !important;
  color: red !important;
}
</style>

<!-- <style scoped>
.custom-calendar {
  font-size: 0.85rem;
}

.custom-calendar .vuecal__cell {
  padding: 0.25rem;
  font-size: 0.85rem;
}

.custom-calendar .vuecal__event {
  font-size: 0.75rem !important;
  padding: 2px 4px;
  border-radius: 4px;
  white-space: normal !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  max-height: 2.5em;
  line-height: 1.2em;
}

.custom-calendar .vuecal__weekdays span {
  font-size: 0.8rem;
}



/* Responsive adjustments */
@media (max-width: 768px) {
  .custom-calendar {
    font-size: 0.7rem;
  }
  .custom-calendar .vuecal__cell {
    padding: 0.15rem;
    font-size: 0.7rem;
  }
  .custom-calendar .vuecal__event {
    font-size: 0.6rem !important;
    padding: 1px 2px;
    max-height: 3em;
  }
  .custom-calendar .vuecal__weekdays span {
    font-size: 0.55rem;
  }
}
</style> -->


