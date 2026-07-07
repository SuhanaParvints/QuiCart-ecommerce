import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: []
  }),

  getters: {
    totalItems: (state) =>
      state.items.reduce((sum, item) => sum + item.quantity, 0),

    totalPrice: (state) =>
      state.items.reduce((sum, item) => sum + item.price * item.quantity, 0),

    formattedTotalPrice: (state) =>
      state.items
        .reduce((sum, item) => sum + item.price * item.quantity, 0)
        .toLocaleString('en-IN')
  },

  actions: {
    addToCart(product) {
      const existing = this.items.find(item => item.id === product.id)

      if (existing) {
        existing.quantity += 1
      } else {
        this.items.push({
          id: product.id,
          name: product.name,
          price: Number(product.price),
          image: product.image,
          quantity: 1
        })
      }

      this.saveCart()
    },

    increaseQuantity(id) {
      const item = this.items.find(item => item.id === id)

      if (item) {
        item.quantity += 1
        this.saveCart()
      }
    },

    decreaseQuantity(id) {
      const item = this.items.find(item => item.id === id)

      if (!item) return

      if (item.quantity > 1) {
        item.quantity -= 1
        this.saveCart()
      } else {
        this.removeFromCart(id)
      }
    },

    removeFromCart(id) {
      this.items = this.items.filter(item => item.id !== id)
      this.saveCart()
    },

    clearCart() {
      this.items = []
      this.saveCart()
    },

    saveCart() {
      if (!process.client) return

      localStorage.setItem('quicart-items', JSON.stringify(this.items))
    },

    loadCart() {
      if (!process.client) return

      const saved = localStorage.getItem('quicart-items')

      if (!saved) return

      try {
        const parsedItems = JSON.parse(saved)

        if (Array.isArray(parsedItems)) {
          this.items = parsedItems
        }
      } catch (error) {
        console.error('Failed to load cart:', error)
        this.items = []
      }
    }
  }
})