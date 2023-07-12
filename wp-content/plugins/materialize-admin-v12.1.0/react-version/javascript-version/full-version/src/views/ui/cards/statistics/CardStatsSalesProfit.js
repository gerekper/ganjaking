// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Grid from '@mui/material/Grid'
import Divider from '@mui/material/Divider'
import { useTheme } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import ReactApexcharts from 'src/@core/components/react-apexcharts'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const seriesSales = [
  {
    data: [0, 15, 0, 17, 5, 30]
  }
]

const seriesProfit = [
  {
    data: [5, 25, 0, 30, 15, 30]
  }
]

const CardStatsSalesProfit = () => {
  // ** Hook
  const theme = useTheme()

  const optionsSales = {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    grid: {
      show: false,
      padding: {
        left: -7
      }
    },
    tooltip: { enabled: false },
    colors: [hexToRGBA(theme.palette.success.main, 1)],
    markers: {
      size: 5,
      offsetY: 1,
      offsetX: -2,
      strokeWidth: 2,
      strokeOpacity: 1,
      colors: ['transparent'],
      strokeColors: 'transparent',
      discrete: [
        {
          size: 5,
          seriesIndex: 0,
          strokeColor: theme.palette.success.main,
          fillColor: theme.palette.background.paper,
          dataPointIndex: seriesSales[0].data.length - 1
        }
      ]
    },
    stroke: {
      width: 3,
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

  const optionsProfit = {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    grid: {
      show: false,
      padding: {
        left: -5
      }
    },
    tooltip: { enabled: false },
    colors: [hexToRGBA(theme.palette.error.main, 1)],
    markers: {
      size: 5,
      offsetY: 0,
      offsetX: -2,
      strokeWidth: 2,
      strokeOpacity: 1,
      colors: ['transparent'],
      strokeColors: 'transparent',
      discrete: [
        {
          size: 5,
          seriesIndex: 0,
          strokeColor: theme.palette.error.main,
          fillColor: theme.palette.background.paper,
          dataPointIndex: seriesSales[0].data.length - 1
        }
      ]
    },
    stroke: {
      width: 3,
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
      <CardContent sx={{ py: 3 }}>
        <Grid container spacing={5}>
          <Grid item xs={6}>
            <ReactApexcharts type='line' height={90} series={seriesSales} options={optionsSales} />
          </Grid>
          <Grid item xs={6} sx={{ display: 'flex', flexDirection: 'column', justifyContent: 'center' }}>
            <Box sx={{ display: 'flex', flexWrap: 'wrap', alignItems: 'center' }}>
              <Typography variant='h6' sx={{ mr: 1.75 }}>
                152k
              </Typography>
              <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { color: 'success.main' } }}>
                <Typography variant='subtitle2' sx={{ color: 'success.main' }}>
                  +12%
                </Typography>
                <Icon icon='mdi:chevron-up' fontSize={20} />
              </Box>
            </Box>
            <Typography variant='body2'>Total Sales</Typography>
          </Grid>
        </Grid>
      </CardContent>
      <Divider sx={{ my: '0 !important' }} />
      <CardContent sx={{ py: theme => `${theme.spacing(3)} !important` }}>
        <Grid container spacing={5}>
          <Grid item xs={6}>
            <ReactApexcharts type='line' height={90} series={seriesProfit} options={optionsProfit} />
          </Grid>
          <Grid item xs={6} sx={{ display: 'flex', flexDirection: 'column', justifyContent: 'center' }}>
            <Box sx={{ display: 'flex', flexWrap: 'wrap', alignItems: 'center' }}>
              <Typography variant='h6' sx={{ mr: 1.75 }}>
                89.5k
              </Typography>
              <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { color: 'error.main' } }}>
                <Typography variant='subtitle2' sx={{ color: 'error.main' }}>
                  -8%
                </Typography>
                <Icon icon='mdi:chevron-down' fontSize={20} />
              </Box>
            </Box>
            <Typography variant='body2'>Total Profit</Typography>
          </Grid>
        </Grid>
      </CardContent>
    </Card>
  )
}

export default CardStatsSalesProfit
