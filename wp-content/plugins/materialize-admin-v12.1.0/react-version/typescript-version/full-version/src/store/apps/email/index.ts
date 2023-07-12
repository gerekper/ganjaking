// ** Redux Imports
import { createSlice, createAsyncThunk } from '@reduxjs/toolkit'

// ** Axios Imports
import axios from 'axios'

// ** Types
import { Dispatch } from 'redux'
import {
  MailType,
  UpdateMailLabelType,
  FetchMailParamsType,
  UpdateMailParamsType,
  PaginateMailParamsType
} from 'src/types/apps/emailTypes'

interface ReduxType {
  getState: any
  dispatch: Dispatch<any>
}

// ** Fetch Mails
export const fetchMails = createAsyncThunk('appEmail/fetchMails', async (params: FetchMailParamsType) => {
  const response = await axios.get('/apps/email/emails', {
    params
  })

  return { ...response.data, filter: params }
})

// ** Get Current Mail
export const getCurrentMail = createAsyncThunk('appEmail/selectMail', async (id: number | string) => {
  const response = await axios.get('/apps/email/get-email', {
    params: {
      id
    }
  })

  return response.data
})

// ** Update Mail
export const updateMail = createAsyncThunk(
  'appEmail/updateMail',
  async (params: UpdateMailParamsType, { dispatch, getState }: ReduxType) => {
    const response = await axios.post('/apps/email/update-emails', {
      data: { emailIds: params.emailIds, dataToUpdate: params.dataToUpdate }
    })

    await dispatch(fetchMails(getState().email.filter))
    if (Array.isArray(params.emailIds)) {
      await dispatch(getCurrentMail(params.emailIds[0]))
    }

    return response.data
  }
)

// ** Update Mail Label
export const updateMailLabel = createAsyncThunk(
  'appEmail/updateMailLabel',
  async (params: UpdateMailLabelType, { dispatch, getState }: ReduxType) => {
    const response = await axios.post('/apps/email/update-emails-label', {
      data: { emailIds: params.emailIds, label: params.label }
    })

    await dispatch(fetchMails(getState().email.filter))

    if (Array.isArray(params.emailIds)) {
      await dispatch(getCurrentMail(params.emailIds[0]))
    }

    return response.data
  }
)

// ** Prev/Next Mails
export const paginateMail = createAsyncThunk('appEmail/paginateMail', async (params: PaginateMailParamsType) => {
  const response = await axios.get('/apps/email/paginate-email', { params })

  return response.data
})

export const appEmailSlice = createSlice({
  name: 'appEmail',
  initialState: {
    mails: null,
    mailMeta: null,
    filter: {
      q: '',
      label: '',
      folder: 'inbox'
    },
    currentMail: null,
    selectedMails: []
  },
  reducers: {
    handleSelectMail: (state, action) => {
      const mails: any = state.selectedMails
      if (!mails.includes(action.payload)) {
        mails.push(action.payload)
      } else {
        mails.splice(mails.indexOf(action.payload), 1)
      }
      state.selectedMails = mails
    },
    handleSelectAllMail: (state, action) => {
      const selectAllMails: number[] = []
      if (action.payload && state.mails !== null) {
        selectAllMails.length = 0

        // @ts-ignore
        state.mails.forEach((mail: MailType) => selectAllMails.push(mail.id))
      } else {
        selectAllMails.length = 0
      }
      state.selectedMails = selectAllMails as any
    }
  },
  extraReducers: builder => {
    builder.addCase(fetchMails.fulfilled, (state, action) => {
      state.mails = action.payload.emails
      state.filter = action.payload.filter
      state.mailMeta = action.payload.emailsMeta
    })
    builder.addCase(getCurrentMail.fulfilled, (state, action) => {
      state.currentMail = action.payload
    })
    builder.addCase(paginateMail.fulfilled, (state, action) => {
      state.currentMail = action.payload
    })
  }
})

export const { handleSelectMail, handleSelectAllMail } = appEmailSlice.actions

export default appEmailSlice.reducer
