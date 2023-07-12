// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import toast from 'react-hot-toast'
import DatePicker, { ReactDatePickerProps } from 'react-datepicker'

// ** Types
import { DateType } from 'src/types/forms/reactDatepickerTypes'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersCallbacks = ({ popperPlacement }: { popperPlacement: ReactDatePickerProps['popperPlacement'] }) => {
  // ** States
  const [date, setDate] = useState<DateType>(new Date())

  const handlePickerCallback = (msg: string) => {
    toast(msg, { duration: 2000 })
  }

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          selected={date}
          id='callback-open'
          dateFormat='MM/dd/yyyy'
          popperPlacement={popperPlacement}
          onChange={(date: Date) => setDate(date)}
          customInput={<CustomInput label='Open & Closed' />}
          onCalendarOpen={() => handlePickerCallback(`Selected Date: ${new Date(date || '').toLocaleDateString()}`)}
          onCalendarClose={() => handlePickerCallback(`Selected Date: ${new Date(date || '').toLocaleDateString()}`)}
        />
      </div>
      <div>
        <DatePicker
          selected={date}
          id='callback-blur'
          popperPlacement={popperPlacement}
          onChange={(date: Date) => setDate(date)}
          customInput={<CustomInput label='Blur' />}
          onBlur={() => handlePickerCallback('Picker Closed')}
        />
      </div>
      <div>
        <DatePicker
          selected={date}
          id='callback-change'
          popperPlacement={popperPlacement}
          customInput={<CustomInput label='onChange' />}
          onChange={(date: Date) => {
            setDate(date)
            handlePickerCallback(`Selected Date: ${new Date(date || '').toLocaleDateString()}`)
          }}
        />
      </div>
    </Box>
  )
}

export default PickersCallbacks
