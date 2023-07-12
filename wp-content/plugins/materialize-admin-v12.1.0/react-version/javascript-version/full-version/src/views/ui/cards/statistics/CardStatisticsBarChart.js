// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import { useTheme } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Custom Components Imports
import ReactApexcharts from 'src/@core/components/react-apexcharts'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const series = [
  {
    name: 'Earning',
    data: [120, 200, 150, 120]
  },
  {
    name: 'Expense',
    data: [72, 120, 50, 65]
  }
]

const CardStatisticsBarChart = () => {
  // ** Hook
  const theme = useTheme()

  const options = {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    grid: {
      padding: {
        top: -15,
        left: -14,
        right: -4,
        bottom: -15
      },
      yaxis: {
        lines: { show: false }
      }
    },
    legend: { show: false },
    dataLabels: { enabled: false },
    colors: [hexToRGBA(theme.palette.primary.main, 1), hexToRGBA(theme.palette.warning.main, 1)],
    plotOptions: {
      bar: {
        borderRadius: 5,
        columnWidth: '48%',
        startingShape: 'rounded'
      }
    },
    states: {
      hover: {
        filter: { type: 'none' }
      },
      active: {
        filter: { type: 'none' }
      }
    },
    xaxis: {
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false },
      categories: ['Jan', 'Feb', 'Mar', 'Apr']
    },
    yaxis: {
      labels: { show: false }
    }
  }

  return (
    <Card>
      <CardContent>
        <Box sx={{ display: 'flex', flexWrap: 'wrap', alignItems: 'center' }}>
          <Typography variant='h6' sx={{ mr: 1.5 }}>
            $42.5k
          </Typography>
          <Typography variant='subtitle2' sx={{ color: 'error.main' }}>
            -22%
          </Typography>
        </Box>
        <Typography variant='body2'>Total Revenue</Typography>
        <ReactApexcharts type='bar' height={108} options={options} series={series} />
      </CardContent>
    </Card>
  )
}

export default CardStatisticsBarChart
