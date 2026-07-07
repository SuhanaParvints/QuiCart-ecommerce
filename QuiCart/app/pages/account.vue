<script setup>
import { onMounted, ref, reactive } from 'vue'
import { useAuthStore } from '~/stores/auth'

const auth = useAuthStore()

const loading = ref(true)
const saving = ref(false)
const activeTab = ref('profile')
const orders = ref([])

const form = reactive({
  name: '',
  email: '',
  phone: '',
  address: '',
  location: '',
  state: '',
  pincode: ''
})

const syncForm = () => {
  if (!auth.user) return

  form.name = auth.user.name || ''
  form.email = auth.user.email || ''
  form.phone = auth.user.phone || ''
  form.address = auth.user.address || ''
  form.location = auth.user.location || ''
  form.state = auth.user.state || ''
  form.pincode = auth.user.pincode || ''
}

const formatDate = (date) => {
  if (!date) return 'N/A'

  return new Date(date).toLocaleDateString('en-IN', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const fetchOrders = async () => {
  try {
    orders.value = await $fetch('http://127.0.0.1:8000/api/orders', {
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json'
      }
    })
  } catch (error) {
    console.error(error)
  }
}

const saveProfile = async () => {
  try {
    saving.value = true
    await auth.updateProfile(form)
    syncForm()
    alert('Profile updated successfully')
  } catch (error) {
    console.error(error)
    alert(error?.data?.message || 'Update failed')
  } finally {
    saving.value = false
  }
}

const uploadAvatar = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  try {
    await auth.uploadAvatar(file)
    event.target.value = ''
  } catch (error) {
    console.error(error)
    alert('Image upload failed')
  }
}

const removeAvatar = async () => {
  try {
    await auth.deleteAvatar()
  } catch (error) {
    console.error(error)
    alert('Image delete failed')
  }
}

const cancelOrder = async (id) => {
  try {
    await $fetch(`http://127.0.0.1:8000/api/orders/${id}/cancel`, {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json'
      }
    })

    await fetchOrders()
  } catch (error) {
    console.error(error)
    alert(error?.data?.message || 'Order cancel failed')
  }
}

onMounted(async () => {
  if (!auth.token) {
    navigateTo('/login')
    return
  }

  await auth.fetchUser()
  syncForm()
  await fetchOrders()

  loading.value = false
})
</script>

<template>
  <section class="min-h-screen bg-slate-100 text-slate-900">
    <div class="bg-white shadow-sm py-10 text-center">
      <h1 class="text-4xl font-black text-slate-900">
        My Account
      </h1>

      <p class="text-slate-600 mt-2">
        Manage your profile, orders and delivery address
      </p>
    </div>

    <div v-if="loading" class="text-center py-20 text-slate-700">
      Loading account...
    </div>

    <div v-else class="max-w-7xl mx-auto px-6 py-10 grid md:grid-cols-4 gap-8">
      <div class="space-y-3">
        <button
          @click="activeTab='profile'"
          class="w-full text-left px-5 py-4 rounded-xl font-semibold transition"
          :class="activeTab==='profile' ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-slate-900 shadow hover:bg-slate-50'"
        >
          Personal Information
        </button>

        <button
          @click="activeTab='orders'"
          class="w-full text-left px-5 py-4 rounded-xl font-semibold transition"
          :class="activeTab==='orders' ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-slate-900 shadow hover:bg-slate-50'"
        >
          My Orders
        </button>

        <button
          @click="activeTab='address'"
          class="w-full text-left px-5 py-4 rounded-xl font-semibold transition"
          :class="activeTab==='address' ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-slate-900 shadow hover:bg-slate-50'"
        >
          Manage Address
        </button>

        <button
          @click="auth.logout()"
          class="w-full text-left px-5 py-4 rounded-xl bg-white shadow text-red-500 font-semibold hover:bg-red-50 transition"
        >
          Logout
        </button>
      </div>

      <div class="md:col-span-3 text-slate-900">
        <div
          v-if="activeTab==='profile'"
          class="bg-white rounded-3xl p-8 shadow border border-slate-200"
        >
          <h2 class="text-2xl font-black text-slate-900 mb-6">
            Personal Information
          </h2>

          <div class="flex flex-col items-center mb-10">
            <img
              v-if="auth.user?.avatar"
              :src="`http://127.0.0.1:8000/storage/${auth.user.avatar}`"
              class="w-32 h-32 rounded-full object-cover border border-slate-200"
              alt="Profile image"
            />

            <div
              v-else
              class="w-32 h-32 rounded-full bg-purple-100 flex items-center justify-center text-5xl font-bold text-purple-600"
            >
              {{ auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
            </div>

            <div class="flex flex-col sm:flex-row gap-3 mt-5 items-center">
              <label class="cursor-pointer bg-slate-900 text-white px-5 py-3 rounded-xl hover:bg-purple-700 transition">
                Upload Image
                <input
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="uploadAvatar"
                />
              </label>

              <button
                @click="removeAvatar"
                class="bg-red-500 text-white px-5 py-3 rounded-xl hover:bg-red-600 transition"
              >
                Delete Image
              </button>
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block mb-2 font-semibold text-slate-900">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border border-slate-300 rounded-xl px-4 py-3 mt-2 bg-white text-slate-900 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
              />
            </div>

            <div>
              <label class="block mb-2 font-semibold text-slate-900">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full border border-slate-300 rounded-xl px-4 py-3 mt-2 bg-white text-slate-900 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
              />
            </div>

            <div>
              <label class="block mb-2 font-semibold text-slate-900">Phone</label>
              <input
                v-model="form.phone"
                type="text"
                placeholder="Enter phone number"
                class="w-full border border-slate-300 rounded-xl px-4 py-3 mt-2 bg-white text-slate-900 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
              />
            </div>
          </div>

          <button
            @click="saveProfile"
            :disabled="saving"
            class="mt-8 bg-green-600 text-white px-8 py-3 rounded-xl hover:bg-green-700 transition disabled:opacity-60"
          >
            {{ saving ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>

        <div
          v-if="activeTab==='address'"
          class="bg-white rounded-3xl p-8 shadow border border-slate-200"
        >
          <h2 class="text-2xl font-black text-slate-900 mb-6">
            Manage Address
          </h2>

          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block mb-2 font-semibold text-slate-900">Address</label>
              <input
                v-model="form.address"
                type="text"
                placeholder="House name, street, area"
                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
              />
            </div>

            <div>
              <label class="block mb-2 font-semibold text-slate-900">Location / City</label>
              <input
                v-model="form.location"
                type="text"
                placeholder="City or location"
                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
              />
            </div>

            <div>
              <label class="block mb-2 font-semibold text-slate-900">State</label>
              <input
                v-model="form.state"
                type="text"
                placeholder="State"
                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
              />
            </div>

            <div>
              <label class="block mb-2 font-semibold text-slate-900">Pincode</label>
              <input
                v-model="form.pincode"
                type="text"
                placeholder="Pincode"
                class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
              />
            </div>
          </div>

          <button
            @click="saveProfile"
            :disabled="saving"
            class="mt-8 bg-green-600 text-white px-8 py-3 rounded-xl hover:bg-green-700 transition disabled:opacity-60"
          >
            {{ saving ? 'Saving...' : 'Save Address' }}
          </button>
        </div>

        <div
          v-if="activeTab==='orders'"
          class="bg-white rounded-3xl p-8 shadow border border-slate-200"
        >
          <h2 class="text-2xl font-black text-slate-900 mb-6">
            My Orders
          </h2>

          <div v-if="orders.length === 0" class="text-slate-600">
            No orders found.
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="order in orders"
              :key="order.id"
              class="border border-slate-200 rounded-xl p-5 bg-white"
            >
              <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                  <h3 class="font-bold text-slate-900 text-lg">
                    Order #{{ order.id }}
                  </h3>

                  <p class="text-slate-900 font-medium mt-1">
                    {{ order.product_name }}
                  </p>

                  <p class="text-slate-600 text-sm mt-1">
                    Quantity: {{ order.quantity }}
                  </p>

                  <p class="text-slate-600 text-sm mt-1">
                    Ordered on {{ formatDate(order.created_at) }}
                  </p>

                  <p class="text-slate-900 font-bold mt-1">
                    ₹{{ order.total_price }}
                  </p>

                  <p class="text-slate-600 text-sm mt-2">
                    {{ order.address }}, {{ order.location }}, {{ order.state }} - {{ order.pincode }}
                  </p>
                </div>

                <span
                  class="font-semibold px-4 py-2 rounded-full text-sm"
                  :class="order.status === 'Delivered'
                    ? 'bg-green-100 text-green-700'
                    : order.status === 'Cancelled'
                      ? 'bg-red-100 text-red-700'
                      : order.status === 'Shipped'
                        ? 'bg-blue-100 text-blue-700'
                        : 'bg-yellow-100 text-yellow-700'"
                >
                  {{ order.status }}
                </span>
              </div>

              <button
                v-if="order.status === 'Pending' || order.status === 'Processing'"
                @click="cancelOrder(order.id)"
                class="mt-4 text-red-500 font-semibold hover:text-red-700"
              >
                Cancel Order
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>