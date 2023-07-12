// ** React Imports
import { MouseEvent, useState } from 'react'

// ** MUI Imports
import ToggleButton from '@mui/material/ToggleButton'
import ToggleButtonGroup from '@mui/material/ToggleButtonGroup'

const ButtonToggleColors = () => {
  // ** State
  const [alignment, setAlignment] = useState<string | null>('web')

  const handleAlignment = (event: MouseEvent<HTMLElement>, newAlignment: string | null) => {
    setAlignment(newAlignment)
  }

  return (
    <div className='demo-space-x'>
      <ToggleButtonGroup exclusive color='primary' value={alignment} onChange={handleAlignment}>
        <ToggleButton value='web'>Web</ToggleButton>
        <ToggleButton value='android'>Android</ToggleButton>
        <ToggleButton value='ios'>iOS</ToggleButton>
      </ToggleButtonGroup>

      <ToggleButtonGroup exclusive color='secondary' value={alignment} onChange={handleAlignment}>
        <ToggleButton value='web'>Web</ToggleButton>
        <ToggleButton value='android'>Android</ToggleButton>
        <ToggleButton value='ios'>iOS</ToggleButton>
      </ToggleButtonGroup>

      <ToggleButtonGroup exclusive color='success' value={alignment} onChange={handleAlignment}>
        <ToggleButton value='web'>Web</ToggleButton>
        <ToggleButton value='android'>Android</ToggleButton>
        <ToggleButton value='ios'>iOS</ToggleButton>
      </ToggleButtonGroup>

      <ToggleButtonGroup exclusive color='error' value={alignment} onChange={handleAlignment}>
        <ToggleButton value='web'>Web</ToggleButton>
        <ToggleButton value='android'>Android</ToggleButton>
        <ToggleButton value='ios'>iOS</ToggleButton>
      </ToggleButtonGroup>

      <ToggleButtonGroup exclusive color='warning' value={alignment} onChange={handleAlignment}>
        <ToggleButton value='web'>Web</ToggleButton>
        <ToggleButton value='android'>Android</ToggleButton>
        <ToggleButton value='ios'>iOS</ToggleButton>
      </ToggleButtonGroup>

      <ToggleButtonGroup exclusive color='info' value={alignment} onChange={handleAlignment}>
        <ToggleButton value='web'>Web</ToggleButton>
        <ToggleButton value='android'>Android</ToggleButton>
        <ToggleButton value='ios'>iOS</ToggleButton>
      </ToggleButtonGroup>
    </div>
  )
}

export default ButtonToggleColors
