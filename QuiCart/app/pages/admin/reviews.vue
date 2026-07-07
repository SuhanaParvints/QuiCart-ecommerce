<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminSidebar from '~/components/admin/AdminSidebar.vue'

const reviews = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('all')

const getImageUrl = (image) => {
  if (!image) return ''
  if (image.startsWith('http')) return image
  return `http://127.0.0.1:8000/storage/${image}`
}

const filteredReviews = computed(() => {
  let data = reviews.value

  if (statusFilter.value !== 'all') {
    data = data.filter(review => review.status === statusFilter.value)
  }

  if (search.value.trim()) {
    const keyword = search.value.toLowerCase()

    data = data.filter(review =>
      review.customer_name?.toLowerCase().includes(keyword) ||
      review.customer_email?.toLowerCase().includes(keyword) ||
      review.product_name?.toLowerCase().includes(keyword) ||
      review.comment?.toLowerCase().includes(keyword) ||
      review.experience?.toLowerCase().includes(keyword)
    )
  }

  return data
})

const statusClass = (status) => {
  if (status === 'approved') return 'bg-green-100 text-green-700'
  if (status === 'rejected') return 'bg-red-100 text-red-700'
  return 'bg-yellow-100 text-yellow-700'
}

const experienceClass = (experience) => {
  if (experience === 'good') return 'bg-green-100 text-green-700'
  if (experience === 'bad') return 'bg-red-100 text-red-700'
  return 'bg-yellow-100 text-yellow-700'
}

const fetchReviews = async () => {
  try {
    loading.value = true

    reviews.value = await $fetch('http://127.0.0.1:8000/api/admin/reviews')
  } catch (error) {
    console.error(error)
    alert('Failed to load reviews')
  } finally {
    loading.value = false
  }
}

const updateStatus = async (id, status) => {
  try {
    await $fetch(`http://127.0.0.1:8000/api/admin/reviews/${id}/status`, {
      method: 'PUT',
      body: { status }
    })

    await fetchReviews()
  } catch (error) {
    console.error(error)
    alert('Status update failed')
  }
}

const deleteReview = async (id) => {
  if (!confirm('Delete this review?')) return

  try {
    await $fetch(`http://127.0.0.1:8000/api/admin/reviews/${id}`, {
      method: 'DELETE'
    })

    reviews.value = reviews.value.filter(review => review.id !== id)
  } catch (error) {
    console.error(error)
    alert('Delete failed')
  }
}

onMounted(fetchReviews)
</script>

<template>
  <div class="flex min-h-screen bg-slate-100 text-slate-900">
    <AdminSidebar />

    <main class="flex-1 p-8 overflow-x-auto">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5 mb-8">
        <div>
          <h1 class="text-4xl font-black text-slate-900">
            Reviews
          </h1>

          <p class="text-slate-600 mt-2">
            Manage customer product reviews and experience feedback
          </p>
        </div>

        <div class="grid grid-cols-3 gap-4">
          <div class="bg-white rounded-2xl px-5 py-4 shadow border">
            <p class="text-sm text-slate-500">Total</p>
            <h2 class="text-3xl font-black text-purple-700">
              {{ reviews.length }}
            </h2>
          </div>

          <div class="bg-white rounded-2xl px-5 py-4 shadow border">
            <p class="text-sm text-slate-500">Pending</p>
            <h2 class="text-3xl font-black text-yellow-600">
              {{ reviews.filter(r => r.status === 'pending').length }}
            </h2>
          </div>

          <div class="bg-white rounded-2xl px-5 py-4 shadow border">
            <p class="text-sm text-slate-500">Approved</p>
            <h2 class="text-3xl font-black text-green-600">
              {{ reviews.filter(r => r.status === 'approved').length }}
            </h2>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-3xl shadow border overflow-hidden">
        <div class="p-6 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <h2 class="text-2xl font-bold text-slate-900">
            Review List
          </h2>

          <div class="flex flex-col md:flex-row gap-3">
            <select
              v-model="statusFilter"
              class="border border-slate-300 rounded-full px-5 py-3 bg-white text-slate-900"
            >
              <option value="all">All Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>

            <input
              v-model="search"
              type="text"
              placeholder="Search reviews..."
              class="w-full md:w-80 rounded-full border border-slate-300 px-5 py-3 text-slate-900 bg-white"
            />
          </div>
        </div>

        <div v-if="loading" class="p-10 text-center text-slate-600">
          Loading reviews...
        </div>

        <div v-else-if="filteredReviews.length === 0" class="p-10 text-center text-slate-600">
          No reviews found.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full min-w-[1500px]">
            <thead class="bg-slate-900 text-white">
              <tr>
                <th class="p-5 text-left">Customer</th>
                <th class="p-5 text-left">Product</th>
                <th class="p-5 text-left">Rating</th>
                <th class="p-5 text-left">Experience</th>
                <th class="p-5 text-left">Review</th>
                <th class="p-5 text-left">Image</th>
                <th class="p-5 text-left">Status</th>
                <th class="p-5 text-left">Action</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="review in filteredReviews"
                :key="review.id"
                class="border-t hover:bg-slate-50"
              >
                <td class="p-5">
                  <p class="font-bold text-slate-900">
                    {{ review.customer_name || 'Guest User' }}
                  </p>

                  <p class="text-sm text-slate-500">
                    {{ review.customer_email || '-' }}
                  </p>
                </td>

                <td class="p-5 font-semibold">
                  {{ review.product_name || '-' }}
                </td>

                <td class="p-5">
                  <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-bold">
                    ⭐ {{ review.rating }}/5
                  </span>
                </td>

                <td class="p-5">
                  <span
                    class="capitalize px-3 py-1 rounded-full text-sm font-bold"
                    :class="experienceClass(review.experience)"
                  >
                    {{ review.experience }}
                  </span>
                </td>

                <td class="p-5 max-w-[320px] text-slate-700">
                  {{ review.comment }}
                </td>

                <td class="p-5">
                  <img
                    v-if="review.image"
                    :src="getImageUrl(review.image)"
                    class="w-20 h-20 rounded-xl object-cover border"
                  />

                  <span v-else class="text-slate-400">
                    No image
                  </span>
                </td>

                <td class="p-5">
                  <span
                    class="capitalize px-3 py-1 rounded-full text-sm font-bold"
                    :class="statusClass(review.status)"
                  >
                    {{ review.status }}
                  </span>
                </td>

                <td class="p-5">
                  <div class="flex flex-wrap gap-2">
                    <button
                      @click="updateStatus(review.id, 'approved')"
                      class="bg-green-600 text-white px-4 py-2 rounded-xl hover:bg-green-700"
                    >
                      Approve
                    </button>

                    <button
                      @click="updateStatus(review.id, 'rejected')"
                      class="bg-yellow-500 text-white px-4 py-2 rounded-xl hover:bg-yellow-600"
                    >
                      Reject
                    </button>

                    <button
                      @click="deleteReview(review.id)"
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
      </div>
    </main>
  </div>
</template>