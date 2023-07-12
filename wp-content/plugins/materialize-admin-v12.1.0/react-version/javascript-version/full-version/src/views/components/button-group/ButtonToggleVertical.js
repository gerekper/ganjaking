// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import ToggleButton from '@mui/material/ToggleButton'
import ToggleButtonGroup from '@mui/material/ToggleButtonGroup'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ButtonToggleVertical = () => {
  // ** State
  const [view, setView] = useState('left')

  const handleView = (event, newView) => {
    setView(newView)
  }

  return (
    <ToggleButtonGroup exclusive value={view} orientation='vertical' onChange={handleView} aria-label='text alignment'>
      <ToggleButton value='left' aria-label='left aligned'>
        <Icon icon='mdi:view-list' />
      </ToggleButton>
      <ToggleButton value='center' aria-label='center aligned'>
        <Icon icon='mdi:view-module' />
      </ToggleButton>
      <ToggleButton value='right' aria-label='right aligned'>
        <Icon icon='mdi:view-quilt' />
      </ToggleButton>
    </ToggleButtonGroup>
  )
}

export default ButtonToggleVertical
