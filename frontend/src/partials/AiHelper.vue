<template>
  <div class="bg-slate-800 text-white p-4 rounded-lg shadow-md relative">
    <h3 class="text-lg font-semibold mb-2">🤖 {{ $t('aihelper.title') }}</h3>

    <button
      class="px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded text-white text-sm cursor-pointer disabled:opacity-50"
      @click="generateSuggestion"
      :disabled="loading"
    >
      {{ loading ? $t('aihelper.generating') : $t('aihelper.request') }}
    </button>

    <div v-if="suggestion" class="mt-4 bg-slate-700 p-3 rounded relative">
      <p class="text-sm whitespace-pre-line">{{ suggestion }}</p>

      <!-- Dugme za zatvaranje -->
      <button
        @click="suggestion = ''"
        class="absolute top-2 right-2 text-xs text-red-400 hover:text-red-200"
        :title="$t('aihelper.close')"
      >
        ✖
      </button>
    </div>

    <div v-if="error" class="mt-2 text-red-400 text-sm">⚠️ {{ error }}</div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AiHelper',
  props: {
    prompt: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      suggestion: '',
      loading: false,
      error: ''
    }
  },
  methods: {
    async generateSuggestion() {
      console.log('🔍 AI helper pokrenut...')
      this.error = ''
      this.suggestion = ''

      const cleanPrompt = this.prompt?.trim()
      if (!cleanPrompt) {
        this.error = this.$t('aihelper.error')
        return
      }

      console.log('📥 Prompt koji šaljemo:', cleanPrompt)
      this.loading = true

      try {
        const response = await axios.post('http://localhost:8080/api/ai/suggest', {
          prompt: cleanPrompt
        })

        const result = response.data.suggestion?.trim()
        this.suggestion = result || this.$t('aihelper.noResponse')
      } catch (err) {
        if (err.response?.status === 429) {
          this.error = this.$t('aihelper.limit')
        } else if (err.response?.status === 403) {
          this.error = this.$t('aihelper.planRestriction')
        } else {
          this.error = this.$t('aihelper.error')
        }
        console.error('❌ AI error:', err)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
