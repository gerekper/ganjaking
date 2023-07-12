// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Badge from '@mui/material/Badge'
import { Direction } from '@mui/material'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Third Party Components
import clsx from 'clsx'
import { useKeenSlider } from 'keen-slider/react'

const SwiperControls = ({ direction }: { direction: Direction }) => {
  // ** States
  const [loaded, setLoaded] = useState<boolean>(false)
  const [currentSlide, setCurrentSlide] = useState<number>(0)

  // ** Hook
  const [sliderRef, instanceRef] = useKeenSlider<HTMLDivElement>({
    rtl: direction === 'rtl',
    slideChanged(slider) {
      setCurrentSlide(slider.track.details.rel)
    },
    created() {
      setLoaded(true)
    }
  })

  return (
    <>
      <Box className='navigation-wrapper'>
        <Box ref={sliderRef} className='keen-slider'>
          <Box className='keen-slider__slide'>
            <img src='/images/banners/banner-1.jpg' alt='swiper 1' />
          </Box>
          <Box className='keen-slider__slide'>
            <img src='/images/banners/banner-2.jpg' alt='swiper 2' />
          </Box>
          <Box className='keen-slider__slide'>
            <img src='/images/banners/banner-3.jpg' alt='swiper 3' />
          </Box>
          <Box className='keen-slider__slide'>
            <img src='/images/banners/banner-4.jpg' alt='swiper 4' />
          </Box>
          <Box className='keen-slider__slide'>
            <img src='/images/banners/banner-5.jpg' alt='swiper 5' />
          </Box>
        </Box>
        {loaded && instanceRef.current && (
          <>
            <Icon
              icon='mdi:chevron-left'
              className={clsx('arrow arrow-left', {
                'arrow-disabled': currentSlide === 0
              })}
              onClick={(e: any) => e.stopPropagation() || instanceRef.current?.prev()}
            />

            <Icon
              icon='mdi:chevron-right'
              className={clsx('arrow arrow-right', {
                'arrow-disabled': currentSlide === instanceRef.current.track.details.slides.length - 1
              })}
              onClick={(e: any) => e.stopPropagation() || instanceRef.current?.next()}
            />
          </>
        )}
      </Box>
      {loaded && instanceRef.current && (
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
              ></Badge>
            )
          })}
        </Box>
      )}
    </>
  )
}

export default SwiperControls
