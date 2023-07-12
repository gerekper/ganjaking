// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import DatePicker from 'react-datepicker'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersBasic = ({ popperPlacement }) => {
  // ** States
  const [date, setDate] = useState(new Date())

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          selected={date}
          id='basic-input'
          popperPlacement={popperPlacement}
          onChange={date => setDate(date)}
          placeholderText='Click to select a date'
          customInput={<CustomInput label='Basic' />}
        />
      </div>
      <div>
        <DatePicker
          disabled
          selected={date}
          id='disabled-input'
          popperPlacement={popperPlacement}
          onChange={date => setDate(date)}
          placeholderText='Click to select a date'
          customInput={<CustomInput label='Disabled' />}
        />
      </div>
      <div>
        <DatePicker
          readOnly
          selected={date}
          id='read-only-input'
          popperPlacement={popperPlacement}
          onChange={date => setDate(date)}
          placeholderText='Click to select a date'
          customInput={<CustomInput readOnly label='Readonly' />}
        />
      </div>
    </Box>
  )
}

export default PickersBasic
