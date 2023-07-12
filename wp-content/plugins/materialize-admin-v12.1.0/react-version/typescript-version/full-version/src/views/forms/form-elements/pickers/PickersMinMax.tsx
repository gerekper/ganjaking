// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import subDays from 'date-fns/subDays'
import addDays from 'date-fns/addDays'
import DatePicker, { ReactDatePickerProps } from 'react-datepicker'

// ** Types
import { DateType } from 'src/types/forms/reactDatepickerTypes'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersMinMax = ({ popperPlacement }: { popperPlacement: ReactDatePickerProps['popperPlacement'] }) => {
  // ** States
  const [minDate, setMinDate] = useState<DateType>(new Date())
  const [maxDate, setMaxDate] = useState<DateType>(new Date())

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          id='min-date'
          selected={minDate}
          minDate={subDays(new Date(), 5)}
          popperPlacement={popperPlacement}
          onChange={(date: Date) => setMinDate(date)}
          customInput={<CustomInput label='Min Date' />}
        />
      </div>
      <div>
        <DatePicker
          id='max-date'
          selected={maxDate}
          maxDate={addDays(new Date(), 5)}
          popperPlacement={popperPlacement}
          onChange={(date: Date) => setMaxDate(date)}
          customInput={<CustomInput label='Max Date' />}
        />
      </div>
    </Box>
  )
}

export default PickersMinMax
