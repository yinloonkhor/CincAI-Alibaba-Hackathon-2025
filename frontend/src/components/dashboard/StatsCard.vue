<template>
  <div class="bg-white overflow-hidden shadow-sm rounded-lg transition-all duration-300 hover:shadow-md h-full">
    <div class="p-5 flex flex-col h-full">
      <div class="flex-shrink-0 mb-4">
        <div :class="`${color} text-white p-3 rounded-lg inline-flex items-center justify-center`">
          <component :is="icon" class="h-6 w-6" aria-hidden="true" />
        </div>
      </div>
      <div class="flex-1">
        <h3 class="text-sm font-medium text-gray-500 truncate">{{ title }}</h3>
        <div class="mt-2 mb-1">
          <div class="text-2xl font-semibold text-gray-900">{{ value }}</div>
        </div>
        <p class="text-xs text-gray-600">{{ description }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  icon: {
    type: Object,
    required: true
  },
  color: {
    type: String,
    default: 'bg-blue-500'
  }
});
</script>
