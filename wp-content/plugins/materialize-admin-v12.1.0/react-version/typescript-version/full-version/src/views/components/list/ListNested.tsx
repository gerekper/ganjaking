// ** React Imports
import { Fragment, useState } from 'react'

// ** MUI Imports
import List from '@mui/material/List'
import Divider from '@mui/material/Divider'
import ListItem from '@mui/material/ListItem'
import Collapse from '@mui/material/Collapse'
import ListItemIcon from '@mui/material/ListItemIcon'
import ListItemText from '@mui/material/ListItemText'
import ListItemButton from '@mui/material/ListItemButton'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ListNested = () => {
  // ** State
  const [open, setOpen] = useState<boolean>(true)

  const handleClick = () => {
    setOpen(!open)
  }

  return (
    <Fragment>
      <List component='nav' aria-label='main mailbox'>
        <ListItem disablePadding>
          <ListItemButton onClick={handleClick}>
            <ListItemIcon>
              <Icon icon='mdi:email-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Inbox' />
            <Icon icon={open ? 'mdi:chevron-up' : 'mdi:chevron-down'} />
          </ListItemButton>
        </ListItem>
        <Collapse in={open} timeout='auto' unmountOnExit>
          <List component='div' disablePadding>
            <ListItem disablePadding>
              <ListItemButton sx={{ pl: 8 }}>
                <ListItemIcon sx={{ mr: 4 }}>
                  <Icon icon='mdi:send-clock' fontSize={20} />
                </ListItemIcon>
                <ListItemText primary='Scheduled' />
              </ListItemButton>
            </ListItem>
          </List>
        </Collapse>
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
      <List component='nav' aria-label='secondary mailbox'>
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

export default ListNested
