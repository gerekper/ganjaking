// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Alert from '@mui/material/Alert'
import Button from '@mui/material/Button'
import Rating from '@mui/material/Rating'
import Divider from '@mui/material/Divider'
import ListItem from '@mui/material/ListItem'
import TextField from '@mui/material/TextField'
import IconButton from '@mui/material/IconButton'
import AlertTitle from '@mui/material/AlertTitle'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import { styled } from '@mui/material/styles'
import List from '@mui/material/List'
import ListItemText from '@mui/material/ListItemText'
import useMediaQuery from '@mui/material/useMediaQuery'
import ListItemAvatar from '@mui/material/ListItemAvatar'

// ** Custom Components Imports
import CustomChip from 'src/@core/components/mui/chip'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const StyledList = styled(List)(({ theme }) => ({
  padding: 0,
  '& .MuiListItem-root': {
    padding: theme.spacing(5),
    border: `1px solid ${theme.palette.divider}`,
    '&:first-of-type': {
      borderTopLeftRadius: 6,
      borderTopRightRadius: 6
    },
    '&:last-of-type': {
      borderBottomLeftRadius: 6,
      borderBottomRightRadius: 6
    },
    '&:not(:last-of-type)': {
      borderBottom: 0
    },
    '& .MuiListItemText-root': {
      marginTop: 0,
      marginBottom: theme.spacing(4),
      '& .MuiTypography-root': {
        fontWeight: 500
      }
    },
    '& .remove-item': {
      top: '0.5rem',
      right: '0.625rem',
      position: 'absolute',
      color: theme.palette.text.disabled
    },
    [theme.breakpoints.down('sm')]: {
      flexDirection: 'column'
    }
  }
}))

const StepCart = ({ handleNext }) => {
  const breakpointMD = useMediaQuery(theme => theme.breakpoints.between('sm', 'lg'))

  return (
    <Grid container spacing={6}>
      <Grid item xs={12} lg={8}>
        <Alert severity='success' icon={<Icon icon='mdi:tag-outline' />} sx={{ mb: 4 }}>
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
        <Typography variant='h6' sx={{ mb: 4 }}>
          My Shopping Bag (2 Items)
        </Typography>
        <StyledList sx={{ mb: 4 }}>
          <ListItem>
            <ListItemAvatar>
              <img width={140} src='/images/products/google-home.png' alt='Google Home' />
            </ListItemAvatar>
            <IconButton size='small' className='remove-item' sx={{ color: 'text.primary' }}>
              <Icon icon='mdi:close' fontSize={20} />
            </IconButton>
            <Grid container>
              <Grid item xs={12} md={8}>
                <ListItemText primary='Google - Google Home - White' />
                <Box sx={{ display: 'flex' }}>
                  <Typography sx={{ mr: 2, color: 'text.disabled' }}>Sold By:</Typography>
                  <Typography
                    href='/'
                    component={Link}
                    onClick={e => e.preventDefault()}
                    sx={{ mr: 4, color: 'primary.main', textDecoration: 'none' }}
                  >
                    Google
                  </Typography>
                  <CustomChip size='small' skin='light' color='success' label='In Stock' />
                </Box>
                <Rating name='google-nest-rating' value={4} readOnly sx={{ mb: 6 }} />
                <TextField size='small' type='number' defaultValue='1' sx={{ maxWidth: 100, display: 'block' }} />
              </Grid>
              <Grid item xs={12} md={4} sx={{ mt: [6, 6, 8] }}>
                <Box
                  sx={{
                    gap: 3,
                    height: '100%',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'space-between',
                    alignItems: { xs: 'flex-start', md: 'flex-end' }
                  }}
                >
                  <Box sx={{ display: 'flex' }}>
                    <Typography sx={{ color: 'primary.main' }}>$299</Typography>
                    <Typography sx={{ color: 'text.disabled', textDecoration: 'line-through' }}>/$359</Typography>
                  </Box>
                  <Button variant='outlined' size='small'>
                    Move to wishlist
                  </Button>
                </Box>
              </Grid>
            </Grid>
          </ListItem>
          <ListItem>
            <ListItemAvatar>
              <img width={140} src='/images/products/iphone-11.png' alt='iphone 11' />
            </ListItemAvatar>
            <IconButton size='small' className='remove-item' sx={{ color: 'text.primary' }}>
              <Icon icon='mdi:close' fontSize={20} />
            </IconButton>
            <Grid container>
              <Grid item xs={12} md={8}>
                <ListItemText primary='Apple iPhone 11 (64GB, Black)' />
                <Box sx={{ display: 'flex' }}>
                  <Typography sx={{ mr: 2, color: 'text.disabled' }}>Sold By:</Typography>
                  <Typography
                    href='/'
                    component={Link}
                    onClick={e => e.preventDefault()}
                    sx={{ mr: 4, color: 'primary.main', textDecoration: 'none' }}
                  >
                    Apple
                  </Typography>
                  <CustomChip size='small' skin='light' color='success' label='In Stock' />
                </Box>
                <Rating name='iphone-11-rating' value={4} readOnly sx={{ mb: 6 }} />
                <TextField size='small' type='number' defaultValue='1' sx={{ maxWidth: 100, display: 'block' }} />
              </Grid>
              <Grid item xs={12} md={4} sx={{ mt: [6, 6, 8] }}>
                <Box
                  sx={{
                    gap: 3,
                    height: '100%',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: 'space-between',
                    alignItems: { xs: 'flex-start', md: 'flex-end' }
                  }}
                >
                  <Box sx={{ display: 'flex' }}>
                    <Typography sx={{ color: 'primary.main' }}>$899</Typography>
                    <Typography sx={{ color: 'text.disabled', textDecoration: 'line-through' }}>/$999</Typography>
                  </Box>
                  <Button variant='outlined' size='small'>
                    Move to wishlist
                  </Button>
                </Box>
              </Grid>
            </Grid>
          </ListItem>
        </StyledList>
        <Box
          sx={{
            px: 5,
            gap: 2,
            py: 2.5,
            display: 'flex',
            borderRadius: 1,
            alignItems: 'center',
            justifyContent: 'space-between',
            border: theme => `1px solid ${theme.palette.divider}`
          }}
        >
          <Typography
            href='/'
            component={Link}
            onClick={e => e.preventDefault()}
            sx={{ color: 'primary.main', textDecoration: 'none' }}
          >
            Add more products from wishlist
          </Typography>
          <Icon icon='mdi:chevron-right' />
        </Box>
      </Grid>
      <Grid item xs={12} lg={4}>
        <Box sx={{ mb: 4, borderRadius: 1, border: theme => `1px solid ${theme.palette.divider}` }}>
          <CardContent>
            <Typography sx={{ mb: 4, fontWeight: 600 }}>Offer</Typography>
            <Box sx={{ mb: 4, display: 'flex', alignItems: 'center' }}>
              <TextField fullWidth sx={{ mr: 4 }} size='small' placeholder='Enter Promo Code' />
              <Button variant='outlined'>Apply</Button>
            </Box>
            <Box sx={{ p: 4, borderRadius: 1, backgroundColor: 'action.hover' }}>
              <Typography sx={{ mb: 2, fontWeight: 600 }}>Buying gift for a loved one?</Typography>
              <Typography sx={{ mb: 2, color: 'text.secondary' }}>
                Gift wrap and personalized message on card, Only for $2.
              </Typography>
              <Typography
                href='/'
                variant='body2'
                component={Link}
                onClick={e => e.preventDefault()}
                sx={{ color: 'primary.main', fontWeight: 600, textDecoration: 'none' }}
              >
                Add a gift wrap
              </Typography>
            </Box>
          </CardContent>
          <Divider sx={{ my: '0 !important' }} />
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
                <Typography variant='body2' sx={{ color: 'text.primary' }}>
                  Bag Total
                </Typography>
                <Typography variant='body2'>$1198.00</Typography>
              </Box>
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
                <Typography variant='body2' sx={{ color: 'text.primary' }}>
                  Coupon Discount
                </Typography>
                <Typography
                  href='/'
                  variant='body2'
                  component={Link}
                  onClick={e => e.preventDefault()}
                  sx={{ display: 'block', fontWeight: 600, color: 'primary.main', textDecoration: 'none' }}
                >
                  Apply Coupon
                </Typography>
              </Box>
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
                <Typography variant='body2' sx={{ color: 'text.primary' }}>
                  Order Total
                </Typography>
                <Typography variant='body2'>$1198.00</Typography>
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
          <CardContent sx={{ py: theme => `${theme.spacing(3.5)} !important` }}>
            <Box
              sx={{ gap: 2, display: 'flex', flexWrap: 'wrap', alignItems: 'center', justifyContent: 'space-between' }}
            >
              <Typography sx={{ fontWeight: 600 }}>Total</Typography>
              <Typography sx={{ fontWeight: 600 }}>$1198.00</Typography>
            </Box>
          </CardContent>
        </Box>
        <Box sx={{ display: 'flex', ...(breakpointMD ? { justifyContent: 'flex-end' } : {}) }}>
          <Button fullWidth={!breakpointMD} variant='contained' onClick={handleNext}>
            Place Order
          </Button>
        </Box>
      </Grid>
    </Grid>
  )
}

export default StepCart
