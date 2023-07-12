// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import DatePicker from 'react-datepicker'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersMonthYearDropdowns = ({ popperPlacement }) => {
  // ** States
  const [year, setYear] = useState(new Date())
  const [month, setMonth] = useState(new Date())
  const [monthYear, setMonthYear] = useState(new Date())

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          selected={month}
          showMonthDropdown
          id='month-dropdown'
          placeholderText='MM-DD-YYYY'
          popperPlacement={popperPlacement}
          onChange={date => setMonth(date)}
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
          onChange={date => setYear(date)}
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
          onChange={date => setMonthYear(date)}
          customInput={<CustomInput label='Month & Year Dropdown' />}
        />
      </div>
    </Box>
  )
}

export default PickersMonthYearDropdowns
