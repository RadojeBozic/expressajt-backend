// src/router.js
import { createRouter, createWebHistory } from 'vue-router'
import { api } from './api/http' // ⬅️ koristimo server proveru umesto isLoggedIn()

// Osnovne
import Home from './pages/Home.vue'
import About from './pages/About.vue'
import Contact from './pages/Contact.vue'
import Changelog from './pages/Changelog.vue'
import Customers from './pages/Customers.vue'
import Customer from './pages/Customer.vue'
import Dashboard from './pages/Dashboard.vue'
import Projects from './pages/Projects.vue'
import Pricing from './pages/Pricing.vue'
import SelectTemplate from './pages/SelectTemplate.vue'

// Auth
import SignIn from './pages/SignIn.vue'
import SignUp from './pages/SignUp.vue'
import ResetPassword from './pages/ResetPassword.vue'

// Admin
import AdminDashboard from './pages/AdminDashboard.vue'
import AdminUsers from './partials/admin/AdminUsers.vue'

// Servisi
import ServiceFreeSite from './services/ServiceFreeSite.vue'
import ServiceProSite from './services/ServiceProSite.vue'          // ako ti je baš services/ProSiteForm.vue, ostavi tako
import ServiceW3Site from './services/ServiceW3Site.vue'
import ServiceCruipSite from './services/ServiceCruipSite.vue'
import ServiceOriginalSite from './services/ServiceOriginalSite.vue'
import ServiceOnlineshop from './services/ServiceOnlineshop.vue'
import ServiceUniShop from './services/ServiceUniShop.vue'
import ServiceDomainHosting from './services/ServiceDomainHosting.vue'
import ServiceMaintenance from './services/ServiceMaintenance.vue'
import ServiceDesign from './services/ServiceDesign.vue'
import ServiceSeo from './services/ServiceSeo.vue'
import ServiceMarketing from './services/ServiceMarketing.vue'
import ServiceTranslations from './services/ServiceTranslations.vue'

// Forme i pregledi
import FreeSiteForm from './pages/FreeSiteForm.vue'
import ProSiteForm from './services/ProSiteForm.vue'
import DemoFree from './services/DemoFree.vue'
import Preview from './pages/PresentationView.vue'
import PublicPresentation from './pages/PublicPresentation.vue'

const router = createRouter({
  history: createWebHistory(),
  scrollBehavior(to, from, saved) {
    if (saved) return saved
    return { top: 0 }
  },
  routes: [
    // 🌐 Javne
    { path: '/', name: 'Home', component: Home },
    { path: '/about', name: 'About', component: About },
    { path: '/contact', name: 'Contact', component: Contact },
    { path: '/changelog', name: 'Changelog', component: Changelog },
    { path: '/pricing', name: 'Pricing', component: Pricing },

    // 🧩 Integracije
    { path: '/integrations', name: 'Integrations', component: () => import('./pages/Integrations.vue') },
    { path: '/integrations-single', name: 'IntegrationsSingle', component: () => import('./pages/IntegrationsSingle.vue'), meta: { requiresAuth: true } },

    // 👥 Korisnici i klijenti
    { path: '/customers', name: 'Customers', component: Customers, meta: { requiresAuth: true } },
    { path: '/customer', name: 'Customer', component: Customer, meta: { requiresAuth: true } },

    // 🧾 Projekti i dashboard
    { path: '/projects', name: 'Projects', component: Projects, meta: { requiresAuth: true } },
    { path: '/dashboard', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true } },

    // 🔐 Autentifikacija
    { path: '/signin', name: 'SignIn', component: SignIn, meta: { guestOnly: true } },
    { path: '/signup', name: 'SignUp', component: SignUp, meta: { guestOnly: true } },
    { path: '/reset-password', name: 'ResetPassword', component: ResetPassword, meta: { guestOnly: true } },

    // 🛠️ Admin
    { path: '/admin/dashboard', name: 'AdminDashboard', component: AdminDashboard, meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/admin/users', name: 'AdminUsers', component: AdminUsers, meta: { requiresAuth: true, requiresAdmin: true } },

    // 🧰 Servisi
    { path: '/services/freesite', name: 'FreeSite', component: ServiceFreeSite, props: { slug: 'freesite' } },
    { path: '/services/prosite', name: 'ServiceProSite', component: ServiceProSite, props: { slug: 'prosite' } },
    { path: '/services/w3site', name: 'ServiceW3Site', component: ServiceW3Site, props: { slug: 'w3site' } },
    { path: '/services/cruipsite', name: 'ServiceCruipSite', component: ServiceCruipSite, props: { slug: 'cruipsite' } },
    { path: '/services/originalsite', name: 'ServiceOriginalSite', component: ServiceOriginalSite, props: { slug: 'originalsite' } },
    { path: '/services/basicshop', name: 'ServiceOnlineshop', component: ServiceOnlineshop, props: { slug: 'basicshop' } },
    { path: '/services/unishop', name: 'ServiceUniShop', component: ServiceUniShop, props: { slug: 'unishop' } },
    { path: '/services/domain-hosting', name: 'ServiceDomainHosting', component: ServiceDomainHosting, props: { slug: 'domain-hosting' } },
    { path: '/services/maintenance', name: 'ServiceMaintenance', component: ServiceMaintenance, props: { slug: 'maintenance' } },
    { path: '/services/design', name: 'ServiceDesign', component: ServiceDesign, props: { slug: 'design' } },
    { path: '/services/seo', name: 'ServiceSeo', component: ServiceSeo, props: { slug: 'seo' } },
    { path: '/services/marketing', name: 'ServiceMarketing', component: ServiceMarketing, props: { slug: 'marketing' } },
    { path: '/services/translation', name: 'ServiceTranslations', component: ServiceTranslations, props: { slug: 'translations' } },

    // 📄 Javne prezentacije
    { path: '/print/:slug', name: 'PublicPresentationPrint', component: PublicPresentation, props: true },
    { path: '/preview/:slug', name: 'PublicPresentationPreview', component: () => import('./pages/PublicPresentation.vue'), props: true },

    // 📝 Forme za sajtove
    { path: '/free-site-form', name: 'FreeSiteForm', component: FreeSiteForm },
    { path: '/pro-site-form', name: 'ProSiteForm', component: ProSiteForm },

    // 🔎 Pregled prezentacija
    { path: '/prezentacije/demo-free', name: 'DemoFree', component: DemoFree, props: { slug: 'demo-free' } },
    { path: '/prezentacije/:slug', name: 'Preview', component: Preview, props: true },

    // 🧩 Template izbor
    { path: '/select-template', name: 'SelectTemplate', component: SelectTemplate },

    // Stripe
    { path: '/stripe-test', name: 'StripeTest', component: () => import('./partials/StripeTest.vue') },

    // 🛒 Korpa i naplata
    { path: '/checkout', name: 'Checkout', component: () => import('./pages/CheckoutPage.vue') },

    // ✏️ Izmena sajta
    { path: '/edit-site/:slug', name: 'EditSite', component: () => import('./pages/EditSite.vue'), props: true },

    // Demo pregledi
    { path: '/demo', name: 'DemoPreviews', component: () => import('./pages/DemoPreviews.vue') },

    // Footer (lazy) …
    { path: '/footer/products/features', name: 'FooterFeatures', component: () => import('./pages/footer/products/Features.vue') },
    { path: '/footer/products/integrations', name: 'FooterIntegrations', component: () => import('./pages/footer/products/Integrations.vue') },
    { path: '/footer/products/changelog', name: 'ChangelogPage', component: () => import('./pages/footer/products/Changelog.vue') },
    { path: '/footer/products/method', name: 'MethodPage', component: () => import('./pages/footer/products/Method.vue') },
    { path: '/footer/company/about', name: 'AboutPage', component: () => import('./pages/footer/company/About.vue') },
    { path: '/footer/company/diversity', name: 'DiversityPage', component: () => import('./pages/footer/company/Diversity.vue') },
    { path: '/footer/company/blog', name: 'BlogPage', component: () => import('./pages/footer/company/Blog.vue') },
    { path: '/footer/company/careers', name: 'CareersPage', component: () => import('./pages/footer/company/Careers.vue') },
    { path: '/footer/company/financials', name: 'FinancePage', component: () => import('./pages/footer/company/Finance.vue') },
    { path: '/footer/resources/community', name: 'CommunityPage', component: () => import('./pages/footer/resources/Community.vue') },
    { path: '/footer/resources/terms', name: 'TermsPage', component: () => import('./pages/footer/resources/Terms.vue') },
    { path: '/footer/resources/vulnerability', name: 'VulnerabilityPage', component: () => import('./pages/footer/resources/Vulnerability.vue') },
    { path: '/footer/legals/refund', name: 'RefundPage', component: () => import('./pages/footer/legals/Refund.vue') },
    { path: '/footer/legals/terms_conditions', name: 'TermsConditionsPage', component: () => import('./pages/footer/legals/Terms.vue') },
    { path: '/footer/legals/privacy', name: 'PrivacyPage', component: () => import('./pages/footer/legals/Privacy.vue') },
    { path: '/footer/legals/cookie-policy', name: 'CookiePolicyPage', component: () => import('./pages/footer/legals/CookiePolicy.vue') },
    { path: '/footer/legals/brandkit', name: 'BrandPage', component: () => import('./pages/footer/legals/Brandkit.vue') },


    // Nove stranice
    { path: '/pricing-detailed', name: 'PricingDetailed', component: () => import('@/partials/PricingDetailed.vue') },

    // 404
    { path: '/:pathMatch(.*)*', name: 'NotFound', component: () => import('./pages/NotFound.vue') },
  ],
})

// --- SERVER-SIDE AUTH GUARD ---
// jednostavan cache da ne spamujemo /api/user
let _user = null
let _inFlight = null
async function whoAmI() {
  if (_user) return _user
  if (_inFlight) return _inFlight
  _inFlight = api.get('/user')
    .then(r => (_user = r.data))
    .catch(() => (_user = null))
    .finally(() => { _inFlight = null })
  return _inFlight
}

router.beforeEach(async (to) => {
  const needsAuth = !!to.meta?.requiresAuth
  const needsAdmin = !!to.meta?.requiresAdmin
  const guestOnly = !!to.meta?.guestOnly

  // provera samo kad treba
  let user = _user
  if (needsAuth || needsAdmin || guestOnly) {
    user = await whoAmI()
  }

  // zaštićene rute
  if (needsAuth && !user) {
    return { path: '/signin', query: { redirect: to.fullPath } }
  }

  // /signin i /signup kad si već ulogovan
  if (guestOnly && user) {
    const target = typeof to.query.redirect === 'string' && to.query.redirect.startsWith('/')
      ? to.query.redirect
      : '/dashboard'
    return { path: target }
  }

  // admin
  if (needsAdmin) {
    if (!user) return { path: '/signin', query: { redirect: to.fullPath } }
    if (!['admin', 'superadmin'].includes(user.role)) {
      return { path: '/dashboard' }
    }
  }

  return true
})

// 👇 jedina (i javna) reset funkcija keša
export function resetAuthCache() {
  _user = null
  _inFlight = null
}

export default router
