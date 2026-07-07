<script setup>
import Navbar from '~/components/Navbar.vue'
import Footer from '~/components/Footer.vue'
</script>

<template>
  <div class="min-h-screen bg-[#0f172a] text-white overflow-x-hidden">
    <Navbar />

    <main>
      <slot />
    </main>

    <Footer />
  </div>
</template>