// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import DatePicker from 'react-datepicker'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersOptions = ({ popperPlacement }) => {
  // ** States
  const [dateOpen, setDateOpen] = useState(null)
  const [dateClear, setDateClear] = useState(new Date())
  const [dateFilter, setDateFilter] = useState(new Date())
  const [dateWeekNum, setDateWeekNum] = useState(new Date())
  const [dateTodayBtn, setDateTodayBtn] = useState(new Date())

  const isWeekday = date => {
    const day = new Date(date).getDay()

    return day !== 0 && day !== 6
  }

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          isClearable
          id='picker-clear'
          selected={dateClear}
          popperPlacement={popperPlacement}
          customInput={<CustomInput label='Clear' />}
          onChange={date => setDateClear(date)}
        />
      </div>
      <div>
        <DatePicker
          showWeekNumbers
          id='picker-week-num'
          selected={dateWeekNum}
          popperPlacement={popperPlacement}
          onChange={date => setDateWeekNum(date)}
          customInput={<CustomInput label='Week Numbers' />}
        />
      </div>
      <div>
        <DatePicker
          id='picker-filter'
          selected={dateFilter}
          filterDate={isWeekday}
          popperPlacement={popperPlacement}
          onChange={date => setDateFilter(date)}
          customInput={<CustomInput label='Filter Dates' />}
        />
      </div>
      <div>
        <DatePicker
          selected={dateOpen}
          id='picker-open-date'
          popperPlacement={popperPlacement}
          openToDate={new Date('1993/09/28')}
          onChange={date => setDateOpen(date)}
          customInput={<CustomInput label='Open To Date' />}
        />
      </div>
      <div>
        <DatePicker
          todayButton='Today'
          selected={dateTodayBtn}
          id='picker-date-today-btn'
          popperPlacement={popperPlacement}
          onChange={date => setDateTodayBtn(date)}
          customInput={<CustomInput label='Date Today Button' />}
        />
      </div>
    </Box>
  )
}

export default PickersOptions
