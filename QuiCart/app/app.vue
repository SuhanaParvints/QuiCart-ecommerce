<script setup>
import { onMounted } from 'vue'
import { useCartStore } from '~/stores/cart'
import { useWishlistStore } from '~/stores/wishlist'

const cart = useCartStore()
const wishlist = useWishlistStore()

onMounted(() => {
  cart.loadCart()
  wishlist.loadWishlist()
})
</script>

<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>