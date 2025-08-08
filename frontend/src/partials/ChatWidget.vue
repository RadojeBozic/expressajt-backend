<template>
  <div class="fixed bottom-6 right-6 z-50">
    <!-- Tooltip balon sa strelicom -->
    <transition name="fade">
      <div
        v-if="showTooltip"
        class="absolute right-16 bottom-3 bg-slate-800 text-white text-xs px-3 py-2 rounded shadow-md max-w-[200px] relative"
      >
        <span class="block">{{ $t('chat.tooltip') }}</span>
        <div class="absolute top-1/2 right-[-8px] transform -translate-y-1/2 w-0 h-0 border-y-8 border-y-transparent border-l-8 border-l-slate-800"></div>
      </div>
    </transition>

    <!-- Dugme za otvaranje -->
    <button @click="toggleChat" class="bg-purple-600 text-white p-3 rounded-full shadow-lg cursor-pointer">
      💬
    </button>

    <!-- Glavni chat prozor -->
    <div v-if="visible" class="bg-white w-80 h-[400px] rounded shadow-lg overflow-hidden flex flex-col">
      <div class="bg-purple-700 text-white px-4 py-2 font-semibold">
        {{ $t('chat.title') }}
      </div>
      <div class="flex-1 p-3 overflow-y-auto text-sm text-gray-800">
        <div v-for="(msg, index) in messages" :key="index" class="mb-2">
          <strong>{{ msg.role === 'user' ? $t('chat.userLabel') : $t('chat.assistantLabel') }}:</strong>
          {{ msg.content }}
        </div>
      </div>
      <form @submit.prevent="sendMessage" class="p-2 border-t border-gray-200 flex gap-2">
        <input
          v-model="input"
          class="flex-1 border rounded px-2 py-1 text-sm text-black bg-white"
          :placeholder="$t('chat.inputPlaceholder')"
        />
        <button type="submit" class="bg-purple-600 text-white px-3 py-1 text-sm rounded">
          {{ $t('chat.buttonSend') }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ChatWidget',
  data() {
    return {
      visible: false,
      showTooltip: false,
      input: '',
      messages: [
        { role: 'assistant', content: this.$t('chat.default') }
      ]
    }
  },
  mounted() {
    if (!localStorage.getItem('chat_opened')) {
      this.visible = true
      this.showTooltip = true
      localStorage.setItem('chat_opened', 'true')
      setTimeout(() => this.showTooltip = false, 5000)
    }
  },
  methods: {
    toggleChat() {
      this.visible = !this.visible
      this.showTooltip = false
    },
    async sendMessage() {
      const userMsg = this.input.trim()
      if (!userMsg) return

      this.messages.push({ role: 'user', content: userMsg })
      this.input = ''

      try {
        const res = await axios.post('http://localhost:8080/api/ai/chat', {
          message: userMsg
        })
        const reply = res.data.reply || this.$t('chat.fallback')
        this.messages.push({ role: 'assistant', content: reply })
      } catch (error) {
        this.messages.push({ role: 'assistant', content: this.$t('chat.error') })
        console.error('AI error:', error)
      }
    }
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.4s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
