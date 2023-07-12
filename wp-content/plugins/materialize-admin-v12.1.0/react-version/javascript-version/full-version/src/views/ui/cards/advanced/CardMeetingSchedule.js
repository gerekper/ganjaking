// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Avatar from '@mui/material/Avatar'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components
import CustomChip from 'src/@core/components/mui/chip'
import OptionsMenu from 'src/@core/components/option-menu'

const data = [
  {
    chipText: 'Business',
    chipColor: 'primary',
    title: 'Call with Woods',
    src: '/images/avatars/4.png',
    subtitle: '21 Jul | 08:20-10:30'
  },
  {
    chipText: 'Dinner',
    chipColor: 'warning',
    title: 'Conference call',
    src: '/images/avatars/5.png',
    subtitle: '28 Jul | 05:00-6:45'
  },
  {
    chipText: 'Meetup',
    chipColor: 'secondary',
    title: 'Meeting with Mark',
    src: '/images/avatars/3.png',
    subtitle: '03 Aug | 07:00-8:30'
  },
  {
    chipText: 'Dinner',
    chipColor: 'error',
    title: 'Meeting in Oakland',
    src: '/images/avatars/2.png',
    subtitle: '14 Feb | 04:15-05:30'
  },
  {
    chipColor: 'success',
    chipText: 'Meditation',
    title: 'Call with Hilda',
    src: '/images/avatars/8.png',
    subtitle: '24 Jul | 11:30-12:00'
  },
  {
    chipText: 'Business',
    chipColor: 'primary',
    title: 'Meeting with Carl',
    src: '/images/avatars/7.png',
    subtitle: '05 Oct | 10:00-12:45'
  }
]

const CardMeetingSchedule = () => {
  return (
    <Card>
      <CardHeader
        title='Meeting Schedule'
        action={
          <OptionsMenu
            options={['Refresh', 'Share', 'Reschedule']}
            iconButtonProps={{ size: 'small', sx: { color: 'text.primary' } }}
          />
        }
      />
      <CardContent>
        {data.map((item, index) => {
          return (
            <Box
              key={item.title}
              sx={{
                display: 'flex',
                alignItems: 'center',
                mb: index !== data.length - 1 ? 7 : undefined
              }}
            >
              <Avatar src={item.src} variant='rounded' sx={{ mr: 3, width: 38, height: 38 }} />
              <Box
                sx={{
                  width: '100%',
                  display: 'flex',
                  flexWrap: 'wrap',
                  alignItems: 'center',
                  justifyContent: 'space-between'
                }}
              >
                <Box sx={{ mr: 2, display: 'flex', mb: 0.4, flexDirection: 'column' }}>
                  <Typography variant='body2' sx={{ mb: 0.5, fontWeight: 600, color: 'text.primary' }}>
                    {item.title}
                  </Typography>
                  <Box
                    sx={{
                      display: 'flex',
                      alignItems: 'center',
                      '& svg': { mr: 1.5, color: 'text.secondary', verticalAlign: 'middle' }
                    }}
                  >
                    <Icon fontSize='1rem' icon='mdi:calendar-blank-outline' />
                    <Typography variant='caption'>{item.subtitle}</Typography>
                  </Box>
                </Box>
                <CustomChip
                  skin='light'
                  size='small'
                  label={item.chipText}
                  color={item.chipColor}
                  sx={{ height: 20, fontSize: '0.75rem', fontWeight: 500 }}
                />
              </Box>
            </Box>
          )
        })}
      </CardContent>
    </Card>
  )
}

export default CardMeetingSchedule
