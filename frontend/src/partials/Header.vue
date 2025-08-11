<template>
  <header class="absolute w-full z-30">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
      <div class="flex items-center justify-between h-16 md:h-20">
        
        <!-- Logo -->
        <router-link to="/" aria-label="Logo" class="inline-flex">
          <img src="../images/logo_express02.png" width="138" height="138" alt="Logo" />
        </router-link>

        <!-- Navigacija desktop -->
        <nav class="hidden md:flex flex-grow justify-center">
          <ul class="flex gap-4 items-center">
            <li v-for="link in navLinks" :key="link.to">
              <router-link :to="link.to" class="font-medium text-sm text-slate-300 hover:text-white">
                {{ $t(link.label) }}
              </router-link>
            </li>
          </ul>
        </nav>

        <!-- Desni meni -->
        <div class="flex items-center gap-3">
          <!-- Korpa -->
          <router-link to="/checkout" class="text-sm text-white hover:text-purple-300 cursor-pointer">
            🛒 {{ $t('header.menu.cart') }} ({{ totalItems() }})
          </router-link>

          <!-- Login/Logout -->
          <template v-if="!user">
            <router-link to="/signin" class="text-sm text-slate-300 hover:text-white">
              {{ $t('header.menu.signin') }}
            </router-link>
            <router-link to="/signup" class="ml-2 text-sm bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded">
              {{ $t('header.menu.signup') }}
            </router-link>
          </template>

          <template v-else>
            <span class="text-sm text-white bg-slate-700 px-2 py-1 rounded font-medium">
              👋 {{ user.name }}
            </span>
            <router-link to="/dashboard" class="text-sm text-slate-300 hover:text-white">
              {{ $t('header.menu.account') }} →
            </router-link>
            <button @click="logout" class="text-sm px-3 py-1 bg-slate-700 hover:bg-slate-600 text-white rounded">
              {{ $t('header.menu.logout') }}
            </button>
          </template>

          <!-- Admin -->
          <router-link
            v-if="isAdmin"
            to="/admin/dashboard"
            class="text-sm border border-purple-400 text-purple-400 px-3 py-1 rounded hover:bg-purple-800"
          >
            {{ $t('header.menu.admin') }} →
          </router-link>

          <!-- Jezik -->
          <div class="relative">
            <button @click="toggleLangDropdown" class="flex items-center gap-1 text-sm text-slate-300 hover:text-white border border-slate-600 px-2 py-1 rounded">
              <img :src="currentFlag" class="w-4 h-4" />
              <span>{{ currentLabel }}</span>
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.23 7.21a.75.75 0 011.06.02L10 11.585l3.71-4.355a.75.75 0 111.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z"/></svg>
            </button>
            <div v-if="showLang" class="absolute right-0 mt-1 w-32 bg-slate-800 border border-slate-600 rounded shadow-lg z-50">
              <button @click="setLanguage('sr')" class="flex items-center w-full px-3 py-2 text-sm text-slate-300 hover:bg-slate-700">
                <img src="../images/flag-rs.png" class="w-4 h-4 mr-2" /> SR
              </button>
              <button @click="setLanguage('en')" class="flex items-center w-full px-3 py-2 text-sm text-slate-300 hover:bg-slate-700">
                <img src="../images/flag-uk.png" class="w-4 h-4 mr-2" /> EN
              </button>
            </div>
          </div>

          <!-- Hamburger za mobile -->
          <button class="md:hidden text-slate-300 hover:text-white" @click="mobileNavOpen = !mobileNavOpen">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile navigacija -->
      <nav
        v-if="mobileNavOpen"
        class="md:hidden mt-2 border rounded bg-slate-900 px-4 py-3 transition-all"
      >
        <ul>
          <li v-for="link in navLinks" :key="link.to" class="py-1">
            <router-link :to="link.to" class="block text-slate-300 hover:text-white">
              {{ $t(link.label) }}
            </router-link>
          </li>
          <li class="py-1 flex gap-2">
            <button @click="setLanguage('sr')" class="flex items-center gap-2 text-sm text-slate-300 hover:text-white">
              <img src="../images/flag-rs.png" class="w-4 h-4" /> SR
            </button>
            <button @click="setLanguage('en')" class="flex items-center gap-2 text-sm text-slate-300 hover:text-white">
              <img src="../images/flag-uk.png" class="w-4 h-4" /> EN
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </header>
</template>

<script>
import { api, web } from '@/api/http'          // server-autoritet za /user i /logout
import { useCart } from '../utils/CartService'
import { clearAuth as doLogout } from '../utils/auth'

// global/store instanca (ostaje reaktivna)
const cartStore = useCart()

export default {
  name: 'SiteHeader',

  data() {
    return {
      mobileNavOpen: false,
      showLang: false,
      user: null, // puni se sa /api/user u mounted()
      navLinks: [
        { to: '/', label: 'header.menu.home' },
        { to: '/about', label: 'header.menu.about' },
        { to: '/projects', label: 'header.menu.projects' },
        { to: '/pricing', label: 'header.menu.pricing' },
        { to: '/contact', label: 'header.menu.contact' },
      ],
    }
  },

  computed: {
    // I18n preko this.$i18n (Options API friendly)
    currentFlag() {
      const isSr = this.$i18n?.locale === 'sr'
      return new URL(isSr ? '../images/flag-rs.png' : '../images/flag-uk.png', import.meta.url).href
    },
    currentLabel() {
      return this.$i18n?.locale === 'sr' ? 'SR' : 'EN'
    },

    // Cart (iz store-a)
    cart() { return cartStore.cart },
    totalItems() { return cartStore.totalItems },

    // Auth helpers
    isLoggedIn() { return !!this.user },
    isAdmin() {
      if (!this.user) return false
      // primarno po roli, fallback po e-mailu
      return (['admin', 'superadmin'].includes(this.user.role)) ||
             (['admin@example.com', 'radoje@example.com'].includes(this.user.email))
    },
  },

  methods: {
    // jezici
    toggleLangDropdown() { this.showLang = !this.showLang },
    setLanguage(lang) {
      if (this.$i18n) this.$i18n.locale = lang
      try { localStorage.setItem('locale', lang) } catch {}
      this.showLang = false
    },

    // mobile nav
    toggleMobile() { this.mobileNavOpen = !this.mobileNavOpen },
    clickHandler(e) {
      if (!this.mobileNavOpen) return
      const t = e.target
      if (!t.closest('nav') && !t.closest('button')) this.mobileNavOpen = false
    },
    keyHandler(e) {
      if (e.key === 'Escape') this.mobileNavOpen = false
    },

    // server-autoritet: ko sam ja?
    async refreshUser() {
      try {
        const { data } = await api.get('/user')
        this.user = data
      } catch {
        this.user = null
      }
    },

    // account / admin navigacija (uvek pitamo server)
    async goToAccount() {
      try {
        await api.get('/user')
        this.$router.push('/dashboard')
      } catch {
        this.$router.push({ path: '/signin', query: { redirect: '/dashboard' } })
      }
    },
    async goToAdmin() {
      try {
        const { data } = await api.get('/user')
        if (['admin', 'superadmin'].includes(data.role)) {
          this.$router.push('/admin/dashboard')
        } else {
          this.$router.push('/dashboard')
        }
      } catch {
        this.$router.push({ path: '/signin', query: { redirect: '/admin/dashboard' } })
      }
    },

    // logout: backend + lokalni clear + korpa
    async logout() {
      try { await web.post('/logout') } catch {}
      try { doLogout() } catch {}
      try { cartStore.clearCart() } catch {}
      this.user = null
      this.$router.replace('/signin')
    },
  },

  mounted() {
    document.addEventListener('click', this.clickHandler)
    document.addEventListener('keydown', this.keyHandler)
    this.refreshUser()
  },

  beforeUnmount() {
    document.removeEventListener('click', this.clickHandler)
    document.removeEventListener('keydown', this.keyHandler)
  },
}
</script>
