// ** MUI Imports
import Box from '@mui/material/Box'
import { styled } from '@mui/material/styles'
import ListItem from '@mui/material/ListItem'
import List, { ListProps } from '@mui/material/List'
import ListItemText from '@mui/material/ListItemText'
import ListItemAvatar from '@mui/material/ListItemAvatar'
import LinearProgress from '@mui/material/LinearProgress'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const StyledList = styled(List)<ListProps>(({ theme }) => ({
  '& .MuiListItem-root': {
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
    '& .MuiListItemText-root': {
      margin: theme.spacing(0, 0, 2),
      '& .MuiTypography-root': {
        fontWeight: 500
      }
    }
  }
}))

const ListProgress = () => {
  return (
    <StyledList disablePadding>
      <ListItem>
        <ListItemAvatar>
          <CustomAvatar skin='light' variant='rounded' color='info' sx={{ height: 36, width: 36 }}>
            <Icon icon='mdi:react' />
          </CustomAvatar>
        </ListItemAvatar>
        <Box sx={{ width: '100%' }}>
          <ListItemText primary='React is a JavaScript library for building user interfaces' />
          <LinearProgress color='info' value={90} sx={{ height: 5 }} variant='determinate' />
        </Box>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <CustomAvatar skin='light' variant='rounded' sx={{ height: 36, width: 36 }}>
            <Icon icon='mdi:bootstrap' />
          </CustomAvatar>
        </ListItemAvatar>
        <Box sx={{ width: '100%' }}>
          <ListItemText primary='Bootstrap is an open source toolkit' />
          <LinearProgress value={75} sx={{ height: 5 }} variant='determinate' />
        </Box>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <CustomAvatar skin='light' variant='rounded' color='success' sx={{ height: 36, width: 36 }}>
            <Icon icon='mdi:vuejs' />
          </CustomAvatar>
        </ListItemAvatar>
        <Box sx={{ width: '100%' }}>
          <ListItemText primary='Vue.js is the Progressive JavaScript Framework' />
          <LinearProgress color='success' value={85} sx={{ height: 5 }} variant='determinate' />
        </Box>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <CustomAvatar skin='light' variant='rounded' color='error' sx={{ height: 36, width: 36 }}>
            <Icon icon='mdi:angular' />
          </CustomAvatar>
        </ListItemAvatar>
        <Box sx={{ width: '100%' }}>
          <ListItemText primary='Angular implements Functional Programming concepts' />
          <LinearProgress color='error' value={60} sx={{ height: 5 }} variant='determinate' />
        </Box>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <CustomAvatar skin='light' variant='rounded' color='warning' sx={{ height: 36, width: 36 }}>
            <Icon icon='mdi:language-javascript' />
          </CustomAvatar>
        </ListItemAvatar>
        <Box sx={{ width: '100%' }}>
          <ListItemText primary='JavaScript is the programming language of the Web' />
          <LinearProgress color='warning' value={70} sx={{ height: 5 }} variant='determinate' />
        </Box>
      </ListItem>
    </StyledList>
  )
}

export default ListProgress
