<!-- app/pages/footwear/women.vue -->
<script setup>
import CategoryProductPage from '~/components/CategoryProductPage.vue'
import { products } from '~/data/products'
const pageProducts = products.womenFootwear || []
</script>
<template><CategoryProductPage title="Women's Footwear" parent="Footwear" parent-link="/footwear" subtitle="Sneakers, heels, flats, sandals and loafers." :products="pageProducts" /></template>