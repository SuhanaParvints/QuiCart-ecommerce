<script setup>
const categories = [
  {
    title: 'Women Footwear',
    link: '/footwear/women'
  },
  {
    title: 'Men Footwear',
    link: '/footwear/men'
  }
]
</script>

<template>
  <div class="min-h-screen bg-slate-50 py-20 px-6">
    <div class="max-w-7xl mx-auto">

      <h1 class="text-5xl font-black text-center text-slate-900 mb-4">
        Footwear Collection
      </h1>

      <p class="text-center text-slate-500 mb-12">
        Premium shoes for every style and occasion
      </p>

      <div class="grid md:grid-cols-2 gap-6">
        <NuxtLink
          v-for="item in categories"
          :key="item.title"
          :to="item.link"
          class="bg-white rounded-3xl p-10 shadow-lg hover:-translate-y-2 transition"
        >
          <h2 class="text-3xl font-bold text-slate-900">
            {{ item.title }}
          </h2>
        </NuxtLink>
      </div>

    </div>
  </div>
</template>