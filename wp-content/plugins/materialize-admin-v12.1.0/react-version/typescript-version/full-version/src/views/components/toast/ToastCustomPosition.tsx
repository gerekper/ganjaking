// ** MUI Imports
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Third Party Components
import toast from 'react-hot-toast'

const ToastCustomPosition = () => {
  const handleClick = () => {
    return toast.success('Always at the bottom.', {
      position: 'bottom-right'
    })
  }

  return (
    <Box
      sx={{ display: 'flex', textAlign: 'center', alignItems: 'center', flexDirection: 'column', '& svg': { mb: 2 } }}
    >
      <Icon icon='mdi:view-grid-plus-outline' fontSize='2rem' />
      <Typography sx={{ mb: 4, fontWeight: 600 }}>Custom Position</Typography>
      <Typography sx={{ mb: 3 }}>You can change the toast's position as you like.</Typography>
      <Button sx={{ mb: 8 }} variant='contained' onClick={handleClick}>
        Position
      </Button>
    </Box>
  )
}

export default ToastCustomPosition
