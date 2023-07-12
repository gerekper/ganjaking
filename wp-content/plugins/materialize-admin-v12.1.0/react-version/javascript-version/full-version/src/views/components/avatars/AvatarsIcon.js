// ** MUI Imports
import Box from '@mui/material/Box'
import Avatar from '@mui/material/Avatar'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'

const AvatarsIcon = () => {
  return (
    <Box className='demo-space-x' sx={{ display: 'flex' }}>
      <Avatar>
        <Icon icon='mdi:folder-outline' />
      </Avatar>
      <CustomAvatar color='success'>
        <Icon icon='mdi:cached' />
      </CustomAvatar>
      <CustomAvatar skin='light' color='info'>
        <Icon icon='mdi:checkbox-marked-circle-outline' />
      </CustomAvatar>
    </Box>
  )
}

export default AvatarsIcon
