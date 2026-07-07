<!-- app/pages/beauty/lip-makeup.vue -->
<script setup>
import CategoryProductPage from '~/components/CategoryProductPage.vue'
import { products } from '~/data/products'
const pageProducts = products.lipMakeup || []
</script>
<template><CategoryProductPage title="Lip Makeup" parent="Beauty" parent-link="/beauty" subtitle="Lipsticks, gloss, tints and lip liners." :products="pageProducts" /></template>