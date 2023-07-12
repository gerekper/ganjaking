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

const CardWidgetsPerformanceOverview = () => {
  // ** Hook
  const theme = useTheme()

  const options = {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    stroke: {
      curve: 'stepline'
    },
    colors: [hexToRGBA(theme.palette.warning.main, 1)],
    grid: {
      yaxis: {
        lines: { show: false }
      }
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
      <CardHeader
        title='Performance Overview'
        action={
          <OptionsMenu
            options={['Last 28 Days', 'Last Month', 'Last Year']}
            iconButtonProps={{ size: 'small', className: 'card-more-options' }}
          />
        }
      />
      <CardContent>
        <ReactApexcharts
          type='line'
          height={202}
          options={options}
          series={[{ data: [7, 65, 40, 7, 40, 80, 45, 65, 65] }]}
        />
        <Box
          sx={{
            mt: 4,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            '& svg': { mr: 1.5, color: 'warning.main' }
          }}
        >
          <Icon icon='mdi:circle' fontSize='0.75rem' />
          <Typography variant='body2' sx={{ color: 'text.disabled' }}>
            Avarage cost per interaction is $5.65
          </Typography>
        </Box>
      </CardContent>
    </Card>
  )
}

export default CardWidgetsPerformanceOverview
