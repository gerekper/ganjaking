// ** MUI Imports
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Third Party Components
import toast from 'react-hot-toast'

const ToastEmoji = () => {
  return (
    <Box
      sx={{ display: 'flex', textAlign: 'center', alignItems: 'center', flexDirection: 'column', '& svg': { mb: 2 } }}
    >
      <Icon icon='mdi:sticker-emoji' fontSize='2rem' />
      <Typography sx={{ mb: 4, fontWeight: 600 }}>Emoji</Typography>
      <Typography sx={{ mb: 3 }}>Add any emoji instead of an icon</Typography>
      <Button sx={{ mb: 8 }} variant='contained' onClick={() => toast('Good Job!', { icon: '👏' })}>
        Emoji
      </Button>
    </Box>
  )
}

export default ToastEmoji
