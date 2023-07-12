// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import addDays from 'date-fns/addDays'
import setHours from 'date-fns/setHours'
import setMinutes from 'date-fns/setMinutes'
import DatePicker, { ReactDatePickerProps } from 'react-datepicker'

// ** Types
import { DateType } from 'src/types/forms/reactDatepickerTypes'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersSpecificRange = ({ popperPlacement }: { popperPlacement: ReactDatePickerProps['popperPlacement'] }) => {
  // ** States
  const [date, setDate] = useState<DateType>(new Date())
  const [time, setTime] = useState<DateType>(new Date())

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          selected={date}
          id='specific-date'
          minDate={new Date()}
          maxDate={addDays(new Date(), 5)}
          popperPlacement={popperPlacement}
          onChange={(date: Date) => setDate(date)}
          customInput={<CustomInput label='Specific Date Range' />}
        />
      </div>
      <div>
        <DatePicker
          showTimeSelect
          selected={time}
          id='specific-time'
          dateFormat='MM/dd/yyyy h:mm aa'
          popperPlacement={popperPlacement}
          onChange={(date: Date) => setTime(date)}
          minTime={setHours(setMinutes(new Date(), 0), 17)}
          maxTime={setHours(setMinutes(new Date(), 30), 20)}
          customInput={<CustomInput label='Specific Time' />}
        />
      </div>
    </Box>
  )
}

export default PickersSpecificRange
