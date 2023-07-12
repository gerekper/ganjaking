// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomChip from 'src/@core/components/mui/chip'
import OptionsMenu from 'src/@core/components/option-menu'

const data = [
  {
    amount: '12,348',
    chipText: '+12%',
    title: 'Facebook',
    imgAlt: 'facebook',
    chipColor: 'success',
    subtitle: 'Social Media',
    imgSrc: '/images/cards/social-facebook.png'
  },
  {
    amount: '8,450',
    chipText: '+32%',
    title: 'Dribbble',
    imgAlt: 'dribbble',
    chipColor: 'success',
    subtitle: 'Community',
    imgSrc: '/images/cards/social-dribbble.png'
  },
  {
    amount: '350',
    chipText: '-18%',
    title: 'Twitter',
    imgAlt: 'twitter',
    chipColor: 'error',
    subtitle: 'Social Media',
    imgSrc: '/images/cards/social-twitter.png'
  },
  {
    amount: '25,566',
    chipText: '+45%',
    title: 'Instagram',
    imgAlt: 'instagram',
    chipColor: 'success',
    subtitle: 'Social Media',
    imgSrc: '/images/cards/social-instagram.png'
  }
]

const CardSocialNetworkVisits = () => {
  return (
    <Card>
      <CardHeader
        title='Social Network Visits'
        titleTypographyProps={{ sx: { lineHeight: '2rem !important', letterSpacing: '0.15px !important' } }}
        action={
          <OptionsMenu
            options={['Last 28 Days', 'Last Month', 'Last Year']}
            iconButtonProps={{ size: 'small', className: 'card-more-options' }}
          />
        }
      />
      <CardContent>
        <Box sx={{ mb: 0.5, display: 'flex', alignItems: 'center', '& svg': { mr: 0.5, color: 'success.main' } }}>
          <Typography variant='h5' sx={{ mr: 0.5 }}>
            28,468
          </Typography>
          <Icon icon='mdi:menu-up' fontSize='1.875rem' />
          <Typography variant='body2' sx={{ fontWeight: 600, color: 'success.main' }}>
            62%
          </Typography>
        </Box>

        <Typography component='p' variant='caption' sx={{ mb: 5 }}>
          Last 1 Year Visits
        </Typography>
        {data.map((item, index) => {
          return (
            <Box
              key={item.title}
              sx={{
                display: 'flex',
                alignItems: 'center',
                mb: index !== data.length - 1 ? 5.75 : undefined
              }}
            >
              <img width={34} height={34} alt={item.imgAlt} src={item.imgSrc} />
              <Box
                sx={{ ml: 3, width: '100%', display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}
              >
                <Box sx={{ mr: 2, display: 'flex', flexDirection: 'column' }}>
                  <Typography variant='body2' sx={{ fontWeight: 600, color: 'text.primary' }}>
                    {item.title}
                  </Typography>
                  <Typography variant='caption'>{item.subtitle}</Typography>
                </Box>
                <Box sx={{ display: 'flex', flexWrap: 'wrap', alignItems: 'center', justifyContent: 'flex-end' }}>
                  <Typography variant='body2' sx={{ fontWeight: 600, color: 'text.primary' }}>
                    {item.amount}
                  </Typography>
                  <CustomChip
                    skin='light'
                    size='small'
                    label={item.chipText}
                    color={item.chipColor}
                    sx={{ ml: 4.5, height: 20, fontSize: '0.75rem', fontWeight: 500 }}
                  />
                </Box>
              </Box>
            </Box>
          )
        })}
      </CardContent>
    </Card>
  )
}

export default CardSocialNetworkVisits
