// ** MUI Imports
import Checkbox from '@mui/material/Checkbox'
import FormGroup from '@mui/material/FormGroup'
import FormControlLabel from '@mui/material/FormControlLabel'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const CheckboxesCustomIcons = () => {
  return (
    <FormGroup row>
      <FormControlLabel
        label='Heart'
        control={
          <Checkbox
            defaultChecked
            name='size-small'
            checkedIcon={<Icon icon='mdi:heart' fontSize={24} />}
            icon={<Icon icon='mdi:heart-outline' fontSize={24} />}
          />
        }
      />
      <FormControlLabel
        label='Star'
        control={
          <Checkbox
            defaultChecked
            name='size-small'
            checkedIcon={<Icon icon='mdi:star' fontSize={24} />}
            icon={<Icon icon='mdi:star-outline' fontSize={24} />}
          />
        }
      />
    </FormGroup>
  )
}

export default CheckboxesCustomIcons
