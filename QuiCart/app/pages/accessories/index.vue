<script setup>
const categories = [
  {
    title: 'Watches',
    description: 'Luxury watches, smart watches and classic timepieces.',
    image: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=900',
    link: '/accessories/watches'
  },
  {
    title: 'Handbags',
    description: 'Elegant handbags, tote bags and shoulder bags.',
    image: 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=900',
    link: '/accessories/handbags'
  },
  {
    title: 'Jewelry',
    description: 'Necklaces, earrings, rings and bracelets.',
    image: 'https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=900',
    link: '/accessories/jewelry'
  },
  {
    title: 'Sunglasses',
    description: 'Fashion sunglasses for every occasion.',
    image: 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=900',
    link: '/accessories/sunglasses'
  },
  {
    title: 'Belts',
    description: 'Leather belts and casual belts.',
    image: 'https://images.unsplash.com/photo-1624222247344-550fb60583dc?w=900',
    link: '/accessories/belts'
  }
]
</script>

<template>
  <div class="min-h-screen bg-slate-50">

    <section class="bg-gradient-to-r from-slate-900 via-purple-800 to-pink-600 text-white py-24">
      <div class="max-w-7xl mx-auto px-6 text-center">

        <p class="uppercase tracking-[0.3em] font-semibold">
          QuiCart Collection
        </p>

        <h1 class="text-6xl font-black mt-4">
          Accessories
        </h1>

        <p class="mt-6 text-lg text-white/90 max-w-2xl mx-auto">
          Complete your look with premium accessories designed for every style.
        </p>

      </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-20">

      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

        <NuxtLink
          v-for="category in categories"
          :key="category.title"
          :to="category.link"
          class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition hover:-translate-y-2"
        >

          <img
            :src="category.image"
            :alt="category.title"
            class="w-full h-72 object-cover group-hover:scale-105 transition duration-500"
          >

          <div class="p-6">

            <h2 class="text-3xl font-black text-slate-900">
              {{ category.title }}
            </h2>

            <p class="mt-3 text-slate-600 leading-7">
              {{ category.description }}
            </p>

            <div
              class="mt-6 inline-flex items-center gap-2 text-purple-600 font-bold"
            >
              Explore →
            </div>

          </div>

        </NuxtLink>

      </div>

    </section>

  </div>
</template>