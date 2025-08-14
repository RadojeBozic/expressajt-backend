<template>
  <div class="flex flex-col min-h-screen overflow-hidden supports-[overflow:clip]:overflow-clip">
    <Header />

    <section class="min-h-screen bg-slate-900 text-white py-16 px-4 sm:px-6">
      <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-6 mt-[100px]">{{ $t('demoPreviews.title') }}</h1>
        <p class="text-slate-400 text-center mb-12 max-w-2xl mx-auto">
          {{ $t('demoPreviews.subtitle') }}
        </p>

        <!-- Ručno dodaj koliko god DemoBox-ova želiš -->
        <div class="grid md:grid-cols-2 gap-6">

          <!-- PRIMERI (slobodno menjaj/povećavaj listu) -->
          <DemoBox
            title="🏡 Frizer Neša / FREE - besplatna varijanta"
            description="Primer besplatne verzije."
            link="/prezentacije/frizer-nesa-689dbf092251a"
          />

          <DemoBox
            title="🏡 Frizer Neša / PRO paket"
            description="Primer pro prezentacije, sa minimumom dodatne usluge.Cena od 10 € do 50 € u zavisnosti od nivoa dodatnih usluga."
            link="/prezentacije/frizer-nesa-689db59a24081"
          />

           <DemoBox
            title="🏡 HIT Auto/ FREE - besplatna varijanta"
            description="Primer besplatne verzije; Dodata izrada logoa 990,00 din"
            link="/prezentacije/hit-auto-sabac-689ddd8396c41"
          />

           <DemoBox
            title="🏡 HIT Auto/ modern - pro varijanta"
            description="Primer pro prezentacije, sa izradom logoa. Cena od 10 € do 50 € u zavisnosti od nivoa dodatnih usluga"
            link="/prezentacije/hit-auto-sabac-689ddf3418f94"
          />

          <!-- 👇 Ovde samo kopiraj i menjaj -->
          <!--
          <DemoBox
            title="✳️ Naziv prezentacije / modern"
            description="Kratak opis prezentacije."
            link="/prezentacije/tvoj-slug-abcdef123456"
          />
          -->
        </div>

        <!-- CTA dugmad -->
        <div class="text-center mt-12 space-y-4">
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
        </div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<script>
import DemoBox from '../components/DemoBox.vue'
import Header from '../partials/Header.vue'
import Footer from '../partials/Footer.vue'

export default {
  name: 'DemoPreviews',
  components: { DemoBox, Header, Footer },
}
</script>
