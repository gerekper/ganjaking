// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Slider from '@mui/material/Slider'
import Typography from '@mui/material/Typography'

const SliderControlledUncontrolled = () => {
  // ** State
  const [value, setValue] = useState<number>(30)

  return (
    <div>
      <Typography sx={{ fontWeight: 500 }}>Controlled Slider</Typography>
      <Slider
        value={value}
        aria-labelledby='controlled-slider'
        onChange={(event, newValue: number | number[]) => setValue(newValue as number)}
      />
      <Typography sx={{ fontWeight: 500 }}>Uncontrolled Slider</Typography>
      <Slider defaultValue={30} aria-labelledby='uncontrolled-slider' />
    </div>
  )
}

export default SliderControlledUncontrolled
