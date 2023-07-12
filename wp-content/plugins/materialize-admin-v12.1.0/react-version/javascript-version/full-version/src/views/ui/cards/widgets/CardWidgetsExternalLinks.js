// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Table from '@mui/material/Table'
import TableRow from '@mui/material/TableRow'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import { useTheme } from '@mui/material/styles'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import TableContainer from '@mui/material/TableContainer'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'
import ReactApexcharts from 'src/@core/components/react-apexcharts'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const data = [
  {
    amount: '$845k',
    trendAmount: 82,
    color: 'primary',
    title: 'Google Analytics',
    icon: (
      <Box component='span' sx={{ display: 'flex', color: 'success.main' }}>
        <Icon icon='mdi:chevron-up' />
      </Box>
    )
  },
  {
    trendAmount: 52,
    amount: '$12.5k',
    color: 'secondary',
    title: 'Facebook Ads',
    icon: (
      <Box component='span' sx={{ display: 'flex', color: 'error.main' }}>
        <Icon icon='mdi:chevron-down' />
      </Box>
    )
  }
]

const series = [
  {
    name: 'Google Analytics',
    data: [155, 135, 320, 100, 150, 335, 160]
  },
  {
    name: 'Facebook Ads',
    data: [110, 235, 125, 230, 215, 115, 200]
  }
]

const CardWidgetsExternalLinks = () => {
  // ** Hook
  const theme = useTheme()

  const options = {
    chart: {
      stacked: true,
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    plotOptions: {
      bar: {
        borderRadius: 10,
        columnWidth: '40%',
        endingShape: 'rounded',
        startingShape: 'rounded'
      }
    },
    xaxis: {
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false },
      categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    },
    yaxis: { show: false },
    colors: [hexToRGBA(theme.palette.primary.main, 1), hexToRGBA(theme.palette.secondary.main, 1)],
    grid: {
      strokeDashArray: 10,
      borderColor: theme.palette.divider,
      padding: {
        top: -12,
        left: -4,
        right: -5,
        bottom: -14
      }
    },
    legend: { show: false },
    dataLabels: { enabled: false },
    stroke: {
      width: 6,
      curve: 'smooth',
      lineCap: 'round',
      colors: [theme.palette.background.paper]
    },
    states: {
      hover: {
        filter: { type: 'none' }
      },
      active: {
        filter: { type: 'none' }
      }
    },
    responsive: [
      {
        breakpoint: theme.breakpoints.values.xl,
        options: {
          plotOptions: {
            bar: {
              columnWidth: '50%'
            }
          }
        }
      },
      {
        breakpoint: theme.breakpoints.values.lg,
        options: {
          plotOptions: {
            bar: {
              columnWidth: '50%'
            }
          }
        }
      },
      {
        breakpoint: theme.breakpoints.values.sm,
        options: {
          plotOptions: {
            bar: {
              columnWidth: '35%'
            }
          }
        }
      },
      {
        breakpoint: 430,
        options: {
          plotOptions: {
            bar: {
              columnWidth: '45%'
            }
          }
        }
      }
    ]
  }

  return (
    <Card>
      <CardHeader
        title='External Links'
        action={
          <OptionsMenu
            options={['Refresh', 'Update', 'Share']}
            iconButtonProps={{ size: 'small', className: 'card-more-options' }}
          />
        }
      />
      <CardContent sx={{ '& .apexcharts-xcrosshairs.apexcharts-active': { opacity: 0 } }}>
        <ReactApexcharts type='bar' height={213} series={series} options={options} />
      </CardContent>
      <TableContainer sx={{ mb: 3.75 }}>
        <Table>
          <TableBody>
            {data.map((item, index) => (
              <TableRow
                key={index}
                sx={{ '& .MuiTableCell-root': { borderBottomWidth: 0, py: `${theme.spacing(1.125)} !important` } }}
              >
                <TableCell>
                  <Box
                    sx={{
                      display: 'flex',
                      alignItems: 'center',
                      '& svg': { mr: 2.25, color: `${theme.palette[item.color].main}` }
                    }}
                  >
                    <Icon icon='mdi:circle' fontSize='0.75rem' />
                    <Typography variant='body2' sx={{ fontWeight: 600, whiteSpace: 'nowrap', color: 'text.primary' }}>
                      {item.title}
                    </Typography>
                  </Box>
                </TableCell>
                <TableCell>
                  <Typography variant='body2'>{item.amount}</Typography>
                </TableCell>
                <TableCell>
                  <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'flex-end' }}>
                    <Typography
                      variant='body2'
                      sx={{ mr: 2.5, fontWeight: 600, color: 'text.primary' }}
                    >{`${item.trendAmount}%`}</Typography>
                    {item.icon}
                  </Box>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Card>
  )
}

export default CardWidgetsExternalLinks
