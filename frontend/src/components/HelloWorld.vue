<template>
  <h1 class="bg-red-500">{{ msg }}</h1>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const msg = ref('Hello World!')
</script>
