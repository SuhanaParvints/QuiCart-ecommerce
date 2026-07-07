<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import AdminSidebar from '~/components/admin/AdminSidebar.vue'

const products = ref([])
const loading = ref(true)
const saving = ref(false)
const editingId = ref(null)
const search = ref('')
const imagePreview = ref('')

const form = reactive({
  name: '',
  category: 'women',
  description: '',
  image: '',
  price: '',
  rating: 4.5,
  stock: 10
})

const categories = ['women', 'men', 'beauty', 'accessories', 'footwear']

const getImageUrl = (image) => {
  if (!image) return ''
  if (image.startsWith('http')) return image
  return `http://127.0.0.1:8000/storage/${image}`
}

const filteredProducts = computed(() => {
  if (!search.value.trim()) return products.value

  const keyword = search.value.toLowerCase()

  return products.value.filter(product =>
    product.name?.toLowerCase().includes(keyword) ||
    product.category?.toLowerCase().includes(keyword) ||
    product.status?.toLowerCase().includes(keyword)
  )
})

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

const fetchProducts = async () => {
  try {
    loading.value = true
    products.value = await $fetch('http://127.0.0.1:8000/api/admin/products')
  } catch (error) {
    console.error(error)
    alert('Failed to load products')
  } finally {
    loading.value = false
  }
}

const handleImageUpload = async (event) => {
  try {
    const file = event.target.files?.[0]
    if (!file) return

    const formData = new FormData()
    formData.append('image', file)

    const response = await $fetch(
      'http://127.0.0.1:8000/api/admin/products/upload-image',
      {
        method: 'POST',
        body: formData
      }
    )

    form.image = response.image
    imagePreview.value = response.image_url
  } catch (error) {
    console.error(error)
    alert('Image upload failed')
  }
}

const resetForm = () => {
  editingId.value = null
  imagePreview.value = ''

  form.name = ''
  form.category = 'women'
  form.description = ''
  form.image = ''
  form.price = ''
  form.rating = 4.5
  form.stock = 10
}

const saveProduct = async () => {
  if (!form.name || !form.price || !form.category) {
    alert('Please fill product name, category and price')
    return
  }

  try {
    saving.value = true

    const payload = {
      name: form.name,
      category: form.category,
      description: form.description,
      image: form.image,
      price: Number(form.price),
      rating: Number(form.rating),
      stock: Number(form.stock)
    }

    if (editingId.value) {
      await $fetch(`http://127.0.0.1:8000/api/admin/products/${editingId.value}`, {
        method: 'PUT',
        body: payload
      })
    } else {
      await $fetch('http://127.0.0.1:8000/api/admin/products', {
        method: 'POST',
        body: payload
      })
    }

    resetForm()
    await fetchProducts()
  } catch (error) {
    console.error(error)
    alert(error?.data?.message || 'Product save failed')
  } finally {
    saving.value = false
  }
}

const editProduct = (product) => {
  editingId.value = product.id

  form.name = product.name || ''
  form.category = product.category || 'women'
  form.description = product.description || ''
  form.image = product.image || ''
  form.price = product.price || ''
  form.rating = product.rating || 4.5
  form.stock = product.stock || 0

  imagePreview.value = getImageUrl(product.image)
}

const deleteProduct = async (id) => {
  if (!confirm('Delete this product?')) return

  try {
    await $fetch(`http://127.0.0.1:8000/api/admin/products/${id}`, {
      method: 'DELETE'
    })

    products.value = products.value.filter(product => product.id !== id)
  } catch (error) {
    console.error(error)
    alert('Delete failed')
  }
}

onMounted(fetchProducts)
</script>

<template>
  <div class="flex min-h-screen bg-slate-100 text-slate-900">
    <AdminSidebar />

    <main class="flex-1 p-8 overflow-x-auto">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">
        <div>
          <h1 class="text-4xl font-black text-slate-900">
            Products
          </h1>

          <p class="text-slate-600 mt-2">
            Add, edit and manage QuiCart products
          </p>
        </div>

        <div class="bg-white rounded-2xl px-5 py-4 shadow border border-slate-200">
          <p class="text-sm text-slate-500">
            Total Products
          </p>

          <h2 class="text-3xl font-black text-purple-700">
            {{ products.length }}
          </h2>
        </div>
      </div>

      <div class="grid xl:grid-cols-3 gap-8">
        <section class="xl:col-span-1 bg-white rounded-3xl shadow border border-slate-200 p-6 h-fit">
          <h2 class="text-2xl font-black text-slate-900 mb-6">
            {{ editingId ? 'Edit Product' : 'Add Product' }}
          </h2>

          <div class="space-y-5">
            <div>
              <label class="font-semibold text-slate-900">
                Product Image
              </label>

              <input
                type="file"
                accept="image/*"
                @change="handleImageUpload"
                class="w-full mt-2 border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900"
              />

              <img
                v-if="imagePreview || form.image"
                :src="imagePreview || getImageUrl(form.image)"
                class="w-full h-56 object-cover rounded-2xl mt-4 border border-slate-200 shadow"
                alt="Preview"
              />
            </div>

            <div>
              <label class="font-semibold text-slate-900">
                Product Name
              </label>

              <input
                v-model="form.name"
                type="text"
                placeholder="Oversized Cotton Shirt"
                class="w-full mt-2 border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-purple-300"
              />
            </div>

            <div>
              <label class="font-semibold text-slate-900">
                Category
              </label>

              <select
                v-model="form.category"
                class="w-full mt-2 border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900 outline-none focus:ring-2 focus:ring-purple-300"
              >
                <option
                  v-for="category in categories"
                  :key="category"
                  :value="category"
                >
                  {{ category }}
                </option>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="font-semibold text-slate-900">
                  Price
                </label>

                <input
                  v-model="form.price"
                  type="number"
                  placeholder="1299"
                  class="w-full mt-2 border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-purple-300"
                />
              </div>

              <div>
                <label class="font-semibold text-slate-900">
                  Rating
                </label>

                <input
                  v-model="form.rating"
                  type="number"
                  step="0.1"
                  min="0"
                  max="5"
                  placeholder="4.7"
                  class="w-full mt-2 border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-purple-300"
                />
              </div>
            </div>

            <div>
              <label class="font-semibold text-slate-900">
                Stock Quantity
              </label>

              <input
                v-model="form.stock"
                type="number"
                min="0"
                placeholder="25"
                class="w-full mt-2 border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-purple-300"
              />

              <p class="text-sm text-slate-500 mt-2">
                0 = Sold Out, 1-10 = Limited Stock, 11+ = In Stock
              </p>
            </div>

            <div>
              <label class="font-semibold text-slate-900">
                Description
              </label>

              <textarea
                v-model="form.description"
                rows="4"
                placeholder="Premium quality product from QuiCart..."
                class="w-full mt-2 border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-purple-300"
              ></textarea>
            </div>

            <div class="flex gap-3">
              <button
                @click="saveProduct"
                :disabled="saving"
                class="flex-1 bg-purple-600 text-white py-3 rounded-xl font-bold hover:bg-purple-700 transition disabled:opacity-60"
              >
                {{ saving ? 'Saving...' : editingId ? 'Update Product' : 'Add Product' }}
              </button>

              <button
                v-if="editingId"
                @click="resetForm"
                class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition"
              >
                Cancel
              </button>
            </div>
          </div>
        </section>

        <section class="xl:col-span-2 bg-white rounded-3xl shadow border border-slate-200 overflow-hidden">
          <div class="p-6 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-2xl font-black text-slate-900">
              Product List
            </h2>

            <input
              v-model="search"
              type="text"
              placeholder="Search product, category, status..."
              class="w-full md:w-96 border border-slate-300 rounded-full px-5 py-3 bg-white text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-purple-300"
            />
          </div>

          <div v-if="loading" class="p-10 text-center text-slate-600">
            Loading products...
          </div>

          <div v-else-if="filteredProducts.length === 0" class="p-10 text-center text-slate-600">
            No products found.
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
              <thead class="bg-slate-900 text-white">
                <tr>
                  <th class="p-5 text-left">
                    Product
                  </th>
                  <th class="p-5 text-left">
                    Category
                  </th>
                  <th class="p-5 text-left">
                    Price
                  </th>
                  <th class="p-5 text-left">
                    Stock
                  </th>
                  <th class="p-5 text-left">
                    Status
                  </th>
                  <th class="p-5 text-left">
                    Action
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="product in filteredProducts"
                  :key="product.id"
                  class="border-t border-slate-200 hover:bg-slate-50"
                >
                  <td class="p-5">
                    <div class="flex items-center gap-4">
                      <img
                        v-if="product.image"
                        :src="getImageUrl(product.image)"
                        :alt="product.name"
                        class="w-16 h-16 rounded-xl object-cover border border-slate-200"
                      />

                      <div
                        v-else
                        class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center"
                      >
                        🛍️
                      </div>

                      <div>
                        <p class="font-bold text-slate-900">
                          {{ product.name }}
                        </p>

                        <p class="text-sm text-slate-500">
                          ⭐ {{ product.rating }}
                        </p>
                      </div>
                    </div>
                  </td>

                  <td class="p-5 capitalize text-slate-700">
                    {{ product.category }}
                  </td>

                  <td class="p-5 font-bold text-slate-900">
                    ₹{{ Number(product.price).toLocaleString('en-IN') }}
                  </td>

                  <td class="p-5 text-slate-700">
                    {{ product.stock }}
                  </td>

                  <td class="p-5">
                    <span
                      class="px-3 py-1 rounded-full text-sm font-bold"
                      :class="statusClass(product.status)"
                    >
                      {{ statusText(product.status) }}
                    </span>
                  </td>

                  <td class="p-5">
                    <div class="flex gap-2">
                      <button
                        @click="editProduct(product)"
                        class="bg-blue-500 text-white px-4 py-2 rounded-xl hover:bg-blue-600"
                      >
                        Edit
                      </button>

                      <button
                        @click="deleteProduct(product.id)"
                        class="bg-red-500 text-white px-4 py-2 rounded-xl hover:bg-red-600"
                      >
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>