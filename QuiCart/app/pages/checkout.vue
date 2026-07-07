<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useCartStore } from '~/stores/cart'
import { useAuthStore } from '~/stores/auth'

const cart = useCartStore()
const auth = useAuthStore()

const paymentMethod = ref('cod')
const orderSuccess = ref(false)
const loading = ref(false)
const error = ref('')

const form = reactive({
  name: '',
  phone: '',
  address: '',
  location: '',
  state: '',
  pincode: ''
})

const getProductImage = (item) => {
  return item.image || item.product_image || item.image_url || item.thumbnail || ''
}

onMounted(async () => {
  if (cart.loadCart) {
    cart.loadCart()
  }

  if (!auth.token) {
    navigateTo('/login')
    return
  }

  await auth.fetchUser()

  form.name = auth.user?.name || ''
  form.phone = auth.user?.phone || ''
  form.address = auth.user?.address || ''
  form.location = auth.user?.location || ''
  form.state = auth.user?.state || ''
  form.pincode = auth.user?.pincode || ''
})

const placeOrder = async () => {
  if (!auth.token) {
    navigateTo('/login')
    return
  }

  if (!cart.items || cart.items.length === 0) {
    alert('Your cart is empty')
    return
  }

  if (
    !form.name ||
    !form.phone ||
    !form.address ||
    !form.location ||
    !form.state ||
    !form.pincode
  ) {
    alert('Please fill all delivery details')
    return
  }

  try {
    loading.value = true
    error.value = ''

    for (const item of cart.items) {
      await $fetch('http://127.0.0.1:8000/api/orders', {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${auth.token}`,
          Accept: 'application/json'
        },
        body: {
          product_name: item.name,
          product_image: getProductImage(item),
          quantity: item.quantity,
          total_price: item.price * item.quantity,
          address: form.address,
          location: form.location,
          state: form.state,
          pincode: form.pincode,
          payment_method: paymentMethod.value
        }
      })
    }

    orderSuccess.value = true
    cart.clearCart()
  } catch (e) {
    console.error('Order error:', e)

    error.value =
      e?.data?.message ||
      e?.statusMessage ||
      'Order failed. Check Laravel server.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-100 text-slate-900 py-16 px-6">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-5xl font-black text-center mb-12">
        Checkout
      </h1>

      <div
        v-if="orderSuccess"
        class="bg-white border border-slate-200 rounded-3xl p-10 text-center shadow"
      >
        <div class="text-7xl mb-5">
          ✅
        </div>

        <h2 class="text-3xl font-bold mb-3 text-slate-900">
          Order Placed Successfully
        </h2>

        <p class="text-slate-600 mb-8">
          Thank you for shopping with QuiCart. Your order is now visible in Admin Orders and My Orders.
        </p>

        <div class="flex flex-wrap justify-center gap-4">
          <NuxtLink
            to="/account"
            class="bg-slate-900 text-white px-6 py-3 rounded-full font-semibold hover:bg-purple-700 transition"
          >
            View My Orders
          </NuxtLink>

          <NuxtLink
            to="/"
            class="bg-purple-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-purple-700 transition"
          >
            Continue Shopping
          </NuxtLink>
        </div>
      </div>

      <div
        v-else
        class="grid lg:grid-cols-3 gap-8"
      >
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-8 shadow">
          <h2 class="text-2xl font-bold mb-6 text-slate-900">
            Delivery Address
          </h2>

          <div class="grid md:grid-cols-2 gap-5">
            <input
              v-model="form.name"
              type="text"
              placeholder="Full Name"
              class="w-full bg-white text-slate-900 placeholder:text-slate-500 border border-slate-300 rounded-full px-5 py-3 outline-none focus:ring-2 focus:ring-purple-500"
            />

            <input
              v-model="form.phone"
              type="text"
              placeholder="Phone Number"
              class="w-full bg-white text-slate-900 placeholder:text-slate-500 border border-slate-300 rounded-full px-5 py-3 outline-none focus:ring-2 focus:ring-purple-500"
            />

            <input
              v-model="form.location"
              type="text"
              placeholder="Location / City"
              class="w-full bg-white text-slate-900 placeholder:text-slate-500 border border-slate-300 rounded-full px-5 py-3 outline-none focus:ring-2 focus:ring-purple-500"
            />

            <input
              v-model="form.state"
              type="text"
              placeholder="State"
              class="w-full bg-white text-slate-900 placeholder:text-slate-500 border border-slate-300 rounded-full px-5 py-3 outline-none focus:ring-2 focus:ring-purple-500"
            />

            <input
              v-model="form.pincode"
              type="text"
              placeholder="Pincode"
              class="w-full bg-white text-slate-900 placeholder:text-slate-500 border border-slate-300 rounded-full px-5 py-3 outline-none focus:ring-2 focus:ring-purple-500"
            />

            <textarea
              v-model="form.address"
              placeholder="Full Address"
              rows="4"
              class="md:col-span-2 w-full bg-white text-slate-900 placeholder:text-slate-500 border border-slate-300 rounded-3xl px-5 py-3 outline-none focus:ring-2 focus:ring-purple-500"
            ></textarea>
          </div>

          <h2 class="text-2xl font-bold mt-10 mb-6 text-slate-900">
            Payment Method
          </h2>

          <div class="space-y-4 text-slate-900">
            <label class="flex items-center gap-3 bg-slate-50 border border-slate-200 p-4 rounded-2xl cursor-pointer">
              <input v-model="paymentMethod" type="radio" value="cod" />
              Cash on Delivery
            </label>

            <label class="flex items-center gap-3 bg-slate-50 border border-slate-200 p-4 rounded-2xl cursor-pointer">
              <input v-model="paymentMethod" type="radio" value="upi" />
              UPI / Online Payment
            </label>

            <label class="flex items-center gap-3 bg-slate-50 border border-slate-200 p-4 rounded-2xl cursor-pointer">
              <input v-model="paymentMethod" type="radio" value="card" />
              Debit / Credit Card
            </label>
          </div>

          <p
            v-if="error"
            class="mt-6 text-red-600 font-semibold"
          >
            {{ error }}
          </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-3xl p-8 h-fit shadow">
          <h2 class="text-2xl font-bold mb-6 text-slate-900">
            Order Summary
          </h2>

          <div
            v-if="!cart.items || cart.items.length === 0"
            class="text-slate-600"
          >
            Your cart is empty.
          </div>

          <div
            v-for="item in cart.items"
            :key="item.id"
            class="flex items-center justify-between text-slate-700 border-b border-slate-200 py-3 gap-4"
          >
            <div class="flex items-center gap-3 min-w-0">
              <img
                v-if="getProductImage(item)"
                :src="getProductImage(item)"
                :alt="item.name"
                class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0"
              />

              <div
                v-else
                class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center shrink-0"
              >
                🛍️
              </div>

              <span class="truncate">
                {{ item.name }} × {{ item.quantity }}
              </span>
            </div>

            <span class="font-semibold whitespace-nowrap">
              ₹{{ item.price * item.quantity }}
            </span>
          </div>

          <div class="flex justify-between mt-6 text-xl font-bold text-slate-900">
            <span>Total</span>
            <span>₹{{ cart.totalPrice }}</span>
          </div>

          <button
            @click="placeOrder"
            :disabled="loading || !cart.items || cart.items.length === 0"
            class="mt-8 w-full bg-green-600 text-white py-3 rounded-full font-bold hover:bg-green-700 transition disabled:opacity-60"
          >
            {{ loading ? 'Placing Order...' : 'Place Order' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>