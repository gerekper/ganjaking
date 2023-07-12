// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Grid from '@mui/material/Grid'
import Badge from '@mui/material/Badge'
import Button from '@mui/material/Button'
import { useTheme } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

// ** Third Party Components
import clsx from 'clsx'
import { useKeenSlider } from 'keen-slider/react'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'

const data = [
  {
    title: 'Mobiles & Computers',
    img: '/images/cards/apple-iphone-x.png',
    details: {
      Mobiles: '24',
      Accessories: '50',
      Tablets: '12',
      Computers: '38'
    }
  },
  {
    title: 'Appliances & Electronics',
    img: '/images/cards/ps4-joystick.png',
    details: {
      "TV's": '16',
      Cameras: '9',
      Speakers: '40',
      Consoles: '18'
    }
  },
  {
    title: 'Fashion',
    img: '/images/cards/apple-watch-green.png',
    details: {
      'T-shirts': '16',
      Shoes: '43',
      Watches: '29',
      SunGlasses: '7'
    }
  }
]

const Slides = () => {
  return (
    <>
      {data.map((slide, index) => {
        return (
          <Box key={index} className='keen-slider__slide'>
            <Box sx={{ mb: 4.5, display: 'flex', alignItems: 'center' }}>
              <Box component='img' src={slide.img} alt={slide.title} sx={{ mr: 5, width: 84, borderRadius: 1 }} />
              <div>
                <Typography sx={{ mb: 2.5, fontWeight: 600 }}>{slide.title}</Typography>
                <Grid container spacing={2.5}>
                  {Object.keys(slide.details).map((key, index) => (
                    <Grid item xs={6} key={index}>
                      <Box sx={{ display: 'flex', alignItems: 'center' }}>
                        <CustomAvatar
                          skin='light'
                          color='secondary'
                          variant='rounded'
                          sx={{
                            mr: 1.5,
                            width: 36,
                            height: 24,
                            fontSize: '0.75rem',
                            borderRadius: '6px',
                            color: 'text.primary'
                          }}
                        >
                          {slide.details[key]}
                        </CustomAvatar>
                        <Typography variant='caption'>{key}</Typography>
                      </Box>
                    </Grid>
                  ))}
                </Grid>
              </div>
            </Box>
            <div>
              <Button size='small' sx={{ mr: 3.5 }} variant='outlined'>
                Details
              </Button>
              <Button size='small' variant='contained'>
                Report
              </Button>
            </div>
          </Box>
        )
      })}
    </>
  )
}

const CardStatisticsWeeklySales = () => {
  // ** States
  const [loaded, setLoaded] = useState(false)
  const [currentSlide, setCurrentSlide] = useState(0)

  // ** Hook
  const theme = useTheme()

  const [sliderRef, instanceRef] = useKeenSlider({
    initial: 0,
    slides: {
      spacing: 16
    },
    rtl: theme.direction === 'rtl',
    slideChanged(slider) {
      setCurrentSlide(slider.track.details.rel)
    },
    created() {
      setLoaded(true)
    }
  })

  return (
    <Card>
      <CardHeader
        title='Weekly Sales'
        titleTypographyProps={{ variant: 'h6' }}
        sx={{ '& .swiper-dots': { mt: 0.75, mr: -1.75 } }}
        subheader={
          <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { color: 'success.main' } }}>
            <Typography variant='caption' sx={{ mr: 1.5 }}>
              Total $23.5k Earning
            </Typography>
            <Typography variant='subtitle2' sx={{ color: 'success.main' }}>
              +62%
            </Typography>
            <Icon icon='mdi:chevron-up' fontSize={20} />
          </Box>
        }
        action={
          loaded &&
          instanceRef.current && (
            <Box className='swiper-dots'>
              {[...Array(instanceRef.current.track.details.slides.length).keys()].map(idx => {
                return (
                  <Badge
                    key={idx}
                    variant='dot'
                    component='div'
                    className={clsx({ active: currentSlide === idx })}
                    onClick={() => instanceRef.current?.moveToIdx(idx)}
                    sx={{
                      mr: theme => `${theme.spacing(2.5)} !important`,
                      '& .MuiBadge-dot': {
                        minWidth: 6,
                        width: '6px !important',
                        height: '6px !important'
                      }
                    }}
                  ></Badge>
                )
              })}
            </Box>
          )
        }
      />
      <CardContent>
        <Box ref={sliderRef} className='keen-slider'>
          <Slides />
        </Box>
      </CardContent>
    </Card>
  )
}

export default CardStatisticsWeeklySales
