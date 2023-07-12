// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import { useTheme } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Custom Components Imports
import ReactApexcharts from 'src/@core/components/react-apexcharts'

const CardStatsDonutChart = () => {
  // ** Hook
  const theme = useTheme()

  const options = {
    legend: { show: false },
    stroke: { width: 5, colors: [theme.palette.background.paper] },
    colors: [theme.palette.primary.main, theme.palette.success.main, theme.palette.secondary.main],
    labels: [`${new Date().getFullYear()}`, `${new Date().getFullYear() - 1}`, `${new Date().getFullYear() - 2}`],
    tooltip: {
      y: { formatter: val => `${val}%` }
    },
    dataLabels: {
      enabled: false
    },
    states: {
      hover: {
        filter: { type: 'none' }
      },
      active: {
        filter: { type: 'none' }
      }
    },
    plotOptions: {
      pie: {
        donut: {
          size: '70%',
          labels: {
            show: true,
            name: { show: false },
            total: {
              label: '',
              show: true,
              fontWeight: 600,
              fontSize: '1rem',
              color: theme.palette.text.secondary,
              formatter: val => (typeof val === 'string' ? `${val}%` : '12%')
            },
            value: {
              offsetY: 6,
              fontWeight: 600,
              fontSize: '1rem',
              formatter: val => `${val}%`,
              color: theme.palette.text.secondary
            }
          }
        }
      }
    }
  }

  return (
    <Card>
      <CardContent>
        <Box sx={{ display: 'flex', flexWrap: 'wrap', alignItems: 'center' }}>
          <Typography variant='h6' sx={{ mr: 1.5 }}>
            $27.9k
          </Typography>
          <Typography variant='subtitle2' sx={{ color: 'success.main' }}>
            +16%
          </Typography>
        </Box>
        <Typography variant='body2'>Total Growth</Typography>
        <ReactApexcharts type='donut' height={135} options={options} series={[35, 30, 23]} />
      </CardContent>
    </Card>
  )
}

export default CardStatsDonutChart
