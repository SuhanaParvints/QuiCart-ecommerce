<script setup>
import ProductGrid from '~/components/ProductGrid.vue'

defineProps({
  title: String,
  subtitle: String,
  parent: String,
  parentLink: String,
  products: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <div class="min-h-screen bg-slate-50">
    <section class="bg-gradient-to-r from-purple-600 via-pink-500 to-purple-700 text-white py-20">
      <div class="max-w-7xl mx-auto px-6 text-center">
        <p class="uppercase tracking-[0.3em] text-sm font-semibold mb-3">
          {{ parent }}
        </p>

        <h1 class="text-5xl md:text-6xl font-black mb-4">
          {{ title }}
        </h1>

        <p class="text-lg text-white/90 max-w-2xl mx-auto">
          {{ subtitle }}
        </p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 py-6">
      <div class="flex items-center gap-2 text-sm text-slate-500">
        <NuxtLink to="/" class="hover:text-purple-600">Home</NuxtLink>
        <span>/</span>
        <NuxtLink :to="parentLink" class="hover:text-purple-600">{{ parent }}</NuxtLink>
        <span>/</span>
        <span class="text-slate-900 font-medium">{{ title }}</span>
      </div>
    </div>

    <section class="max-w-7xl mx-auto px-6 pb-20">
      <div class="flex items-center justify-between mb-10">
        <h2 class="text-3xl font-black text-slate-900">
          All {{ title }}
        </h2>

        <p class="text-slate-500">
          {{ products.length }} Products
        </p>
      </div>

      <ProductGrid :products="products" />
    </section>
  </div>
</template>