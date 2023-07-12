// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import DatePicker, { ReactDatePickerProps } from 'react-datepicker'

// ** Types
import { DateType } from 'src/types/forms/reactDatepickerTypes'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersMonthYearDropdowns = ({
  popperPlacement
}: {
  popperPlacement: ReactDatePickerProps['popperPlacement']
}) => {
  // ** States
  const [year, setYear] = useState<DateType>(new Date())
  const [month, setMonth] = useState<DateType>(new Date())
  const [monthYear, setMonthYear] = useState<DateType>(new Date())

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          selected={month}
          showMonthDropdown
          id='month-dropdown'
          placeholderText='MM-DD-YYYY'
          popperPlacement={popperPlacement}
          onChange={(date: Date) => setMonth(date)}
          customInput={<CustomInput label='Month Dropdown' />}
        />
      </div>
      <div>
        <DatePicker
          selected={year}
          showYearDropdown
          id='year-dropdown'
          placeholderText='MM-DD-YYYY'
          popperPlacement={popperPlacement}
          onChange={(date: Date) => setYear(date)}
          customInput={<CustomInput label='Year Dropdown' />}
        />
      </div>
      <div>
        <DatePicker
          showYearDropdown
          showMonthDropdown
          selected={monthYear}
          id='month-year-dropdown'
          placeholderText='MM-DD-YYYY'
          popperPlacement={popperPlacement}
          onChange={(date: Date) => setMonthYear(date)}
          customInput={<CustomInput label='Month & Year Dropdown' />}
        />
      </div>
    </Box>
  )
}

export default PickersMonthYearDropdowns
