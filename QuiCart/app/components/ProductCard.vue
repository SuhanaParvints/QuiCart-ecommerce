<script setup>
import { useCartStore } from '~/stores/cart'
import { useWishlistStore } from '~/stores/wishlist'

const cart = useCartStore()
const wishlist = useWishlistStore()

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const toggleWishlist = () => {
  wishlist.addToWishlist(props.product)
}
</script>

<template>
  <div
    class="group bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-md hover:-translate-y-2 hover:shadow-2xl transition-all duration-300"
  >
    <!-- Product Image -->
    <div class="relative overflow-hidden">
      <NuxtLink :to="`/product/${product.id}`">
        <img
          :src="product.image"
          :alt="product.name"
          class="w-full h-72 object-cover group-hover:scale-110 transition duration-700"
        />
      </NuxtLink>

      <!-- Wishlist Button -->
      <button
        @click="toggleWishlist"
        class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/90 backdrop-blur flex items-center justify-center shadow-lg hover:bg-pink-500 hover:text-white transition"
      >
        ❤
      </button>

      <!-- Rating Badge -->
      <div
        class="absolute bottom-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm font-semibold"
      >
        ⭐ {{ product.rating }}
      </div>
    </div>

    <!-- Product Info -->
    <div class="p-5">
      <NuxtLink :to="`/product/${product.id}`">
        <h3
          class="font-bold text-xl text-slate-900 hover:text-purple-600 transition"
        >
          {{ product.name }}
        </h3>
      </NuxtLink>

      <p class="text-slate-500 text-sm mt-2">
        Premium quality product from QuiCart.
      </p>

      <div class="flex items-center justify-between mt-4">
        <p class="text-purple-700 text-2xl font-black">
          ₹{{ product.price }}
        </p>

        <span
          class="text-green-600 text-sm font-semibold"
        >
          In Stock
        </span>
      </div>

      <!-- Buttons -->
      <div class="grid grid-cols-2 gap-3 mt-5">
        <NuxtLink
          :to="`/product/${product.id}`"
          class="text-center py-3 rounded-full border border-slate-300 text-slate-700 hover:bg-slate-100 transition"
        >
          View
        </NuxtLink>

        <button
          @click="cart.addToCart(product)"
          class="bg-slate-900 text-white py-3 rounded-full hover:bg-purple-700 transition"
        >
          Add Cart
        </button>
      </div>
    </div>
  </div>
</template>