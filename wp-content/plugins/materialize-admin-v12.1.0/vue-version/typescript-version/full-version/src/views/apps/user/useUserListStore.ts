import type { AxiosResponse } from 'axios'
import { defineStore } from 'pinia'
import type { UserProperties } from '@/@fake-db/types'
import type { UserParams } from '@/views/apps/user/types'
import axios from '@axios'

export const useUserListStore = defineStore('UserListStore', {
  actions: {

    // 👉 Fetch users data
    fetchUsers(params: UserParams) { return axios.get('/apps/users/list', { params }) },

    // 👉 Add User
    addUser(userData: UserProperties) {
      return new Promise((resolve, reject) => {
        axios.post('/apps/users/user', {
          user: userData,
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    fetchUser(id: number) {
      return new Promise<AxiosResponse>((resolve, reject) => {
        axios.get(`/apps/users/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Delete User
    deleteUser(id: number) {
      return new Promise((resolve, reject) => {
        axios.delete(`/apps/users/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
