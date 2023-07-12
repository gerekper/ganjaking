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
    title: 'Marketing Expense',
    img: '/images/cards/marketing-expense-logo.png',
    details: {
      Operating: '5k',
      Financial: '2k',
      COGF: '6k',
      Expense: '1k'
    }
  },
  {
    title: 'Accounting',
    img: '/images/cards/accounting-logo.png',
    details: {
      Billing: '18',
      Sales: '28',
      Leads: '30',
      Impression: '80'
    }
  },
  {
    title: 'Sales Overview',
    img: '/images/cards/sales-overview-logo.png',
    details: {
      Open: '68',
      Converted: '52',
      Lost: '04',
      Quotations: '12'
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
              <Box sx={{ width: '100%' }}>
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
              </Box>
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

const CardStatisticsMarketingSales = () => {
  // ** States
  const [loaded, setLoaded] = useState(false)
  const [currentSlide, setCurrentSlide] = useState(0)

  // ** Hook
  const theme = useTheme()

  const [sliderRef, instanceRef] = useKeenSlider({
    initial: 0,
    rtl: theme.direction === 'rtl',
    slides: {
      spacing: 16
    },
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
        title='Marketing & Sales'
        titleTypographyProps={{ variant: 'h6' }}
        sx={{ '& .swiper-dots': { mt: 0.75, mr: -1.75 } }}
        subheader={
          <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { color: 'success.main' } }}>
            <Typography variant='caption' sx={{ mr: 1.5 }}>
              Total 245.8k Sales
            </Typography>
            <Typography variant='subtitle2' sx={{ color: 'success.main' }}>
              +25%
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
                    className={clsx({
                      active: currentSlide === idx
                    })}
                    onClick={() => {
                      instanceRef.current?.moveToIdx(idx)
                    }}
                    sx={{
                      mr: theme => `${theme.spacing(2.5)} !important`,
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

export default CardStatisticsMarketingSales
