<template>
  <footer class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-black text-white border-t border-white/10">

    <!-- Background Glow -->
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute -top-32 -left-20 w-96 h-96 rounded-full bg-purple-600/20 blur-[140px]"></div>
      <div class="absolute bottom-0 right-0 w-[420px] h-[420px] rounded-full bg-blue-500/20 blur-[160px]"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 py-20">

      <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-12">

        <!-- Logo -->
        <div>

          <NuxtLink
            to="/"
            class="text-5xl font-black bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent"
          >
            QuiCart
          </NuxtLink>

          <p class="mt-6 text-slate-400 leading-8">
            Experience premium shopping with modern fashion,
            accessories, footwear and beauty products.
            Discover quality products with secure shopping.
          </p>

        </div>

        <!-- Shop -->

        <div>

          <h3 class="text-xl font-bold mb-6">
            Shop
          </h3>

          <ul class="space-y-4 text-slate-400">

            <li>
              <NuxtLink to="/women" class="hover:text-blue-400 transition">
                Women's Fashion
              </NuxtLink>
            </li>

            <li>
              <NuxtLink to="/men" class="hover:text-blue-400 transition">
                Men's Fashion
              </NuxtLink>
            </li>

            <li>
              <NuxtLink to="/accessories" class="hover:text-blue-400 transition">
                Accessories
              </NuxtLink>
            </li>

            <li>
              <NuxtLink to="/beauty" class="hover:text-blue-400 transition">
                Beauty
              </NuxtLink>
            </li>

            <li>
              <NuxtLink to="/footwear" class="hover:text-blue-400 transition">
                Footwear
              </NuxtLink>
            </li>

          </ul>

        </div>

        <!-- Customer -->

        <div>

          <h3 class="text-xl font-bold mb-6">
            Customer Service
          </h3>

          <ul class="space-y-4 text-slate-400">

            <li>
              <NuxtLink to="/" class="hover:text-blue-400 transition">
                Home
              </NuxtLink>
            </li>

            <li>
              <NuxtLink to="/about" class="hover:text-blue-400 transition">
                About Us
              </NuxtLink>
            </li>

            <li>
              <NuxtLink to="/cart" class="hover:text-blue-400 transition">
                Shopping Cart
              </NuxtLink>
            </li>

            <li>
              <NuxtLink to="/wishlist" class="hover:text-blue-400 transition">
                Wishlist
              </NuxtLink>
            </li>

            <li>
              <NuxtLink to="/account" class="hover:text-blue-400 transition">
                My Account
              </NuxtLink>
            </li>

          </ul>

        </div>

        <!-- Contact -->

        <div>

          <h3 class="text-xl font-bold mb-6">
            Contact Us
          </h3>

          <div class="space-y-4 text-slate-400">

            <p>
              📞 +91 98765 43210
            </p>

            <p>
              ✉️ support@quicart.com
            </p>

            <p>
              📍 Kerala, India
            </p>

          </div>

          <!-- Social -->

          <div class="flex gap-4 mt-8">

            <a
              href="https://facebook.com"
              target="_blank"
              class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-blue-600 hover:-translate-y-1 transition-all"
            >
              📘
            </a>

            <a
              href="https://instagram.com"
              target="_blank"
              class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-pink-600 hover:-translate-y-1 transition-all"
            >
              📷
            </a>

            <a
              href="https://x.com"
              target="_blank"
              class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-slate-700 hover:-translate-y-1 transition-all"
            >
              🐦
            </a>

            <a
              href="https://youtube.com"
              target="_blank"
              class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-red-600 hover:-translate-y-1 transition-all"
            >
              ▶️
            </a>

          </div>

        </div>

      </div>

      <!-- Bottom -->

      <div class="border-t border-white/10 mt-16 pt-8">

        <div class="flex flex-col md:flex-row items-center justify-between gap-6">

          <p class="text-slate-500 text-center md:text-left">
            © {{ new Date().getFullYear() }} QuiCart. All Rights Reserved.
          </p>

          <div class="flex items-center gap-3">

            <span class="text-slate-400">
              Secure Payments
            </span>

            <div class="flex gap-2 text-3xl">

              💳

              🏦

              📱

              💰

            </div>

          </div>

        </div>

      </div>

    </div>

  </footer>
</template>