<!-- app/pages/beauty/skincare.vue -->
<script setup>
import CategoryProductPage from '~/components/CategoryProductPage.vue'
import { products } from '~/data/products'
const pageProducts = products.skincare || []
</script>
<template><CategoryProductPage title="Skincare" parent="Beauty" parent-link="/beauty" subtitle="Cleanser, serum, moisturizer, sunscreen and toner." :products="pageProducts" /></template>