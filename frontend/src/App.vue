<template>
  <div class="min-h-screen bg-gray-100">
    <router-view />
    <chat-bot />

  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import ChatBot from '@/components/chatbot/ChatBot.vue';

const authStore = useAuthStore();

onMounted(async () => {
  // Try to fetch the user data if there's a token in localStorage
  if (localStorage.getItem('token')) {
    try {
      await authStore.fetchUser();
    } catch (error) {
      console.error('Failed to fetch user data:', error);
    }
  }
});
</script>
