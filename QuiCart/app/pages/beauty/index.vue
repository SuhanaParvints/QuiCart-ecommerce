<script setup>
const categories = [
  { title: 'Face Makeup', link: '/beauty/face-makeup' },
  { title: 'Lip Makeup', link: '/beauty/lip-makeup' },
  { title: 'Eye Makeup', link: '/beauty/eye-makeup' },
  { title: 'Skincare', link: '/beauty/skincare' },
  { title: 'Makeup Tools', link: '/beauty/tools' }
]
</script>

<template>
  <div class="min-h-screen bg-[#0f172a] text-white py-20 px-6">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-5xl font-black text-center mb-12">Beauty Collection</h1>

      <div class="grid md:grid-cols-3 gap-6">
        <NuxtLink
          v-for="item in categories"
          :key="item.title"
          :to="item.link"
          class="bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-blue-600/20 transition"
        >
          <h2 class="text-2xl font-bold">{{ item.title }}</h2>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>