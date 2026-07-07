<!-- app/pages/beauty/eye-makeup.vue -->
<script setup>
import CategoryProductPage from '~/components/CategoryProductPage.vue'
import { products } from '~/data/products'
const pageProducts = products.eyeMakeup || []
</script>
<template><CategoryProductPage title="Eye Makeup" parent="Beauty" parent-link="/beauty" subtitle="Eyeshadow, eyeliner, mascara and kajal products." :products="pageProducts" /></template>