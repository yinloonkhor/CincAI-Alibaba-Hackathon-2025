import { defineStore } from 'pinia';
import { ref } from 'vue';

interface ChatMessage {
  text: string;
  isBot: boolean;
  time: string;
}

export const useChatStore = defineStore('chat', () => {
  const isOpen = ref(false);
  const messages = ref<ChatMessage[]>([
    {
      text: "Hello! I'm your tax assistant. How can I help you today?",
      isBot: true,
      time: formatTime(new Date())
    }
  ]);
  function toggleChat() {
    isOpen.value = !isOpen.value;
  }

  function minimizeChat() {
    isOpen.value = false;
  }

  function addMessage(message: ChatMessage) {
    messages.value.push(message);
  }

  function removeLastMessage() {
    messages.value.pop();
  }

  function clearMessages() {
    messages.value = [];
  }

  function formatTime(date: Date): string {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  return {
    isOpen,
    messages,
    toggleChat,
    minimizeChat,
    addMessage,
    removeLastMessage,
    clearMessages,
    formatTime
  };
});
