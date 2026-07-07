<script setup>
import BackendMenProducts from '~/components/BackendMenProducts.vue'
</script>

<template>
  <BackendMenProducts
    title="Jackets"
    subtitle="Premium jackets for casual, winter and streetwear looks."
    :filter-keywords="['jacket', 'denim jacket', 'bomber', 'coat', 'winter']"
  />
</template>