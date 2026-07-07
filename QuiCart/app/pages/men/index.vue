<script setup>
const categories = [
  {
    title: 'Shirts',
    desc: 'Formal, casual and premium shirts',
    link: '/men/shirts',
    image: 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=900'
  },
  {
    title: 'T-Shirts',
    desc: 'Graphic, oversized and daily wear tees',
    link: '/men/tshirts',
    image: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=900'
  },
  {
    title: 'Trousers',
    desc: 'Classic trousers, chinos and pants',
    link: '/men/trousers',
    image: 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=900'
  },
  {
    title: 'Jackets',
    desc: 'Denim, bomber and winter jackets',
    link: '/men/jackets',
    image: 'https://images.unsplash.com/photo-1543076447-215ad9ba6923?w=900'
  },
  {
    title: 'Ethnic Wear',
    desc: 'Kurta, sherwani and festive outfits',
    link: '/men/ethnic',
    image: 'https://images.unsplash.com/photo-1593032465175-481ac7f401a0?w=900'
  }
]
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-900">
    <section class="bg-gradient-to-r from-slate-900 via-blue-900 to-purple-800 text-white py-24 px-6 text-center">
      <p class="uppercase tracking-[0.3em] text-sm font-semibold mb-4">
        QuiCart Collection
      </p>

      <h1 class="text-5xl md:text-6xl font-black">
        Men Fashion
      </h1>

      <p class="max-w-2xl mx-auto mt-5 text-white/90 text-lg">
        Explore premium men’s shirts, t-shirts, trousers, jackets and ethnic wear.
      </p>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-16">
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <NuxtLink
          v-for="category in categories"
          :key="category.title"
          :to="category.link"
          class="group relative h-80 rounded-3xl overflow-hidden shadow-xl border border-slate-200"
        >
          <img
            :src="category.image"
            :alt="category.title"
            class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700"
          />

          <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>

          <div class="absolute bottom-6 left-6 right-6">
            <h2 class="text-3xl font-black text-white">
              {{ category.title }}
            </h2>

            <p class="text-slate-200 mt-2">
              {{ category.desc }}
            </p>

            <span class="inline-block mt-5 bg-white text-slate-900 px-5 py-2 rounded-full font-bold">
              Explore
            </span>
          </div>
        </NuxtLink>
      </div>
    </section>
  </div>
</template>