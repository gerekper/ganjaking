// ** React Imports
import { Fragment, SyntheticEvent, useEffect, useState } from 'react'

// ** MUI Imports
import Alert from '@mui/material/Alert'
import Button from '@mui/material/Button'
import Snackbar from '@mui/material/Snackbar'

export interface SnackbarMessage {
  key: number
  message: string
}

const SnackbarConsecutive = () => {
  // ** States
  const [open, setOpen] = useState<boolean>(false)
  const [snackPack, setSnackPack] = useState<SnackbarMessage[]>([])
  const [messageInfo, setMessageInfo] = useState<SnackbarMessage | undefined>(undefined)

  useEffect(() => {
    if (snackPack.length && !messageInfo) {
      setOpen(true)
      setSnackPack(prev => prev.slice(1))
      setMessageInfo({ ...snackPack[0] })
    } else if (snackPack.length && messageInfo && open) {
      setOpen(false)
    }
  }, [snackPack, messageInfo, open])

  const handleClick = (message: string) => () => {
    setSnackPack(prev => [...prev, { message, key: new Date().getTime() }])
  }

  const handleClose = (event: Event | SyntheticEvent, reason?: string) => {
    if (reason === 'clickaway') {
      return
    }
    setOpen(false)
  }

  const handleExited = () => {
    setMessageInfo(undefined)
  }

  return (
    <Fragment>
      <div className='demo-space-x'>
        <Button variant='outlined' onClick={handleClick('success')}>
          Success Alert
        </Button>
        <Button variant='outlined' onClick={handleClick('error')}>
          Error Alert
        </Button>
      </div>
      <Snackbar
        open={open}
        onClose={handleClose}
        autoHideDuration={3000}
        TransitionProps={{ onExited: handleExited }}
        key={messageInfo ? messageInfo.key : undefined}
        message={messageInfo ? messageInfo.message : undefined}
      >
        <Alert
          elevation={3}
          variant='filled'
          onClose={handleClose}
          severity={messageInfo?.message === 'success' ? 'success' : 'error'}
        >
          This is {messageInfo?.message === 'success' ? 'a success' : 'an error'} message!
        </Alert>
      </Snackbar>
    </Fragment>
  )
}

export default SnackbarConsecutive
