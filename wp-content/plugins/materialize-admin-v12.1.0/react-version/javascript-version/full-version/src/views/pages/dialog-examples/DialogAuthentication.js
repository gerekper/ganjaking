// ** React Imports
import { useState, forwardRef } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Grid from '@mui/material/Grid'
import Alert from '@mui/material/Alert'
import Dialog from '@mui/material/Dialog'
import Button from '@mui/material/Button'
import TextField from '@mui/material/TextField'
import AlertTitle from '@mui/material/AlertTitle'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import Fade from '@mui/material/Fade'
import DialogContent from '@mui/material/DialogContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Hooks
import useBgColor from 'src/@core/hooks/useBgColor'
import { useSettings } from 'src/@core/hooks/useSettings'

const Transition = forwardRef(function Transition(props, ref) {
  return <Fade ref={ref} {...props} />
})

const DialogAuthentication = () => {
  // ** States
  const [show, setShow] = useState(false)
  const [authType, setAuthType] = useState('app')
  const [showAuthDialog, setShowAuthDialog] = useState(false)

  // ** Hooks
  const bgColors = useBgColor()
  const { settings } = useSettings()

  // ** Var
  const { direction } = settings

  const handleClose = () => {
    setShow(false)
    setAuthType('app')
  }

  const handleAuthDialogClose = () => {
    if (show) {
      setShow(false)
    }
    setShowAuthDialog(false)
    if (authType !== 'app') {
      setTimeout(() => {
        setAuthType('app')
      }, 250)
    }
  }
  const arrowIcon = direction === 'ltr' ? 'mdi:chevron-right' : 'mdi:chevron-left'

  return (
    <Card>
      <CardContent sx={{ textAlign: 'center', '& svg': { mb: 2 } }}>
        <Icon icon='mdi:lock-outline' fontSize='2rem' />
        <Typography variant='h6' sx={{ mb: 4 }}>
          Two Factor Auth
        </Typography>
        <Typography sx={{ mb: 3 }}>Enhance your application security by enabling two factor authentication.</Typography>
        <Button variant='contained' onClick={() => setShow(true)}>
          Show
        </Button>
      </CardContent>
      <Dialog
        fullWidth
        open={show}
        maxWidth='md'
        scroll='body'
        onClose={handleClose}
        onBackdropClick={handleClose}
        TransitionComponent={Transition}
      >
        <DialogContent
          sx={{
            position: 'relative',
            px: theme => [`${theme.spacing(5)} !important`, `${theme.spacing(15)} !important`],
            py: theme => [`${theme.spacing(8)} !important`, `${theme.spacing(12.5)} !important`]
          }}
        >
          <IconButton size='small' onClick={handleClose} sx={{ position: 'absolute', right: '1rem', top: '1rem' }}>
            <Icon icon='mdi:close' />
          </IconButton>

          <Grid container spacing={6}>
            <Grid item xs={12}>
              <Box sx={{ mb: 3, textAlign: 'center' }}>
                <Typography variant='h5' sx={{ mb: 3, lineHeight: '2rem' }}>
                  Select Authentication Method
                </Typography>
                <Typography variant='body2'>
                  You also need to select a method by which the proxy authenticates to the directory serve.
                </Typography>
              </Box>
            </Grid>
            <Grid item xs={12}>
              <Box
                onClick={() => setAuthType('app')}
                sx={{
                  pt: 4,
                  pb: 2.75,
                  px: 7.2,
                  borderRadius: 1,
                  cursor: 'pointer',
                  ...(authType === 'app' ? { ...bgColors.primaryLight } : { backgroundColor: 'action.hover' }),
                  border: theme =>
                    `1px solid ${authType === 'app' ? theme.palette.primary.main : theme.palette.secondary.main}`,
                  ...(authType === 'app'
                    ? { ...bgColors.primaryLight }
                    : { backgroundColor: bgColors.secondaryLight.backgroundColor })
                }}
              >
                <Box
                  sx={{
                    rowGap: 1.5,
                    columnGap: 3,
                    display: 'flex',
                    alignItems: 'center',
                    textAlign: ['center', 'start'],
                    flexDirection: ['column', 'row']
                  }}
                >
                  <Box sx={{ display: 'flex' }}>
                    <Icon icon='mdi:cog-outline' fontSize={35} />
                  </Box>
                  <div>
                    <Typography variant='h6' sx={{ mb: 1.25, ...(authType === 'app' && { color: 'primary.main' }) }}>
                      Authenticator Apps
                    </Typography>
                    <Typography sx={{ ...(authType === 'app' && { color: 'primary.main' }) }}>
                      Get code from an app like Google Authenticator or Microsoft Authenticator.
                    </Typography>
                  </div>
                </Box>
              </Box>
            </Grid>
            <Grid item xs={12}>
              <Box
                onClick={() => setAuthType('sms')}
                sx={{
                  pt: 4,
                  pb: 2.75,
                  px: 7.2,
                  borderRadius: 1,
                  cursor: 'pointer',
                  ...(authType === 'sms' ? { ...bgColors.primaryLight } : { backgroundColor: 'action.hover' }),
                  border: theme =>
                    `1px solid ${authType === 'sms' ? theme.palette.primary.main : theme.palette.secondary.main}`,
                  ...(authType === 'sms'
                    ? { ...bgColors.primaryLight }
                    : { backgroundColor: bgColors.secondaryLight.backgroundColor })
                }}
              >
                <Box
                  sx={{
                    rowGap: 1.5,
                    columnGap: 3,
                    display: 'flex',
                    alignItems: 'center',
                    textAlign: ['center', 'start'],
                    flexDirection: ['column', 'row']
                  }}
                >
                  <Box sx={{ display: 'flex' }}>
                    <Icon icon='mdi:message-outline' fontSize={35} />
                  </Box>
                  <div>
                    <Typography
                      variant='h6'
                      sx={{
                        mb: 1.25,
                        fontWeight: 600,
                        textTransform: 'uppercase',
                        ...(authType === 'sms' && { color: 'primary.main' })
                      }}
                    >
                      sms
                    </Typography>
                    <Typography sx={{ ...(authType === 'sms' && { color: 'primary.main' }) }}>
                      We will send a code via SMS if you need to use your backup login method.
                    </Typography>
                  </div>
                </Box>
              </Box>
            </Grid>
            <Grid item xs={12} sx={{ display: 'flex', justifyContent: 'flex-end' }}>
              <Button
                variant='contained'
                endIcon={<Icon icon={arrowIcon} />}
                onClick={() => {
                  setShow(false)
                  setShowAuthDialog(true)
                }}
              >
                Continue
              </Button>
            </Grid>
          </Grid>
        </DialogContent>
      </Dialog>

      <Dialog
        fullWidth
        maxWidth='md'
        scroll='body'
        open={showAuthDialog}
        onClose={handleAuthDialogClose}
        TransitionComponent={Transition}
        onBackdropClick={handleAuthDialogClose}
      >
        <DialogContent
          sx={{
            position: 'relative',
            px: theme => [`${theme.spacing(5)} !important`, `${theme.spacing(15)} !important`],
            py: theme => [`${theme.spacing(8)} !important`, `${theme.spacing(12.5)} !important`]
          }}
        >
          <IconButton
            size='small'
            onClick={handleAuthDialogClose}
            sx={{ position: 'absolute', right: '1rem', top: '1rem' }}
          >
            <Icon icon='mdi:close' />
          </IconButton>

          <Grid container spacing={6}>
            <Grid item xs={12}>
              {authType === 'sms' ? (
                <div>
                  <Typography variant='h6'>Verify Your Mobile Number for SMS</Typography>
                  <Typography variant='body2'>
                    Enter your mobile phone number with country code and we will send you a verification code.
                  </Typography>
                  <TextField fullWidth sx={{ my: 4 }} label='Mobile Number' placeholder='+1 123 456 7890' />
                  <Grid container spacing={6}>
                    <Grid item xs={12} sx={{ display: 'flex', justifyContent: 'flex-end' }}>
                      <Button variant='outlined' color='secondary' onClick={handleAuthDialogClose} sx={{ mr: 4 }}>
                        Cancel
                      </Button>
                      <Button variant='contained' endIcon={<Icon icon={arrowIcon} />} onClick={handleAuthDialogClose}>
                        Continue
                      </Button>
                    </Grid>
                  </Grid>
                </div>
              ) : (
                <div>
                  <Typography variant='h5' sx={{ mb: 4, textAlign: 'center' }}>
                    Add Authenticator App
                  </Typography>
                  <Typography variant='h6'>Authenticator Apps</Typography>
                  <Typography variant='body2' sx={{ mb: 4 }}>
                    Using an authenticator app like Google Authenticator, Microsoft Authenticator, Authy, or 1Password,
                    scan the QR code. It will generate a 6 digit code for you to enter below.
                  </Typography>

                  <Box sx={{ my: 12, display: 'flex', justifyContent: 'center' }}>
                    <img width={122} height={122} alt='qr-code' src='/images/pages/pixinvent-qr.png' />
                  </Box>

                  <Alert severity='warning' icon={false} sx={{ mb: 4, '& .MuiAlert-message': { overflow: 'hidden' } }}>
                    <AlertTitle sx={{ whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                      ASDLKNASDA9AHS678dGhASD78AB
                    </AlertTitle>
                    If you having trouble using the QR code, select manual entry on your app
                  </Alert>

                  <TextField
                    fullWidth
                    sx={{ mb: 4 }}
                    label='Enter Authentication Code'
                    placeholder='Enter Authentication Code'
                  />
                  <Grid container spacing={6}>
                    <Grid item xs={12} sx={{ display: 'flex', justifyContent: 'flex-end' }}>
                      <Button variant='outlined' color='secondary' onClick={handleAuthDialogClose} sx={{ mr: 4 }}>
                        Cancel
                      </Button>
                      <Button variant='contained' endIcon={<Icon icon={arrowIcon} />} onClick={handleAuthDialogClose}>
                        Continue
                      </Button>
                    </Grid>
                  </Grid>
                </div>
              )}
            </Grid>
          </Grid>
        </DialogContent>
      </Dialog>
    </Card>
  )
}

export default DialogAuthentication
