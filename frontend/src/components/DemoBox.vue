<template>
  <div class="bg-slate-800 rounded-lg shadow-md p-6 flex flex-col justify-between h-full">
    <div>
      <h2 class="text-xl font-bold mb-2 text-white">{{ title }}</h2>
      <p class="text-slate-400 text-sm mb-4">{{ description }}</p>
    </div>

    <!--
      Pravila:
      - /prezentacije/:slug i /preview/:slug su Blade rute → koristimo <a href="..."> (da ne presretne Vue Router)
      - interne SPA rute (npr. /dashboard, /signup) → <router-link>
      - eksterni linkovi (http/https) → <a href="..." target="_blank" rel="noopener noreferrer">
    -->
    <component
      :is="componentTag"
      :to="useRouter ? safeLink : null"
      :href="!useRouter ? safeLink : null"
      :target="external ? '_blank' : null"
      :rel="external ? 'noopener noreferrer' : null"
      class="mt-auto inline-block text-center bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded text-sm transition"
      aria-label="Pogledaj prezentaciju"
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
    rawLink() {
      return (this.link || '').trim()
    },
    external() {
      return /^https?:\/\//i.test(this.rawLink)
    },
    isBladeRoute() {
      // sve što ide na Laravel Blade (mimo SPA Router-a)
      return /^\/(prezentacije|preview)\//i.test(this.rawLink)
    },
    useRouter() {
      // koristimo <router-link> samo za čisto SPA rute
      return !this.external && !this.isBladeRoute && this.rawLink.startsWith('/')
    },
    safeLink() {
      if (this.external || this.rawLink.startsWith('/')) return this.rawLink
      return '/' // fallback
    },
    componentTag() {
      return this.useRouter ? 'router-link' : 'a'
    },
  },
}
</script>
