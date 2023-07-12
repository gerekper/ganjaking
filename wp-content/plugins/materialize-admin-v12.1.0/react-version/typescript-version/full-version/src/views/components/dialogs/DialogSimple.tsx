// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import List from '@mui/material/List'
import Button from '@mui/material/Button'
import Dialog from '@mui/material/Dialog'
import MuiAvatar from '@mui/material/Avatar'
import ListItem from '@mui/material/ListItem'
import Typography from '@mui/material/Typography'
import DialogTitle from '@mui/material/DialogTitle'
import ListItemText from '@mui/material/ListItemText'
import ListItemButton from '@mui/material/ListItemButton'
import ListItemAvatar from '@mui/material/ListItemAvatar'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'

const emails = ['username@gmail.com', 'user02@gmail.com']

const DialogSimple = () => {
  // ** States
  const [open, setOpen] = useState<boolean>(false)
  const [selectedValue, setSelectedValue] = useState<string>(emails[1])

  const handleClickOpen = () => setOpen(true)

  const handleDialogClose = () => setOpen(false)

  const handleClose = (value: string) => {
    setOpen(false)
    setSelectedValue(value)
  }

  return (
    <div>
      <Typography variant='subtitle1' sx={{ mb: 2 }}>
        Selected: {selectedValue}
      </Typography>
      <Button variant='outlined' onClick={handleClickOpen}>
        Open simple dialog
      </Button>
      <Dialog onClose={handleDialogClose} aria-labelledby='simple-dialog-title' open={open}>
        <DialogTitle id='simple-dialog-title'>Set backup account</DialogTitle>
        <List sx={{ pt: 0, px: '0 !important' }}>
          {emails.map(email => (
            <ListItem key={email} disablePadding onClick={() => handleClose(email)}>
              <ListItemButton>
                <ListItemAvatar>
                  <CustomAvatar skin='light'>
                    <Icon icon='mdi:account-outline' />
                  </CustomAvatar>
                </ListItemAvatar>
                <ListItemText primary={email} />
              </ListItemButton>
            </ListItem>
          ))}
          <ListItem disablePadding onClick={() => handleClose('addAccount')}>
            <ListItemButton>
              <ListItemAvatar>
                <MuiAvatar>
                  <Icon icon='mdi:plus' />
                </MuiAvatar>
              </ListItemAvatar>
              <ListItemText primary='Add account' />
            </ListItemButton>
          </ListItem>
        </List>
      </Dialog>
    </div>
  )
}

export default DialogSimple
