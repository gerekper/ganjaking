// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import { useTheme } from '@mui/material/styles'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'
import ReactApexcharts from 'src/@core/components/react-apexcharts'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const CardWidgetsOrganicSessions = () => {
  // ** Hook
  const theme = useTheme()

  const options = {
    chart: {
      sparkline: { enabled: true }
    },
    colors: [
      theme.palette.warning.main,
      hexToRGBA(theme.palette.warning.main, 0.8),
      hexToRGBA(theme.palette.warning.main, 0.6),
      hexToRGBA(theme.palette.warning.main, 0.4),
      hexToRGBA(theme.palette.warning.main, 0.2)
    ],
    legend: { show: false },
    tooltip: { enabled: false },
    dataLabels: { enabled: false },
    stroke: { width: 3, lineCap: 'round', colors: [theme.palette.background.paper] },
    labels: ['USA', 'India', 'Canada', 'Japan', 'France'],
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
        endAngle: 130,
        startAngle: -130,
        customScale: 0.9,
        donut: {
          size: '83%',
          labels: {
            show: true,
            name: {
              offsetY: 25,
              fontSize: '1rem',
              color: theme.palette.text.secondary
            },
            value: {
              offsetY: -15,
              fontWeight: 500,
              fontSize: '2.125rem',
              formatter: value => `${value}k`,
              color: theme.palette.text.primary
            },
            total: {
              show: true,
              label: '2022',
              fontSize: '1rem',
              color: theme.palette.text.secondary,
              formatter: value => `${value.globals.seriesTotals.reduce((total, num) => total + num)}k`
            }
          }
        }
      }
    },
    responsive: [
      {
        breakpoint: 1709,
        options: {
          chart: { height: 270 }
        }
      }
    ]
  }

  return (
    <Card>
      <CardHeader
        title='Organic Sessions'
        action={
          <OptionsMenu
            options={['Last 28 Days', 'Last Month', 'Last Year']}
            iconButtonProps={{ size: 'small', className: 'card-more-options' }}
          />
        }
      />
      <CardContent>
        <ReactApexcharts type='donut' height={290} options={options} series={[13, 18, 18, 24, 16]} />
        <Box sx={{ display: 'flex', flexWrap: 'wrap', alignItems: 'center', justifyContent: 'center' }}>
          <Box sx={{ mx: 3, display: 'flex', alignItems: 'center', '& svg': { mr: 1.25, color: 'warning.main' } }}>
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>USA</Typography>
          </Box>
          <Box
            sx={{
              mx: 3,
              display: 'flex',
              alignItems: 'center',
              '& svg': { mr: 1.25, color: hexToRGBA(theme.palette.warning.main, 0.8) }
            }}
          >
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>India</Typography>
          </Box>
          <Box
            sx={{
              mx: 3,
              display: 'flex',
              alignItems: 'center',
              '& svg': { mr: 1.25, color: hexToRGBA(theme.palette.warning.main, 0.6) }
            }}
          >
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>Canada</Typography>
          </Box>
          <Box
            sx={{
              mx: 3,
              display: 'flex',
              alignItems: 'center',
              '& svg': { mr: 1.25, color: hexToRGBA(theme.palette.warning.main, 0.4) }
            }}
          >
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>Japan</Typography>
          </Box>
          <Box
            sx={{
              mx: 3,
              display: 'flex',
              alignItems: 'center',
              '& svg': { mr: 1.25, color: hexToRGBA(theme.palette.warning.main, 0.2) }
            }}
          >
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>France</Typography>
          </Box>
        </Box>
      </CardContent>
    </Card>
  )
}

export default CardWidgetsOrganicSessions
