<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useCartStore } from '~/stores/cart'
import { useAuthStore } from '~/stores/auth'

const cart = useCartStore()
const auth = useAuthStore()

const API_URL = 'http://127.0.0.1:8000/api'
const STORAGE_URL = 'http://127.0.0.1:8000/storage'

const paymentMethod = ref('cod')
const orderSuccess = ref(false)
const loading = ref(false)
const error = ref('')
const successMessage = ref('')
const paymentReference = ref('')

const razorpayLoaded = ref(false)
const razorpayOpen = ref(false)
const razorpayPaymentCompleted = ref(false)

const form = reactive({
  name: '',
  phone: '',
  address: '',
  location: '',
  state: '',
  pincode: ''
})

const orderTotal = computed(() => {
  return Number(cart.totalPrice || 0)
})

const getProductImage = (item) => {
  const image =
    item?.image ||
    item?.product_image ||
    item?.image_url ||
    item?.thumbnail ||
    ''

  if (!image) return ''

  if (
    image.startsWith('http://') ||
    image.startsWith('https://') ||
    image.startsWith('blob:') ||
    image.startsWith('data:')
  ) {
    return image
  }

  return `${STORAGE_URL}/${image.replace(/^\/+/, '')}`
}

const normaliseCartItems = () => {
  return (cart.items || []).map((item) => {
    const quantity = Number(item.quantity || 1)
    const price = Number(item.price || 0)

    return {
      product_id: Number(item.id),
      product_name: item.name,
      product_image: getProductImage(item),
      quantity,
      price,
      total_price: price * quantity
    }
  })
}

const validateCheckout = () => {
  error.value = ''

  if (!auth.token) {
    navigateTo('/login')
    return false
  }

  if (!cart.items?.length) {
    error.value = 'Your cart is empty.'
    return false
  }

  if (!form.name.trim()) {
    error.value = 'Please enter your full name.'
    return false
  }

  if (!/^[0-9]{10}$/.test(form.phone.trim())) {
    error.value = 'Please enter a valid 10-digit phone number.'
    return false
  }

  if (!form.location.trim()) {
    error.value = 'Please enter your location or city.'
    return false
  }

  if (!form.state.trim()) {
    error.value = 'Please enter your state.'
    return false
  }

  if (!/^[0-9]{6}$/.test(form.pincode.trim())) {
    error.value = 'Please enter a valid 6-digit pincode.'
    return false
  }

  if (!form.address.trim()) {
    error.value = 'Please enter your complete delivery address.'
    return false
  }

  return true
}

const getCheckoutPayload = () => ({
  customer_name: form.name.trim(),
  customer_email: auth.user?.email || '',
  phone: form.phone.trim(),
  address: form.address.trim(),
  location: form.location.trim(),
  state: form.state.trim(),
  pincode: form.pincode.trim(),
  payment_method: paymentMethod.value,
  total_amount: orderTotal.value,
  items: normaliseCartItems()
})

const loadRazorpayScript = () => {
  return new Promise((resolve) => {
    if (!process.client) {
      resolve(false)
      return
    }

    if (window.Razorpay) {
      razorpayLoaded.value = true
      resolve(true)
      return
    }

    const scriptUrl =
      'https://checkout.razorpay.com/v1/checkout.js'

    const existingScript = document.querySelector(
      `script[src="${scriptUrl}"]`
    )

    if (existingScript) {
      existingScript.addEventListener(
        'load',
        () => {
          razorpayLoaded.value = true
          resolve(true)
        },
        { once: true }
      )

      existingScript.addEventListener(
        'error',
        () => resolve(false),
        { once: true }
      )

      return
    }

    const script = document.createElement('script')

    script.src = scriptUrl
    script.async = true

    script.onload = () => {
      razorpayLoaded.value = true
      resolve(true)
    }

    script.onerror = () => {
      razorpayLoaded.value = false
      resolve(false)
    }

    document.body.appendChild(script)
  })
}

const completeOrder = ({
  message = 'Order placed successfully.',
  reference = ''
} = {}) => {
  orderSuccess.value = true
  successMessage.value = message
  paymentReference.value = reference
  error.value = ''

  if (cart.clearCart) {
    cart.clearCart()
  }
}

const placeCashOnDeliveryOrder = async () => {
  const payload = getCheckoutPayload()

  for (const item of payload.items) {
    await $fetch(`${API_URL}/orders`, {
      method: 'POST',

      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json'
      },

      body: {
        product_id: item.product_id,
        product_name: item.product_name,
        product_image: item.product_image,
        quantity: item.quantity,
        total_price: item.total_price,

        customer_name: payload.customer_name,
        customer_email: payload.customer_email,
        phone: payload.phone,
        address: payload.address,
        location: payload.location,
        state: payload.state,
        pincode: payload.pincode,

        payment_method: 'cod',
        payment_status: 'Cash On Delivery'
      }
    })
  }

  completeOrder({
    message:
      'Your Cash on Delivery order has been placed successfully.'
  })
}

const verifyRazorpayPayment = async (response) => {
  return await $fetch(`${API_URL}/payment/verify`, {
    method: 'POST',

    headers: {
      Authorization: `Bearer ${auth.token}`,
      Accept: 'application/json',
      'Content-Type': 'application/json'
    },

    body: {
      razorpay_payment_id:
        response.razorpay_payment_id,

      razorpay_order_id:
        response.razorpay_order_id,

      razorpay_signature:
        response.razorpay_signature
    }
  })
}

const payWithRazorpay = async () => {
  const loaded = await loadRazorpayScript()

  if (!loaded || !window.Razorpay) {
    throw new Error(
      'Razorpay could not be loaded. Check your internet connection.'
    )
  }

  const payload = getCheckoutPayload()

  const razorpayOrder = await $fetch(
    `${API_URL}/payment/create-order`,
    {
      method: 'POST',

      headers: {
        Authorization: `Bearer ${auth.token}`,
        Accept: 'application/json',
        'Content-Type': 'application/json'
      },

      body: {
        items: payload.items,
        customer_name: payload.customer_name,
        customer_email: payload.customer_email,
        phone: payload.phone,
        address: payload.address,
        location: payload.location,
        state: payload.state,
        pincode: payload.pincode
      }
    }
  )

  const razorpayOrderId =
    razorpayOrder.order_id ||
    razorpayOrder.razorpay_order_id ||
    razorpayOrder.id

  const razorpayKey =
    razorpayOrder.key ||
    razorpayOrder.key_id ||
    razorpayOrder.razorpay_key

  if (!razorpayOrderId) {
    throw new Error(
      'Laravel did not return a Razorpay order ID.'
    )
  }

  if (!razorpayKey) {
    throw new Error(
      'Laravel did not return the Razorpay Key ID.'
    )
  }

  razorpayPaymentCompleted.value = false
  razorpayOpen.value = true

  const options = {
    key: razorpayKey,

    amount: Number(razorpayOrder.amount),

    currency:
      razorpayOrder.currency || 'INR',

    name: 'QuiCart',

    description:
      'Secure payment for your QuiCart order',

    order_id:
      razorpayOrderId,

    /*
     * Important:
     * Payment verification is performed here using POST.
     *
     * Do not add:
     * callback_url
     * redirect: true
     */
    handler: async (response) => {
      try {
        loading.value = true
        error.value = ''

        const verification =
          await verifyRazorpayPayment(response)

        if (
          verification?.success === false ||
          verification?.verified === false
        ) {
          throw new Error(
            verification?.message ||
              'Payment verification failed.'
          )
        }

        razorpayPaymentCompleted.value = true
        razorpayOpen.value = false

        completeOrder({
          message:
            verification?.message ||
            'Payment verified and order placed successfully.',

          reference:
            response.razorpay_payment_id
        })
      } catch (verificationError) {
        console.error(
          'Razorpay verification failed:',
          verificationError
        )

        error.value =
          verificationError?.data?.message ||
          verificationError?.message ||
          'Payment succeeded, but verification failed. Contact support before paying again.'
      } finally {
        loading.value = false
      }
    },

    prefill: {
      name: form.name.trim(),
      email: auth.user?.email || '',
      contact: form.phone.trim()
    },

    notes: {
      user_id: String(auth.user?.id || ''),
      source: 'QuiCart checkout'
    },

    theme: {
      color: '#7c3aed'
    },

    modal: {
      escape: true,
      confirm_close: true,

      ondismiss: () => {
        razorpayOpen.value = false
        loading.value = false

        if (!razorpayPaymentCompleted.value) {
          error.value =
            'Payment window was closed. No order was created and your cart was not cleared.'
        }
      }
    },

    retry: {
      enabled: true,
      max_count: 3
    }
  }

  const razorpay = new window.Razorpay(options)

  razorpay.on('payment.failed', (response) => {
    razorpayOpen.value = false
    loading.value = false

    const description =
      response?.error?.description ||
      response?.error?.reason ||
      'Payment could not be completed.'

    error.value = `Payment failed: ${description}`
  })

  loading.value = false
  razorpay.open()
}

const placeOrder = async () => {
  if (!validateCheckout()) return

  if (loading.value || razorpayOpen.value) return

  try {
    loading.value = true
    error.value = ''
    successMessage.value = ''
    paymentReference.value = ''

    if (paymentMethod.value === 'cod') {
      await placeCashOnDeliveryOrder()
      return
    }

    if (paymentMethod.value === 'razorpay') {
      await payWithRazorpay()
      return
    }

    error.value =
      'Please select a valid payment method.'
  } catch (checkoutError) {
    console.error(
      'Checkout failed:',
      checkoutError
    )

    error.value =
      checkoutError?.data?.message ||
      checkoutError?.data?.errors?.items?.[0] ||
      checkoutError?.data?.errors?.amount?.[0] ||
      checkoutError?.statusMessage ||
      checkoutError?.message ||
      'Checkout failed. Check the Laravel server and try again.'
  } finally {
    if (
      paymentMethod.value === 'cod' ||
      !razorpayOpen.value
    ) {
      loading.value = false
    }
  }
}

onMounted(async () => {
  if (cart.loadCart) {
    cart.loadCart()
  }

  if (!auth.token) {
    await navigateTo('/login')
    return
  }

  try {
    await auth.fetchUser()

    form.name =
      auth.user?.name || ''

    form.phone =
      auth.user?.phone || ''

    form.address =
      auth.user?.address || ''

    form.location =
      auth.user?.location || ''

    form.state =
      auth.user?.state || ''

    form.pincode =
      auth.user?.pincode || ''
  } catch (fetchUserError) {
    console.error(
      'Unable to load user:',
      fetchUserError
    )

    error.value =
      'Unable to load your account information. Please log in again.'
  }
})
</script>

<template>
  <div
    class="min-h-screen bg-slate-100 px-4 py-12 text-slate-900 sm:px-6 lg:py-16"
  >
    <div class="mx-auto max-w-6xl">
      <header class="mb-12 text-center">
        <p
          class="mb-3 text-sm font-bold uppercase tracking-[0.3em] text-purple-600"
        >
          Secure checkout
        </p>

        <h1 class="text-4xl font-black sm:text-5xl">
          Complete Your Order
        </h1>

        <p class="mx-auto mt-4 max-w-2xl text-slate-600">
          Enter your delivery address and select a payment
          method.
        </p>
      </header>

      <section
        v-if="orderSuccess"
        class="rounded-3xl border border-green-200 bg-white p-8 text-center shadow-xl sm:p-12"
      >
        <div
          class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-green-100 text-5xl text-green-700"
        >
          ✓
        </div>

        <h2 class="mt-6 text-3xl font-black">
          Order Placed Successfully
        </h2>

        <p class="mx-auto mt-3 max-w-xl text-slate-600">
          {{ successMessage }}
        </p>

        <div
          v-if="paymentReference"
          class="mx-auto mt-6 max-w-lg rounded-2xl bg-slate-50 p-4"
        >
          <p class="text-sm text-slate-500">
            Payment reference
          </p>

          <p class="mt-1 break-all font-bold">
            {{ paymentReference }}
          </p>
        </div>

        <div
          class="mt-8 flex flex-wrap justify-center gap-4"
        >
          <NuxtLink
            to="/account"
            class="rounded-full bg-slate-900 px-7 py-3 font-bold text-white transition hover:bg-purple-700"
          >
            View My Orders
          </NuxtLink>

          <NuxtLink
            to="/"
            class="rounded-full bg-purple-600 px-7 py-3 font-bold text-white transition hover:bg-purple-700"
          >
            Continue Shopping
          </NuxtLink>
        </div>
      </section>

      <div
        v-else
        class="grid gap-8 lg:grid-cols-3"
      >
        <div class="space-y-8 lg:col-span-2">
          <section
            class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg sm:p-8"
          >
            <div class="mb-7 flex items-center gap-4">
              <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-100 text-xl"
              >
                📍
              </div>

              <div>
                <h2 class="text-2xl font-black">
                  Delivery Address
                </h2>

                <p class="text-sm text-slate-500">
                  Enter the address where your order should
                  be delivered.
                </p>
              </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
              <div>
                <label
                  class="mb-2 block text-sm font-bold"
                >
                  Full name
                </label>

                <input
                  v-model="form.name"
                  type="text"
                  autocomplete="name"
                  placeholder="Enter full name"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-5 py-3 outline-none transition focus:border-purple-500 focus:ring-4 focus:ring-purple-100"
                />
              </div>

              <div>
                <label
                  class="mb-2 block text-sm font-bold"
                >
                  Phone number
                </label>

                <input
                  v-model="form.phone"
                  type="tel"
                  inputmode="numeric"
                  maxlength="10"
                  autocomplete="tel"
                  placeholder="10-digit mobile number"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-5 py-3 outline-none transition focus:border-purple-500 focus:ring-4 focus:ring-purple-100"
                />
              </div>

              <div>
                <label
                  class="mb-2 block text-sm font-bold"
                >
                  City / location
                </label>

                <input
                  v-model="form.location"
                  type="text"
                  autocomplete="address-level2"
                  placeholder="City or location"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-5 py-3 outline-none transition focus:border-purple-500 focus:ring-4 focus:ring-purple-100"
                />
              </div>

              <div>
                <label
                  class="mb-2 block text-sm font-bold"
                >
                  State
                </label>

                <input
                  v-model="form.state"
                  type="text"
                  autocomplete="address-level1"
                  placeholder="State"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-5 py-3 outline-none transition focus:border-purple-500 focus:ring-4 focus:ring-purple-100"
                />
              </div>

              <div>
                <label
                  class="mb-2 block text-sm font-bold"
                >
                  Pincode
                </label>

                <input
                  v-model="form.pincode"
                  type="text"
                  inputmode="numeric"
                  maxlength="6"
                  autocomplete="postal-code"
                  placeholder="6-digit pincode"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-5 py-3 outline-none transition focus:border-purple-500 focus:ring-4 focus:ring-purple-100"
                />
              </div>

              <div class="md:col-span-2">
                <label
                  class="mb-2 block text-sm font-bold"
                >
                  Complete address
                </label>

                <textarea
                  v-model="form.address"
                  rows="4"
                  autocomplete="street-address"
                  placeholder="House name, street, landmark and district"
                  class="w-full resize-none rounded-2xl border border-slate-300 bg-white px-5 py-3 outline-none transition focus:border-purple-500 focus:ring-4 focus:ring-purple-100"
                ></textarea>
              </div>
            </div>
          </section>

          <section
            class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg sm:p-8"
          >
            <div class="mb-7 flex items-center gap-4">
              <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-100 text-xl"
              >
                💳
              </div>

              <div>
                <h2 class="text-2xl font-black">
                  Payment Method
                </h2>

                <p class="text-sm text-slate-500">
                  Select how you would like to pay.
                </p>
              </div>
            </div>

            <div class="space-y-4">
              <label
                class="flex cursor-pointer items-start gap-4 rounded-2xl border p-5 transition"
                :class="
                  paymentMethod === 'cod'
                    ? 'border-purple-500 bg-purple-50 ring-2 ring-purple-100'
                    : 'border-slate-200 hover:border-purple-300'
                "
              >
                <input
                  v-model="paymentMethod"
                  type="radio"
                  value="cod"
                  class="mt-1 h-5 w-5 accent-purple-600"
                />

                <div>
                  <p class="font-black">
                    Cash on Delivery
                  </p>

                  <p class="mt-1 text-sm text-slate-500">
                    Pay when your order is delivered.
                  </p>
                </div>
              </label>

              <label
                class="flex cursor-pointer items-start gap-4 rounded-2xl border p-5 transition"
                :class="
                  paymentMethod === 'razorpay'
                    ? 'border-purple-500 bg-purple-50 ring-2 ring-purple-100'
                    : 'border-slate-200 hover:border-purple-300'
                "
              >
                <input
                  v-model="paymentMethod"
                  type="radio"
                  value="razorpay"
                  class="mt-1 h-5 w-5 accent-purple-600"
                />

                <div class="flex-1">
                  <div
                    class="flex flex-wrap items-center justify-between gap-2"
                  >
                    <p class="font-black">
                      Razorpay Secure Payment
                    </p>

                    <span
                      class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700"
                    >
                      Secure
                    </span>
                  </div>

                  <p class="mt-1 text-sm text-slate-500">
                    Pay using UPI, card, wallet or net
                    banking.
                  </p>
                </div>
              </label>
            </div>

            <div
              v-if="paymentMethod === 'razorpay'"
              class="mt-5 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800"
            >
              Razorpay Checkout will open after clicking
              Pay Securely. Laravel will verify the payment
              using a POST request before creating the order.
            </div>
          </section>

          <div
            v-if="error"
            class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700"
          >
            <p class="font-bold">
              Checkout error
            </p>

            <p class="mt-1 text-sm">
              {{ error }}
            </p>
          </div>
        </div>

        <aside
          class="h-fit rounded-3xl border border-slate-200 bg-white p-6 shadow-xl sm:p-8 lg:sticky lg:top-28"
        >
          <h2 class="text-2xl font-black">
            Order Summary
          </h2>

          <p class="mt-1 text-sm text-slate-500">
            {{ cart.totalItems || 0 }} item(s) in your cart
          </p>

          <div
            v-if="!cart.items?.length"
            class="py-12 text-center text-slate-500"
          >
            <div class="mb-3 text-5xl">
              🛒
            </div>

            <p>Your cart is empty.</p>
          </div>

          <div
            v-else
            class="mt-6 max-h-[430px] space-y-4 overflow-y-auto pr-1"
          >
            <div
              v-for="item in cart.items"
              :key="item.id"
              class="flex gap-4 border-b border-slate-100 pb-4"
            >
              <img
                v-if="getProductImage(item)"
                :src="getProductImage(item)"
                :alt="item.name"
                class="h-16 w-16 shrink-0 rounded-2xl border border-slate-200 object-cover"
              />

              <div
                v-else
                class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-2xl"
              >
                🛍️
              </div>

              <div class="min-w-0 flex-1">
                <p class="truncate font-bold">
                  {{ item.name }}
                </p>

                <p class="mt-1 text-sm text-slate-500">
                  ₹{{
                    Number(item.price).toLocaleString(
                      'en-IN'
                    )
                  }}
                  × {{ item.quantity }}
                </p>
              </div>

              <p class="whitespace-nowrap font-black">
                ₹{{
                  (
                    Number(item.price) *
                    Number(item.quantity)
                  ).toLocaleString('en-IN')
                }}
              </p>
            </div>
          </div>

          <div
            class="mt-6 space-y-3 border-t border-slate-200 pt-6"
          >
            <div
              class="flex justify-between text-slate-600"
            >
              <span>Subtotal</span>

              <span>
                ₹{{ orderTotal.toLocaleString('en-IN') }}
              </span>
            </div>

            <div
              class="flex justify-between text-slate-600"
            >
              <span>Delivery</span>

              <span class="font-bold text-green-600">
                Free
              </span>
            </div>

            <div
              class="flex justify-between border-t border-slate-200 pt-4 text-xl font-black"
            >
              <span>Total</span>

              <span class="text-purple-700">
                ₹{{ orderTotal.toLocaleString('en-IN') }}
              </span>
            </div>
          </div>

          <button
            type="button"
            :disabled="
              loading ||
              razorpayOpen ||
              !cart.items?.length
            "
            class="mt-8 w-full rounded-full bg-gradient-to-r from-purple-600 to-indigo-600 py-4 font-black text-white shadow-lg shadow-purple-200 transition hover:-translate-y-0.5 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50"
            @click="placeOrder"
          >
            <span v-if="loading">
              Processing...
            </span>

            <span v-else-if="razorpayOpen">
              Payment window open...
            </span>

            <span
              v-else-if="
                paymentMethod === 'razorpay'
              "
            >
              Pay ₹{{
                orderTotal.toLocaleString('en-IN')
              }}
              Securely
            </span>

            <span v-else>
              Place Cash on Delivery Order
            </span>
          </button>

          <p
            class="mt-4 text-center text-xs leading-5 text-slate-500"
          >
            By placing this order, you confirm that your
            delivery information is correct.
          </p>
        </aside>
      </div>
    </div>
  </div>
</template>