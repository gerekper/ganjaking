// ** React Imports
import { ChangeEvent, useState } from 'react'

// ** MUI Imports
import Checkbox from '@mui/material/Checkbox'
import FormGroup from '@mui/material/FormGroup'
import FormControlLabel from '@mui/material/FormControlLabel'

const CheckboxesControlledUncontrolled = () => {
  // ** State
  const [checked, setChecked] = useState<boolean>(true)

  const handleChange = (event: ChangeEvent<HTMLInputElement>) => {
    setChecked(event.target.checked)
  }

  return (
    <FormGroup row>
      <FormControlLabel
        label='Controlled'
        control={<Checkbox checked={checked} onChange={handleChange} name='controlled' />}
      />
      <FormControlLabel label='Uncontrolled' control={<Checkbox defaultChecked name='uncontrolled' />} />
    </FormGroup>
  )
}

export default CheckboxesControlledUncontrolled
