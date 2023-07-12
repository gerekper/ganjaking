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
import CustomAvatar from 'src/@core/components/mui/avatar'
import OptionsMenu from 'src/@core/components/option-menu'
import ReactApexcharts from 'src/@core/components/react-apexcharts'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const CardWidgetsVisitsByDay = () => {
  // ** Hook
  const theme = useTheme()

  const options = {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    legend: { show: false },
    dataLabels: { enabled: false },
    colors: [
      hexToRGBA(theme.palette.primary.main, 0.1),
      hexToRGBA(theme.palette.primary.main, 1),
      hexToRGBA(theme.palette.primary.main, 0.1),
      hexToRGBA(theme.palette.primary.main, 1),
      hexToRGBA(theme.palette.primary.main, 1),
      hexToRGBA(theme.palette.primary.main, 0.1),
      hexToRGBA(theme.palette.primary.main, 0.1)
    ],
    grid: {
      show: false,
      padding: {
        top: -15,
        left: -7,
        right: -4
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
    plotOptions: {
      bar: {
        borderRadius: 8,
        distributed: true,
        columnWidth: '55%',
        endingShape: 'rounded',
        startingShape: 'rounded'
      }
    },
    xaxis: {
      axisTicks: { show: false },
      axisBorder: { show: false },
      categories: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
      labels: {
        style: { colors: theme.palette.text.disabled }
      }
    },
    yaxis: { show: false }
  }

  return (
    <Card>
      <CardHeader
        title='Visits by Day'
        subheader='Total 248.5k Visits'
        subheaderTypographyProps={{ sx: { lineHeight: 1.429 } }}
        titleTypographyProps={{ sx: { letterSpacing: '0.15px' } }}
        action={
          <OptionsMenu
            options={['Refresh', 'Update', 'Share']}
            iconButtonProps={{ size: 'small', className: 'card-more-options' }}
          />
        }
      />
      <CardContent>
        <ReactApexcharts type='bar' height={222} options={options} series={[{ data: [38, 55, 48, 65, 80, 38, 48] }]} />
        <Box sx={{ mt: 4, display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
          <Box sx={{ display: 'flex', flexDirection: 'column' }}>
            <Typography sx={{ mb: 0.75, fontWeight: 600 }}>Most Visited Day</Typography>
            <Typography variant='body2'>Total 62.4k Visits on Thursday</Typography>
          </Box>
          <CustomAvatar skin='light' variant='rounded'>
            <Icon icon='mdi:chevron-right' />
          </CustomAvatar>
        </Box>
      </CardContent>
    </Card>
  )
}

export default CardWidgetsVisitsByDay
