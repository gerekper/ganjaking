// ** React Imports
import { useState, forwardRef } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Switch from '@mui/material/Switch'
import Dialog from '@mui/material/Dialog'
import Button from '@mui/material/Button'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import InputLabel from '@mui/material/InputLabel'
import CardContent from '@mui/material/CardContent'
import Fade from '@mui/material/Fade'
import DialogContent from '@mui/material/DialogContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Component Import
import PricingPlans from 'src/views/pages/pricing/PricingPlans'

const Transition = forwardRef(function Transition(props, ref) {
  return <Fade ref={ref} {...props} />
})

const DialogPricing = ({ data }) => {
  // ** States
  const [show, setShow] = useState(false)
  const [plan, setPlan] = useState('annually')

  const handleChange = e => {
    if (e.target.checked) {
      setPlan('annually')
    } else {
      setPlan('monthly')
    }
  }

  return (
    <Card>
      <CardContent sx={{ textAlign: 'center', '& svg': { mb: 2 } }}>
        <Icon icon='mdi:currency-usd' fontSize='2rem' />
        <Typography variant='h6' sx={{ mb: 4 }}>
          Pricing
        </Typography>
        <Typography sx={{ mb: 3 }}>Elegant pricing options dialog popup example, easy to use in any page.</Typography>
        <Button variant='contained' onClick={() => setShow(true)}>
          Show
        </Button>
      </CardContent>
      <Dialog
        fullWidth
        open={show}
        scroll='body'
        maxWidth='lg'
        onClose={() => setShow(false)}
        TransitionComponent={Transition}
        onBackdropClick={() => setShow(false)}
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
            onClick={() => setShow(false)}
            sx={{ position: 'absolute', right: '1rem', top: '1rem' }}
          >
            <Icon icon='mdi:close' />
          </IconButton>
          <Box sx={{ mb: 4, textAlign: 'center' }}>
            <Typography variant='h5' sx={{ mb: 3, lineHeight: '2rem' }}>
              Subscription Plan
            </Typography>
            <Typography variant='body2'>
              All plans include 40+ advanced tools and features to boost your product. Choose the best plan to fit your
              needs.
            </Typography>
          </Box>
          <Box sx={{ mb: 7, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
            <InputLabel
              htmlFor='modal-pricing-switch'
              sx={{ fontWeight: 500, cursor: 'pointer', fontSize: '0.875rem' }}
            >
              Monthly
            </InputLabel>
            <Switch color='secondary' onChange={handleChange} id='modal-pricing-switch' checked={plan === 'annually'} />
            <InputLabel
              htmlFor='modal-pricing-switch'
              sx={{ fontWeight: 500, cursor: 'pointer', fontSize: '0.875rem' }}
            >
              Annually
            </InputLabel>
          </Box>
          <PricingPlans data={data} plan={plan} />
          <Box sx={{ mt: 6, textAlign: 'center' }}>
            <Typography variant='body2' sx={{ mb: 2.5 }}>
              Still Not Convinced? Start with a 14-day FREE trial!
            </Typography>
            <Button variant='contained' onClick={() => setShow(false)}>
              Start your trial
            </Button>
          </Box>
        </DialogContent>
      </Dialog>
    </Card>
  )
}

export default DialogPricing
