<script setup>
import BackendMenProducts from '~/components/BackendMenProducts.vue'
</script>

<template>
  <BackendMenProducts
    title="T-Shirts"
    subtitle="Trendy t-shirts for comfort, street style and daily wear."
    :filter-keywords="['tshirt', 't-shirt', 'tee', 'graphic tee', 'oversized']"
  />
</template>