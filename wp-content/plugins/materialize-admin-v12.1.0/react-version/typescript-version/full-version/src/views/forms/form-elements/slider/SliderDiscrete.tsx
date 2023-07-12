// ** MUI Imports
import Slider from '@mui/material/Slider'

const valuetext = (value: number) => {
  return `${value}°C`
}

const SliderDiscrete = () => {
  return (
    <Slider
      marks
      min={10}
      max={110}
      step={10}
      defaultValue={30}
      valueLabelDisplay='auto'
      getAriaValueText={valuetext}
      aria-labelledby='discrete-slider'
    />
  )
}

export default SliderDiscrete
