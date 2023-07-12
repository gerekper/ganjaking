// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Demo Components Imports
import CustomRadioBasic from 'src/@core/components/custom-radio/basic'

const data = [
  {
    meta: 'Free',
    title: 'Basic',
    value: 'basic',
    isSelected: true,
    content: 'Get 1 project with 1 team member.'
  },
  {
    meta: '$5.00',
    title: 'Premium',
    value: 'premium',
    content: 'Get 5 projects with 5 team members.'
  }
]

const BasicCustomRadio = () => {
  const initialSelected = data.filter(item => item.isSelected)[data.filter(item => item.isSelected).length - 1].value

  // ** State
  const [selected, setSelected] = useState(initialSelected)

  const handleChange = prop => {
    if (typeof prop === 'string') {
      setSelected(prop)
    } else {
      setSelected(prop.target.value)
    }
  }

  return (
    <Grid container spacing={4}>
      {data.map((item, index) => (
        <CustomRadioBasic
          key={index}
          data={data[index]}
          selected={selected}
          name='custom-radios-basic'
          handleChange={handleChange}
          gridProps={{ sm: 6, xs: 12 }}
        />
      ))}
    </Grid>
  )
}

export default BasicCustomRadio
