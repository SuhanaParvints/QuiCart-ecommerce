<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

import { useCartStore } from '~/stores/cart'
import { useWishlistStore } from '~/stores/wishlist'
import { useAuthStore } from '~/stores/auth'

const router = useRouter()

const cart = useCartStore()
const wishlist = useWishlistStore()
const auth = useAuthStore()

const search = ref('')
const showSearch = ref(false)
const searching = ref(false)

onMounted(async () => {
  if (cart.loadCart) cart.loadCart()
  if (wishlist.loadWishlist) wishlist.loadWishlist()

  if (auth.token) {
    await auth.fetchUser()
  }
})

const handleSearch = async () => {
  const keyword = search.value.trim().toLowerCase()

  if (!keyword) {
    alert('Please enter a product name')
    return
  }

  try {
    searching.value = true

    const products = await $fetch('http://127.0.0.1:8000/api/products')

    const matchedProducts = products.filter(product => {
      const name = product.name?.toLowerCase() || ''
      const category = product.category?.toLowerCase() || ''
      const description = product.description?.toLowerCase() || ''

      return (
        name.includes(keyword) ||
        category.includes(keyword) ||
        description.includes(keyword)
      )
    })

    showSearch.value = false
    search.value = ''

    if (matchedProducts.length === 1) {
      router.push(`/product/${matchedProducts[0].id}`)
      return
    }

    router.push({
      path: '/search',
      query: {
        q: keyword
      }
    })
  } catch (error) {
    console.error(error)
    alert('Search failed. Start Laravel backend using php artisan serve.')
  } finally {
    searching.value = false
  }
}
</script>

<template>
  <header class="sticky top-0 z-50 bg-slate-900/95 backdrop-blur-xl border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-6">
      <NuxtLink to="/" class="text-3xl font-black text-white">
        <span class="text-purple-500">Qui</span>Cart
      </NuxtLink>

      <nav class="hidden lg:flex items-center gap-6">
        <NuxtLink to="/" class="text-slate-300 hover:text-white transition">Home</NuxtLink>
        <NuxtLink to="/women" class="text-slate-300 hover:text-purple-400 transition">Women</NuxtLink>
        <NuxtLink to="/men" class="text-slate-300 hover:text-purple-400 transition">Men</NuxtLink>
        <NuxtLink to="/accessories" class="text-slate-300 hover:text-purple-400 transition">Accessories</NuxtLink>
        <NuxtLink to="/beauty" class="text-slate-300 hover:text-purple-400 transition">Beauty</NuxtLink>
        <NuxtLink to="/footwear" class="text-slate-300 hover:text-purple-400 transition">Footwear</NuxtLink>
      </nav>

      <div class="flex items-center gap-3">
        <div class="relative">
          <button
            type="button"
            @click="showSearch = !showSearch"
            class="w-10 h-10 rounded-full border border-slate-700 bg-slate-800 text-white hover:bg-purple-600 transition"
          >
            🔍
          </button>

          <div
            v-if="showSearch"
            class="absolute right-0 top-14 z-50 bg-slate-900 border border-slate-700 rounded-full overflow-hidden shadow-xl flex"
          >
            <input
              v-model="search"
              @keyup.enter="handleSearch"
              type="text"
              placeholder="Search products..."
              class="bg-transparent px-5 py-3 w-72 text-white placeholder:text-slate-400 outline-none"
            />

            <button
              type="button"
              @click="handleSearch"
              :disabled="searching"
              class="bg-purple-600 hover:bg-purple-700 px-5 text-white disabled:opacity-60"
            >
              {{ searching ? '...' : 'Go' }}
            </button>
          </div>
        </div>

        <NuxtLink
          to="/wishlist"
          class="px-4 py-2 rounded-full bg-slate-800 border border-slate-700 text-white hover:border-pink-500 transition"
        >
          ❤️ <span class="ml-1 font-semibold">{{ wishlist.totalItems || 0 }}</span>
        </NuxtLink>

        <NuxtLink
          to="/cart"
          class="px-4 py-2 rounded-full bg-slate-800 border border-slate-700 text-white hover:border-blue-500 transition"
        >
          🛒 <span class="ml-1 font-semibold">{{ cart.totalItems || 0 }}</span>
        </NuxtLink>

        <template v-if="auth.user">
          <NuxtLink
            to="/account"
            class="px-4 py-2 rounded-full bg-slate-800 border border-slate-700 text-white hover:border-purple-500 transition"
          >
            My Account
          </NuxtLink>

          <button
            type="button"
            @click="auth.logout()"
            class="px-4 py-2 rounded-full bg-red-500 text-white hover:bg-red-600 transition"
          >
            Logout
          </button>
        </template>

        <template v-else>
          <NuxtLink
            to="/login"
            class="px-4 py-2 rounded-full border border-slate-700 text-white hover:border-purple-500 transition"
          >
            Login
          </NuxtLink>

          <NuxtLink
            to="/signup"
            class="px-5 py-2 rounded-full bg-purple-600 text-white hover:bg-purple-700 transition"
          >
            Sign Up
          </NuxtLink>
        </template>
      </div>
    </div>
  </header>
</template>