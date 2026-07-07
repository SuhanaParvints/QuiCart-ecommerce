<script setup>
import BackendWomenProducts from '~/components/BackendWomenProducts.vue'
</script>

<template>
  <BackendWomenProducts
    title="Dresses"
    subtitle="Premium dresses for elegant, casual and festive looks."
    :filter-keywords="['dress', 'maxi', 'midi', 'gown', 'wrap', 'evening', 'floral', 'satin']"
  />
</template>