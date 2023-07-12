// ** React Imports
import { MouseEvent, useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'
import ToggleButton from '@mui/material/ToggleButton'
import ToggleButtonGroup from '@mui/material/ToggleButtonGroup'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ButtonToggleEnforceValue = () => {
  // ** States
  const [formats, setFormats] = useState<string[]>(() => ['phone'])
  const [alignment, setAlignment] = useState<string | null>('left')

  const handleAlignment = (event: MouseEvent<HTMLElement>, newAlignment: string | null) => {
    if (newAlignment !== null) {
      setAlignment(newAlignment)
    }
  }

  const handleFormat = (event: MouseEvent<HTMLElement>, newFormats: string[]) => {
    if (newFormats.length) {
      setFormats(newFormats)
    }
  }

  return (
    <Grid container spacing={6}>
      <Grid item xs={12} sm={6}>
        <Typography sx={{ fontWeight: 500, mb: 2 }}>Exclusive Selection</Typography>
        <ToggleButtonGroup exclusive value={alignment} onChange={handleAlignment} aria-label='text alignment'>
          <ToggleButton value='left' aria-label='left aligned'>
            <Icon icon='mdi:format-align-left' />
          </ToggleButton>
          <ToggleButton value='center' aria-label='center aligned'>
            <Icon icon='mdi:format-align-center' />
          </ToggleButton>
          <ToggleButton value='right' aria-label='right aligned'>
            <Icon icon='mdi:format-align-right' />
          </ToggleButton>
          <ToggleButton value='justify' aria-label='justified' disabled>
            <Icon icon='mdi:format-align-justify' />
          </ToggleButton>
        </ToggleButtonGroup>
      </Grid>
      <Grid item xs={12} sm={6}>
        <Typography sx={{ fontWeight: 500, mb: 2 }}>Multiple Selection</Typography>
        <ToggleButtonGroup value={formats} onChange={handleFormat} aria-label='device'>
          <ToggleButton value='laptop' aria-label='laptop'>
            <Icon icon='mdi:laptop' />
          </ToggleButton>
          <ToggleButton value='desktop' aria-label='desktop'>
            <Icon icon='mdi:monitor' />
          </ToggleButton>
          <ToggleButton value='phone' aria-label='phone'>
            <Icon icon='mdi:cellphone' />
          </ToggleButton>
        </ToggleButtonGroup>
      </Grid>
    </Grid>
  )
}

export default ButtonToggleEnforceValue
