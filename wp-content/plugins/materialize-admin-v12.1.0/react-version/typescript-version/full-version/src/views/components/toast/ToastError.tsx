// ** MUI Imports
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Third Party Components
import toast from 'react-hot-toast'

const ToastError = () => {
  return (
    <Box
      sx={{ display: 'flex', textAlign: 'center', alignItems: 'center', flexDirection: 'column', '& svg': { mb: 2 } }}
    >
      <Icon icon='mdi:close' fontSize='2rem' />
      <Typography sx={{ mb: 4, fontWeight: 600 }}>Error</Typography>
      <Typography sx={{ mb: 3 }}>Creates a notification with an animated error icon.</Typography>
      <Button sx={{ mb: 8 }} color='error' variant='contained' onClick={() => toast.error("This didn't work.")}>
        Error
      </Button>
    </Box>
  )
}

export default ToastError
