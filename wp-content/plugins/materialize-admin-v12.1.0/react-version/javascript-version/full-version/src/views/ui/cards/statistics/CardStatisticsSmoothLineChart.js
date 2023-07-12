// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import { useTheme } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Custom Components Imports
import ReactApexcharts from 'src/@core/components/react-apexcharts'

const series = [
  {
    data: [0, 30, 10, 70, 40, 110, 95]
  }
]

const CardStatsSmoothLineChart = () => {
  // ** Hook
  const theme = useTheme()

  const options = {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    grid: {
      show: false,
      padding: {
        left: -5,
        top: -15,
        right: 5,
        bottom: -10
      }
    },
    colors: [theme.palette.warning.main],
    stroke: {
      width: 4,
      curve: 'smooth',
      lineCap: 'round'
    },
    xaxis: {
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false }
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
            $22.6k
          </Typography>
          <Typography variant='subtitle2' sx={{ color: 'success.main' }}>
            +38%
          </Typography>
        </Box>
        <Typography variant='body2'>Total Sales</Typography>
        <ReactApexcharts type='line' height={108} options={options} series={series} />
      </CardContent>
    </Card>
  )
}

export default CardStatsSmoothLineChart
