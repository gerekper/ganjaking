// ** React Imports
import { useState } from 'react'

// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Tab from '@mui/material/Tab'
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Alert from '@mui/material/Alert'
import TabPanel from '@mui/lab/TabPanel'
import Switch from '@mui/material/Switch'
import Button from '@mui/material/Button'
import Divider from '@mui/material/Divider'
import TabContext from '@mui/lab/TabContext'
import Tooltip from '@mui/material/Tooltip'
import { styled } from '@mui/material/styles'
import TextField from '@mui/material/TextField'
import AlertTitle from '@mui/material/AlertTitle'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import InputAdornment from '@mui/material/InputAdornment'
import MuiTabList from '@mui/lab/TabList'
import FormControlLabel from '@mui/material/FormControlLabel'

// ** Custom Components Imports
import CustomChip from 'src/@core/components/mui/chip'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const TabList = styled(MuiTabList)(({ theme }) => ({
  '& .MuiTabs-indicator': {
    display: 'none'
  },
  '& .Mui-selected': {
    backgroundColor: theme.palette.primary.main,
    color: `${theme.palette.common.white} !important`
  },
  '& .MuiTab-root': {
    minWidth: 81,
    minHeight: 38,
    borderRadius: theme.shape.borderRadius
  }
}))

const StepPayment = ({ handleNext }) => {
  // ** State
  const [value, setValue] = useState('cc')

  const handleChange = (event, newValue) => {
    setValue(newValue)
  }

  return (
    <Grid container spacing={6}>
      <Grid item xs={12} lg={8}>
        <Alert severity='success' icon={<Icon icon='mdi:tag-outline' />} sx={{ mb: 6 }}>
          <AlertTitle>Available Offers</AlertTitle>
          <div>
            <Typography sx={{ color: 'success.main' }}>
              - 10% Instant Discount on Bank of America Corp Bank Debit and Credit cards
            </Typography>
            <Typography sx={{ color: 'success.main' }}>
              - 25% Cashback Voucher of up to $60 on first ever PayPal transaction. TCA
            </Typography>
          </div>
        </Alert>
        <TabContext value={value}>
          <TabList
            variant='scrollable'
            scrollButtons='auto'
            onChange={handleChange}
            aria-label='customized tabs example'
          >
            <Tab value='cc' label='Card' />
            <Tab value='cod' label='Cash On Delivery' />
            <Tab value='gc' label='Gift Card' />
          </TabList>
          <Grid container sx={{ mt: 5 }}>
            <Grid item md={8} xs={12}>
              <TabPanel value='cc' sx={{ p: 0 }}>
                <Grid container spacing={4}>
                  <Grid item xs={12}>
                    <TextField fullWidth type='number' label='Card Number' placeholder='1356 3215 6548 7898' />
                  </Grid>
                  <Grid item xs={12} sm={6}>
                    <TextField fullWidth label='Name' placeholder='John Doe' />
                  </Grid>
                  <Grid item xs={6} sm={3}>
                    <TextField fullWidth label='Expiry Date' placeholder='MM/YY' />
                  </Grid>
                  <Grid item xs={6} sm={3}>
                    <TextField
                      fullWidth
                      label='CVC'
                      placeholder='654'
                      InputProps={{
                        endAdornment: (
                          <InputAdornment position='start'>
                            <Tooltip title='Card Verification Value'>
                              <Box component='span' sx={{ display: 'inline-flex', '& svg': { cursor: 'pointer' } }}>
                                <Icon icon='mdi:help-circle-outline' fontSize={20} />
                              </Box>
                            </Tooltip>
                          </InputAdornment>
                        )
                      }}
                    />
                  </Grid>
                  <Grid item xs={12}>
                    <FormControlLabel control={<Switch defaultChecked />} label='Save Card for future billing?' />
                  </Grid>
                  <Grid item xs={12}>
                    <Button variant='contained' sx={{ mr: 3.5 }} onClick={handleNext}>
                      Checkout
                    </Button>
                    <Button type='reset' variant='outlined' color='secondary'>
                      Reset
                    </Button>
                  </Grid>
                </Grid>
              </TabPanel>
              <TabPanel value='cod' sx={{ p: 0 }}>
                <Typography sx={{ mb: 4 }}>
                  Cash on Delivery is a type of payment method where the recipient make payment for the order at the
                  time of delivery rather than in advance.
                </Typography>
                <Button variant='contained' onClick={handleNext}>
                  Pay On Delivery
                </Button>
              </TabPanel>
              <TabPanel value='gc' sx={{ p: 0 }}>
                <Typography sx={{ mb: 4, fontWeight: 500 }}>Enter Gift Card Details</Typography>
                <Grid container spacing={4}>
                  <Grid item xs={12}>
                    <TextField fullWidth type='number' label='Gift Card Number' placeholder='Gift Card Number' />
                  </Grid>
                  <Grid item xs={12}>
                    <TextField fullWidth type='number' label='Gift Card Pin' placeholder='Gift Card Pin' />
                  </Grid>
                  <Grid item xs={12}>
                    <Button variant='contained' onClick={handleNext}>
                      Redeem Gift Card
                    </Button>
                  </Grid>
                </Grid>
              </TabPanel>
            </Grid>
          </Grid>
        </TabContext>
      </Grid>
      <Grid item xs={12} lg={4}>
        <Box sx={{ borderRadius: 1, border: theme => `1px solid ${theme.palette.divider}` }}>
          <CardContent>
            <Typography sx={{ mb: 4, fontWeight: 600 }}>Price Details</Typography>
            <Box sx={{ display: 'flex', flexDirection: 'column' }}>
              <Box
                sx={{
                  mb: 2,
                  gap: 2,
                  display: 'flex',
                  flexWrap: 'wrap',
                  alignItems: 'center',
                  justifyContent: 'space-between'
                }}
              >
                <Typography variant='body2' sx={{ fontWeight: 600 }}>
                  Order Total
                </Typography>
                <Typography variant='body2' sx={{ color: 'text.secondary' }}>
                  $1198.00
                </Typography>
              </Box>
              <Box
                sx={{
                  gap: 2,
                  display: 'flex',
                  flexWrap: 'wrap',
                  alignItems: 'center',
                  justifyContent: 'space-between'
                }}
              >
                <Typography variant='body2' sx={{ color: 'text.primary' }}>
                  Delivery Charges
                </Typography>
                <Box sx={{ display: 'flex', justifyContent: 'flex-end' }}>
                  <Typography variant='body2' sx={{ mr: 2, textDecoration: 'line-through', color: 'text.disabled' }}>
                    $5.00
                  </Typography>
                  <CustomChip size='small' skin='light' color='success' label='Free' />
                </Box>
              </Box>
            </Box>
          </CardContent>
          <Divider sx={{ my: '0 !important' }} />
          <CardContent>
            <Box
              sx={{
                mb: 2,
                gap: 2,
                display: 'flex',
                flexWrap: 'wrap',
                alignItems: 'center',
                justifyContent: 'space-between'
              }}
            >
              <Typography sx={{ fontWeight: 600 }}>Total</Typography>
              <Typography>$1198.00</Typography>
            </Box>
            <Box
              sx={{
                mb: 4,
                gap: 2,
                display: 'flex',
                flexWrap: 'wrap',
                alignItems: 'center',
                justifyContent: 'space-between'
              }}
            >
              <Typography sx={{ fontWeight: 600 }}>Deliver to:</Typography>
              <CustomChip size='small' skin='light' color='primary' label='Home' />
            </Box>
            <Typography sx={{ fontWeight: 600 }}>John Doe (Default),</Typography>
            <Typography sx={{ color: 'text.secondary' }}>4135 Parkway Street,</Typography>
            <Typography sx={{ color: 'text.secondary' }}>Los Angeles, CA, 90017.</Typography>
            <Typography sx={{ mb: 4, color: 'text.secondary' }}>Mobile : +1 906 568 2332</Typography>
            <Typography
              href='/'
              component={Link}
              onClick={e => e.preventDefault()}
              sx={{ fontWeight: 600, color: 'primary.main', textDecoration: 'none' }}
            >
              Change address
            </Typography>
          </CardContent>
        </Box>
      </Grid>
    </Grid>
  )
}

export default StepPayment
