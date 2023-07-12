// ** MUI Imports
import Box from '@mui/material/Box'
import Switch from '@mui/material/Switch'
import Typography from '@mui/material/Typography'
import InputLabel from '@mui/material/InputLabel'
import useMediaQuery from '@mui/material/useMediaQuery'

// ** Icon Import
import Icon from 'src/@core/components/icon'

// ** Custom Component Import
import CustomChip from 'src/@core/components/mui/chip'

const PricingHeader = props => {
  // ** Props
  const { plan, handleChange } = props

  // ** Hook
  const hidden = useMediaQuery(theme => theme.breakpoints.down('sm'))

  return (
    <Box sx={{ mb: [10, 17.5], textAlign: 'center' }}>
      <Typography variant='h4'>Pricing Plans</Typography>
      <Box sx={{ mt: 2.5, mb: 10.75 }}>
        <Typography variant='body2'>
          All plans include 40+ advanced tools and features to boost your product.
        </Typography>
        <Typography variant='body2'>Choose the best plan to fit your needs.</Typography>
      </Box>
      <Box sx={{ display: 'flex', position: 'relative', alignItems: 'center', justifyContent: 'center' }}>
        <InputLabel
          htmlFor='pricing-switch'
          sx={{ fontWeight: 600, cursor: 'pointer', fontSize: '0.875rem', color: 'text.secondary' }}
        >
          Monthly
        </InputLabel>
        <Switch color='secondary' id='pricing-switch' onChange={handleChange} checked={plan === 'annually'} />
        <InputLabel htmlFor='pricing-switch' sx={{ fontWeight: 500, cursor: 'pointer', fontSize: '0.875rem' }}>
          Annually
        </InputLabel>
        {!hidden && (
          <Box
            sx={{
              top: -30,
              left: '50%',
              display: 'flex',
              position: 'absolute',
              transform: 'translateX(35%)',
              '& svg': { mt: 2, mr: 1, color: 'text.disabled' }
            }}
          >
            <Icon icon='mdi:arrow-down-left' />
            <CustomChip size='small' skin='light' color='primary' label='Save up to 10%' />
          </Box>
        )}
      </Box>
    </Box>
  )
}

export default PricingHeader
