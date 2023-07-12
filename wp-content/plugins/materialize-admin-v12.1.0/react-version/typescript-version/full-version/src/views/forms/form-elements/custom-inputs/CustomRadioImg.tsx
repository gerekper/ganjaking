// ** React Imports
import { ChangeEvent, useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Type Import
import { CustomRadioImgData } from 'src/@core/components/custom-radio/types'

// ** Demo Components Imports
import CustomRadioImg from 'src/@core/components/custom-radio/image'

const data: CustomRadioImgData[] = [
  {
    value: 'clock',
    isSelected: true,
    img: '/images/pages/background-3.jpg'
  },
  {
    value: 'donuts',
    img: '/images/pages/background-8.jpg'
  },
  {
    value: 'flowers',
    img: '/images/pages/background-5.jpg'
  }
]

const CustomRadioWithImages = () => {
  const initialSelected: string = data.filter(item => item.isSelected)[data.filter(item => item.isSelected).length - 1]
    .value

  // ** State
  const [selected, setSelected] = useState<string>(initialSelected)

  const handleChange = (prop: string | ChangeEvent<HTMLInputElement>) => {
    if (typeof prop === 'string') {
      setSelected(prop)
    } else {
      setSelected((prop.target as HTMLInputElement).value)
    }
  }

  return (
    <Grid container spacing={4}>
      {data.map((item, index) => (
        <CustomRadioImg
          key={index}
          data={data[index]}
          selected={selected}
          name='custom-radios-img'
          handleChange={handleChange}
          gridProps={{ sm: 4, xs: 12 }}
        />
      ))}
    </Grid>
  )
}

export default CustomRadioWithImages
