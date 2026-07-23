export const posts = [
  {
    id: 1,
    slug: 'kako-izabrati-poslovni-sajt',

    titleKey:
      'blog.articles.businessWebsite.meta.title',

    excerptKey:
      'blog.articles.businessWebsite.meta.excerpt',

    category: 'web-sajtovi',
    author: 'radoje-bozic',

    publishedAt: '2026-07-22',
    updatedAt: '2026-07-22',

    image:
      '/images/blog/business-website/hero.jpg',

    imageAltKey:
      'blog.articles.businessWebsite.images.hero.alt',

    featured: true,
    published: true,
    readingTime: 6,

    component: () =>
      import(
        '../posts/KakoIzabratiPoslovniSajt.vue'
      ),

    seo: {
      titleKey:
        'blog.articles.businessWebsite.meta.seoTitle',

      descriptionKey:
        'blog.articles.businessWebsite.meta.seoDescription',
    },
  },
  {
  id: 2,

  slug: 'kada-pokrenuti-internet-prodavnicu',

  titleKey:
    'blog.articles.onlineStore.meta.title',

  excerptKey:
    'blog.articles.onlineStore.meta.excerpt',

  category: 'online-prodaja',

  author: 'radoje-bozic',

  publishedAt: '2026-07-23',

  updatedAt: '2026-07-23',

  image: '/images/blog/online-store/card.jpg',

  imageAltKey:
    'blog.articles.onlineStore.images.hero.alt',

  featured: true,

  published: true,

  readingTime: 7,

  component: () =>
    import(
      '../posts/KadaPokrenutiInternetProdavnicu.vue'
    ),

  seo: {
    titleKey:
      'blog.articles.onlineStore.meta.seoTitle',

    descriptionKey:
      'blog.articles.onlineStore.meta.seoDescription',
  },
},

{
  id: 3,

  slug: 'zasto-google-business-profil-nije-dovoljan',

  titleKey:
    'blog.articles.googleBusiness.meta.title',

  excerptKey:
    'blog.articles.googleBusiness.meta.excerpt',

  category: 'digitalni-marketing',

  author: 'radoje-bozic',

  publishedAt: '2026-07-23',

  updatedAt: '2026-07-23',

  image:
    '/images/blog/google-business/card.jpg',

  imageAltKey:
  'blog.articles.googleBusiness.images.card.alt',

  featured: true,

  published: true,

  readingTime: 6,

  component: () =>
    import(
      '../posts/ZastoGoogleBusinessProfilNijeDovoljan.vue'
    ),

  seo: {
    titleKey:
      'blog.articles.googleBusiness.meta.seoTitle',

    descriptionKey:
      'blog.articles.googleBusiness.meta.seoDescription',
  },
},

{
  id: 4,

  slug: 'kako-ai-moze-pomoci-malom-preduzecu',

  titleKey:
    'blog.articles.aiBusiness.meta.title',

  excerptKey:
    'blog.articles.aiBusiness.meta.excerpt',

  category: 'vestacka-inteligencija',

  author: 'radoje-bozic',

  publishedAt: '2026-07-23',

  updatedAt: '2026-07-23',

  image:
    '/images/blog/ai-small-business/card.jpg',

  imageAltKey:
    'blog.articles.aiBusiness.images.card.alt',

  featured: true,

  published: true,

  readingTime: 7,

  component: () =>
    import(
      '../posts/KakoAiMozePomociMalomPreduzecu.vue'
    ),

  seo: {
    titleKey:
      'blog.articles.aiBusiness.meta.seoTitle',

    descriptionKey:
      'blog.articles.aiBusiness.meta.seoDescription',
  },
},

{
  id: 5,

  slug: 'kada-je-malom-preduzecu-potrebna-poslovna-aplikacija',

  titleKey:
    'blog.articles.businessApplication.meta.title',

  excerptKey:
    'blog.articles.businessApplication.meta.excerpt',

  category: 'poslovne-aplikacije',

  author: 'radoje-bozic',

  publishedAt: '2026-07-23',

  updatedAt: '2026-07-23',

  image:
    '/images/blog/business-application/card.jpg',

  imageAltKey:
    'blog.articles.businessApplication.images.card.alt',

  featured: true,

  published: true,

  readingTime: 9,

  component: () =>
    import(
      '../posts/KadaJePotrebnaPoslovnaAplikacija.vue'
    ),

  seo: {
    titleKey:
      'blog.articles.businessApplication.meta.seoTitle',

    descriptionKey:
      'blog.articles.businessApplication.meta.seoDescription',
  },
},

{
  id: 6,

  slug: 'kako-nastaje-uspesna-web-prezentacija-gvp-consult',

  titleKey:
    'blog.articles.gvpCaseStudy.meta.title',

  excerptKey:
    'blog.articles.gvpCaseStudy.meta.excerpt',

  category: 'projekti-i-studije-slucaja',

  author: 'radoje-bozic',

  publishedAt: '2026-07-23',

  updatedAt: '2026-07-23',

  image:
    '/images/blog/gvp-case-study/card.jpg',

  imageAltKey:
    'blog.articles.gvpCaseStudy.images.card.alt',

  featured: true,

  published: true,

  readingTime: 10,

  component: () =>
    import(
      '../posts/KakoNastajeUspesnaWebPrezentacija.vue'
    ),

  seo: {
    titleKey:
      'blog.articles.gvpCaseStudy.meta.seoTitle',

    descriptionKey:
      'blog.articles.gvpCaseStudy.meta.seoDescription',
  },
},
]