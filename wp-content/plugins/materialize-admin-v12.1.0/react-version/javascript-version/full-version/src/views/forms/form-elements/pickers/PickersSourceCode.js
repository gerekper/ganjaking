export const PickersBasicJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
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
`}</code></pre>) 

export const PickersCallbacksJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import toast from 'react-hot-toast'
import DatePicker from 'react-datepicker'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersCallbacks = ({ popperPlacement }) => {
  // ** States
  const [date, setDate] = useState(new Date())

  const handlePickerCallback = msg => {
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
          onChange={date => setDate(date)}
          customInput={<CustomInput label='Open & Closed' />}
          onCalendarOpen={() => handlePickerCallback(Selected Date: {new Date(date || '').toLocaleDateString()})}
          onCalendarClose={() => handlePickerCallback(Selected Date: {new Date(date || '').toLocaleDateString()})}
        />
      </div>
      <div>
        <DatePicker
          selected={date}
          id='callback-blur'
          popperPlacement={popperPlacement}
          onChange={date => setDate(date)}
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
          onChange={date => {
            setDate(date)
            handlePickerCallback(Selected Date: {new Date(date || '').toLocaleDateString()})
          }}
        />
      </div>
    </Box>
  )
}

export default PickersCallbacks
`}</code></pre>) 

export const PickersIncludeExcludeJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import addDays from 'date-fns/addDays'
import subDays from 'date-fns/subDays'
import setHours from 'date-fns/setHours'
import DatePicker from 'react-datepicker'
import setMinutes from 'date-fns/setMinutes'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersIncludeExclude = ({ popperPlacement }) => {
  // ** States
  const [date, setDate] = useState(new Date())
  const [dateExclude, setDateExclude] = useState(new Date())
  const [time, setTime] = useState(setHours(setMinutes(new Date(), 0), 18))
  const [timeExclude, setTimeExclude] = useState(setHours(setMinutes(new Date(), 0), 18))

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          selected={date}
          id='include-dates'
          popperPlacement={popperPlacement}
          onChange={date => setDate(date)}
          customInput={<CustomInput label='Include Dates' />}
          includeDates={[new Date(), addDays(new Date(), 1)]}
        />
      </div>
      <div>
        <DatePicker
          id='exclude-dates'
          selected={dateExclude}
          popperPlacement={popperPlacement}
          onChange={date => setDateExclude(date)}
          customInput={<CustomInput label='Exclude Dates' />}
          excludeDates={[subDays(new Date(), 1), subDays(new Date(), 2)]}
        />
      </div>
      <div>
        <DatePicker
          showTimeSelect
          selected={time}
          id='include-time'
          dateFormat='MM/dd/yyyy h:mm aa'
          popperPlacement={popperPlacement}
          onChange={date => setTime(date)}
          customInput={<CustomInput label='Include Time' />}
          includeTimes={[
            setHours(setMinutes(new Date(), 0), 17),
            setHours(setMinutes(new Date(), 30), 18),
            setHours(setMinutes(new Date(), 30), 19),
            setHours(setMinutes(new Date(), 30), 17)
          ]}
        />
      </div>
      <div>
        <DatePicker
          showTimeSelect
          id='exclude-time'
          selected={timeExclude}
          dateFormat='MM/dd/yyyy h:mm aa'
          popperPlacement={popperPlacement}
          onChange={date => setTimeExclude(date)}
          customInput={<CustomInput label='Exclude Time' />}
          excludeTimes={[
            setHours(setMinutes(new Date(), 0), 17),
            setHours(setMinutes(new Date(), 30), 18),
            setHours(setMinutes(new Date(), 30), 19),
            setHours(setMinutes(new Date(), 30), 17)
          ]}
        />
      </div>
    </Box>
  )
}

export default PickersIncludeExclude
`}</code></pre>) 

export const PickersLocaleJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import fr from 'date-fns/locale/fr'
import ar from 'date-fns/locale/ar-SA'
import en from 'date-fns/locale/en-US'
import { useTranslation } from 'react-i18next'
import DatePicker, { registerLocale } from 'react-datepicker'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const langObj = { fr, ar, en }

const PickersLocale = ({ popperPlacement }) => {
  // ** States
  const [date, setDate] = useState(new Date())
  const [time, setTime] = useState(new Date())

  // ** Hooks
  const { i18n } = useTranslation()
  registerLocale(i18n.language, langObj[i18n.language])

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          selected={date}
          id='locale-picker'
          locale={i18n.language}
          popperPlacement={popperPlacement}
          onChange={date => setDate(date)}
          customInput={<CustomInput label='Locale Dates' />}
        />
      </div>
      <div>
        <DatePicker
          showTimeSelect
          selected={time}
          id='locale-time'
          locale={i18n.language}
          dateFormat='MM/dd/yyyy h:mm aa'
          popperPlacement={popperPlacement}
          onChange={date => setTime(date)}
          customInput={<CustomInput label='Locale Time' />}
        />
      </div>
    </Box>
  )
}

export default PickersLocale
`}</code></pre>) 

export const PickersMinMaxJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import subDays from 'date-fns/subDays'
import addDays from 'date-fns/addDays'
import DatePicker from 'react-datepicker'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersMinMax = ({ popperPlacement }) => {
  // ** States
  const [minDate, setMinDate] = useState(new Date())
  const [maxDate, setMaxDate] = useState(new Date())

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          id='min-date'
          selected={minDate}
          minDate={subDays(new Date(), 5)}
          popperPlacement={popperPlacement}
          onChange={date => setMinDate(date)}
          customInput={<CustomInput label='Min Date' />}
        />
      </div>
      <div>
        <DatePicker
          id='max-date'
          selected={maxDate}
          maxDate={addDays(new Date(), 5)}
          popperPlacement={popperPlacement}
          onChange={date => setMaxDate(date)}
          customInput={<CustomInput label='Max Date' />}
        />
      </div>
    </Box>
  )
}

export default PickersMinMax
`}</code></pre>) 

export const PickersCustomInputJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { forwardRef } from 'react'

// ** MUI Imports
import TextField from '@mui/material/TextField'

const PickersComponent = forwardRef(({ ...props }, ref) => {
  // ** Props
  const { label, readOnly } = props

  return (
    <TextField inputRef={ref} {...props} label={label || ''} {...(readOnly && { inputProps: { readOnly: true } })} />
  )
})

export default PickersComponent
`}</code></pre>) 

export const PickersMonthYearDropdownsJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
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
`}</code></pre>) 

export const PickersMonthYearQuarterJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import DatePicker from 'react-datepicker'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersMonthYear = ({ popperPlacement }) => {
  // ** States
  const [year, setYear] = useState(new Date())
  const [month, setMonth] = useState(new Date())
  const [quarter, setQuarter] = useState(new Date())

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          selected={month}
          id='month-picker'
          showMonthYearPicker
          dateFormat='MM/yyyy'
          popperPlacement={popperPlacement}
          onChange={date => setMonth(date)}
          customInput={<CustomInput label='Month Picker' />}
        />
      </div>
      <div>
        <DatePicker
          showYearPicker
          selected={year}
          id='year-picker'
          dateFormat='MM/yyyy'
          popperPlacement={popperPlacement}
          onChange={date => setYear(date)}
          customInput={<CustomInput label='Year Picker' />}
        />
      </div>
      <div>
        <DatePicker
          selected={quarter}
          id='quarter-picker'
          showQuarterYearPicker
          dateFormat='yyyy, QQQ'
          popperPlacement={popperPlacement}
          onChange={date => setQuarter(date)}
          customInput={<CustomInput label='Quarter Picker' />}
        />
      </div>
    </Box>
  )
}

export default PickersMonthYear
`}</code></pre>) 

export const PickersOptionsJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
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
`}</code></pre>) 

export const PickersRangeJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState, forwardRef } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import TextField from '@mui/material/TextField'

// ** Third Party Imports
import format from 'date-fns/format'
import addDays from 'date-fns/addDays'
import DatePicker from 'react-datepicker'

const PickersRange = ({ popperPlacement }) => {
  // ** States
  const [startDate, setStartDate] = useState(new Date())
  const [endDate, setEndDate] = useState(addDays(new Date(), 15))
  const [startDateRange, setStartDateRange] = useState(new Date())
  const [endDateRange, setEndDateRange] = useState(addDays(new Date(), 45))

  const handleOnChange = dates => {
    const [start, end] = dates
    setStartDate(start)
    setEndDate(end)
  }

  const handleOnChangeRange = dates => {
    const [start, end] = dates
    setStartDateRange(start)
    setEndDateRange(end)
  }

  const CustomInput = forwardRef((props, ref) => {
    const startDate = format(props.start, 'MM/dd/yyyy')
    const endDate = props.end !== null ?  - {format(props.end, 'MM/dd/yyyy')} : null
    const value = {startDate}{endDate !== null ? endDate : ''}

    return <TextField inputRef={ref} label={props.label || ''} {...props} value={value} />
  })

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          selectsRange
          endDate={endDate}
          selected={startDate}
          startDate={startDate}
          id='date-range-picker'
          onChange={handleOnChange}
          shouldCloseOnSelect={false}
          popperPlacement={popperPlacement}
          customInput={<CustomInput label='Date Range' start={startDate} end={endDate} />}
        />
      </div>
      <div>
        <DatePicker
          selectsRange
          monthsShown={2}
          endDate={endDateRange}
          selected={startDateRange}
          startDate={startDateRange}
          shouldCloseOnSelect={false}
          id='date-range-picker-months'
          onChange={handleOnChangeRange}
          popperPlacement={popperPlacement}
          customInput={<CustomInput label='Multiple Months' end={endDateRange} start={startDateRange} />}
        />
      </div>
    </Box>
  )
}

export default PickersRange
`}</code></pre>) 

export const PickersSpecificRangeJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import addDays from 'date-fns/addDays'
import setHours from 'date-fns/setHours'
import setMinutes from 'date-fns/setMinutes'
import DatePicker from 'react-datepicker'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersSpecificRange = ({ popperPlacement }) => {
  // ** States
  const [date, setDate] = useState(new Date())
  const [time, setTime] = useState(new Date())

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          selected={date}
          id='specific-date'
          minDate={new Date()}
          maxDate={addDays(new Date(), 5)}
          popperPlacement={popperPlacement}
          onChange={date => setDate(date)}
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
          onChange={date => setTime(date)}
          minTime={setHours(setMinutes(new Date(), 0), 17)}
          maxTime={setHours(setMinutes(new Date(), 30), 20)}
          customInput={<CustomInput label='Specific Time' />}
        />
      </div>
    </Box>
  )
}

export default PickersSpecificRange
`}</code></pre>) 

export const PickersCustomizationJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import subDays from 'date-fns/subDays'
import addDays from 'date-fns/addDays'
import DatePicker from 'react-datepicker'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const PickersCustomization = ({ popperPlacement }) => {
  // ** States
  const [dateFormat, setDateFormat] = useState(new Date())
  const [dateHighlight, setDateHighlight] = useState(new Date())

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap' }} className='demo-space-x'>
      <div>
        <DatePicker
          id='custom-format'
          selected={dateFormat}
          dateFormat='MMMM d, yyyy h:mm aa'
          popperPlacement={popperPlacement}
          onChange={date => setDateFormat(date)}
          customInput={<CustomInput label='Custom Date Format' />}
        />
      </div>
      <div>
        <DatePicker
          id='highlight-dates'
          selected={dateHighlight}
          popperPlacement={popperPlacement}
          onChange={date => setDateHighlight(date)}
          customInput={<CustomInput label='Highlight Dates' />}
          highlightDates={[subDays(new Date(), 7), addDays(new Date(), 7)]}
        />
      </div>
    </Box>
  )
}

export default PickersCustomization
`}</code></pre>) 

export const PickersTimeJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
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
`}</code></pre>) 

