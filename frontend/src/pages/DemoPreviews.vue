<template>
  <div class="flex flex-col min-h-screen overflow-hidden supports-[overflow:clip]:overflow-clip">
    <Header />

    <section class="min-h-screen bg-slate-900 text-white py-16 px-4 sm:px-6">
      <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-6 mt-[100px]">{{ $t('demoPreviews.title') }}</h1>
        <p class="text-slate-400 text-center mb-12 max-w-2xl mx-auto">
          {{ $t('demoPreviews.subtitle') }}
        </p>

        <div class="grid md:grid-cols-2 gap-6">
          <DemoBox
            v-for="site in demoSites"
            :key="site.slug"
            :title="`🌐 ${site.name}`"
            :description="site.description"
            :link="`/preview/${site.slug}`"
          />

          <!-- Dev-primjeri: koristimo RELATIVNE rute umesto localhost linkova -->
          <DemoBox
            title="🏡 Frizer Neša / PRO (plaćena varijanta)"
            description="Primer plaćene verzije; cena zavisi od nivoa podrške (obrada fotografija, uređivanje teksta, logo, cenovnik...). Detaljna specifikacija na upit."
            :link="`/preview/frizer-nesa-689d451ced43e`"
          />
          <DemoBox
            title="🏡 Frizer Neša — besplatna / modern"
            description="Primer besplatne prezentacije za samostalno uređivanje. Link možete koristiti neograničeno."
            :link="`/preview/nesa-frizer-6890cdbf41662`"
          />
        </div>

        <!-- CTA dugmad -->
        <div class="text-center mt-12 space-y-4">
          <router-link
            :to="isAuthenticated ? '/dashboard' : '/signup'"
            class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded text-lg font-semibold transition"
          >
            {{ isAuthenticated ? $t('demoPreviews.goToDashboard') : $t('demoPreviews.registerAndCreate') }}
          </router-link>

          <p class="text-bold text-slate-400 mt-4">{{ $t('demoPreviews.note') }}</p>

          <div class="flex flex-col sm:flex-row gap-4 justify-center mt-2">
            <router-link
              to="/free-site-form"
              class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded font-medium text-sm shadow transition"
            >
              {{ $t('demoPreviews.freeSiteButton') }}
            </router-link>

            <router-link
              to="/pro-site-form"
              class="bg-yellow-500 hover:bg-yellow-600 text-slate-900 px-5 py-3 rounded font-medium text-sm shadow transition"
            >
              {{ $t('demoPreviews.proSiteButton') }}
            </router-link>
          </div>

          <p v-if="error" class="text-red-300 text-sm mt-4">{{ error }}</p>
        </div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<script>
import DemoBox from '../components/DemoBox.vue'
import { isLoggedIn } from '../utils/auth'
import Header from '../partials/Header.vue'
import Footer from '../partials/Footer.vue'
import api from '@/api/http' // centralna axios instanca (baseURL = /api)

export default {
  name: 'DemoPreviews',
  components: { DemoBox, Header, Footer },
  data() {
    return {
      demoSites: [],
      loading: false,
      error: null,
    }
  },
  computed: {
    isAuthenticated() {
      return isLoggedIn()
    },
  },
  async mounted() {
    await this.loadDemoSites()
  },
  methods: {
    async loadDemoSites() {
      this.loading = true
      this.error = null
      try {
        const { data } = await api.get('/demo-sites') // → /api/demo-sites
        this.demoSites = Array.isArray(data) ? data : (data?.data ?? [])
      } catch (err) {
        console.error('❌ Greška pri učitavanju demo sajtova:', err)
        this.error = 'Greška pri učitavanju demo sajtova.'
      } finally {
        this.loading = false
      }
    },
  },
}
</script>
