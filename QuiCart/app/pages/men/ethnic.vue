<script setup>
import BackendMenProducts from '~/components/BackendMenProducts.vue'
</script>

<template>
  <BackendMenProducts
    title="Ethnic Wear"
    subtitle="Traditional and festive outfits for men."
    :filter-keywords="['ethnic', 'kurta', 'sherwani', 'traditional', 'festive']"
  />
</template>