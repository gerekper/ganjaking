// ** React Imports
import { useState } from 'react'

// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Avatar from '@mui/material/Avatar'
import Button from '@mui/material/Button'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'

// ** Hooks Imports
import useBgColor from 'src/@core/hooks/useBgColor'

const CardPlanUpgrade = () => {
  // ** States
  const [cvc1, setCvc1] = useState('')
  const [cvc2, setCvc2] = useState('')

  // ** Hook
  const bgColors = useBgColor()

  return (
    <Card>
      <CardHeader
        title='Upgrade Your Plan'
        action={
          <OptionsMenu
            options={['Add Cards', 'Edit Cards', 'Delete Year']}
            iconButtonProps={{ size: 'small', sx: { color: 'text.primary' } }}
          />
        }
      />
      <CardContent
        sx={{
          '& > a': { mt: 4, fontWeight: 500, mb: 4, fontSize: '0.75rem', color: 'primary.main', textDecoration: 'none' }
        }}
      >
        <Typography component='p' variant='caption' sx={{ mb: 3.5 }}>
          Please make the payment to start enjoying all the features of our premium plan as soon as possible.
        </Typography>

        <Box
          sx={{
            mb: 3.5,
            borderRadius: 1,
            color: 'text.primary',
            p: theme => theme.spacing(2.75, 3.5),
            backgroundColor: bgColors.primaryLight.backgroundColor
          }}
        >
          <Box sx={{ width: '100%', display: 'flex', alignItems: 'center' }}>
            <Avatar
              variant='rounded'
              sx={{
                mr: 3,
                width: 40,
                height: 40,
                color: 'primary.main',
                backgroundColor: 'transparent',
                border: theme => `2px solid ${theme.palette.primary.main}`
              }}
            >
              <Icon icon='mdi:star-outline' />
            </Avatar>

            <Box
              sx={{
                width: '100%',
                display: 'flex',
                flexWrap: 'wrap',
                alignItems: 'center',
                justifyContent: 'space-between'
              }}
            >
              <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                <Typography sx={{ fontWeight: 600 }}>Platinum</Typography>
                <Typography
                  href='/'
                  component={Link}
                  variant='caption'
                  onClick={e => e.preventDefault()}
                  sx={{ color: 'primary.main', textDecoration: 'none' }}
                >
                  Upgrade Plan
                </Typography>
              </Box>
              <Box sx={{ display: 'flex' }}>
                <Typography
                  component='sup'
                  variant='body2'
                  sx={{ mt: 0.5, color: 'text.primary', alignSelf: 'flex-start' }}
                >
                  $
                </Typography>
                <Typography variant='h5'>2,199</Typography>
                <Typography component='sub' variant='body2' sx={{ alignSelf: 'flex-end' }}>
                  /Year
                </Typography>
              </Box>
            </Box>
          </Box>
        </Box>

        <Typography variant='body2' sx={{ mb: 4, fontWeight: 600, fontSize: '0.875rem' }}>
          Payment details
        </Typography>

        <Box sx={{ mb: 2, display: 'flex', alignItems: 'center' }}>
          <img width={42} height={30} alt='mastercard' src='/images/cards/logo-mastercard-2.png' />
          <Box
            sx={{
              ml: 3,
              flexGrow: 1,
              display: 'flex',
              flexWrap: 'wrap',
              alignItems: 'center',
              justifyContent: 'space-between'
            }}
          >
            <Box sx={{ mr: 2, display: 'flex', mb: 0.4, flexDirection: 'column' }}>
              <Typography variant='body2' sx={{ fontWeight: 600, color: 'text.primary' }}>
                Credit card
              </Typography>
              <Typography variant='caption'>2566 xxxx xxxx 8908</Typography>
            </Box>
            <TextField
              label='CVC'
              size='small'
              value={cvc1}
              type='number'
              sx={{ width: 80, mt: 0.4 }}
              onChange={e =>
                e.target.value.length > 3 ? setCvc1(e.target.value.slice(0, 3)) : setCvc1(e.target.value)
              }
            />
          </Box>
        </Box>

        <Box sx={{ mb: 1, display: 'flex', alignItems: 'center' }}>
          <img width={42} height={30} alt='credit-card' src='/images/cards/logo-credit-card-2.png' />
          <Box
            sx={{
              ml: 3,
              flexGrow: 1,
              display: 'flex',
              flexWrap: 'wrap',
              alignItems: 'center',
              justifyContent: 'space-between'
            }}
          >
            <Box sx={{ mr: 2, display: 'flex', mb: 0.4, flexDirection: 'column' }}>
              <Typography variant='body2' sx={{ fontWeight: 600, color: 'text.primary' }}>
                Credit card
              </Typography>
              <Typography variant='caption'>8990 xxxx xxxx 6852</Typography>
            </Box>
            <TextField
              label='CVC'
              size='small'
              value={cvc2}
              type='number'
              sx={{ width: 80, mt: 0.4 }}
              onChange={e =>
                e.target.value.length > 3 ? setCvc2(e.target.value.slice(0, 3)) : setCvc2(e.target.value)
              }
            />
          </Box>
        </Box>

        <Typography
          href='/'
          component={Link}
          variant='caption'
          sx={{ color: 'primary.main' }}
          onClick={e => e.preventDefault()}
        >
          Add Payment Method
        </Typography>

        <TextField
          fullWidth
          size='small'
          label='Email Address'
          sx={{ mt: 2.75, mb: 3.5 }}
          placeholder='john.doe@email.com'
        />
        <Button fullWidth variant='contained' endIcon={<Icon icon='mdi:arrow-right' />}>
          Proceed to payment
        </Button>
      </CardContent>
    </Card>
  )
}

export default CardPlanUpgrade
