export const posts = [
  {
    id: 1,

    slug: 'kako-izabrati-poslovni-sajt',

    title: 'Kako izabrati poslovni sajt koji zaista donosi rezultate',

    excerpt:
      'Poslovni sajt nije samo digitalna vizitkarta. Dobro osmišljen sajt predstavlja kompaniju, gradi poverenje i pomaže da posetioci postanu klijenti.',

    category: 'web-sajtovi',

    author: 'radoje-bozic',

    publishedAt: '2026-07-22',

    updatedAt: '2026-07-22',

    image: '/images/blog/kako-izabrati-poslovni-sajt.jpg',

    imageAlt: 'Planiranje modernog poslovnog sajta',

    featured: true,

    published: true,

    readingTime: 6,

    component: () => import('../posts/KakoIzabratiPoslovniSajt.vue'),

    seo: {
      title: 'Kako izabrati poslovni sajt | Express Web',

      description:
        'Saznajte šta jedan kvalitetan poslovni sajt treba da sadrži i kako da izaberete rešenje koje podržava razvoj vašeg poslovanja.',

      keywords: [
        'poslovni sajt',
        'izrada sajta',
        'web prezentacija',
        'digitalno poslovanje',
      ],
    },
  },
]