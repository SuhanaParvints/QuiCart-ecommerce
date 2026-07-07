<!-- app/pages/footwear/men.vue -->
<script setup>
import CategoryProductPage from '~/components/CategoryProductPage.vue'
import { products } from '~/data/products'
const pageProducts = products.menFootwear || []
</script>
<template><CategoryProductPage title="Men's Footwear" parent="Footwear" parent-link="/footwear" subtitle="Sneakers, formal shoes, loafers, running shoes and boots." :products="pageProducts" /></template>