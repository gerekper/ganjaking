// ** MUI Imports
import Box from '@mui/material/Box'
import Avatar from '@mui/material/Avatar'
import Button from '@mui/material/Button'
import ListItem from '@mui/material/ListItem'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import List, { ListProps } from '@mui/material/List'
import ListItemText from '@mui/material/ListItemText'
import ListItemAvatar from '@mui/material/ListItemAvatar'
import ListItemSecondaryAction from '@mui/material/ListItemSecondaryAction'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const StyledList = styled(List)<ListProps>(({ theme }) => ({
  '& .MuiListItem-container': {
    border: `1px solid ${theme.palette.divider}`,
    '&:first-of-type': {
      borderTopLeftRadius: theme.shape.borderRadius,
      borderTopRightRadius: theme.shape.borderRadius
    },
    '&:last-child': {
      borderBottomLeftRadius: theme.shape.borderRadius,
      borderBottomRightRadius: theme.shape.borderRadius
    },
    '&:not(:last-child)': {
      borderBottom: 0
    },
    '& .MuiListItem-root': {
      paddingRight: theme.spacing(24)
    },
    '& .MuiListItemText-root': {
      marginTop: 0,
      '& .MuiTypography-root': {
        fontWeight: 500
      }
    }
  }
}))

const ListUsers = () => {
  return (
    <StyledList disablePadding>
      <ListItem>
        <ListItemAvatar>
          <Avatar src='/images/avatars/2.png' alt='Caroline Black' />
        </ListItemAvatar>
        <div>
          <ListItemText primary='Caroline Black' />
          <Box sx={{ display: 'flex', alignItems: 'center', flexWrap: 'wrap' }}>
            <Box sx={{ mr: 3, display: 'flex', alignItems: 'center', '& svg': { mr: 1, color: 'success.main' } }}>
              <Icon icon='mdi:circle' fontSize='0.625rem' />
              <Typography variant='caption'>Online</Typography>
            </Box>
            <Typography variant='caption' sx={{ color: 'text.disabled' }}>
              13 minutes ago
            </Typography>
          </Box>
        </div>
        <ListItemSecondaryAction>
          <Button variant='contained' size='small'>
            Add
          </Button>
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <Avatar src='/images/avatars/1.png' alt='Alfred Copeland' />
        </ListItemAvatar>
        <div>
          <ListItemText primary='Alfred Copeland' />
          <Box sx={{ display: 'flex', alignItems: 'center', flexWrap: 'wrap' }}>
            <Box sx={{ mr: 3, display: 'flex', alignItems: 'center', '& svg': { mr: 1, color: 'warning.main' } }}>
              <Icon icon='mdi:circle' fontSize='0.625rem' />
              <Typography variant='caption'>Away</Typography>
            </Box>
            <Typography variant='caption' sx={{ color: 'text.disabled' }}>
              11 minutes ago
            </Typography>
          </Box>
        </div>
        <ListItemSecondaryAction>
          <Button variant='contained' size='small'>
            Add
          </Button>
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <Avatar src='/images/avatars/8.png' alt='Celia Schneider' />
        </ListItemAvatar>
        <div>
          <ListItemText primary='Celia Schneider' />
          <Box sx={{ display: 'flex', alignItems: 'center', flexWrap: 'wrap' }}>
            <Box sx={{ mr: 3, display: 'flex', alignItems: 'center', '& svg': { mr: 1, color: 'secondary.main' } }}>
              <Icon icon='mdi:circle' fontSize='0.625rem' />
              <Typography variant='caption'>Offline</Typography>
            </Box>
            <Typography variant='caption' sx={{ color: 'text.disabled' }}>
              9 minutes ago
            </Typography>
          </Box>
        </div>

        <ListItemSecondaryAction>
          <Button variant='contained' size='small'>
            Add
          </Button>
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <Avatar src='/images/avatars/5.png' alt='Celia Schneider' />
        </ListItemAvatar>
        <div>
          <ListItemText primary='Max Rogan' />
          <Box sx={{ display: 'flex', alignItems: 'center', flexWrap: 'wrap' }}>
            <Box sx={{ mr: 3, display: 'flex', alignItems: 'center', '& svg': { mr: 1, color: 'error.main' } }}>
              <Icon icon='mdi:circle' fontSize='0.625rem' />
              <Typography variant='caption'>In Meeting</Typography>
            </Box>
            <Typography variant='caption' sx={{ color: 'text.disabled' }}>
              28 minutes ago
            </Typography>
          </Box>
        </div>

        <ListItemSecondaryAction>
          <Button variant='contained' size='small'>
            Add
          </Button>
        </ListItemSecondaryAction>
      </ListItem>
    </StyledList>
  )
}

export default ListUsers
