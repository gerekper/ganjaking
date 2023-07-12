// ** React Imports
import { Fragment } from 'react'

// ** MUI Imports
import List from '@mui/material/List'
import Divider from '@mui/material/Divider'
import ListItem from '@mui/material/ListItem'
import ListItemIcon from '@mui/material/ListItemIcon'
import ListItemText from '@mui/material/ListItemText'
import ListItemButton from '@mui/material/ListItemButton'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ListDense = () => {
  return (
    <Fragment>
      <List dense>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:email-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Inbox' />
          </ListItemButton>
        </ListItem>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:content-copy' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Draft' />
          </ListItemButton>
        </ListItem>
      </List>
      <Divider sx={{ m: '0 !important' }} />
      <List dense>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:clock-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Snoozed' />
          </ListItemButton>
        </ListItem>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:alert-circle-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Spam' />
          </ListItemButton>
        </ListItem>
      </List>
    </Fragment>
  )
}

export default ListDense
