// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import DatePicker from 'react-datepicker'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersTime = ({ popperPlacement }) => {
  // ** States
  const [time, setTime] = useState(new Date())
  const [dateTime, setDateTime] = useState(new Date())

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          showTimeSelect
          selected={time}
          timeIntervals={15}
          showTimeSelectOnly
          dateFormat='h:mm aa'
          id='time-only-picker'
          popperPlacement={popperPlacement}
          onChange={date => setTime(date)}
          customInput={<CustomInput label='Time Only' />}
        />
      </div>
      <div>
        <DatePicker
          showTimeSelect
          timeFormat='HH:mm'
          timeIntervals={15}
          selected={dateTime}
          id='date-time-picker'
          dateFormat='MM/dd/yyyy h:mm aa'
          popperPlacement={popperPlacement}
          onChange={date => setDateTime(date)}
          customInput={<CustomInput label='Date & Time' />}
        />
      </div>
    </Box>
  )
}

export default PickersTime
