// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import Grid from '@mui/material/Grid'
import { styled, useTheme } from '@mui/material/styles'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'
import OptionsMenu from 'src/@core/components/option-menu'
import ReactApexcharts from 'src/@core/components/react-apexcharts'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

// Styled Grid component
const StyledGrid = styled(Grid)(({ theme }) => ({
  [theme.breakpoints.down('sm')]: {
    borderBottom: `1px solid ${theme.palette.divider}`
  },
  [theme.breakpoints.up('sm')]: {
    borderRight: `1px solid ${theme.palette.divider}`
  }
}))
const labels = ['Development Apps', 'UI Design', 'IOS Application', 'Web App Wireframing', 'Prototyping']

const series = [
  {
    data: [
      {
        x: 'Catherine',
        y: [
          new Date(`${new Date().getFullYear()}-01-01`).getTime(),
          new Date(`${new Date().getFullYear()}-04-02`).getTime()
        ]
      },
      {
        x: 'Janelle',
        y: [
          new Date(`${new Date().getFullYear()}-02-18`).getTime(),
          new Date(`${new Date().getFullYear()}-05-30`).getTime()
        ]
      },
      {
        x: 'Wellington',
        y: [
          new Date(`${new Date().getFullYear()}-02-07`).getTime(),
          new Date(`${new Date().getFullYear()}-04-31`).getTime()
        ]
      },
      {
        x: 'Blake',
        y: [
          new Date(`${new Date().getFullYear()}-01-14`).getTime(),
          new Date(`${new Date().getFullYear()}-06-30`).getTime()
        ]
      },
      {
        x: 'Quinn',
        y: [
          new Date(`${new Date().getFullYear()}-04-01`).getTime(),
          new Date(`${new Date().getFullYear()}-07-31`).getTime()
        ]
      }
    ]
  }
]

const CardWidgetsProjectTimeline = () => {
  // ** Hook
  const theme = useTheme()

  const options = {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    tooltip: { enabled: false },
    plotOptions: {
      bar: {
        horizontal: true,
        borderRadius: 15,
        distributed: true,
        endingShape: 'rounded',
        startingShape: 'rounded'
      }
    },
    stroke: {
      width: 2,
      colors: [theme.palette.background.paper]
    },
    colors: [
      hexToRGBA(theme.palette.primary.main, 1),
      hexToRGBA(theme.palette.success.main, 1),
      hexToRGBA(theme.palette.secondary.main, 1),
      hexToRGBA(theme.palette.info.main, 1),
      hexToRGBA(theme.palette.warning.main, 1)
    ],
    dataLabels: {
      enabled: true,
      style: { fontWeight: 400 },
      formatter: (val, opts) => labels[opts.dataPointIndex]
    },
    states: {
      hover: {
        filter: { type: 'none' }
      },
      active: {
        filter: { type: 'none' }
      }
    },
    legend: { show: false },
    grid: {
      strokeDashArray: 6,
      borderColor: theme.palette.divider,
      xaxis: {
        lines: { show: true }
      },
      yaxis: {
        lines: { show: false }
      },
      padding: {
        top: -22,
        left: 20,
        right: 18,
        bottom: 4
      }
    },
    xaxis: {
      type: 'datetime',
      axisTicks: { show: false },
      axisBorder: { show: false },
      labels: {
        style: { colors: theme.palette.text.disabled },
        datetimeFormatter: {
          year: 'MMM',
          month: 'MMM'
        }
      }
    },
    yaxis: {
      labels: {
        show: true,
        align: theme.direction === 'rtl' ? 'right' : 'left',
        style: {
          fontSize: '0.875rem',
          colors: theme.palette.text.primary
        }
      }
    }
  }

  return (
    <Card>
      <Grid container>
        <StyledGrid item xs={12} sm={8}>
          <CardHeader
            title='Project Timeline'
            subheader='Total 840 Task Completed'
            subheaderTypographyProps={{ sx: { lineHeight: 1.429 } }}
            titleTypographyProps={{ sx: { letterSpacing: '0.15px' } }}
          />
          <ReactApexcharts height={240} type='rangeBar' series={series} options={options} />
        </StyledGrid>
        <Grid item xs={12} sm={4}>
          <CardHeader
            title='Project List'
            subheader='3 Ongoing Projects'
            subheaderTypographyProps={{ sx: { lineHeight: 1.429 } }}
            titleTypographyProps={{ sx: { letterSpacing: '0.15px' } }}
            action={
              <OptionsMenu
                options={['Refresh', 'Update', 'Share']}
                iconButtonProps={{ size: 'small', className: 'card-more-options' }}
              />
            }
          />
          <CardContent sx={{ pt: `${theme.spacing(5)} !important` }}>
            <Box sx={{ mb: 7.5, display: 'flex', alignItems: 'center' }}>
              <CustomAvatar skin='light' variant='rounded' sx={{ mr: 3, width: 45, height: 45 }}>
                <Icon icon='mdi:cellphone' />
              </CustomAvatar>
              <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                <Typography variant='body2' sx={{ mb: 0.5, fontWeight: 600, color: 'text.primary' }}>
                  IOS Application
                </Typography>
                <Typography variant='caption'>Task 840/2.5k</Typography>
              </Box>
            </Box>
            <Box sx={{ mb: 7.5, display: 'flex', alignItems: 'center' }}>
              <CustomAvatar skin='light' color='success' variant='rounded' sx={{ mr: 3, width: 45, height: 45 }}>
                <Icon icon='mdi:creation' />
              </CustomAvatar>
              <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                <Typography variant='body2' sx={{ mb: 0.5, fontWeight: 600, color: 'text.primary' }}>
                  Web Application
                </Typography>
                <Typography variant='caption'>Task 99/1.42k</Typography>
              </Box>
            </Box>
            <Box sx={{ display: 'flex', alignItems: 'center' }}>
              <CustomAvatar skin='light' color='info' variant='rounded' sx={{ mr: 3, width: 45, height: 45 }}>
                <Icon icon='mdi:pencil-ruler' />
              </CustomAvatar>
              <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                <Typography variant='body2' sx={{ mb: 0.5, fontWeight: 600, color: 'text.primary' }}>
                  UI Kit Design
                </Typography>
                <Typography variant='caption'>Task 120/350</Typography>
              </Box>
            </Box>
          </CardContent>
        </Grid>
      </Grid>
    </Card>
  )
}

export default CardWidgetsProjectTimeline
