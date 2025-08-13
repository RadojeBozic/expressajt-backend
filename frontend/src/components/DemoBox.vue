<template>
  <div class="bg-slate-800 rounded-lg shadow-md p-6 flex flex-col justify-between h-full">
    <div>
      <h2 class="text-xl font-bold mb-2 text-white">{{ title }}</h2>
      <p class="text-slate-400 text-sm mb-4">{{ description }}</p>
    </div>

    <component
      :is="isInternal ? 'router-link' : 'a'"
      :to="isInternal ? safeLink : null"
      :href="!isInternal ? safeLink : null"
      target="_blank"
      :rel="!isInternal ? 'noopener noreferrer' : null"
      class="mt-auto inline-block text-center bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded text-sm transition"
    >
      🌐 Pogledaj prezentaciju
    </component>
  </div>
</template>

<script>
export default {
  name: 'DemoBox',
  props: {
    title: { type: String, required: true },
    description: { type: String, default: '' },
    link: { type: String, required: true },
  },
  computed: {
    isInternal() {
      const l = this.link || ''
      return l.startsWith('/')
    },
    safeLink() {
      const l = (this.link || '').trim()
      if (this.isInternal) return l
      // dozvoli samo http/https za eksterne
      if (/^https?:\/\//i.test(l)) return l
      return '/' // fallback
    },
  },
}
</script>
