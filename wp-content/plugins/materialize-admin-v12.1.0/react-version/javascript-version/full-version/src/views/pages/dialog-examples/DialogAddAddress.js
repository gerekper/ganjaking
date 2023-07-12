// ** React Imports
import { useState, forwardRef } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Card from '@mui/material/Card'
import Switch from '@mui/material/Switch'
import Select from '@mui/material/Select'
import Dialog from '@mui/material/Dialog'
import Button from '@mui/material/Button'
import MenuItem from '@mui/material/MenuItem'
import TextField from '@mui/material/TextField'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import InputLabel from '@mui/material/InputLabel'
import FormControl from '@mui/material/FormControl'
import CardContent from '@mui/material/CardContent'
import Fade from '@mui/material/Fade'
import DialogContent from '@mui/material/DialogContent'
import DialogActions from '@mui/material/DialogActions'
import FormControlLabel from '@mui/material/FormControlLabel'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Hooks
import useBgColor from 'src/@core/hooks/useBgColor'

const Transition = forwardRef(function Transition(props, ref) {
  return <Fade ref={ref} {...props} />
})

const DialogAddAddress = () => {
  // ** States
  const [show, setShow] = useState(false)
  const [addressType, setAddressType] = useState('home')

  // ** Hooks
  const bgColors = useBgColor()

  return (
    <Card>
      <CardContent sx={{ textAlign: 'center', '& svg': { mb: 2 } }}>
        <Icon icon='mdi:home-outline' fontSize='2rem' />
        <Typography variant='h6' sx={{ mb: 4 }}>
          Add New Address
        </Typography>
        <Typography sx={{ mb: 3 }}>
          Ready to use form to collect user address data with validation and custom input support.
        </Typography>
        <Button variant='contained' onClick={() => setShow(true)}>
          Show
        </Button>
      </CardContent>
      <Dialog
        fullWidth
        open={show}
        maxWidth='md'
        scroll='body'
        onClose={() => setShow(false)}
        TransitionComponent={Transition}
        onBackdropClick={() => setShow(false)}
      >
        <DialogContent
          sx={{
            position: 'relative',
            pb: theme => `${theme.spacing(8)} !important`,
            px: theme => [`${theme.spacing(5)} !important`, `${theme.spacing(15)} !important`],
            pt: theme => [`${theme.spacing(8)} !important`, `${theme.spacing(12.5)} !important`]
          }}
        >
          <IconButton
            size='small'
            onClick={() => setShow(false)}
            sx={{ position: 'absolute', right: '1rem', top: '1rem' }}
          >
            <Icon icon='mdi:close' />
          </IconButton>
          <Box sx={{ mb: 9, textAlign: 'center' }}>
            <Typography variant='h5' sx={{ mb: 3, lineHeight: '2rem' }}>
              Add New Address
            </Typography>
            <Typography variant='body2'>Add address for billing address</Typography>
          </Box>
          <Grid container spacing={6}>
            <Grid item sm={6} xs={12}>
              <Box
                onClick={() => setAddressType('home')}
                sx={{
                  py: 3,
                  px: 4,
                  borderRadius: 1,
                  cursor: 'pointer',
                  ...(addressType === 'home' ? { ...bgColors.primaryLight } : { backgroundColor: 'action.hover' }),
                  border: theme =>
                    `1px solid ${addressType === 'home' ? theme.palette.primary.main : theme.palette.secondary.main}`,
                  ...(addressType === 'home'
                    ? { ...bgColors.primaryLight }
                    : { backgroundColor: bgColors.secondaryLight.backgroundColor })
                }}
              >
                <Box sx={{ mb: 1, display: 'flex', alignItems: 'center', '& svg': { mr: 2 } }}>
                  <Icon icon='mdi:home-outline' />
                  <Typography variant='h6' sx={{ ...(addressType === 'home' ? { color: 'primary.main' } : {}) }}>
                    Home
                  </Typography>
                </Box>
                <Typography sx={{ ...(addressType === 'home' ? { color: 'primary.main' } : {}) }}>
                  Delivery Time (7am - 9pm)
                </Typography>
              </Box>
            </Grid>
            <Grid item sm={6} xs={12}>
              <Box
                onClick={() => setAddressType('office')}
                sx={{
                  py: 3,
                  px: 4,
                  borderRadius: 1,
                  cursor: 'pointer',
                  ...(addressType === 'office' ? { ...bgColors.primaryLight } : { backgroundColor: 'action.hover' }),
                  border: theme =>
                    `1px solid ${addressType === 'office' ? theme.palette.primary.main : theme.palette.secondary.main}`,
                  ...(addressType === 'office'
                    ? { ...bgColors.primaryLight }
                    : { backgroundColor: bgColors.secondaryLight.backgroundColor })
                }}
              >
                <Box sx={{ mb: 1, display: 'flex', alignItems: 'center', '& svg': { mr: 2 } }}>
                  <Icon icon='mdi:briefcase-outline' />
                  <Typography variant='h6' sx={{ ...(addressType === 'office' ? { color: 'primary.main' } : {}) }}>
                    Office
                  </Typography>
                </Box>
                <Typography sx={{ ...(addressType === 'office' ? { color: 'primary.main' } : {}) }}>
                  Delivery Time (10am - 6pm)
                </Typography>
              </Box>
            </Grid>
            <Grid item sm={6} xs={12}>
              <TextField fullWidth label='First Name' placeholder='John' />
            </Grid>
            <Grid item sm={6} xs={12}>
              <TextField fullWidth label='Last Name' placeholder='Doe' />
            </Grid>
            <Grid item xs={12}>
              <FormControl fullWidth>
                <InputLabel id='country-select'>Country</InputLabel>
                <Select
                  fullWidth
                  placeholder='UK'
                  label='Country'
                  labelId='country-select'
                  defaultValue='Select Country'
                >
                  <MenuItem value='Select Country'>Select Country</MenuItem>
                  <MenuItem value='France'>France</MenuItem>
                  <MenuItem value='Russia'>Russia</MenuItem>
                  <MenuItem value='China'>China</MenuItem>
                  <MenuItem value='UK'>UK</MenuItem>
                  <MenuItem value='US'>US</MenuItem>
                </Select>
              </FormControl>
            </Grid>
            <Grid item xs={12}>
              <TextField fullWidth label='Address Line 1' placeholder='12, Business Park' />
            </Grid>
            <Grid item xs={12}>
              <TextField fullWidth label='Address Line 2' placeholder='Mall Road' />
            </Grid>
            <Grid item xs={12}>
              <TextField fullWidth label='Town' placeholder='Los Angeles' />
            </Grid>
            <Grid item sm={6} xs={12}>
              <TextField fullWidth label='State / Province' placeholder='California' />
            </Grid>
            <Grid item sm={6} xs={12}>
              <TextField fullWidth label='Zip Code' placeholder='99950' />
            </Grid>
            <Grid item xs={12}>
              <FormControlLabel control={<Switch defaultChecked />} label='Make this default shipping address' />
            </Grid>
          </Grid>
        </DialogContent>
        <DialogActions
          sx={{
            justifyContent: 'center',
            px: theme => [`${theme.spacing(5)} !important`, `${theme.spacing(15)} !important`],
            pb: theme => [`${theme.spacing(8)} !important`, `${theme.spacing(12.5)} !important`]
          }}
        >
          <Button variant='contained' sx={{ mr: 2 }} onClick={() => setShow(false)}>
            Submit
          </Button>
          <Button variant='outlined' color='secondary' onClick={() => setShow(false)}>
            Cancel
          </Button>
        </DialogActions>
      </Dialog>
    </Card>
  )
}

export default DialogAddAddress
