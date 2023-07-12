// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Button from '@mui/material/Button'
import { useTheme } from '@mui/material/styles'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'
import ReactApexcharts from 'src/@core/components/react-apexcharts'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const series = [
  {
    name: 'Sales',
    type: 'column',
    data: [83, 68, 56, 65, 65, 50, 39]
  },
  {
    type: 'line',
    name: 'Sales',
    data: [63, 38, 31, 45, 46, 27, 18]
  }
]

const CrmWeeklyOverview = () => {
  // ** Hook
  const theme = useTheme()

  const options = {
    chart: {
      offsetY: -9,
      offsetX: -16,
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    plotOptions: {
      bar: {
        borderRadius: 8,
        columnWidth: '35%',
        endingShape: 'rounded',
        startingShape: 'rounded',
        colors: {
          ranges: [
            {
              to: 50,
              from: 40,
              color: hexToRGBA(theme.palette.primary.main, 1)
            }
          ]
        }
      }
    },
    markers: {
      size: 3.5,
      strokeWidth: 2,
      fillOpacity: 1,
      strokeOpacity: 1,
      colors: [theme.palette.background.paper],
      strokeColors: hexToRGBA(theme.palette.primary.main, 1)
    },
    stroke: {
      width: [0, 2],
      colors: [theme.palette.customColors.trackBg, theme.palette.primary.main]
    },
    legend: { show: false },
    dataLabels: { enabled: false },
    colors: [hexToRGBA(theme.palette.customColors.trackBg, 1)],
    grid: {
      strokeDashArray: 7,
      borderColor: theme.palette.divider
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
      categories: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      tickPlacement: 'on',
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false }
    },
    yaxis: {
      min: 0,
      max: 90,
      show: true,
      tickAmount: 3,
      labels: {
        formatter: value => `${value > 999 ? `${(value / 1000).toFixed(0)}` : value}k`,
        style: {
          fontSize: '0.75rem',
          colors: theme.palette.text.disabled
        }
      }
    }
  }

  return (
    <Card>
      <CardHeader
        title='Weekly Overview'
        action={
          <OptionsMenu
            options={['Refresh', 'Update', 'Share']}
            iconButtonProps={{ size: 'small', className: 'card-more-options' }}
          />
        }
      />
      <CardContent sx={{ '& .apexcharts-xcrosshairs.apexcharts-active': { opacity: 0 } }}>
        <ReactApexcharts type='line' height={208} series={series} options={options} />
        <Box sx={{ mb: 4, display: 'flex', alignItems: 'center' }}>
          <Typography sx={{ mr: 4 }} variant='h5'>
            62%
          </Typography>
          <Typography variant='body2'>Your sales performance is 35% 😎 better compared to last month</Typography>
        </Box>
        <Button fullWidth variant='contained'>
          Details
        </Button>
      </CardContent>
    </Card>
  )
}

export default CrmWeeklyOverview
