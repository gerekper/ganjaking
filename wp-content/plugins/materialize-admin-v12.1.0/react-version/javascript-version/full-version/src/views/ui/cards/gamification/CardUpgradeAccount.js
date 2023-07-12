// ** MUI Imports
import Card from '@mui/material/Card'
import Button from '@mui/material/Button'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// Styled component for the avatar image
const AvatarImg = styled('img')(({ theme }) => ({
  right: 21,
  bottom: 24,
  height: 162,
  position: 'absolute',
  [theme.breakpoints.down('md')]: {
    height: 154
  },
  [theme.breakpoints.down('sm')]: {
    height: 149
  }
}))

const CardUpgradeAccount = () => {
  return (
    <Card sx={{ position: 'relative' }}>
      <CardContent>
        <Typography variant='h6'>Upgrade Account 😀</Typography>
        <Typography sx={{ mb: 4 }} variant='body2'>
          Add 15 team members
        </Typography>
        <Typography variant='h5' sx={{ fontWeight: 600, color: 'primary.main' }}>
          $199
        </Typography>
        <Typography variant='body2' sx={{ mb: 4 }}>
          40% OFF 😍
        </Typography>
        <Button size='small' variant='contained'>
          Upgrade Plan
        </Button>
        <AvatarImg alt='Upgrade Account' src='/images/cards/illustration-upgrade-account.png' />
      </CardContent>
    </Card>
  )
}

export default CardUpgradeAccount
