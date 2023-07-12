// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Button from '@mui/material/Button'
import Dialog from '@mui/material/Dialog'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import DialogTitle from '@mui/material/DialogTitle'
import DialogContent from '@mui/material/DialogContent'
import DialogActions from '@mui/material/DialogActions'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const DialogFullScreen = () => {
  // ** State
  const [open, setOpen] = useState<boolean>(false)

  const handleClickOpen = () => setOpen(true)

  const handleClose = () => setOpen(false)

  return (
    <div>
      <Button variant='outlined' onClick={handleClickOpen}>
        Open dialog
      </Button>
      <Dialog fullScreen onClose={handleClose} aria-labelledby='full-screen-dialog-title' open={open}>
        <DialogTitle id='full-screen-dialog-title'>
          <Typography variant='h6' component='span'>
            Modal title
          </Typography>
          <IconButton
            aria-label='close'
            onClick={handleClose}
            sx={{ top: 8, right: 10, position: 'absolute', color: 'grey.500' }}
          >
            <Icon icon='mdi:close' />
          </IconButton>
        </DialogTitle>
        <DialogContent>
          <Typography sx={{ mb: 4 }}>
            Chupa chups jelly-o candy sweet roll wafer cake chocolate bar. Brownie sweet roll topping cake chocolate
            cake cheesecake tiramisu chocolate cake. Jujubes liquorice chocolate bar pastry. Chocolate jujubes caramels
            pastry.
          </Typography>
          <Typography sx={{ mb: 4 }}>
            Ice cream marshmallow dragée bonbon croissant. Carrot cake sweet donut ice cream bonbon oat cake danish
            sugar plum. Gingerbread gummies marzipan gingerbread.
          </Typography>
          <Typography>
            Soufflé toffee ice cream. Jelly-o pudding sweet roll bonbon. Marshmallow liquorice icing. Jelly beans
            chocolate bar chocolate marzipan candy fruitcake jujubes.
          </Typography>
        </DialogContent>
        <DialogActions className='dialog-actions-dense'>
          <Button onClick={handleClose}>Save changes</Button>
        </DialogActions>
      </Dialog>
    </div>
  )
}

export default DialogFullScreen
