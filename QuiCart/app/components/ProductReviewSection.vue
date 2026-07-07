<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useAuthStore } from '~/stores/auth'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const auth = useAuthStore()

const reviews = ref([])
const loading = ref(true)
const submitting = ref(false)
const message = ref('')
const error = ref('')
const imagePreview = ref('')

const form = reactive({
  rating: 5,
  experience: 'good',
  comment: '',
  image: null
})

const getImageUrl = (image) => {
  if (!image) return ''
  if (image.startsWith('http')) return image
  return `http://127.0.0.1:8000/storage/${image}`
}

const averageRating = computed(() => {
  if (!reviews.value.length) return 0

  const total = reviews.value.reduce((sum, review) => {
    return sum + Number(review.rating)
  }, 0)

  return (total / reviews.value.length).toFixed(1)
})

const fetchReviews = async () => {
  try {
    loading.value = true

    reviews.value = await $fetch(
      `http://127.0.0.1:8000/api/products/${props.product.id}/reviews`
    )
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const handleImageChange = (event) => {
  const file = event.target.files?.[0]

  if (!file) return

  form.image = file
  imagePreview.value = URL.createObjectURL(file)
}

const submitReview = async () => {
  if (!auth.token) {
    navigateTo('/login')
    return
  }

  if (!form.comment.trim()) {
    alert('Please write your review')
    return
  }

  try {
    submitting.value = true
    message.value = ''
    error.value = ''

    const formData = new FormData()
    formData.append('rating', form.rating)
    formData.append('experience', form.experience)
    formData.append('comment', form.comment)

    if (form.image) {
      formData.append('image', form.image)
    }

    const response = await $fetch(
      `http://127.0.0.1:8000/api/products/${props.product.id}/reviews`,
      {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${auth.token}`,
          Accept: 'application/json'
        },
        body: formData
      }
    )

    message.value = response.message || 'Review submitted successfully'

    form.rating = 5
    form.experience = 'good'
    form.comment = ''
    form.image = null
    imagePreview.value = ''

    await fetchReviews()
  } catch (e) {
    console.error(e)
    error.value = e?.data?.message || 'Review submission failed'
  } finally {
    submitting.value = false
  }
}

onMounted(fetchReviews)
</script>

<template>
  <section class="mt-16 bg-white rounded-[2rem] p-6 md:p-10 shadow-xl border border-slate-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5 mb-10">
      <div>
        <p class="text-purple-600 font-bold uppercase tracking-[0.25em] text-xs mb-2">
          Customer Reviews
        </p>

        <h2 class="text-3xl font-black text-slate-900">
          Ratings & Reviews
        </h2>

        <p class="text-slate-500 mt-2">
          {{ reviews.length }} approved reviews
        </p>
      </div>

      <div class="bg-purple-50 text-purple-700 px-6 py-4 rounded-2xl text-center">
        <p class="text-4xl font-black">
          ⭐ {{ averageRating || 0 }}
        </p>

        <p class="text-sm font-semibold mt-1">
          Average Rating
        </p>
      </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
      <div class="bg-slate-50 rounded-3xl p-6 border border-slate-200">
        <h3 class="text-2xl font-black text-slate-900 mb-6">
          Write a Review
        </h3>

        <div class="space-y-5">
          <div>
            <label class="font-semibold text-slate-900">
              Rating
            </label>

            <select
              v-model="form.rating"
              class="w-full mt-2 border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900"
            >
              <option :value="5">⭐⭐⭐⭐⭐ Excellent</option>
              <option :value="4">⭐⭐⭐⭐ Very Good</option>
              <option :value="3">⭐⭐⭐ Average</option>
              <option :value="2">⭐⭐ Poor</option>
              <option :value="1">⭐ Bad</option>
            </select>
          </div>

          <div>
            <label class="font-semibold text-slate-900">
              Experience
            </label>

            <div class="grid grid-cols-3 gap-3 mt-2">
              <button
                type="button"
                @click="form.experience = 'good'"
                class="py-3 rounded-xl font-bold border"
                :class="form.experience === 'good'
                  ? 'bg-green-600 text-white border-green-600'
                  : 'bg-white text-slate-700 border-slate-300'"
              >
                Good
              </button>

              <button
                type="button"
                @click="form.experience = 'average'"
                class="py-3 rounded-xl font-bold border"
                :class="form.experience === 'average'
                  ? 'bg-yellow-500 text-white border-yellow-500'
                  : 'bg-white text-slate-700 border-slate-300'"
              >
                Average
              </button>

              <button
                type="button"
                @click="form.experience = 'bad'"
                class="py-3 rounded-xl font-bold border"
                :class="form.experience === 'bad'
                  ? 'bg-red-600 text-white border-red-600'
                  : 'bg-white text-slate-700 border-slate-300'"
              >
                Bad
              </button>
            </div>
          </div>

          <div>
            <label class="font-semibold text-slate-900">
              Review
            </label>

            <textarea
              v-model="form.comment"
              rows="5"
              placeholder="Write your product experience..."
              class="w-full mt-2 border border-slate-300 rounded-2xl px-4 py-3 bg-white text-slate-900 placeholder:text-slate-400"
            ></textarea>
          </div>

          <div>
            <label class="font-semibold text-slate-900">
              Upload Real Product Image
            </label>

            <input
              type="file"
              accept="image/*"
              @change="handleImageChange"
              class="w-full mt-2 border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900"
            />

            <img
              v-if="imagePreview"
              :src="imagePreview"
              class="w-full h-56 object-cover rounded-2xl mt-4 border border-slate-200"
            />
          </div>

          <p
            v-if="message"
            class="bg-green-50 text-green-700 border border-green-200 rounded-xl px-4 py-3"
          >
            {{ message }}
          </p>

          <p
            v-if="error"
            class="bg-red-50 text-red-700 border border-red-200 rounded-xl px-4 py-3"
          >
            {{ error }}
          </p>

          <button
            @click="submitReview"
            :disabled="submitting"
            class="w-full bg-purple-600 text-white py-3 rounded-full font-bold hover:bg-purple-700 transition disabled:opacity-60"
          >
            {{ submitting ? 'Submitting...' : 'Submit Review' }}
          </button>
        </div>
      </div>

      <div>
        <h3 class="text-2xl font-black text-slate-900 mb-6">
          Customer Feedback
        </h3>

        <div v-if="loading" class="text-slate-500">
          Loading reviews...
        </div>

        <div v-else-if="reviews.length === 0" class="text-slate-500">
          No approved reviews yet.
        </div>

        <div v-else class="space-y-5">
          <div
            v-for="review in reviews"
            :key="review.id"
            class="border border-slate-200 rounded-3xl p-5"
          >
            <div class="flex justify-between gap-4">
              <div>
                <h4 class="font-black text-slate-900">
                  {{ review.customer_name }}
                </h4>

                <p class="text-yellow-600 font-semibold mt-1">
                  ⭐ {{ review.rating }}/5
                </p>
              </div>

              <span class="capitalize px-3 py-1 h-fit rounded-full text-sm font-bold"
                :class="review.experience === 'good'
                  ? 'bg-green-100 text-green-700'
                  : review.experience === 'average'
                    ? 'bg-yellow-100 text-yellow-700'
                    : 'bg-red-100 text-red-700'"
              >
                {{ review.experience }}
              </span>
            </div>

            <p class="text-slate-600 leading-7 mt-4">
              {{ review.comment }}
            </p>

            <img
              v-if="review.image"
              :src="getImageUrl(review.image)"
              class="w-full h-56 object-cover rounded-2xl mt-4 border border-slate-200"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>