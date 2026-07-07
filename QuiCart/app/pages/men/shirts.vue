<script setup>
import BackendMenProducts from '~/components/BackendMenProducts.vue'
</script>

<template>
  <BackendMenProducts
    title="Shirts"
    subtitle="Premium shirts for formal, casual and smart looks."
    :filter-keywords="['shirt', 'formal shirt', 'casual shirt', 'cotton shirt']"
  />
</template>