// ** MUI Imports
import Slider from '@mui/material/Slider'

const valuetext = (value: number) => {
  return `${value}°C`
}

const SliderRange = () => {
  return (
    <Slider
      defaultValue={[20, 37]}
      valueLabelDisplay='auto'
      getAriaValueText={valuetext}
      aria-labelledby='range-slider'
    />
  )
}

export default SliderRange
