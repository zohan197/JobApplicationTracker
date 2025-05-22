<!-- @/components/ui/textarea.vue -->
<template>
  <textarea
  :value="modelValue"
  @input="$emit('update:modelValue', $event.target.value)"
  class="w-full min-h-[120px] rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
  v-bind="$attrs"
></textarea>

</template>

<script setup lang="ts">
defineProps({
  modelValue: String
})
defineEmits(['update:modelValue'])
</script>