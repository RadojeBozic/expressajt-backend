<script setup>
import {
  computed,
  defineAsyncComponent,
  watch,
} from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

import Header from '@/partials/Header.vue'
import Footer from '@/partials/Footer.vue'

import {
  getAuthorBySlug,
  getCategoryBySlug,
  getPostBySlug,
  getRelatedPosts,
} from '@/blog/services/blogService'

const route = useRoute()
const { t, te, locale } = useI18n()

// ========================================================
// POMOĆNE FUNKCIJE ZA LOKALIZACIJU
// ========================================================

const translateOrFallback = (key, fallback = '') => {
  if (!key) {
    return fallback
  }

  return te(key) ? t(key) : fallback
}

// ========================================================
// ČLANAK
// ========================================================

const post = computed(() =>
  getPostBySlug(String(route.params.slug || '')),
)

const category = computed(() => {
  if (!post.value) {
    return null
  }

  return getCategoryBySlug(post.value.category)
})

const author = computed(() => {
  if (!post.value) {
    return null
  }

  return getAuthorBySlug(post.value.author)
})

const relatedPosts = computed(() => {
  if (!post.value) {
    return []
  }

  return getRelatedPosts(post.value, 3)
})

// ========================================================
// LOKALIZOVANI PODACI ČLANKA
// ========================================================

const postTitle = computed(() =>
  translateOrFallback(
    post.value?.titleKey,
    post.value?.title || '',
  ),
)

const postExcerpt = computed(() =>
  translateOrFallback(
    post.value?.excerptKey,
    post.value?.excerpt || '',
  ),
)

const postImageAlt = computed(() =>
  translateOrFallback(
    post.value?.imageAltKey,
    post.value?.imageAlt || postTitle.value,
  ),
)

const seoTitle = computed(() =>
  translateOrFallback(
    post.value?.seo?.titleKey,
    post.value?.seo?.title ||
      `${postTitle.value} | Express Web`,
  ),
)

const seoDescription = computed(() =>
  translateOrFallback(
    post.value?.seo?.descriptionKey,
    post.value?.seo?.description ||
      postExcerpt.value,
  ),
)

// ========================================================
// KATEGORIJA I AUTOR
// ========================================================

const categoryName = computed(() => {
  if (!category.value?.slug) {
    return ''
  }

  return translateOrFallback(
    `blog.categories.${category.value.slug}.name`,
    category.value.name || '',
  )
})

const authorRole = computed(() => {
  if (!author.value) {
    return ''
  }

  return translateOrFallback(
    author.value.roleKey,
    author.value.role || '',
  )
})

// ========================================================
// DINAMIČKA KOMPONENTA SADRŽAJA
// ========================================================

const articleComponent = computed(() => {
  const loader = post.value?.component

  if (typeof loader !== 'function') {
    return null
  }

  return defineAsyncComponent(loader)
})

// ========================================================
// DATUMI
// ========================================================

const dateLocale = computed(() => {
  const localeMap = {
    sr: 'sr-RS',
    en: 'en-GB',
    de: 'de-DE',
  }

  return localeMap[locale.value] || 'sr-RS'
})

const formatDate = (value) => {
  if (!value) {
    return ''
  }

  const date = new Date(value)

  if (Number.isNaN(date.getTime())) {
    return ''
  }

  return new Intl.DateTimeFormat(dateLocale.value, {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  }).format(date)
}

const formattedPublishedDate = computed(() =>
  formatDate(post.value?.publishedAt),
)

const formattedUpdatedDate = computed(() => {
  if (
    !post.value?.updatedAt ||
    post.value.updatedAt === post.value.publishedAt
  ) {
    return ''
  }

  return formatDate(post.value.updatedAt)
})

// ========================================================
// SEO META PODACI
// ========================================================

const getAbsoluteUrl = (path) => {
  if (!path || typeof window === 'undefined') {
    return ''
  }

  return new URL(path, window.location.origin).href
}

const setMetaTag = ({
  selector,
  attribute,
  attributeValue,
  content,
}) => {
  if (!content) {
    return
  }

  let element = document.head.querySelector(selector)

  if (!element) {
    element = document.createElement('meta')
    element.setAttribute(attribute, attributeValue)
    document.head.appendChild(element)
  }

  element.setAttribute('content', content)
}

const updateDocumentMeta = () => {
  if (typeof document === 'undefined') {
    return
  }

  if (!post.value) {
    document.title = t(
      'blog.post.notFound.metaTitle',
    )

    return
  }

  document.title = seoTitle.value

  setMetaTag({
    selector: 'meta[name="description"]',
    attribute: 'name',
    attributeValue: 'description',
    content: seoDescription.value,
  })

  setMetaTag({
    selector: 'meta[property="og:title"]',
    attribute: 'property',
    attributeValue: 'og:title',
    content: seoTitle.value,
  })

  setMetaTag({
    selector: 'meta[property="og:description"]',
    attribute: 'property',
    attributeValue: 'og:description',
    content: seoDescription.value,
  })

  setMetaTag({
    selector: 'meta[property="og:type"]',
    attribute: 'property',
    attributeValue: 'og:type',
    content: 'article',
  })

  setMetaTag({
    selector: 'meta[property="og:url"]',
    attribute: 'property',
    attributeValue: 'og:url',
    content: window.location.href,
  })

  setMetaTag({
    selector: 'meta[property="og:image"]',
    attribute: 'property',
    attributeValue: 'og:image',
    content: getAbsoluteUrl(post.value.image),
  })

  setMetaTag({
    selector: 'meta[property="article:published_time"]',
    attribute: 'property',
    attributeValue: 'article:published_time',
    content: post.value.publishedAt,
  })

  setMetaTag({
    selector: 'meta[property="article:modified_time"]',
    attribute: 'property',
    attributeValue: 'article:modified_time',
    content:
      post.value.updatedAt ||
      post.value.publishedAt,
  })
}

watch(
  [
    post,
    locale,
    seoTitle,
    seoDescription,
  ],
  updateDocumentMeta,
  {
    immediate: true,
  },
)
</script>
<template>
  <div
    class="flex min-h-screen flex-col bg-slate-950 text-white"
  >
    <Header />

    <main class="grow">
      <!-- ================================================= -->
      <!-- PRONAĐEN ČLANAK                                   -->
      <!-- ================================================= -->

      <template v-if="post">
        <article>
          <!-- Zaglavlje članka -->

          <header
            class="relative overflow-hidden border-b border-slate-800"
          >
            <div
              class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(124,58,237,0.20),transparent_42%)]"
              aria-hidden="true"
            />

            <div
              class="relative mx-auto max-w-5xl px-6 pb-14 pt-28 lg:px-8 lg:pb-20 lg:pt-36"
            >
              <RouterLink
                to="/blog"
                class="inline-flex items-center gap-2 text-sm font-semibold text-purple-300 transition hover:text-purple-200"
              >
                <span aria-hidden="true">←</span>

                {{ t('blog.post.backToBlog') }}
              </RouterLink>

              <div
                class="mt-8 flex flex-wrap items-center gap-3 text-sm text-slate-400"
              >
                <span
                  v-if="categoryName"
                  class="rounded-full border border-purple-500/30 bg-purple-500/10 px-3 py-1 text-purple-300"
                >
                  {{ categoryName }}
                </span>

                <time
                  v-if="formattedPublishedDate"
                  :datetime="post.publishedAt"
                >
                  {{ formattedPublishedDate }}
                </time>

                <span v-if="post.readingTime">
                  {{
                    t('blog.post.readingTime', {
                      minutes: post.readingTime,
                    })
                  }}
                </span>
              </div>

              <h1
                class="mt-6 max-w-4xl text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl"
              >
                {{ postTitle }}
              </h1>

              <p
                class="mt-6 max-w-3xl text-lg leading-8 text-slate-300"
              >
                {{ postExcerpt }}
              </p>

              <div
                v-if="author"
                class="mt-8 flex items-center gap-4"
              >
                <img
                  v-if="author.image"
                  :src="author.image"
                  :alt="author.name"
                  class="h-14 w-14 rounded-full object-cover ring-2 ring-purple-500/30"
                />

                <div>
                  <p class="font-semibold text-white">
                    {{ author.name }}
                  </p>

                  <p
                    v-if="authorRole"
                    class="text-sm text-slate-400"
                    >
                    {{ authorRole }}
                    </p>
                </div>
              </div>
            </div>
          </header>

          <!-- Naslovna fotografija -->

          <div
            v-if="post.image"
            class="mx-auto max-w-6xl px-6 pt-10 lg:px-8"
          >
            <img
              :src="post.image"
              :alt="postImageAlt"
              class="aspect-[16/9] w-full rounded-3xl border border-slate-800 object-cover shadow-2xl"
            />
          </div>

          <!-- Sadržaj članka -->

          <section
            class="mx-auto max-w-4xl px-6 py-14 lg:px-8 lg:py-20"
          >
            <component
              :is="articleComponent"
              v-if="articleComponent"
            />

            <div
              v-else
              class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/60 px-6 py-12 text-center"
            >
              <h2 class="text-xl font-semibold">
                {{
                  t(
                    'blog.post.contentUnavailable.title',
                  )
                }}
              </h2>

              <p class="mt-3 text-slate-400">
                {{
                  t(
                    'blog.post.contentUnavailable.description',
                  )
                }}
              </p>
            </div>

            <p
              v-if="formattedUpdatedDate"
              class="mt-12 border-t border-slate-800 pt-6 text-sm text-slate-500"
            >
              {{
                t('blog.post.updatedAt', {
                  date: formattedUpdatedDate,
                })
              }}
            </p>
          </section>
        </article>

        <!-- Povezani članci -->

        <section
          v-if="relatedPosts.length"
          class="border-t border-slate-800 bg-slate-900/40"
        >
          <div
            class="mx-auto max-w-7xl px-6 py-16 lg:px-8"
          >
            <p class="text-sm font-semibold text-purple-300">
              {{ t('blog.post.related.eyebrow') }}
            </p>

            <h2 class="mt-2 text-3xl font-bold">
              {{ t('blog.post.related.title') }}
            </h2>

            <div class="mt-8 grid gap-6 md:grid-cols-3">
              <article
                v-for="relatedPost in relatedPosts"
                :key="relatedPost.id"
                class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-950 transition hover:border-purple-500/50"
              >
                <RouterLink
                  :to="`/blog/${relatedPost.slug}`"
                  class="block overflow-hidden"
                >
                  <img
                    :src="relatedPost.image"
                    :alt="
                      relatedPost.imageAlt ||
                      relatedPost.title
                    "
                    class="h-44 w-full object-cover transition duration-500 hover:scale-105"
                    loading="lazy"
                  />
                </RouterLink>

                <div class="p-5">
                  <h3
                    class="text-lg font-semibold leading-snug"
                  >
                    <RouterLink
                      :to="`/blog/${relatedPost.slug}`"
                      class="transition hover:text-purple-300"
                    >
                      {{ relatedPost.title }}
                    </RouterLink>
                  </h3>

                  <RouterLink
                    :to="`/blog/${relatedPost.slug}`"
                    class="mt-4 inline-flex items-center text-sm font-semibold text-purple-300 transition hover:text-purple-200"
                  >
                    {{ t('blog.post.readMore') }}

                    <span
                      class="ml-1"
                      aria-hidden="true"
                    >
                      →
                    </span>
                  </RouterLink>
                </div>
              </article>
            </div>
          </div>
        </section>
      </template>

      <!-- ================================================= -->
      <!-- ČLANAK NIJE PRONAĐEN                              -->
      <!-- ================================================= -->

      <section
        v-else
        class="mx-auto flex min-h-[75vh] max-w-3xl items-center px-6 py-28 text-center lg:px-8"
      >
        <div class="w-full">
          <p
            class="text-sm font-semibold uppercase tracking-[0.2em] text-purple-300"
          >
            404
          </p>

          <h1
            class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl"
          >
            {{ t('blog.post.notFound.title') }}
          </h1>

          <p class="mt-6 text-lg text-slate-400">
            {{ t('blog.post.notFound.description') }}
          </p>

          <RouterLink
            to="/blog"
            class="mt-8 inline-flex rounded-full bg-purple-600 px-6 py-3 font-semibold text-white transition hover:bg-purple-500"
          >
            {{ t('blog.post.notFound.button') }}
          </RouterLink>
        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>