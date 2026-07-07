<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '~/stores/cart'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  filterKeywords: {
    type: Array,
    default: () => []
  }
})

const cart = useCartStore()

const products = ref([])
const loading = ref(true)
const search = ref('')

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
  if (status === 'sold_out') return 'bg-red-100 text-red-700'
  if (status === 'limited_stock') return 'bg-yellow-100 text-yellow-700'
  return 'bg-green-100 text-green-700'
}

const filteredProducts = computed(() => {
  let data = products.value

  if (props.filterKeywords.length) {
    data = data.filter(product => {
      const text = `${product.name} ${product.description}`.toLowerCase()

      return props.filterKeywords.some(keyword =>
        text.includes(keyword.toLowerCase())
      )
    })
  }

  if (search.value.trim()) {
    const keyword = search.value.toLowerCase()

    data = data.filter(product =>
      product.name?.toLowerCase().includes(keyword) ||
      product.description?.toLowerCase().includes(keyword) ||
      product.price?.toString().includes(keyword)
    )
  }

  return data
})

const fetchProducts = async () => {
  try {
    loading.value = true

    products.value = await $fetch(
      'http://127.0.0.1:8000/api/products?category=men'
    )
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const addToCart = (product) => {
  cart.addToCart({
    ...product,
    image: getImageUrl(product.image)
  })
}

onMounted(fetchProducts)
</script>

<template>
  <div class="min-h-screen bg-slate-50 text-slate-900">
    <section class="bg-gradient-to-r from-slate-900 via-blue-900 to-purple-800 text-white py-20 px-6">
      <div class="max-w-7xl mx-auto text-center">
        <p class="uppercase tracking-[0.3em] text-sm font-semibold mb-3">
          Men's Fashion
        </p>

        <h1 class="text-5xl md:text-6xl font-black mb-4">
          {{ title }}
        </h1>

        <p class="text-lg text-white/90 max-w-2xl mx-auto">
          {{ subtitle }}
        </p>
      </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-10">
      <div class="flex items-center gap-2 text-sm text-slate-500 mb-8">
        <NuxtLink to="/" class="hover:text-purple-600">
          Home
        </NuxtLink>

        <span>/</span>

        <NuxtLink to="/men" class="hover:text-purple-600">
          Men
        </NuxtLink>

        <span>/</span>

        <span class="text-slate-900 font-medium">
          {{ title }}
        </span>
      </div>

      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5 mb-10">
        <div>
          <h2 class="text-3xl font-black text-slate-900">
            {{ title }} Products
          </h2>

          <p class="text-slate-500 mt-2">
            {{ filteredProducts.length }} products found
          </p>
        </div>

        <input
          v-model="search"
          type="text"
          placeholder="Search products..."
          class="w-full md:w-96 bg-white border border-slate-300 rounded-full px-5 py-3 text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-purple-300"
        />
      </div>

      <div v-if="loading" class="py-20 text-center text-slate-600">
        Loading products...
      </div>

      <div v-else-if="filteredProducts.length === 0" class="py-20 text-center text-slate-600">
        No products found. Add men products from Admin Products.
      </div>

      <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-md hover:-translate-y-2 hover:shadow-xl transition"
        >
          <NuxtLink :to="`/product/${product.id}`">
            <img
              :src="getImageUrl(product.image)"
              :alt="product.name"
              class="w-full h-72 object-cover"
            />
          </NuxtLink>

          <div class="p-6">
            <p class="text-purple-600 font-bold uppercase tracking-[0.25em] text-xs mb-3">
              QuiCart Product
            </p>

            <NuxtLink :to="`/product/${product.id}`">
              <h3 class="text-2xl font-black text-slate-900 hover:text-purple-700">
                {{ product.name }}
              </h3>
            </NuxtLink>

            <div class="flex items-center gap-3 mt-4">
              <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-semibold text-sm">
                ⭐ {{ product.rating }}
              </span>

              <span
                class="px-3 py-1 rounded-full text-sm font-bold"
                :class="statusClass(product.status)"
              >
                {{ statusText(product.status) }}
              </span>
            </div>

            <p class="text-purple-700 text-3xl font-black mt-5">
              ₹{{ Number(product.price).toLocaleString('en-IN') }}
            </p>

            <p class="text-slate-600 leading-7 mt-4 line-clamp-3">
              {{ product.description || 'Premium quality product from QuiCart. Designed with modern style, comfort, and daily use in mind.' }}
            </p>

            <p class="text-slate-500 mt-4">
              Stock: {{ product.stock }}
            </p>

            <button
              @click="addToCart(product)"
              :disabled="product.status === 'sold_out'"
              class="mt-6 w-full bg-slate-900 text-white py-3 rounded-full font-bold hover:bg-purple-700 transition disabled:opacity-50"
            >
              {{ product.status === 'sold_out' ? 'Sold Out' : 'Add To Cart' }}
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>