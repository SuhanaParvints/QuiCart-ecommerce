<script setup>
import { useCartStore } from '~/stores/cart'

const cart = useCartStore()
</script>

<template>
  <div class="min-h-screen bg-[#0f172a] text-white py-10 px-6">
    <div class="max-w-6xl mx-auto bg-white/5 backdrop-blur-lg border border-white/10 rounded-3xl shadow-2xl p-8">
      <h1 class="text-4xl font-bold text-center mb-10">
        Your Cart
      </h1>

      <div v-if="cart.items.length === 0" class="text-center py-20">
        <p class="text-slate-400 text-xl mb-6">
          Your cart is empty
        </p>

        <NuxtLink
          to="/shop"
          class="inline-block bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition"
        >
          Continue Shopping
        </NuxtLink>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
          <div class="grid grid-cols-5 text-xs font-bold text-slate-400 uppercase border-b border-white/10 pb-3">
            <p class="col-span-2">Product</p>
            <p>Price</p>
            <p>Quantity</p>
            <p>Total</p>
          </div>

          <div
            v-for="item in cart.items"
            :key="item.id"
            class="grid grid-cols-5 items-center border-b border-white/10 py-6"
          >
            <div class="col-span-2 flex items-center gap-4">
              <img
                :src="item.image"
                :alt="item.name"
                class="w-20 h-20 object-cover rounded-xl shadow-lg"
              />

              <div>
                <h3 class="font-semibold text-white">
                  {{ item.name }}
                </h3>

                <p class="text-sm text-slate-400">
                  {{ item.category || 'Product' }}
                </p>
              </div>
            </div>

            <p class="text-slate-300">
              ₹{{ Number(item.price).toLocaleString('en-IN') }}
            </p>

            <div class="flex items-center gap-2">
              <button
                @click="cart.decreaseQuantity(item.id)"
                class="w-8 h-8 bg-white/10 border border-white/10 rounded-full hover:bg-blue-600 transition"
              >
                -
              </button>

              <span class="text-slate-200">
                {{ item.quantity }}
              </span>

              <button
                @click="cart.increaseQuantity(item.id)"
                class="w-8 h-8 bg-white/10 border border-white/10 rounded-full hover:bg-blue-600 transition"
              >
                +
              </button>
            </div>

            <div class="flex items-center justify-between">
              <p class="text-slate-300">
                ₹{{ Number(item.price * item.quantity).toLocaleString('en-IN') }}
              </p>

              <button
                @click="cart.removeFromCart(item.id)"
                class="text-slate-400 hover:text-red-400 text-2xl transition"
              >
                ×
              </button>
            </div>
          </div>

          <div class="mt-8">
            <NuxtLink
              to="/shop"
              class="inline-flex px-6 py-3 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition"
            >
              ← Continue Shopping
            </NuxtLink>
          </div>
        </div>

        <div class="bg-white/5 backdrop-blur-lg border border-white/10 p-6 rounded-3xl h-fit shadow-xl">
          <h2 class="font-bold text-xl mb-6">
            Order Summary
          </h2>

          <div class="flex justify-between mb-3 text-slate-400">
            <span>Subtotal</span>
            <span>₹{{ Number(cart.totalPrice).toLocaleString('en-IN') }}</span>
          </div>

          <div class="flex justify-between mb-3 text-slate-400">
            <span>Shipping</span>
            <span class="text-green-400">Free</span>
          </div>

          <p class="text-blue-400 text-sm mb-6 cursor-pointer hover:text-blue-300 transition">
            Add coupon code →
          </p>

          <div class="border-t border-white/10 pt-4 flex justify-between font-bold text-lg">
            <span>Total</span>
            <span>₹{{ Number(cart.totalPrice).toLocaleString('en-IN') }}</span>
          </div>

          <NuxtLink
            to="/checkout"
            class="block text-center mt-6 w-full bg-green-600 text-white py-3 rounded-full hover:bg-green-700 hover:scale-105 transition-all duration-300"
          >
            CHECKOUT
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>