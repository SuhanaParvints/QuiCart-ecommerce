<script setup>
import { computed, onMounted, ref } from 'vue'
import AdminSidebar from '~/components/admin/AdminSidebar.vue'

const payments = ref([])
const stats = ref({
  total_payments: 0,
  paid_payments: 0,
  pending_payments: 0,
  failed_payments: 0,
  refunded_payments: 0,
  total_revenue: 0,
  today_revenue: 0,
  monthly_revenue: 0
})

const loading = ref(true)
const error = ref('')
const search = ref('')
const statusFilter = ref('all')

const API_URL = 'http://127.0.0.1:8000/api'

const filteredPayments = computed(() => {
  let data = payments.value

  if (statusFilter.value !== 'all') {
    data = data.filter(
      payment => payment.status === statusFilter.value
    )
  }

  const keyword = search.value.trim().toLowerCase()

  if (keyword) {
    data = data.filter(payment => {
      const searchableText = [
        payment.user?.name,
        payment.user?.email,
        payment.razorpay_payment_id,
        payment.razorpay_order_id,
        payment.gateway,
        payment.amount,
        payment.status
      ]
        .filter(Boolean)
        .join(' ')
        .toLowerCase()

      return searchableText.includes(keyword)
    })
  }

  return data
})

const formatCurrency = value => {
  return `₹${Number(value || 0).toLocaleString('en-IN')}`
}

const formatDate = value => {
  if (!value) return '-'

  return new Date(value).toLocaleString('en-IN', {
    dateStyle: 'medium',
    timeStyle: 'short'
  })
}

const statusClass = status => {
  if (status === 'paid') {
    return 'bg-green-100 text-green-700'
  }

  if (status === 'failed') {
    return 'bg-red-100 text-red-700'
  }

  if (status === 'refunded') {
    return 'bg-blue-100 text-blue-700'
  }

  return 'bg-yellow-100 text-yellow-700'
}

const fetchPayments = async () => {
  payments.value = await $fetch(
    `${API_URL}/admin/payments`
  )
}

const fetchStats = async () => {
  stats.value = await $fetch(
    `${API_URL}/admin/payments/dashboard`
  )
}

const loadPage = async () => {
  try {
    loading.value = true
    error.value = ''

    await Promise.all([
      fetchPayments(),
      fetchStats()
    ])
  } catch (fetchError) {
    console.error('Payment page error:', fetchError)

    error.value =
      fetchError?.data?.message ||
      'Unable to load payment details.'
  } finally {
    loading.value = false
  }
}

onMounted(loadPage)
</script>

<template>
  <div class="flex min-h-screen bg-slate-100 text-slate-900">
    <AdminSidebar />

    <main class="flex-1 overflow-x-auto p-6 lg:p-8">
      <div
        class="mb-8 flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between"
      >
        <div>
          <p
            class="text-sm font-bold uppercase tracking-[0.25em] text-purple-600"
          >
            Transaction management
          </p>

          <h1 class="mt-2 text-4xl font-black">
            Payments
          </h1>

          <p class="mt-2 text-slate-600">
            View Razorpay transactions and revenue information.
          </p>
        </div>

        <button
          type="button"
          class="rounded-full bg-slate-900 px-6 py-3 font-bold text-white transition hover:bg-purple-700"
          @click="loadPage"
        >
          Refresh Payments
        </button>
      </div>

      <div
        v-if="error"
        class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700"
      >
        {{ error }}
      </div>

      <div
        class="mb-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4"
      >
        <div class="rounded-3xl border bg-white p-6 shadow">
          <p class="text-sm text-slate-500">
            Total Revenue
          </p>

          <h2 class="mt-2 text-3xl font-black text-green-600">
            {{ formatCurrency(stats.total_revenue) }}
          </h2>
        </div>

        <div class="rounded-3xl border bg-white p-6 shadow">
          <p class="text-sm text-slate-500">
            Today's Revenue
          </p>

          <h2 class="mt-2 text-3xl font-black text-purple-600">
            {{ formatCurrency(stats.today_revenue) }}
          </h2>
        </div>

        <div class="rounded-3xl border bg-white p-6 shadow">
          <p class="text-sm text-slate-500">
            Monthly Revenue
          </p>

          <h2 class="mt-2 text-3xl font-black text-blue-600">
            {{ formatCurrency(stats.monthly_revenue) }}
          </h2>
        </div>

        <div class="rounded-3xl border bg-white p-6 shadow">
          <p class="text-sm text-slate-500">
            Total Payments
          </p>

          <h2 class="mt-2 text-3xl font-black text-slate-900">
            {{ stats.total_payments }}
          </h2>
        </div>
      </div>

      <div
        class="mb-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4"
      >
        <div class="rounded-3xl border bg-white p-5 shadow-sm">
          <p class="text-sm text-slate-500">
            Successful
          </p>

          <p class="mt-2 text-2xl font-black text-green-600">
            {{ stats.paid_payments }}
          </p>
        </div>

        <div class="rounded-3xl border bg-white p-5 shadow-sm">
          <p class="text-sm text-slate-500">
            Pending
          </p>

          <p class="mt-2 text-2xl font-black text-yellow-600">
            {{ stats.pending_payments }}
          </p>
        </div>

        <div class="rounded-3xl border bg-white p-5 shadow-sm">
          <p class="text-sm text-slate-500">
            Failed
          </p>

          <p class="mt-2 text-2xl font-black text-red-600">
            {{ stats.failed_payments }}
          </p>
        </div>

        <div class="rounded-3xl border bg-white p-5 shadow-sm">
          <p class="text-sm text-slate-500">
            Refunded
          </p>

          <p class="mt-2 text-2xl font-black text-blue-600">
            {{ stats.refunded_payments }}
          </p>
        </div>
      </div>

      <section
        class="overflow-hidden rounded-3xl border bg-white shadow"
      >
        <div
          class="flex flex-col gap-4 border-b p-6 lg:flex-row lg:items-center lg:justify-between"
        >
          <div>
            <h2 class="text-2xl font-black">
              Payment Transactions
            </h2>

            <p class="mt-1 text-sm text-slate-500">
              {{ filteredPayments.length }} transaction(s)
            </p>
          </div>

          <div class="flex flex-col gap-3 sm:flex-row">
            <select
              v-model="statusFilter"
              class="rounded-full border border-slate-300 bg-white px-5 py-3 outline-none focus:ring-2 focus:ring-purple-300"
            >
              <option value="all">All statuses</option>
              <option value="paid">Paid</option>
              <option value="pending">Pending</option>
              <option value="failed">Failed</option>
              <option value="refunded">Refunded</option>
            </select>

            <input
              v-model="search"
              type="text"
              placeholder="Search payment ID, customer..."
              class="w-full rounded-full border border-slate-300 px-5 py-3 outline-none focus:ring-2 focus:ring-purple-300 sm:w-80"
            />
          </div>
        </div>

        <div
          v-if="loading"
          class="p-12 text-center text-slate-500"
        >
          Loading payments...
        </div>

        <div
          v-else-if="filteredPayments.length === 0"
          class="p-12 text-center text-slate-500"
        >
          No payment transactions found.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full min-w-[1300px]">
            <thead class="bg-slate-900 text-white">
              <tr>
                <th class="p-5 text-left">
                  Customer
                </th>

                <th class="p-5 text-left">
                  Payment ID
                </th>

                <th class="p-5 text-left">
                  Razorpay Order
                </th>

                <th class="p-5 text-left">
                  Amount
                </th>

                <th class="p-5 text-left">
                  Gateway
                </th>

                <th class="p-5 text-left">
                  Orders
                </th>

                <th class="p-5 text-left">
                  Status
                </th>

                <th class="p-5 text-left">
                  Date
                </th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="payment in filteredPayments"
                :key="payment.id"
                class="border-t transition hover:bg-slate-50"
              >
                <td class="p-5">
                  <p class="font-bold">
                    {{ payment.user?.name || 'Unknown User' }}
                  </p>

                  <p class="mt-1 text-sm text-slate-500">
                    {{ payment.user?.email || '-' }}
                  </p>
                </td>

                <td class="p-5">
                  <p class="max-w-[220px] break-all font-mono text-sm">
                    {{ payment.razorpay_payment_id }}
                  </p>
                </td>

                <td class="p-5">
                  <p class="max-w-[220px] break-all font-mono text-sm">
                    {{ payment.razorpay_order_id }}
                  </p>
                </td>

                <td class="p-5 text-lg font-black">
                  {{ formatCurrency(payment.amount) }}
                </td>

                <td class="p-5 capitalize">
                  {{ payment.gateway }}
                </td>

                <td class="p-5">
                  <span
                    v-for="orderId in payment.order_ids || []"
                    :key="orderId"
                    class="mr-2 inline-flex rounded-full bg-purple-100 px-3 py-1 text-xs font-bold text-purple-700"
                  >
                    #{{ orderId }}
                  </span>
                </td>

                <td class="p-5">
                  <span
                    class="inline-flex rounded-full px-3 py-1 text-sm font-bold capitalize"
                    :class="statusClass(payment.status)"
                  >
                    {{ payment.status }}
                  </span>
                </td>

                <td class="p-5 text-sm text-slate-600">
                  {{ formatDate(payment.created_at) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</template>