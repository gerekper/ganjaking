// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Grid from '@mui/material/Grid'
import Badge from '@mui/material/Badge'
import { useTheme } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
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
    title: 'Fashion',
    img: '/images/cards/apple-watch-green-lg.png',
    details: {
      'T-shirts': '16',
      Shoes: '43',
      Watches: '29',
      SunGlasses: '7'
    }
  },
  {
    title: 'Mobiles & Computers',
    img: '/images/cards/apple-iphone-x-lg.png',
    details: {
      Mobiles: '24',
      Accessories: '50',
      Tablets: '12',
      Computers: '38'
    }
  },
  {
    title: 'Appliances & Electronics',
    img: '/images/cards/ps4-joystick-lg.png',
    details: {
      "TV's": '16',
      Cameras: '9',
      Speakers: '40',
      Consoles: '18'
    }
  }
]

const Slides = () => {
  return (
    <>
      {data.map((slide, index) => {
        return (
          <Box key={index} className='keen-slider__slide'>
            <Typography variant='h6' sx={{ color: 'common.white' }}>
              Weekly Sales
            </Typography>
            <Box sx={{ mb: 4, display: 'flex', alignItems: 'center', '& svg': { color: 'success.main' } }}>
              <Typography variant='caption' sx={{ mr: 1.5, color: 'common.white' }}>
                Total $23.5k Earning
              </Typography>
              <Typography variant='subtitle2' sx={{ color: 'success.main' }}>
                +62%
              </Typography>
              <Icon icon='mdi:chevron-up' fontSize={20} />
            </Box>
            <Grid container>
              <Grid item xs={12} sm={6} lg={8} sx={{ order: [2, 1] }}>
                <Typography sx={{ mb: 4.5, color: 'common.white' }}>{slide.title}</Typography>
                <Grid container spacing={4}>
                  {Object.keys(slide.details).map((key, index) => {
                    return (
                      <Grid item key={index} xs={6}>
                        <Box sx={{ display: 'flex', alignItems: 'center' }}>
                          <CustomAvatar
                            color='primary'
                            variant='rounded'
                            sx={{
                              mr: 2,
                              width: 40,
                              height: 30,
                              fontSize: '0.875rem',
                              color: 'common.white',
                              backgroundColor: 'primary.dark'
                            }}
                          >
                            {slide.details[key]}
                          </CustomAvatar>
                          <Typography variant='caption' sx={{ color: 'common.white' }}>
                            {key}
                          </Typography>
                        </Box>
                      </Grid>
                    )
                  })}
                </Grid>
              </Grid>
              <Grid
                item
                xs={12}
                sm={6}
                lg={4}
                sx={{
                  order: [1, 2],
                  textAlign: 'center',
                  '& img': {
                    top: 0,
                    right: 0,
                    height: '200px !important',
                    maxWidth: 'none !important',
                    position: ['static', 'absolute']
                  }
                }}
              >
                <img src={slide.img} alt={slide.title} />
              </Grid>
            </Grid>
          </Box>
        )
      })}
    </>
  )
}

const EcommerceWeeklySalesBg = () => {
  // ** States
  const [loaded, setLoaded] = useState(false)
  const [currentSlide, setCurrentSlide] = useState(0)

  // ** Hook
  const theme = useTheme()

  const [sliderRef, instanceRef] = useKeenSlider(
    {
      loop: true,
      initial: 0,
      rtl: theme.direction === 'rtl',
      slideChanged(slider) {
        setCurrentSlide(slider.track.details.rel)
      },
      created() {
        setLoaded(true)
      }
    },
    [
      slider => {
        let mouseOver = false
        let timeout

        const clearNextTimeout = () => {
          clearTimeout(timeout)
        }

        const nextTimeout = () => {
          clearTimeout(timeout)
          if (mouseOver) return
          timeout = setTimeout(() => {
            slider.next()
          }, 4000)
        }
        slider.on('created', () => {
          slider.container.addEventListener('mouseover', () => {
            mouseOver = true
            clearNextTimeout()
          })
          slider.container.addEventListener('mouseout', () => {
            mouseOver = false
            nextTimeout()
          })
          nextTimeout()
        })
        slider.on('dragStarted', clearNextTimeout)
        slider.on('animationEnded', nextTimeout)
        slider.on('updated', nextTimeout)
      }
    ]
  )

  return (
    <Card sx={{ position: 'relative', backgroundColor: 'primary.main' }}>
      <CardContent>
        {loaded && instanceRef.current && (
          <Box className='swiper-dots' sx={{ top: 7, right: 13, position: 'absolute' }}>
            {[...Array(instanceRef.current.track.details.slides.length).keys()].map(idx => {
              return (
                <Badge
                  key={idx}
                  variant='dot'
                  component='div'
                  className={clsx({
                    active: currentSlide === idx
                  })}
                  onClick={() => {
                    instanceRef.current?.moveToIdx(idx)
                  }}
                  sx={{
                    mr: theme => `${theme.spacing(2.5)} !important`,
                    '&.active': {
                      '& .MuiBadge-dot': {
                        backgroundColor: theme => `${theme.palette.common.white} !important`
                      }
                    },
                    '& .MuiBadge-dot': {
                      height: '6px !important',
                      width: '6px !important',
                      minWidth: '6px !important'
                    }
                  }}
                ></Badge>
              )
            })}
          </Box>
        )}
        <Box ref={sliderRef} className='keen-slider'>
          <Slides />
        </Box>
      </CardContent>
    </Card>
  )
}

export default EcommerceWeeklySalesBg
