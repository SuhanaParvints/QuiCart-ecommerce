<!-- app/pages/beauty/tools.vue -->
<script setup>
import CategoryProductPage from '~/components/CategoryProductPage.vue'
import { products } from '~/data/products'
const pageProducts = products.tools || []
</script>
<template><CategoryProductPage title="Makeup Tools" parent="Beauty" parent-link="/beauty" subtitle="Brushes, blenders, mirrors and beauty tools." :products="pageProducts" /></template>