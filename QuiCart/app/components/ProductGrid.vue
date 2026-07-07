<script setup>
import ProductCard from '~/components/ProductCard.vue'

defineProps({
  products: {
    type: Array,
    required: true
  }
})
</script>

<template>
  <div class="grid md:grid-cols-3 gap-6">
    <ProductCard
      v-for="product in products"
      :key="product.id"
      :product="product"
    />
  </div>
</template>