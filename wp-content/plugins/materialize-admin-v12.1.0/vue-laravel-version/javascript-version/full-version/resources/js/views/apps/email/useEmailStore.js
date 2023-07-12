import axios from '@axios'

export const useEmailStore = defineStore('email', {
  // ℹ️ arrow function recommended for full type inference
  state: () => ({
    emails: [],
    emailsMeta: {
      draft: 0,
      inbox: 0,
      spam: 0,
    },
  }),
  actions: {
    async fetchEmails(payload) {
      const response = await axios.get('/apps/email/emails', { params: payload })
      const { emails, emailsMeta } = response.data

      this.emails = emails
      this.emailsMeta = emailsMeta
    },
    async updateEmails(ids, data) {
      return axios.post('/apps/email/update-emails/', {
        ids,
        data,
      })
    },
    async updateEmailLabels(ids, label) {
      return axios.post('/apps/email/update-emails-label', {
        ids,
        label,
      })
    },
  },
})
