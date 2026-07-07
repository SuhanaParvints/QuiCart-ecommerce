<script setup>
import { ref, onMounted, computed } from 'vue'
import AdminSidebar from '~/components/admin/AdminSidebar.vue'

const orders = ref([])
const loading = ref(true)
const search = ref('')

const filteredOrders = computed(() => {
  if (!search.value.trim()) return orders.value

  const keyword = search.value.toLowerCase()

  return orders.value.filter(order =>
    String(order.id).includes(keyword) ||
    order.customer_name?.toLowerCase().includes(keyword) ||
    order.customer_email?.toLowerCase().includes(keyword) ||
    order.product_name?.toLowerCase().includes(keyword) ||
    order.status?.toLowerCase().includes(keyword)
  )
})

const fetchOrders = async () => {
  try {
    loading.value = true
    orders.value = await $fetch('http://127.0.0.1:8000/api/admin/orders')
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

const updateStatus = async (id, status) => {
  try {
    await $fetch(`http://127.0.0.1:8000/api/admin/orders/${id}/status`, {
      method: 'PUT',
      body: { status }
    })

    await fetchOrders()
  } catch (error) {
    console.error(error)
    alert('Status update failed')
  }
}

const deleteOrder = async (id) => {
  if (!confirm('Delete this order?')) return

  try {
    await $fetch(`http://127.0.0.1:8000/api/admin/orders/${id}`, {
      method: 'DELETE'
    })

    orders.value = orders.value.filter(order => order.id !== id)
  } catch (error) {
    console.error(error)
    alert('Delete failed')
  }
}

onMounted(() => {
  fetchOrders()
})
</script>

<template>
  <div class="flex min-h-screen bg-slate-100 text-slate-900">
    <AdminSidebar />

    <main class="flex-1 p-8 overflow-x-auto">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-4xl font-black text-slate-900">
            Orders
          </h1>

          <p class="text-slate-600 mt-2">
            Manage customer orders
          </p>
        </div>

        <div class="bg-white rounded-2xl px-5 py-4 shadow border">
          <p class="text-sm text-slate-500">
            Total Orders
          </p>

          <h2 class="text-3xl font-black text-purple-700">
            {{ orders.length }}
          </h2>
        </div>
      </div>

      <div class="bg-white rounded-3xl shadow border overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center">
          <h2 class="text-2xl font-bold text-slate-900">
            Order List
          </h2>

          <input
            v-model="search"
            placeholder="Search orders..."
            class="w-80 rounded-full border border-slate-300 px-5 py-3 text-slate-900 bg-white placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-purple-300"
          />
        </div>

        <div
          v-if="loading"
          class="p-10 text-center text-slate-600"
        >
          Loading orders...
        </div>

        <div
          v-else-if="filteredOrders.length === 0"
          class="p-10 text-center text-slate-600"
        >
          No orders found.
        </div>

        <div
          v-else
          class="overflow-x-auto"
        >
          <table class="w-full min-w-[1450px]">
            <thead class="bg-slate-900 text-white">
              <tr>
                <th class="p-5 text-left">
                  Order ID
                </th>

                <th class="p-5 text-left">
                  Customer
                </th>

                <th class="p-5 text-left">
                  Product
                </th>

                <th class="p-5 text-left">
                  Qty
                </th>

                <th class="p-5 text-left">
                  Total
                </th>

                <th class="p-5 text-left">
                  Address
                </th>

                <th class="p-5 text-left">
                  Payment
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
                v-for="order in filteredOrders"
                :key="order.id"
                class="border-t hover:bg-slate-50"
              >
                <td class="p-5 font-bold text-slate-900">
                  #{{ order.id }}
                </td>

                <td class="p-5">
                  <p class="font-semibold text-slate-900">
                    {{ order.customer_name || 'Customer' }}
                  </p>

                  <p class="text-sm text-slate-500">
                    {{ order.customer_email || '-' }}
                  </p>
                </td>

                <td class="p-5">
                  <div class="flex items-center gap-4">
                    <img
                      v-if="order.product_image"
                      :src="order.product_image"
                      :alt="order.product_name"
                      class="w-16 h-16 rounded-xl object-cover border border-slate-200 bg-slate-100"
                    />

                    <div
                      v-else
                      class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center text-xl border border-slate-200"
                    >
                      🛍️
                    </div>

                    <div>
                      <p class="font-semibold text-slate-900">
                        {{ order.product_name || 'Product' }}
                      </p>

                      <p class="text-xs text-slate-500">
                        Product Order
                      </p>
                    </div>
                  </div>
                </td>

                <td class="p-5 text-slate-700">
                  {{ order.quantity }}
                </td>

                <td class="p-5 font-bold text-slate-900">
                  ₹{{ order.total_price }}
                </td>

                <td class="p-5 max-w-[300px] text-slate-700">
                  {{ order.address }},
                  {{ order.location }},
                  {{ order.state }},
                  {{ order.pincode }}
                </td>

                <td class="p-5">
                  <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                    {{ order.payment_status || 'Pending' }}
                  </span>
                </td>

                <td class="p-5">
                  <select
                    :value="order.status"
                    @change="updateStatus(order.id, $event.target.value)"
                    class="border border-slate-300 rounded-xl px-4 py-2 text-slate-900 bg-white"
                  >
                    <option>Pending</option>
                    <option>Processing</option>
                    <option>Shipped</option>
                    <option>Delivered</option>
                    <option>Cancelled</option>
                  </select>
                </td>

                <td class="p-5">
                  <button
                    @click="deleteOrder(order.id)"
                    class="bg-red-500 text-white px-4 py-2 rounded-xl hover:bg-red-600"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</template>