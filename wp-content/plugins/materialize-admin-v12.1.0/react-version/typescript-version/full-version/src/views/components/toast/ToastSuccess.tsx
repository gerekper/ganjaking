// ** MUI Imports
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Third Party Components
import toast from 'react-hot-toast'

const ToastSuccess = () => {
  return (
    <Box
      sx={{ display: 'flex', textAlign: 'center', alignItems: 'center', flexDirection: 'column', '& svg': { mb: 2 } }}
    >
      <Icon icon='mdi:check-circle-outline' fontSize='2rem' />
      <Typography sx={{ mb: 4, fontWeight: 600 }}>Success</Typography>
      <Typography sx={{ mb: 3 }}>Creates a notification with an animated checkmark.</Typography>
      <Button sx={{ mb: 8 }} color='success' variant='contained' onClick={() => toast.success('Successfully toasted!')}>
        Success
      </Button>
    </Box>
  )
}

export default ToastSuccess
