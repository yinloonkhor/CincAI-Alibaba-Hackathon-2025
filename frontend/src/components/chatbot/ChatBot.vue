<template>
  <div v-if="authStore.isAuthenticated" class="fixed bottom-4 right-4 z-50">
    <button 
      @click="chatStore.toggleChat"
      class="bg-green-600 hover:bg-green-700 text-white rounded-full p-3 shadow-lg transition-all duration-300 flex items-center justify-center"
      :class="{ 'rotate-45': chatStore.isOpen }"
    >
      <ChatBubbleLeftRightIcon v-if="!chatStore.isOpen" class="h-6 w-6" />
      <XMarkIcon v-else class="h-6 w-6" />
    </button>

    <div 
      v-show="chatStore.isOpen" 
      class="absolute bottom-16 right-0 w-80 bg-white rounded-lg shadow-xl border border-gray-200 transition-all duration-300 transform origin-bottom-right"
      :class="{ 'scale-100 opacity-100': chatStore.isOpen, 'scale-95 opacity-0': !chatStore.isOpen }"
    >
      <div class="bg-green-600 text-white p-3 rounded-t-lg flex items-center justify-between">
        <div class="flex items-center">
          <div class="bg-white p-1 rounded-full mr-2">
            <ChatBubbleLeftRightIcon class="h-5 w-5 text-green-600" />
          </div>
          <h3 class="font-medium text-sm">Tax Assistant</h3>
        </div>
        <div class="flex space-x-2">
         
          <button @click="chatStore.minimizeChat" class="text-white hover:text-green-200">
            <MinusSmallIcon class="h-5 w-5" />
          </button>
        </div>
      </div>

      <div class="h-80 overflow-y-auto p-3 bg-gray-50" ref="messagesContainer">
        <div v-for="(message, index) in chatStore.messages" :key="index" class="mb-3">
  <div 
    :class="[
      'max-w-[80%] rounded-lg p-2 text-sm', 
      message.isBot 
        ? 'bg-gray-200 text-gray-800 mr-auto' 
        : 'bg-green-600 text-white ml-auto'
    ]"
  >
    {{ message.text }}
    <!-- Loading animation dots -->
    <span v-if="message.isBot && isProcessing" class="inline-flex">
      <span class="animate-bounce">.</span>
      <span class="animate-bounce delay-100">.</span>
      <span class="animate-bounce delay-200">.</span>
    </span>
  </div>
  <div 
    :class="[
      'text-xs mt-1', 
      message.isBot ? 'text-left' : 'text-right'
    ]"
  >
    {{ message.time }}
  </div>
</div>

      </div>

      <div class="p-3 border-t border-gray-200">
        <div class="flex">
          <input 
            v-model="newMessage" 
            @keyup.enter="sendMessage"
            type="text" 
            placeholder="Type your message..." 
            class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
          />
          <button 
            @click="sendMessage"
            class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-r-lg transition-colors duration-150"
          >
            <PaperAirplaneIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick, defineComponent, h, watch } from 'vue';
import { useChatStore } from '@/stores/chat';
import api from '@/services/api';
import { useAuthStore } from '@/stores/auth';
const authStore = useAuthStore();

const chatStore = useChatStore();
const newMessage = ref('');
const messagesContainer = ref<HTMLElement | null>(null);
  const isProcessing = ref(false);
  const loadingMessage = ref('');
  const loadingMessages = [
  "Hold on, scrolling through the tax scrolls... 📜",
  "Crunching numbers at lightning speed! ⚡",
  "Consulting with our tax wizards... 🧙‍♂️",
  "Diving deep into the financial archives... 🏊‍♂️",
  "Calculating with precision and a dash of magic... ✨",
  "Summoning the best tax advice... 🎯",
  "Decoding the tax mysteries... 🔍",
  "Our tax elves are working hard... 🧝‍♂️"
];

const sendMessage = async () => {
  if (!newMessage.value.trim()) return;
  
  chatStore.addMessage({
    text: newMessage.value,
    isBot: false,
    time: chatStore.formatTime(new Date())
  });
  
  const userMessage = newMessage.value;
  newMessage.value = '';
  
  // Show random loading message
  isProcessing.value = true;
  loadingMessage.value = loadingMessages[Math.floor(Math.random() * loadingMessages.length)];
  chatStore.addMessage({
    text: loadingMessage.value,
    isBot: true,
    time: chatStore.formatTime(new Date())
  });
  
  try {
    const response = await api.post('/chat', {
      message: userMessage
    });

    if (response.data.success) {
      // Remove loading message
      chatStore.removeLastMessage();
      // Add real response
      chatStore.addMessage({
        text: response.data.data.answer,
        isBot: true,
        time: chatStore.formatTime(new Date())
      });
    }
  } catch (err) {
    chatStore.removeLastMessage();
    chatStore.addMessage({
      text: "I'm having trouble connecting right now. Please try again later.",
      isBot: true,
      time: chatStore.formatTime(new Date())
    });
  } finally {
    isProcessing.value = false;
  }
  
  nextTick(() => {
    scrollToBottom();
  });
};



const fetchChatHistory = async () => {
  try {
    const response = await api.get('/chat/history');
    if (response.data.success) {
      const history = response.data.data;
      history.reverse().forEach(chat => {
        chatStore.addMessage({
          text: chat.user_message,
          isBot: false,
          time: chatStore.formatTime(new Date(chat.created_at))
        });
        chatStore.addMessage({
          text: chat.bot_response,
          isBot: true,
          time: chatStore.formatTime(new Date(chat.created_at))
        });
      });
    }
  } catch (err) {
    console.error('Failed to fetch chat history:', err);
  }
};



function scrollToBottom() {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
}

// Icons
const ChatBubbleLeftRightIcon = defineComponent({
  render: () => h('svg', {
    xmlns: 'http://www.w3.org/2000/svg',
    fill: 'none',
    viewBox: '0 0 24 24',
    stroke: 'currentColor'
  }, [
    h('path', {
      'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'
    })
  ])
});

const XMarkIcon = defineComponent({
  render: () => h('svg', {
    xmlns: 'http://www.w3.org/2000/svg',
    fill: 'none',
    viewBox: '0 0 24 24',
    stroke: 'currentColor'
  }, [
    h('path', {
      'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M6 18L18 6M6 6l12 12'
    })
  ])
});

const MinusSmallIcon = defineComponent({
  render: () => h('svg', {
    xmlns: 'http://www.w3.org/2000/svg',
    fill: 'none',
    viewBox: '0 0 24 24',
    stroke: 'currentColor'
  }, [
    h('path', {
      'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M18 12H6'
    })
  ])
});

const PaperAirplaneIcon = defineComponent({
  render: () => h('svg', {
    xmlns: 'http://www.w3.org/2000/svg',
    fill: 'none',
    viewBox: '0 0 24 24',
    stroke: 'currentColor'
  }, [
    h('path', {
      'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M14 5l7 7m0 0l-7 7m7-7H3'
    })
  ])
});

const TrashIcon = defineComponent({
  render: () => h('svg', {
    xmlns: 'http://www.w3.org/2000/svg',
    fill: 'none',
    viewBox: '0 0 24 24',
    stroke: 'currentColor'
  }, [
    h('path', {
      'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
    })
  ])
});

watch(() => chatStore.isOpen, (isOpen) => {
  if (isOpen) {
    nextTick(() => {
      scrollToBottom();
    });
  }
});

watch(() => chatStore.messages.length, () => {
  nextTick(() => {
    scrollToBottom();
  });
});

onMounted(async () => {
  if(authStore.isAuthenticated){
    await fetchChatHistory();
  nextTick(() => {
    scrollToBottom();
  });
  }
 
});
</script>
<style scoped>
.delay-100 {
  animation-delay: 100ms;
}
.delay-200 {
  animation-delay: 200ms;
}
</style>