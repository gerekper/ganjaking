// ** React Imports
import { forwardRef, useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Divider from '@mui/material/Divider'
import TextField from '@mui/material/TextField'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import InputAdornment from '@mui/material/InputAdornment'

// ** Third Party Imports
import format from 'date-fns/format'
import DatePicker from 'react-datepicker'
import { AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, TooltipProps } from 'recharts'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Types
import { DateType } from 'src/types/forms/reactDatepickerTypes'

interface Props {
  direction: 'ltr' | 'rtl'
}

interface PickerProps {
  start: Date | number
  end: Date | number
}

const data = [
  {
    name: '7/12',
    Sales: 20,
    Clicks: 60,
    Visits: 100
  },
  {
    name: '8/12',
    Sales: 40,
    Clicks: 80,
    Visits: 120
  },
  {
    name: '9/12',
    Sales: 30,
    Clicks: 70,
    Visits: 90
  },
  {
    name: '10/12',
    Sales: 70,
    Clicks: 110,
    Visits: 170
  },
  {
    name: '11/12',
    Sales: 40,
    Clicks: 80,
    Visits: 130
  },
  {
    name: '12/12',
    Sales: 60,
    Clicks: 80,
    Visits: 160
  },
  {
    name: '13/12',
    Sales: 50,
    Clicks: 100,
    Visits: 140
  },
  {
    name: '14/12',
    Sales: 140,
    Clicks: 90,
    Visits: 240
  },
  {
    name: '15/12',
    Sales: 120,
    Clicks: 180,
    Visits: 220
  },
  {
    name: '16/12',
    Sales: 100,
    Clicks: 160,
    Visits: 180
  },
  {
    name: '17/12',
    Sales: 140,
    Clicks: 140,
    Visits: 270
  },
  {
    name: '18/12',
    Sales: 180,
    Clicks: 200,
    Visits: 280
  },
  {
    name: '19/12',
    Sales: 220,
    Clicks: 220,
    Visits: 375
  }
]

const CustomTooltip = (data: TooltipProps<any, any>) => {
  const { active, payload } = data

  if (active && payload) {
    return (
      <div className='recharts-custom-tooltip'>
        <Typography>{data.label}</Typography>
        <Divider />
        {data &&
          data.payload &&
          data.payload.map((i: any) => {
            return (
              <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { color: i.fill, mr: 2.5 } }} key={i.dataKey}>
                <Icon icon='mdi:circle' fontSize='0.6rem' />
                <Typography variant='body2'>{`${i.dataKey} : ${i.payload[i.dataKey]}`}</Typography>
              </Box>
            )
          })}
      </div>
    )
  }

  return null
}

const RechartsAreaChart = ({ direction }: Props) => {
  // ** States
  const [endDate, setEndDate] = useState<DateType>(null)
  const [startDate, setStartDate] = useState<DateType>(null)

  const CustomInput = forwardRef((props: PickerProps, ref) => {
    const startDate = props.start !== null ? format(props.start, 'MM/dd/yyyy') : ''
    const endDate = props.end !== null ? ` - ${format(props.end, 'MM/dd/yyyy')}` : null

    const value = `${startDate}${endDate !== null ? endDate : ''}`

    return (
      <TextField
        {...props}
        size='small'
        value={value}
        inputRef={ref}
        InputProps={{
          startAdornment: (
            <InputAdornment position='start'>
              <Icon icon='mdi:bell-outline' />
            </InputAdornment>
          ),
          endAdornment: (
            <InputAdornment position='end'>
              <Icon icon='mdi:chevron-down' />
            </InputAdornment>
          )
        }}
      />
    )
  })

  const handleOnChange = (dates: any) => {
    const [start, end] = dates
    setStartDate(start)
    setEndDate(end)
  }

  return (
    <Card>
      <CardHeader
        title='Website Data'
        sx={{
          flexDirection: ['column', 'row'],
          alignItems: ['flex-start', 'center'],
          '& .MuiCardHeader-action': { mb: 0 },
          '& .MuiCardHeader-content': { mb: [2, 0] }
        }}
        action={
          <DatePicker
            selectsRange
            endDate={endDate}
            id='recharts-area'
            selected={startDate}
            startDate={startDate}
            onChange={handleOnChange}
            placeholderText='Click to select a date'
            customInput={<CustomInput start={startDate as Date | number} end={endDate as Date | number} />}
          />
        }
      />
      <CardContent>
        <Box sx={{ display: 'flex', mb: 4 }}>
          <Box sx={{ mr: 6, display: 'flex', alignItems: 'center', '& svg': { mr: 1.5, color: 'rgb(115, 103, 240)' } }}>
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>Click</Typography>
          </Box>
          <Box
            sx={{
              mr: 6,
              display: 'flex',
              alignItems: 'center',
              '& svg': { mr: 1.5, color: 'rgba(115, 103, 240, .5)' }
            }}
          >
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>Sales</Typography>
          </Box>
          <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { mr: 1.5, color: 'rgba(115, 103, 240, .2)' } }}>
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>Visits</Typography>
          </Box>
        </Box>
        <Box sx={{ height: 350 }}>
          <ResponsiveContainer>
            <AreaChart height={350} data={data} style={{ direction }} margin={{ left: -20 }}>
              <CartesianGrid />
              <XAxis dataKey='name' reversed={direction === 'rtl'} />
              <YAxis orientation={direction === 'rtl' ? 'right' : 'left'} />
              <Tooltip content={CustomTooltip} />
              <Area dataKey='Clicks' stackId='Clicks' stroke='0' fill='rgb(115, 103, 240)' />
              <Area dataKey='Sales' stackId='Sales' stroke='0' fill='rgba(115, 103, 240, .5)' />
              <Area dataKey='Visits' stackId='Visits' stroke='0' fill='rgba(115, 103, 240, .2)' />
            </AreaChart>
          </ResponsiveContainer>
        </Box>
      </CardContent>
    </Card>
  )
}

export default RechartsAreaChart
