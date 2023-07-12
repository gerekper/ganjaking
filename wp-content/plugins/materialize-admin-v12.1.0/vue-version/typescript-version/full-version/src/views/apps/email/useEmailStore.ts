import type { PartialDeep } from 'type-fest'
import type { Email, FetchEmailsPayload } from '@/@fake-db/types'
import axios from '@axios'

interface State {
  emails: Email[]
  emailsMeta: {
    draft: number
    inbox: number
    spam: number
  }
}

export const useEmailStore = defineStore('email', {
  // ℹ️ arrow function recommended for full type inference
  state: (): State => ({
    emails: [],
    emailsMeta: {
      draft: 0,
      inbox: 0,
      spam: 0,
    },
  }),
  actions: {
    async fetchEmails(payload: FetchEmailsPayload) {
      const response = await axios.get('/apps/email/emails', { params: payload })

      const { emails, emailsMeta } = response.data

      this.emails = emails
      this.emailsMeta = emailsMeta
    },

    async updateEmails(ids: Email['id'][], data: PartialDeep<Email>) {
      return axios.post('/apps/email/update-emails/', {
        ids,
        data,
      })
    },

    async updateEmailLabels(ids: Email['id'][], label: Email['labels'][number]) {
      return axios.post('/apps/email/update-emails-label', {
        ids,
        label,
      })
    },
  },
})
