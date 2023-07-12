// ** MUI Imports
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Third Party Components
import toast from 'react-hot-toast'

const ToastPromise = () => {
  const handleClick = () => {
    const myPromise = new Promise((resolve, reject) => {
      setTimeout(() => {
        if (Math.random() < 0.5) {
          resolve('foo')
        } else {
          reject('fox')
        }
      }, 1000)
    })

    return toast.promise(myPromise, {
      loading: 'Loading',
      success: 'Got the data',
      error: 'Error when fetching'
    })
  }

  return (
    <Box
      sx={{ display: 'flex', textAlign: 'center', alignItems: 'center', flexDirection: 'column', '& svg': { mb: 2 } }}
    >
      <Icon icon='mdi:timer-sand' fontSize='2rem' />
      <Typography sx={{ mb: 4, fontWeight: 600 }}>Promise</Typography>
      <Typography sx={{ mb: 3 }}>Update automatically when promise resolves / fails.</Typography>
      <Button sx={{ mb: 8 }} variant='contained' onClick={handleClick}>
        Promise
      </Button>
    </Box>
  )
}

export default ToastPromise
