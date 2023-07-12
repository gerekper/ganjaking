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

const PickersCustomization = ({ popperPlacement }: { popperPlacement: ReactDatePickerProps['popperPlacement'] }) => {
  // ** States
  const [dateFormat, setDateFormat] = useState<DateType>(new Date())
  const [dateHighlight, setDateHighlight] = useState<DateType>(new Date())

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          id='custom-format'
          selected={dateFormat}
          dateFormat='MMMM d, yyyy h:mm aa'
          popperPlacement={popperPlacement}
          onChange={(date: Date) => setDateFormat(date)}
          customInput={<CustomInput label='Custom Date Format' />}
        />
      </div>
      <div>
        <DatePicker
          id='highlight-dates'
          selected={dateHighlight}
          popperPlacement={popperPlacement}
          onChange={(date: Date) => setDateHighlight(date)}
          customInput={<CustomInput label='Highlight Dates' />}
          highlightDates={[subDays(new Date(), 7), addDays(new Date(), 7)]}
        />
      </div>
    </Box>
  )
}

export default PickersCustomization
