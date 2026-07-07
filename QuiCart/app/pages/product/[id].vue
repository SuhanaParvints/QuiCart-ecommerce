<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '~/stores/cart'
import { useWishlistStore } from '~/stores/wishlist'
import ProductReviewSection from '~/components/ProductReviewSection.vue'

const route = useRoute()
const cart = useCartStore()
const wishlist = useWishlistStore()

const product = ref(null)
const allProducts = ref([])
const loading = ref(true)
const quantity = ref(1)

const getImageUrl = (image) => {
  if (!image) return ''
  if (image.startsWith('http')) return image
  return `http://127.0.0.1:8000/storage/${image}`
}

const statusText = (status) => {
  if (status === 'sold_out') return 'Sold Out'
  if (status === 'limited_stock') return 'Limited Stock'
  return 'In Stock'
}

const statusClass = (status) => {
  if (status === 'sold_out') return 'text-red-600 bg-red-100'
  if (status === 'limited_stock') return 'text-yellow-700 bg-yellow-100'
  return 'text-green-600 bg-green-100'
}

const relatedProducts = computed(() => {
  if (!product.value) return []

  return allProducts.value
    .filter(item =>
      item.id !== product.value.id &&
      item.category === product.value.category
    )
    .slice(0, 4)
})

const fetchProduct = async () => {
  try {
    loading.value = true

    const id = route.params.id

    product.value = await $fetch(`http://127.0.0.1:8000/api/products/${id}`)
    allProducts.value = await $fetch('http://127.0.0.1:8000/api/products')
  } catch (error) {
    console.error(error)
    product.value = null
  } finally {
    loading.value = false
  }
}

const addToCart = () => {
  if (!product.value || product.value.status === 'sold_out') return

  const productData = {
    ...product.value,
    image: getImageUrl(product.value.image)
  }

  for (let i = 0; i < quantity.value; i++) {
    cart.addToCart(productData)
  }
}

const addToWishlist = () => {
  if (!product.value) return

  wishlist.addToWishlist({
    ...product.value,
    image: getImageUrl(product.value.image)
  })
}

const buyNow = () => {
  addToCart()
  navigateTo('/checkout')
}

const increaseQty = () => {
  if (product.value?.stock && quantity.value >= product.value.stock) return
  quantity.value++
}

const decreaseQty = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

onMounted(() => {
  fetchProduct()

  if (cart.loadCart) cart.loadCart()
  if (wishlist.loadWishlist) wishlist.loadWishlist()
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 py-16 px-6">
    <div
      v-if="loading"
      class="max-w-3xl mx-auto text-center py-24 text-slate-600"
    >
      Loading product...
    </div>

    <div
      v-else-if="product"
      class="max-w-7xl mx-auto"
    >
      <div class="grid lg:grid-cols-2 gap-12 bg-white rounded-[2rem] p-6 md:p-10 shadow-xl border border-slate-200">
        <div>
          <img
            :src="getImageUrl(product.image)"
            :alt="product.name"
            class="w-full h-[520px] object-cover rounded-[2rem] shadow-lg"
          />
        </div>

        <div class="flex flex-col justify-center">
          <p class="text-purple-600 font-bold uppercase tracking-[0.25em] text-sm mb-4">
            QuiCart Product
          </p>

          <h1 class="text-4xl md:text-6xl font-black text-slate-900 leading-tight">
            {{ product.name }}
          </h1>

          <div class="flex items-center gap-4 mt-5 flex-wrap">
            <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full font-semibold">
              ⭐ {{ product.rating }}
            </span>

            <span
              class="px-4 py-2 rounded-full font-semibold"
              :class="statusClass(product.status)"
            >
              {{ statusText(product.status) }}
            </span>

            <span class="text-slate-500 font-semibold">
              Stock: {{ product.stock }}
            </span>
          </div>

          <p class="text-purple-700 text-4xl font-black mt-6">
            ₹{{ Number(product.price).toLocaleString('en-IN') }}
          </p>

          <p class="text-slate-600 leading-8 mt-6">
            {{ product.description || 'Premium quality product from QuiCart. Designed with modern style, comfort, and daily use in mind. Perfect for customers who love elegant and trendy shopping collections.' }}
          </p>

          <div class="flex items-center gap-4 mt-8">
            <span class="font-semibold text-slate-700">
              Quantity:
            </span>

            <div class="flex items-center rounded-full border-2 border-purple-300 overflow-hidden bg-white shadow-sm">
              <button
                @click="decreaseQty"
                class="w-12 h-12 text-2xl font-bold text-slate-900 hover:bg-purple-100 transition"
              >
                −
              </button>

              <div class="w-16 h-12 flex items-center justify-center text-lg font-black text-slate-900 border-x border-purple-200">
                {{ quantity }}
              </div>

              <button
                @click="increaseQty"
                class="w-12 h-12 text-2xl font-bold text-slate-900 hover:bg-purple-100 transition"
              >
                +
              </button>
            </div>
          </div>

          <div class="grid sm:grid-cols-3 gap-4 mt-8">
            <button
              @click="addToCart"
              :disabled="product.status === 'sold_out'"
              class="sm:col-span-2 bg-slate-900 text-white py-4 rounded-full font-bold hover:bg-purple-700 transition disabled:opacity-50"
            >
              {{ product.status === 'sold_out' ? 'Sold Out' : 'Add To Cart' }}
            </button>

            <button
              @click="addToWishlist"
              class="bg-white border border-slate-300 text-slate-900 py-4 rounded-full font-bold hover:bg-pink-50 hover:text-pink-600 transition"
            >
              ❤️ Wishlist
            </button>
          </div>

          <button
            @click="buyNow"
            :disabled="product.status === 'sold_out'"
            class="block text-center mt-4 bg-green-600 text-white py-4 rounded-full font-bold hover:bg-green-700 transition disabled:opacity-50"
          >
            Buy Now
          </button>
        </div>
      </div>

      <ProductReviewSection :product="product" />

      <section class="mt-16">
        <h2 class="text-3xl font-black text-slate-900 mb-8">
          Related Products
        </h2>

        <div
          v-if="relatedProducts.length === 0"
          class="text-slate-500"
        >
          No related products found.
        </div>

        <div
          v-else
          class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6"
        >
          <NuxtLink
            v-for="item in relatedProducts"
            :key="item.id"
            :to="`/product/${item.id}`"
            class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-md hover:-translate-y-2 hover:shadow-xl transition"
          >
            <img
              :src="getImageUrl(item.image)"
              :alt="item.name"
              class="w-full h-56 object-cover"
            />

            <div class="p-5">
              <h3 class="font-bold text-slate-900">
                {{ item.name }}
              </h3>

              <p class="text-purple-700 font-black mt-2">
                ₹{{ Number(item.price).toLocaleString('en-IN') }}
              </p>
            </div>
          </NuxtLink>
        </div>
      </section>
    </div>

    <div
      v-else
      class="max-w-3xl mx-auto text-center py-24"
    >
      <h1 class="text-4xl font-black text-slate-900 mb-4">
        Product Not Found
      </h1>

      <p class="text-slate-500 mb-8">
        The product you are looking for does not exist.
      </p>

      <NuxtLink
        to="/"
        class="bg-slate-900 text-white px-8 py-3 rounded-full hover:bg-purple-700 transition"
      >
        Back To Home
      </NuxtLink>
    </div>
  </div>
</template>