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

  image: '/images/blog/online-store/hero.jpg',

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
]