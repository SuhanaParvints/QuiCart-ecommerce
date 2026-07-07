<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import AdminSidebar from '~/components/admin/AdminSidebar.vue'

import {
  Bar,
  Pie,
  Doughnut
} from 'vue-chartjs'

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  ArcElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  ArcElement,
  CategoryScale,
  LinearScale
)

const loading = ref(true)
const lastUpdated = ref('')
let refreshTimer = null

const stats = ref({
  total_users: 0,
  total_orders: 0,
  completed_orders: 0,
  cancelled_orders: 0,
  shipped_orders: 0,
  pending_orders: 0,
  total_products: 0,
  in_stock_products: 0,
  limited_stock_products: 0,
  sold_out_products: 0,
  total_reviews: 0,
  approved_reviews: 0,
  pending_reviews: 0,
  rejected_reviews: 0,
  average_rating: 0,
  total_revenue: 0
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom'
    }
  }
}

const fetchDashboard = async () => {
  try {
    const response = await $fetch('http://127.0.0.1:8000/api/admin/dashboard', {
      cache: 'no-store'
    })

    stats.value = {
      ...stats.value,
      ...response
    }

    lastUpdated.value = new Date().toLocaleTimeString()
  } catch (error) {
    console.error('Dashboard API error:', error)
    alert('Dashboard API not connected. Keep Laravel running: php artisan serve')
  } finally {
    loading.value = false
  }
}
const orderChartData = computed(() => ({
  labels: ['Pending', 'Shipped', 'Delivered', 'Cancelled'],
  datasets: [
    {
      label: 'Orders',
      data: [
        stats.value.pending_orders || 0,
        stats.value.shipped_orders || 0,
        stats.value.completed_orders || 0,
        stats.value.cancelled_orders || 0
      ],
      backgroundColor: ['#facc15', '#38bdf8', '#22c55e', '#ef4444']
    }
  ]
}))

const productChartData = computed(() => ({
  labels: ['In Stock', 'Limited Stock', 'Sold Out'],
  datasets: [
    {
      label: 'Products',
      data: [
        stats.value.in_stock_products || 0,
        stats.value.limited_stock_products || 0,
        stats.value.sold_out_products || 0
      ],
      backgroundColor: ['#22c55e', '#facc15', '#ef4444']
    }
  ]
}))

const reviewChartData = computed(() => ({
  labels: ['Approved', 'Pending', 'Rejected'],
  datasets: [
    {
      label: 'Reviews',
      data: [
        stats.value.approved_reviews || 0,
        stats.value.pending_reviews || 0,
        stats.value.rejected_reviews || 0
      ],
      backgroundColor: ['#22c55e', '#facc15', '#ef4444']
    }
  ]
}))
onMounted(() => {
  fetchDashboard()

  refreshTimer = setInterval(() => {
    fetchDashboard()
  }, 5000)
})

onUnmounted(() => {
  if (refreshTimer) {
    clearInterval(refreshTimer)
  }
})
</script>

<template>
  <div class="flex min-h-screen bg-slate-100 text-slate-900">
    <AdminSidebar />

    <main class="flex-1 p-8 overflow-x-auto">
      <div class="mb-8">
        <h1 class="text-4xl font-black">
          Admin Dashboard
        </h1>

        <p class="text-slate-600 mt-2">
          Evaluate users, orders, products, reviews and ratings.
        </p>
        <p class="text-green-600 font-semibold mt-2">
  Live data • Last updated: {{ lastUpdated }}
</p>
      </div>

      <div v-if="loading" class="text-center py-20 text-slate-600">
        Loading dashboard...
      </div>

      <div v-else>
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
          <div class="bg-white p-6 rounded-3xl shadow border">
            <p class="text-slate-500">
              Total Users
            </p>

            <h2 class="text-4xl font-black text-blue-600 mt-2">
              {{ stats.total_users }}
            </h2>
          </div>

          <div class="bg-white p-6 rounded-3xl shadow border">
            <p class="text-slate-500">
              Total Orders
            </p>

            <h2 class="text-4xl font-black text-purple-600 mt-2">
              {{ stats.total_orders }}
            </h2>
          </div>

          <div class="bg-white p-6 rounded-3xl shadow border">
            <p class="text-slate-500">
              Total Products
            </p>

            <h2 class="text-4xl font-black text-green-600 mt-2">
              {{ stats.total_products }}
            </h2>
          </div>

          <div class="bg-white p-6 rounded-3xl shadow border">
            <p class="text-slate-500">
              Revenue
            </p>

            <h2 class="text-4xl font-black text-pink-600 mt-2">
              ₹{{ Number(stats.total_revenue || 0).toLocaleString('en-IN') }}
            </h2>
          </div>
        </div>

        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
          <div class="bg-white p-6 rounded-3xl shadow border">
            <p class="text-slate-500">
              Delivered Orders
            </p>

            <h2 class="text-3xl font-black text-green-600 mt-2">
              {{ stats.completed_orders }}
            </h2>
          </div>

          <div class="bg-white p-6 rounded-3xl shadow border">
            <p class="text-slate-500">
              Cancelled Orders
            </p>

            <h2 class="text-3xl font-black text-red-600 mt-2">
              {{ stats.cancelled_orders }}
            </h2>
          </div>

          <div class="bg-white p-6 rounded-3xl shadow border">
            <p class="text-slate-500">
              Total Reviews
            </p>

            <h2 class="text-3xl font-black text-yellow-600 mt-2">
              {{ stats.total_reviews }}
            </h2>
          </div>

          <div class="bg-white p-6 rounded-3xl shadow border">
            <p class="text-slate-500">
              Average Rating
            </p>

            <h2 class="text-3xl font-black text-orange-500 mt-2">
              ⭐ {{ stats.average_rating || 0 }}
            </h2>
          </div>
        </div>

        <div class="grid xl:grid-cols-3 gap-8">
          <div class="bg-white rounded-3xl p-6 shadow border h-[420px]">
            <h2 class="text-2xl font-black mb-6">
              Order Status
            </h2>

            <Bar :data="orderChartData" :options="chartOptions" />
          </div>

          <div class="bg-white rounded-3xl p-6 shadow border h-[420px]">
            <h2 class="text-2xl font-black mb-6">
              Product Availability
            </h2>

            <Doughnut :data="productChartData" :options="chartOptions" />
          </div>

          <div class="bg-white rounded-3xl p-6 shadow border h-[420px]">
            <h2 class="text-2xl font-black mb-6">
              Review Status
            </h2>

            <Pie :data="reviewChartData" :options="chartOptions" />
          </div>
        </div>
      </div>
    </main>
  </div>
</template>