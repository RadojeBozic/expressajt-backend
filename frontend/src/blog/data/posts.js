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
]