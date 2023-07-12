// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'

// ** Third Party Imports
import { Locale } from 'date-fns'
import fr from 'date-fns/locale/fr'
import ar from 'date-fns/locale/ar-SA'
import en from 'date-fns/locale/en-US'
import { useTranslation } from 'react-i18next'
import DatePicker, { registerLocale, ReactDatePickerProps } from 'react-datepicker'

// ** Types
import { DateType } from 'src/types/forms/reactDatepickerTypes'

// ** Custom Component Imports
import CustomInput from './PickersCustomInput'

const langObj: { [key: string]: Locale } = { fr, ar, en }

const PickersLocale = ({ popperPlacement }: { popperPlacement: ReactDatePickerProps['popperPlacement'] }) => {
  // ** States
  const [date, setDate] = useState<DateType>(new Date())
  const [time, setTime] = useState<DateType>(new Date())

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
          onChange={(date: Date) => setDate(date)}
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
          onChange={(date: Date) => setTime(date)}
          customInput={<CustomInput label='Locale Time' />}
        />
      </div>
    </Box>
  )
}

export default PickersLocale
