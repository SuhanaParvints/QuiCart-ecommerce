<!-- app/pages/beauty/face-makeup.vue -->
<script setup>
import CategoryProductPage from '~/components/CategoryProductPage.vue'
import { products } from '~/data/products'
const pageProducts = products.faceMakeup || []
</script>
<template><CategoryProductPage title="Face Makeup" parent="Beauty" parent-link="/beauty" subtitle="Foundation, compact powder, concealer and face essentials." :products="pageProducts" /></template>