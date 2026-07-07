<script setup>
import { ref, onMounted, computed } from 'vue'
import AdminSidebar from '~/components/admin/AdminSidebar.vue'

const users = ref([])
const loading = ref(true)
const search = ref('')
const errorMessage = ref('')

const filteredUsers = computed(() => {
  if (!search.value.trim()) return users.value

  const keyword = search.value.toLowerCase()

  return users.value.filter(user =>
    user.name?.toLowerCase().includes(keyword) ||
    user.email?.toLowerCase().includes(keyword) ||
    user.phone?.toLowerCase().includes(keyword) ||
    user.address?.toLowerCase().includes(keyword) ||
    user.location?.toLowerCase().includes(keyword) ||
    user.pincode?.toLowerCase().includes(keyword)
  )
})

const formatDate = (date) => {
  if (!date) return 'N/A'

  return new Date(date).toLocaleDateString('en-IN', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const fetchUsers = async () => {
  try {
    loading.value = true
    errorMessage.value = ''

    users.value = await $fetch('http://127.0.0.1:8000/api/admin/users')
  } catch (error) {
    console.error(error)
    errorMessage.value = error?.data?.message || 'Unable to load users from Laravel API.'
  } finally {
    loading.value = false
  }
}

const deleteUser = async (id) => {
  if (!confirm('Delete this user?')) return

  try {
    await $fetch(`http://127.0.0.1:8000/api/admin/users/${id}`, {
      method: 'DELETE'
    })

    users.value = users.value.filter(user => user.id !== id)
  } catch (error) {
    console.error(error)
    alert(error?.data?.message || 'Delete failed')
  }
}

onMounted(async () => {
  await fetchUsers()
})
</script>

<template>
  <div class="flex min-h-screen bg-slate-100 text-slate-900">
    <AdminSidebar />

    <main class="flex-1 p-8 overflow-x-auto">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5 mb-8">
        <div>
          <h1 class="text-4xl font-black text-slate-900">
            Users
          </h1>

          <p class="text-slate-600 mt-2">
            Manage registered users and customer profile details
          </p>
        </div>

        <div class="bg-white rounded-2xl px-5 py-4 shadow border border-slate-200">
          <p class="text-sm text-slate-500">
            Total Users
          </p>

          <h2 class="text-3xl font-black text-purple-700">
            {{ users.length }}
          </h2>
        </div>
      </div>

      <div
        v-if="errorMessage"
        class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl"
      >
        {{ errorMessage }}
      </div>

      <div class="bg-white rounded-3xl shadow-md border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <h2 class="text-2xl font-bold text-slate-900">
            User List
          </h2>

          <input
            v-model="search"
            type="text"
            placeholder="Search name, email, phone, location..."
            class="w-full md:w-96 rounded-full border border-slate-300 px-5 py-3 text-slate-900 placeholder:text-slate-400 bg-white outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
          />
        </div>

        <div v-if="loading" class="p-10 text-center text-slate-600">
          Loading users...
        </div>

        <div v-else-if="filteredUsers.length === 0" class="p-10 text-center text-slate-600">
          No users found.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full min-w-[1300px]">
            <thead class="bg-slate-900 text-white">
              <tr>
                <th class="text-left p-5 font-bold">Name</th>
                <th class="text-left p-5 font-bold">Email</th>
                <th class="text-left p-5 font-bold">Phone</th>
                <th class="text-left p-5 font-bold">Address</th>
                <th class="text-left p-5 font-bold">Location</th>
                <th class="text-left p-5 font-bold">Pincode</th>
                <th class="text-left p-5 font-bold">Joined Date</th>
                <th class="text-left p-5 font-bold">Action</th>
              </tr>
            </thead>

            <tbody class="bg-white">
              <tr
                v-for="user in filteredUsers"
                :key="user.id"
                class="border-t border-slate-200 hover:bg-slate-50 transition"
              >
                <td class="p-5">
                  <div class="flex items-center gap-3">
                    <img
                      v-if="user.avatar"
                      :src="`http://127.0.0.1:8000/storage/${user.avatar}`"
                      class="w-11 h-11 rounded-full object-cover"
                      alt="User avatar"
                    />

                    <div
                      v-else
                      class="w-11 h-11 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-black"
                    >
                      {{ user.name?.charAt(0)?.toUpperCase() || 'U' }}
                    </div>

                    <div>
                      <p class="text-slate-900 font-semibold">
                        {{ user.name || 'No Name' }}
                      </p>

                      <p class="text-slate-500 text-sm">
                        ID #{{ user.id }}
                      </p>
                    </div>
                  </div>
                </td>

                <td class="p-5 text-slate-700 font-medium">
                  {{ user.email || '-' }}
                </td>

                <td class="p-5 text-slate-700">
                  {{ user.phone || '-' }}
                </td>

                <td class="p-5 text-slate-700 max-w-[260px]">
                  {{ user.address || '-' }}
                </td>

                <td class="p-5 text-slate-700">
                  {{ user.location || '-' }}
                </td>

                <td class="p-5 text-slate-700">
                  {{ user.pincode || '-' }}
                </td>

                <td class="p-5 text-slate-700">
                  {{ formatDate(user.created_at) }}
                </td>

                <td class="p-5">
                  <button
                    @click="deleteUser(user.id)"
                    class="bg-red-500 text-white px-4 py-2 rounded-xl hover:bg-red-600 transition"
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