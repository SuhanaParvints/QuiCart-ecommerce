<script setup>
import { onMounted } from 'vue'
import { useWishlistStore } from '~/stores/wishlist'
import { useCartStore } from '~/stores/cart'

const wishlist = useWishlistStore()
const cart = useCartStore()

onMounted(() => {
  wishlist.loadWishlist()
})

const moveToCart = (product) => {
  cart.addToCart(product)
  wishlist.removeFromWishlist(product.id)
}
</script>

<template>
  <div class="min-h-screen bg-[#0f172a] text-white py-16 px-6">
    <div class="max-w-7xl mx-auto">
      <div class="text-center mb-12">
        <p class="text-blue-300 uppercase tracking-[0.3em] font-semibold mb-3">
          Your Favorites
        </p>

        <h1 class="text-5xl font-black">
          Wishlist
        </h1>
      </div>

      <div v-if="wishlist.items.length === 0" class="text-center py-20">
        <p class="text-slate-400 text-xl mb-6">
          Your wishlist is empty
        </p>

        <NuxtLink
          to="/shop"
          class="bg-blue-600 px-6 py-3 rounded-full font-semibold hover:bg-blue-700 transition"
        >
          Browse Products
        </NuxtLink>
      </div>

      <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="item in wishlist.items"
          :key="item.id"
          class="bg-white rounded-3xl overflow-hidden shadow-xl"
        >
          <img
            :src="item.image"
            :alt="item.name"
            class="w-full h-64 object-cover"
          />

          <div class="p-6">
            <h2 class="text-slate-900 text-xl font-bold mb-2">
              {{ item.name }}
            </h2>

            <p class="text-blue-600 font-black text-2xl mb-5">
              ₹{{ item.price.toLocaleString('en-IN') }}
            </p>

            <div class="flex gap-3">
              <button
                @click="moveToCart(item)"
                class="flex-1 bg-blue-600 text-white py-3 rounded-full font-semibold hover:bg-blue-700 transition"
              >
                Add to Cart
              </button>

              <button
                @click="wishlist.removeFromWishlist(item.id)"
                class="px-5 bg-red-600 text-white rounded-full hover:bg-red-700 transition"
              >
                ×
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>