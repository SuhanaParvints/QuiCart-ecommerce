<script setup>
import BackendAccessoriesProducts from '~/components/BackendAccessoriesProducts.vue'
</script>

<template>
  <BackendAccessoriesProducts
    title="Jewelry"
    subtitle="Premium rings, earrings, bracelets and necklaces."
    :filter-keywords="['jewelry', 'jewellery', 'ring', 'earring', 'bracelet', 'necklace', 'chain']"
  />
</template>