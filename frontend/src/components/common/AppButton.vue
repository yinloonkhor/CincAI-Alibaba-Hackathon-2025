<template>
    <button
      :type="type"
      :disabled="disabled || loading"
      :class="[
        'px-4 py-2 rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors',
        variant === 'primary' ? 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500' : '',
        variant === 'secondary' ? 'bg-gray-200 hover:bg-gray-300 text-gray-800 focus:ring-gray-500' : '',
        variant === 'danger' ? 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500' : '',
        disabled ? 'opacity-50 cursor-not-allowed' : '',
        fullWidth ? 'w-full' : '',
        className
      ]"
    >
      <div class="flex items-center justify-center">
        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <slot></slot>
      </div>
    </button>
  </template>
  
  <script setup lang="ts">
  defineProps({
    type: {
      type: String,
      default: 'button'
    },
    variant: {
      type: String,
      default: 'primary',
      validator: (value: string) => ['primary', 'secondary', 'danger'].includes(value)
    },
    disabled: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    },
    fullWidth: {
      type: Boolean,
      default: false
    },
    className: {
      type: String,
      default: ''
    }
  });
  </script>
  