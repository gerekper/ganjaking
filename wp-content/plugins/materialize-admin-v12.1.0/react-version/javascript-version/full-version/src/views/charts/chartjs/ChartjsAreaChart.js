// ** React Imports
import { forwardRef, useState } from 'react'

// ** MUI Imports
import Card from '@mui/material/Card'
import TextField from '@mui/material/TextField'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import InputAdornment from '@mui/material/InputAdornment'

// ** Third Party Imports
import format from 'date-fns/format'
import { Line } from 'react-chartjs-2'
import DatePicker from 'react-datepicker'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ChartjsAreaChart = props => {
  // ** Props
  const { blue, white, blueLight, greyLight, labelColor, borderColor, legendColor } = props

  // ** States
  const [endDate, setEndDate] = useState(null)
  const [startDate, setStartDate] = useState(null)

  const options = {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
      padding: { top: -20 }
    },
    scales: {
      x: {
        grid: {
          color: borderColor
        },
        ticks: { color: labelColor }
      },
      y: {
        min: 0,
        max: 400,
        grid: {
          color: borderColor
        },
        ticks: {
          stepSize: 100,
          color: labelColor
        }
      }
    },
    plugins: {
      legend: {
        align: 'start',
        position: 'top',
        labels: {
          padding: 25,
          boxWidth: 9,
          color: legendColor,
          usePointStyle: true
        }
      }
    }
  }

  const data = {
    labels: [
      '7/12',
      '8/12',
      '9/12',
      '10/12',
      '11/12',
      '12/12',
      '13/12',
      '14/12',
      '15/12',
      '16/12',
      '17/12',
      '18/12',
      '19/12',
      '20/12',
      ''
    ],
    datasets: [
      {
        fill: true,
        tension: 0,
        label: 'Africa',
        pointRadius: 0.5,
        pointHoverRadius: 5,
        pointStyle: 'circle',
        backgroundColor: blue,
        pointHoverBorderWidth: 5,
        borderColor: 'transparent',
        pointHoverBorderColor: white,
        pointBorderColor: 'transparent',
        pointHoverBackgroundColor: blue,
        data: [40, 55, 45, 75, 65, 55, 70, 60, 100, 98, 90, 120, 125, 140, 155]
      },
      {
        fill: true,
        tension: 0,
        label: 'Asia',
        pointRadius: 0.5,
        pointHoverRadius: 5,
        pointStyle: 'circle',
        pointHoverBorderWidth: 5,
        borderColor: 'transparent',
        backgroundColor: blueLight,
        pointHoverBorderColor: white,
        pointBorderColor: 'transparent',
        pointHoverBackgroundColor: blueLight,
        data: [70, 85, 75, 150, 100, 140, 110, 105, 160, 150, 125, 190, 200, 240, 275]
      },
      {
        fill: true,
        tension: 0,
        label: 'Europe',
        pointRadius: 0.5,
        pointHoverRadius: 5,
        pointStyle: 'circle',
        pointHoverBorderWidth: 5,
        borderColor: 'transparent',
        backgroundColor: greyLight,
        pointHoverBorderColor: white,
        pointBorderColor: 'transparent',
        pointHoverBackgroundColor: greyLight,
        data: [240, 195, 160, 215, 185, 215, 185, 200, 250, 210, 195, 250, 235, 300, 315]
      }
    ]
  }

  const CustomInput = forwardRef((props, ref) => {
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
              <Icon icon='mdi:calendar-outline' />
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

  const handleOnChange = dates => {
    const [start, end] = dates
    setStartDate(start)
    setEndDate(end)
  }

  return (
    <Card>
      <CardHeader
        title='Data Science'
        sx={{
          flexDirection: ['column', 'row'],
          alignItems: ['flex-start', 'center'],
          '& .MuiCardHeader-action': { mb: 0 },
          '& .MuiCardHeader-content': { mb: [2, 0] }
        }}
        action={
          <DatePicker
            selectsRange
            id='chartjs-area'
            endDate={endDate}
            selected={startDate}
            startDate={startDate}
            onChange={handleOnChange}
            placeholderText='Click to select a date'
            customInput={<CustomInput start={startDate} end={endDate} />}
          />
        }
      />
      <CardContent>
        <Line data={data} height={450} options={options} />
      </CardContent>
    </Card>
  )
}

export default ChartjsAreaChart
