import { defineStore } from 'pinia'

export const useWishlistStore = defineStore('wishlist', {
  state: () => ({
    items: []
  }),

  getters: {
    totalItems: state => state.items.length
  },

  actions: {
    addToWishlist(product) {
      const exists = this.items.find(item => item.id === product.id)

      if (!exists) {
        this.items.push(product)
        this.saveWishlist()
      }
    },

    removeFromWishlist(id) {
      this.items = this.items.filter(item => item.id !== id)
      this.saveWishlist()
    },

    toggleWishlist(product) {
      const exists = this.items.find(item => item.id === product.id)

      if (exists) {
        this.removeFromWishlist(product.id)
      } else {
        this.addToWishlist(product)
      }
    },

    saveWishlist() {
      if (!process.client) return
      localStorage.setItem('quicart-wishlist', JSON.stringify(this.items))
    },

    loadWishlist() {
      if (!process.client) return

      const saved = localStorage.getItem('quicart-wishlist')

      if (saved) {
        this.items = JSON.parse(saved)
      }
    }
  }
})