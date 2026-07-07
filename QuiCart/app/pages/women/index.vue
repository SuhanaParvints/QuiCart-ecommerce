<script setup>
const categories = [
  {
    title: 'Dresses',
    desc: 'Maxi, midi, party and casual dresses',
    link: '/women/dresses',
    image: 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=900'
  },
  {
    title: 'Tops & Blouses',
    desc: 'Trendy tops, shirts and blouses',
    link: '/women/tops',
    image: 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=900'
  },
  {
    title: 'Jeans & Pants',
    desc: 'Stylish jeans, trousers and pants',
    link: '/women/pants',
    image: 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=900'
  },
  {
    title: 'Skirts',
    desc: 'Elegant and casual skirts',
    link: '/women/skirts',
    image: 'https://images.unsplash.com/photo-1583496661160-fb5886a0aaaa?w=900'
  },
  {
    title: 'Ethnic Wear',
    desc: 'Traditional and festive outfits',
    link: '/women/ethnic',
    image: 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=900'
  }
]
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-900">
    <section class="bg-gradient-to-r from-purple-600 via-pink-500 to-purple-700 text-white py-24 px-6 text-center">
      <p class="uppercase tracking-[0.3em] text-sm font-semibold mb-4">
        QuiCart Collection
      </p>

      <h1 class="text-5xl md:text-6xl font-black">
        Women Fashion
      </h1>

      <p class="max-w-2xl mx-auto mt-5 text-white/90 text-lg">
        Explore premium women’s dresses, tops, pants, skirts and ethnic wear.
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