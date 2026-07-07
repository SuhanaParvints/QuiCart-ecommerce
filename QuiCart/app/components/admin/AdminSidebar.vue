<template>
  <aside class="w-72 min-h-screen bg-slate-900 text-white p-6">
    <NuxtLink to="/admin" class="text-3xl font-black block mb-10">
      <span class="text-purple-400">Qui</span>Cart
    </NuxtLink>

    <nav class="space-y-3">
      <NuxtLink to="/admin" class="block px-4 py-3 rounded-xl hover:bg-purple-600 hover:text-white transition">
  Dashboard
</NuxtLink>
      <NuxtLink to="/admin/users" class="admin-link">Users</NuxtLink>
      <NuxtLink to="/admin/products" class="admin-link">Products</NuxtLink>
      <NuxtLink to="/admin/orders" class="admin-link">Orders</NuxtLink>
      <NuxtLink to="/admin/reviews" class="admin-link">Reviews</NuxtLink>
     
    </nav>
  </aside>
</template>

<style scoped>
.admin-link {
  display: block;
  padding: 14px 18px;
  border-radius: 14px;
  color: #cbd5e1;
  transition: 0.3s;
}

.admin-link:hover,
.router-link-exact-active {
  background: #7c3aed;
  color: white;
}
</style>