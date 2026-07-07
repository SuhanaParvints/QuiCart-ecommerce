<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useCartStore } from '~/stores/cart'
import gsap from 'gsap'
import ScrollTrigger from 'gsap/dist/ScrollTrigger'

if (process.client) {
  gsap.registerPlugin(ScrollTrigger)
}

const cart = useCartStore()

const search = ref('')
const pageRef = ref(null)
const stripRef = ref(null)
const showCartPopup = ref(false)
const lastAddedProduct = ref(null)
const currentSlide = ref(0)
const products = ref([])
const loading = ref(false)

let ctx
let slideTimer

const categories = [
  {
    title: 'Women',
    desc: 'Dresses • Tops • Beauty',
    link: '/women',
    image: 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1200'
  },
  {
    title: 'Men',
    desc: 'Shirts • Shoes • Watches',
    link: '/men',
    image: 'https://images.unsplash.com/photo-1516257984-b1b4d707412e?w=1200'
  },
  {
    title: 'Accessories',
    desc: 'Bags • Jewelry • Sunglasses',
    link: '/accessories',
    image: 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=1200'
  },
  {
    title: 'Beauty',
    desc: 'Makeup • Skincare • Tools',
    link: '/beauty',
    image: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=1200'
  }
]

const showcaseSlides = [
  [
    { name: 'Luxury Handbags', tag: 'New Arrival', image: 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=900' },
    { name: 'Elegant Dresses', tag: 'Trending', image: 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=900' },
    { name: 'White Sneakers', tag: 'Best Seller', image: 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=900' },
    { name: 'Premium Shades', tag: 'Hot Pick', image: 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=900' }
  ],
  [
    { name: 'Graphic Tees', tag: 'Street Style', image: 'https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=900' },
    { name: 'Denim Jackets', tag: 'Classic', image: 'https://images.unsplash.com/photo-1543076447-215ad9ba6923?w=900' },
    { name: 'Beauty Kits', tag: 'Glow Up', image: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=900' },
    { name: 'Luxury Watches', tag: 'Premium', image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=900' }
  ]
]

const fallbackStripImages = [
  'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=700',
  'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=700',
  'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=700',
  'https://images.unsplash.com/photo-1543076447-215ad9ba6923?w=700',
  'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=700',
  'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=700'
]

const getImageUrl = (image) => {
  if (!image) return ''
  if (image.startsWith('http')) return image
  return `http://127.0.0.1:8000/storage/${image}`
}

const currentShowcase = computed(() => showcaseSlides[currentSlide.value])

const stripImages = computed(() => {
  const backendImages = products.value
    .filter(product => product.image)
    .map(product => getImageUrl(product.image))

  return backendImages.length ? backendImages : fallbackStripImages
})

const filteredProducts = computed(() => {
  if (!search.value.trim()) {
    return products.value
  }

  const keyword = search.value.toLowerCase()

  return products.value.filter(product =>
    product.name?.toLowerCase().includes(keyword) ||
    product.category?.toLowerCase().includes(keyword) ||
    product.description?.toLowerCase().includes(keyword) ||
    product.price?.toString().includes(keyword)
  )
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
  } finally {
    loading.value = false
  }
}

const animateIn = () => {
  gsap.fromTo(
    '.showcase-panel',
    { scale: 0.97, opacity: 0.8 },
    { scale: 1, opacity: 1, duration: 0.7, ease: 'power3.out' }
  )

  gsap.fromTo(
    '.showcase-card',
    { y: 90, opacity: 0, scale: 0.93, rotateX: 6 },
    {
      y: 0,
      opacity: 1,
      scale: 1,
      rotateX: 0,
      duration: 0.85,
      stagger: 0.1,
      ease: 'power3.out'
    }
  )
}

const nextSlide = () => {
  gsap.to('.showcase-card', {
    y: -80,
    opacity: 0,
    scale: 0.92,
    duration: 0.4,
    stagger: 0.06,
    ease: 'power2.in',
    onComplete: () => {
      currentSlide.value = (currentSlide.value + 1) % showcaseSlides.length
      nextTick(() => setTimeout(animateIn, 30))
    }
  })
}

const handleAddToCart = (product) => {
  const productData = {
    ...product,
    image: getImageUrl(product.image)
  }

  cart.addToCart(productData)
  lastAddedProduct.value = productData
  showCartPopup.value = true

  setTimeout(() => {
    showCartPopup.value = false
  }, 3200)
}

onMounted(async () => {
  if (cart.loadCart) {
    cart.loadCart()
  }

  await fetchProducts()

  await nextTick()

  ctx = gsap.context(() => {
    gsap.to('.hero-bg', {
      scale: 1.1,
      duration: 22,
      ease: 'none',
      repeat: -1,
      yoyo: true
    })

    gsap.from('.hero-subtitle', {
      x: -50,
      opacity: 0,
      duration: 0.8,
      delay: 0.1,
      ease: 'power3.out'
    })

    gsap.from('.hero-title', {
      y: 80,
      opacity: 0,
      duration: 1,
      delay: 0.2,
      ease: 'power3.out'
    })

    gsap.from('.hero-text', {
      y: 40,
      opacity: 0,
      duration: 0.9,
      delay: 0.5,
      ease: 'power3.out'
    })

    gsap.from('.hero-btn', {
      scale: 0.75,
      opacity: 0,
      duration: 0.7,
      delay: 0.8,
      ease: 'back.out(1.7)'
    })

    gsap.from('.category-card', {
      scrollTrigger: {
        trigger: '#categories',
        start: 'top 80%',
        once: true
      },
      y: 80,
      opacity: 0,
      scale: 0.94,
      duration: 0.8,
      stagger: 0.12,
      ease: 'power3.out'
    })

    gsap.to('.blob', {
      x: 40,
      y: -30,
      scale: 1.2,
      duration: 5,
      stagger: 0.6,
      repeat: -1,
      yoyo: true,
      ease: 'sine.inOut'
    })

    gsap.from('.showcase-panel', {
      x: 80,
      opacity: 0,
      duration: 1,
      delay: 0.3,
      ease: 'power3.out'
    })

    setTimeout(animateIn, 400)
    slideTimer = setInterval(nextSlide, 3200)

    if (stripRef.value) {
      gsap.to(stripRef.value, {
        x: '-50%',
        duration: 22,
        ease: 'none',
        repeat: -1
      })
    }

    ScrollTrigger.batch('.product-card', {
      onEnter: batch => {
        gsap.fromTo(
          batch,
          { y: 80, opacity: 0, scale: 0.94 },
          {
            y: 0,
            opacity: 1,
            scale: 1,
            duration: 0.75,
            stagger: 0.1,
            ease: 'power3.out'
          }
        )

        gsap.fromTo(
          batch.map(el => el.querySelector('.card-wipe')),
          { scaleY: 1 },
          {
            scaleY: 0,
            duration: 0.65,
            stagger: 0.1,
            ease: 'power3.inOut',
            transformOrigin: 'top center'
          }
        )
      },
      start: 'top 88%',
      once: true
    })
  }, pageRef.value)
})

onUnmounted(() => {
  ctx?.revert()
  clearInterval(slideTimer)

  if (process.client) {
    ScrollTrigger.getAll().forEach(t => t.kill())
  }
})
</script>

<template>
  <div ref="pageRef" class="min-h-screen bg-slate-50 overflow-x-hidden">
    <section class="relative min-h-screen overflow-hidden bg-gradient-to-br from-slate-50 via-white to-purple-50 text-slate-900 flex items-center pt-20 pb-28">
      <div class="absolute inset-0 pointer-events-none">
        <div class="blob absolute -top-24 -left-24 w-80 h-80 rounded-full bg-purple-300 opacity-40 blur-[90px]"></div>
        <div class="blob absolute -bottom-24 right-[8%] w-96 h-96 rounded-full bg-pink-300 opacity-40 blur-[100px]"></div>
        <div class="blob absolute top-1/3 -right-20 w-80 h-80 rounded-full bg-blue-200 opacity-40 blur-[90px]"></div>
      </div>

      <div
        class="hero-bg absolute inset-0 pointer-events-none opacity-30"
        style="background: radial-gradient(ellipse at 60% 40%, #e9d5ff 0%, transparent 70%), radial-gradient(ellipse at 20% 80%, #fbcfe8 0%, transparent 60%);"
      ></div>

      <div class="relative z-10 w-full max-w-7xl mx-auto px-6 py-16 grid md:grid-cols-[1fr_500px] gap-12 items-center">
        <div>
          <p class="hero-subtitle inline-flex items-center gap-2 text-purple-600 font-semibold mb-4 tracking-widest uppercase text-sm">
            <span class="w-6 h-px bg-purple-500"></span>
            New Collection 2026
          </p>

          <h1 class="hero-title text-5xl md:text-7xl font-black leading-[1.05] tracking-tight">
            Shop Your<br />
            <span class="bg-gradient-to-r from-slate-900 via-purple-600 to-pink-500 bg-clip-text text-transparent">
              Perfect
            </span><br />
            Style
          </h1>

          <p class="hero-text mt-6 text-slate-600 text-lg max-w-md leading-relaxed">
            Discover premium fashion, accessories and beauty products with a clean, modern shopping experience.
          </p>

          <div class="flex flex-wrap gap-4 mt-8">
            <a
              href="/#products"
              class="hero-btn bg-slate-900 text-white px-8 py-3.5 rounded-full font-bold hover:bg-purple-700 hover:scale-105 active:scale-95 transition-all duration-200 shadow-xl shadow-purple-300/40"
            >
              Shop Now
            </a>
          </div>
        </div>

        <div class="showcase-panel relative overflow-hidden rounded-[2rem] bg-white border border-slate-200 shadow-2xl p-3">
          <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-white/90 to-transparent z-20 rounded-t-[2rem] pointer-events-none"></div>
          <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-white/90 to-transparent z-20 rounded-b-[2rem] pointer-events-none"></div>

          <div class="absolute top-5 right-5 z-30 flex gap-1.5">
            <button
              v-for="(_, i) in showcaseSlides"
              :key="i"
              @click="currentSlide = i; animateIn()"
              :class="[
                'w-2 h-2 rounded-full transition-all duration-300',
                i === currentSlide ? 'bg-slate-900 w-5' : 'bg-slate-300'
              ]"
            ></button>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div
              v-for="(item, idx) in currentShowcase"
              :key="item.name + idx"
              :class="[
                'showcase-card relative overflow-hidden rounded-2xl shadow-xl group cursor-pointer',
                idx === 0 ? 'col-span-2 h-56' : 'h-44'
              ]"
            >
              <img
                :src="item.image"
                :alt="item.name"
                class="w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-out"
              />

              <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/15 to-transparent"></div>

              <div class="absolute bottom-0 left-0 right-0 p-4">
                <p class="text-purple-200 text-xs font-bold uppercase tracking-wider mb-0.5">
                  {{ item.tag }}
                </p>

                <h3 class="text-white font-bold" :class="idx === 0 ? 'text-2xl' : 'text-lg'">
                  {{ item.name }}
                </h3>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="absolute bottom-0 left-0 right-0 h-24 overflow-hidden border-t border-slate-200 bg-white/80 backdrop-blur-md z-20">
        <div ref="stripRef" class="flex gap-3 w-max h-full py-3 px-3">
          <div
            v-for="(img, i) in [...stripImages, ...stripImages]"
            :key="i"
            class="relative h-full aspect-[4/3] flex-shrink-0 rounded-xl overflow-hidden group cursor-pointer"
          >
            <img :src="img" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" />
            <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition duration-300"></div>
          </div>
        </div>
      </div>
    </section>

    <section id="categories" class="bg-white py-20 px-6">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-14">
          <p class="text-purple-600 uppercase tracking-[0.3em] font-semibold mb-3">
            Shop By Collection
          </p>

          <h2 class="text-4xl md:text-5xl font-black text-slate-900">
            Explore Categories
          </h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          <NuxtLink
            v-for="category in categories"
            :key="category.title"
            :to="category.link"
            class="category-card group relative h-96 rounded-3xl overflow-hidden border border-slate-200 shadow-xl"
          >
            <img
              :src="category.image"
              :alt="category.title"
              class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700"
            />

            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>

            <div class="absolute bottom-6 left-6 right-6">
              <h3 class="text-3xl font-black text-white">
                {{ category.title }}
              </h3>

              <p class="text-slate-200 mt-2">
                {{ category.desc }}
              </p>
            </div>
          </NuxtLink>
        </div>
      </div>
    </section>

    <section id="products" class="relative bg-slate-50 py-20 px-6 md:px-10 overflow-hidden">
      <div class="max-w-7xl mx-auto">
        <div class="section-heading text-center mb-10">
          <p class="text-purple-600 font-bold text-xs uppercase tracking-[0.2em] mb-3">
            Handpicked For You
          </p>

          <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">
            Featured Products
          </h2>

          <input
            v-model="search"
            type="text"
            placeholder="Search product name, category, price..."
            class="mt-8 w-full max-w-xl bg-white border border-slate-300 rounded-full px-6 py-4 text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-purple-300"
          />
        </div>

        <div v-if="loading" class="py-20 text-center text-slate-500">
          Loading products...
        </div>

        <div v-else-if="filteredProducts.length === 0" class="py-20 text-center text-slate-400">
          <div class="text-7xl mb-4">😢</div>

          <p class="text-xl font-semibold mb-2">
            Nothing found for "{{ search }}"
          </p>

          <button @click="search = ''" class="text-purple-600 hover:underline text-sm">
            Clear search
          </button>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          <div
            v-for="p in filteredProducts"
            :key="p.id"
            class="product-card relative bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-md group transition-all duration-300 ease-out hover:-translate-y-3 hover:shadow-[0_30px_60px_rgba(15,23,42,0.16)]"
          >
            <div class="card-wipe absolute inset-0 bg-white z-10 origin-top pointer-events-none"></div>

            <NuxtLink :to="`/product/${p.id}`" class="block relative overflow-hidden h-64">
              <img
                :src="p.image"
                :alt="p.name"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
              />

              <div class="absolute top-4 left-4 bg-purple-600 text-white text-xs font-bold px-4 py-2 rounded-full capitalize">
                {{ p.category }}
              </div>

              <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>
            </NuxtLink>

            <div class="p-6">
              <NuxtLink :to="`/product/${p.id}`">
                <h3 class="font-bold text-xl text-slate-900 mb-1 group-hover:text-purple-700 transition-colors duration-200">
                  {{ p.name }}
                </h3>
              </NuxtLink>

              <p class="text-purple-700 font-black text-xl mb-5">
                ₹{{ Number(p.price).toLocaleString('en-IN') }}
              </p>

              <button
                @click="handleAddToCart(p)"
                :disabled="p.status === 'sold_out'"
                class="w-full bg-slate-900 text-white py-3 rounded-full font-semibold text-sm hover:bg-purple-700 active:scale-95 transition-all duration-200 disabled:opacity-50"
              >
                {{ p.status === 'sold_out' ? 'Sold Out' : 'Add to Cart' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 translate-y-8 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-4 scale-95"
    >
      <div
        v-if="showCartPopup"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] bg-white border border-slate-200 text-slate-900 px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-5 min-w-[300px]"
      >
        <img
          v-if="lastAddedProduct"
          :src="lastAddedProduct.image"
          :alt="lastAddedProduct.name"
          class="w-12 h-12 rounded-xl object-cover ring-2 ring-purple-300 flex-shrink-0"
        />

        <div class="flex-1 min-w-0">
          <p class="text-xs text-slate-500 mb-0.5">
            ✓ Added to cart
          </p>

          <p class="font-bold text-sm truncate">
            {{ lastAddedProduct?.name }}
          </p>
        </div>

        <NuxtLink
          to="/cart"
          class="bg-slate-900 text-white text-xs font-bold px-4 py-2.5 rounded-full hover:bg-purple-700 transition whitespace-nowrap flex-shrink-0"
        >
          Go to Cart →
        </NuxtLink>
      </div>
    </Transition>
  </div>
</template>