<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '~/stores/cart'

const route = useRoute()
const cart = useCartStore()

const products = ref([])
const loading = ref(true)
const search = ref('')

const getImageUrl = (image) => {
  if (!image) return ''
  if (image.startsWith('http')) return image
  return `http://127.0.0.1:8000/storage/${image}`
}

const filteredProducts = computed(() => {
  const keyword = search.value.trim().toLowerCase()

  if (!keyword) return products.value

  return products.value.filter(product => {
    const text = `
      ${product.name || ''}
      ${product.category || ''}
      ${product.description || ''}
      ${product.price || ''}
    `.toLowerCase()

    return keyword.split(' ').every(word => text.includes(word))
  })
})

const fetchProducts = async () => {
  try {
    loading.value = true

    const data = await $fetch('http://127.0.0.1:8000/api/products')

    products.value = data.map(product => ({
      ...product,
      image: getImageUrl(product.image)
    }))
  } catch (error) {
    console.error(error)
    alert('Cannot load products. Start Laravel server.')
  } finally {
    loading.value = false
  }
}

const addToCart = (product) => {
  cart.addToCart(product)
}

onMounted(async () => {
  search.value = route.query.q || ''
  await fetchProducts()

  if (cart.loadCart) cart.loadCart()
})

watch(
  () => route.query.q,
  (value) => {
    search.value = value || ''
  }
)
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-900">
    <section class="bg-slate-900 text-white py-20 px-6 text-center">
      <h1 class="text-5xl font-black">
        Search Products
      </h1>

      <p class="text-slate-300 mt-4">
        Results for "{{ search }}"
      </p>

      <input
        v-model="search"
        type="text"
        placeholder="Search products..."
        class="mt-8 w-full max-w-xl rounded-full px-6 py-4 text-slate-900 outline-none"
      />
    </section>

    <section class="max-w-7xl mx-auto px-6 py-14">
      <div v-if="loading" class="text-center py-20 text-slate-600">
        Loading products...
      </div>

      <div v-else-if="filteredProducts.length === 0" class="text-center py-20 text-slate-600">
        No products found for "{{ search }}".
      </div>

      <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="bg-white rounded-3xl overflow-hidden shadow border hover:-translate-y-2 hover:shadow-xl transition"
        >
          <NuxtLink :to="`/product/${product.id}`">
            <img
              :src="product.image"
              :alt="product.name"
              class="w-full h-72 object-cover"
            />
          </NuxtLink>

          <div class="p-6">
            <p class="text-purple-600 uppercase text-xs tracking-[0.25em] font-bold">
              {{ product.category }}
            </p>

            <NuxtLink :to="`/product/${product.id}`">
              <h3 class="text-2xl font-black mt-3 hover:text-purple-700">
                {{ product.name }}
              </h3>
            </NuxtLink>

            <p class="text-purple-700 text-3xl font-black mt-4">
              ₹{{ Number(product.price).toLocaleString('en-IN') }}
            </p>

            <button
              @click="addToCart(product)"
              class="mt-6 w-full bg-slate-900 text-white py-3 rounded-full font-bold hover:bg-purple-700 transition"
            >
              Add To Cart
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>