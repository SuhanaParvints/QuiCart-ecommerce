import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: process.client
      ? JSON.parse(localStorage.getItem('quicart-user') || 'null')
      : null,

    token: process.client
      ? localStorage.getItem('quicart-token')
      : null
  }),

  getters: {
    isLoggedIn: state => !!state.user && !!state.token
  },

  actions: {
    saveAuth(data) {
      this.token = data.token
      this.user = data.user

      if (process.client) {
        localStorage.setItem(
          'quicart-token',
          data.token
        )

        localStorage.setItem(
          'quicart-user',
          JSON.stringify(data.user)
        )
      }
    },

    async register(form) {
      const data = await $fetch(
        'http://127.0.0.1:8000/api/register',
        {
          method: 'POST',
          headers: {
            Accept: 'application/json'
          },
          body: {
            name: form.name,
            email: form.email,
            password: form.password
          }
        }
      )

      this.saveAuth(data)

      return data
    },

    async login(form) {
      const data = await $fetch(
        'http://127.0.0.1:8000/api/login',
        {
          method: 'POST',
          headers: {
            Accept: 'application/json'
          },
          body: {
            email: form.email,
            password: form.password
          }
        }
      )

      this.saveAuth(data)

      return data
    },

    async fetchUser() {
      if (!this.token) return null

      try {
        const user = await $fetch(
          'http://127.0.0.1:8000/api/user',
          {
            headers: {
              Authorization: `Bearer ${this.token}`,
              Accept: 'application/json'
            }
          }
        )

        this.user = user

        if (process.client) {
          localStorage.setItem(
            'quicart-user',
            JSON.stringify(user)
          )
        }

        return user
      } catch (error) {
        console.error(error)
        return null
      }
    },

    async updateProfile(form) {
      const data = await $fetch(
        'http://127.0.0.1:8000/api/user/profile',
        {
          method: 'PUT',
          headers: {
            Authorization: `Bearer ${this.token}`,
            Accept: 'application/json'
          },
          body: form
        }
      )

      this.user = data.user

      if (process.client) {
        localStorage.setItem(
          'quicart-user',
          JSON.stringify(data.user)
        )
      }

      return data
    },

    async uploadAvatar(file) {
      const formData = new FormData()

      formData.append(
        'avatar',
        file
      )

      const data = await $fetch(
        'http://127.0.0.1:8000/api/user/avatar',
        {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${this.token}`,
            Accept: 'application/json'
          },
          body: formData
        }
      )

      this.user = data.user

      if (process.client) {
        localStorage.setItem(
          'quicart-user',
          JSON.stringify(data.user)
        )
      }

      return data
    },

    async deleteAvatar() {
      const data = await $fetch(
        'http://127.0.0.1:8000/api/user/avatar',
        {
          method: 'DELETE',
          headers: {
            Authorization: `Bearer ${this.token}`,
            Accept: 'application/json'
          }
        }
      )

      this.user = data.user

      if (process.client) {
        localStorage.setItem(
          'quicart-user',
          JSON.stringify(data.user)
        )
      }

      return data
    },

    async logout() {
      const savedToken = this.token

      this.user = null
      this.token = null

      if (process.client) {
        localStorage.removeItem(
          'quicart-token'
        )

        localStorage.removeItem(
          'quicart-user'
        )
      }

      if (savedToken) {
        try {
          await $fetch(
            'http://127.0.0.1:8000/api/logout',
            {
              method: 'POST',
              headers: {
                Authorization: `Bearer ${savedToken}`,
                Accept: 'application/json'
              }
            }
          )
        } catch (error) {
          console.error(error)
        }
      }

      navigateTo('/login')
    }
  }
})