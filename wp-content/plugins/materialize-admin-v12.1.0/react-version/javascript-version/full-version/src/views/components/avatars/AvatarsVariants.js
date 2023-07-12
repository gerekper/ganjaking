// ** MUI Imports
import Box from '@mui/material/Box'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'

const AvatarsVariants = () => {
  return (
    <Box className='demo-space-x' sx={{ display: 'flex' }}>
      <CustomAvatar variant='square'>
        <Icon icon='mdi:bell-outline' />
      </CustomAvatar>
      <CustomAvatar color='success' variant='rounded'>
        <Icon icon='mdi:content-save-outline' />
      </CustomAvatar>
      <CustomAvatar skin='light' variant='square'>
        <Icon icon='mdi:bell-outline' />
      </CustomAvatar>
      <CustomAvatar skin='light' color='success' variant='rounded'>
        <Icon icon='mdi:content-save-outline' />
      </CustomAvatar>
    </Box>
  )
}

export default AvatarsVariants
