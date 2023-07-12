// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'
import OptionsMenu from 'src/@core/components/option-menu'

const data = [
  {
    imgHeight: 18,
    imgWidth: 22.5,
    amount: '-$850',
    title: 'Credit Card',
    imgAlt: 'credit-card',
    avatarColor: 'success',
    subtitle: 'Digital Ocean',
    imgSrc: '/images/cards/credit-card.png',
    trend: (
      <Box component='span' sx={{ color: 'success.main', '& svg': { verticalAlign: 'bottom' } }}>
        <Icon icon='mdi:chevron-up' />
      </Box>
    )
  },
  {
    imgWidth: 17,
    title: 'Paypal',
    imgHeight: 18.4,
    imgAlt: 'paypal',
    amount: '+$34,456',
    avatarColor: 'primary',
    subtitle: 'Received Money',
    imgSrc: '/images/cards/paypal.png',
    trend: (
      <Box component='span' sx={{ color: 'success.main', '& svg': { verticalAlign: 'bottom' } }}>
        <Icon icon='mdi:chevron-up' />
      </Box>
    )
  },
  {
    imgHeight: 18,
    imgWidth: 22.5,
    amount: '-$199',
    title: 'Mastercard',
    subtitle: 'Netflix',
    avatarColor: 'info',
    imgAlt: 'mastercard',
    imgSrc: '/images/cards/mastercard.png',
    trend: (
      <Box component='span' sx={{ color: 'error.main', '& svg': { verticalAlign: 'bottom' } }}>
        <Icon icon='mdi:chevron-down' />
      </Box>
    )
  },
  {
    imgWidth: 20,
    imgHeight: 18,
    title: 'Wallet',
    amount: '-$156',
    imgAlt: 'wallet',
    subtitle: "Mac'D",
    avatarColor: 'error',
    imgSrc: '/images/cards/wallet.png',
    trend: (
      <Box component='span' sx={{ color: 'error.main', '& svg': { verticalAlign: 'bottom' } }}>
        <Icon icon='mdi:chevron-down' />
      </Box>
    )
  },
  {
    imgWidth: 17,
    title: 'Paypal',
    imgHeight: 18.4,
    imgAlt: 'paypal',
    amount: '+$12,872',
    subtitle: 'Refund',
    avatarColor: 'primary',
    imgSrc: '/images/cards/paypal.png',
    trend: (
      <Box component='span' sx={{ color: 'success.main', '& svg': { verticalAlign: 'bottom' } }}>
        <Icon icon='mdi:chevron-up' />
      </Box>
    )
  },
  {
    imgHeight: 18,
    imgWidth: 22.8,
    title: 'Stripe',
    amount: '-$299',
    imgAlt: 'stripe',
    avatarColor: 'warning',
    subtitle: 'Buy Apple Watch',
    imgSrc: '/images/cards/stripe.png',
    trend: (
      <Box component='span' sx={{ color: 'error.main', '& svg': { verticalAlign: 'bottom' } }}>
        <Icon icon='mdi:chevron-down' />
      </Box>
    )
  }
]

const CardTransactions = () => {
  return (
    <Card>
      <CardHeader
        title='Transactions'
        action={
          <OptionsMenu
            options={['Last 28 Days', 'Last Month', 'Last Year']}
            iconButtonProps={{ size: 'small', sx: { color: 'text.primary' } }}
          />
        }
      />
      <CardContent>
        {data.map((item, index) => {
          return (
            <Box
              key={index}
              sx={{
                display: 'flex',
                alignItems: 'center',
                mb: index !== data.length - 1 ? 7 : undefined
              }}
            >
              <CustomAvatar skin='light' color={item.avatarColor} variant='rounded' sx={{ mr: 3 }}>
                <img alt={item.imgAlt} src={item.imgSrc} width={item.imgWidth} height={item.imgHeight} />
              </CustomAvatar>
              <Box
                sx={{
                  width: '100%',
                  display: 'flex',
                  flexWrap: 'wrap',
                  alignItems: 'center',
                  justifyContent: 'space-between'
                }}
              >
                <Box sx={{ mr: 2, display: 'flex', flexDirection: 'column' }}>
                  <Typography variant='body2' sx={{ mb: 0.5, fontWeight: 600, color: 'text.primary' }}>
                    {item.title}
                  </Typography>
                  <Typography variant='caption'>{item.subtitle}</Typography>
                </Box>
                <Box sx={{ display: 'flex', alignItems: 'center' }}>
                  <Typography variant='body2' sx={{ mr: 1.5, fontWeight: 600, color: 'text.primary' }}>
                    {item.amount}
                  </Typography>
                  {item.trend}
                </Box>
              </Box>
            </Box>
          )
        })}
      </CardContent>
    </Card>
  )
}

export default CardTransactions
