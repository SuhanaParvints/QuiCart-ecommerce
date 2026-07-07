<script setup>
import { ref } from 'vue'
import { useAuthStore } from '~/stores/auth'

const auth = useAuthStore()

const form = ref({
  email: '',
  password: ''
})

const error = ref('')
const loading = ref(false)

const login = async () => {
  try {
    error.value = ''
    loading.value = true

    await auth.login(form.value)
    navigateTo('/account')
  } catch (e) {
    error.value = e?.data?.message || 'Invalid email or password.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <section class="min-h-screen flex items-center justify-center bg-slate-50 px-6">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8 border border-slate-200">
      <h1 class="text-4xl font-black mb-2 text-slate-900">
        Login
      </h1>

      <p class="text-slate-500 mb-8">
        Don’t have an account?
        <NuxtLink to="/signup" class="text-purple-600 font-semibold">
          Sign up
        </NuxtLink>
      </p>

      <form @submit.prevent="login" class="space-y-5">
        <input
          v-model="form.email"
          type="email"
          placeholder="Email address"
          required
          class="w-full rounded-full border border-slate-300 px-6 py-4 text-slate-900 placeholder:text-slate-400 bg-white outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
        />

        <input
          v-model="form.password"
          type="password"
          placeholder="Password"
          required
          class="w-full rounded-full border border-slate-300 px-6 py-4 text-slate-900 placeholder:text-slate-400 bg-white outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
        />

        <p v-if="error" class="text-red-500 text-sm">
          {{ error }}
        </p>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-slate-900 text-white py-4 rounded-full font-semibold hover:bg-purple-700 transition disabled:opacity-60"
        >
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>
    </div>
  </section>
</template>