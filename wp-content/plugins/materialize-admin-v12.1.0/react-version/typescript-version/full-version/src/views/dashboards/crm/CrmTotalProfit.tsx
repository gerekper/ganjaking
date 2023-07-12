// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import { useTheme } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Third Party Imports
import { ApexOptions } from 'apexcharts'

// ** Custom Components Imports
import ReactApexcharts from 'src/@core/components/react-apexcharts'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const series = [
  {
    name: 'Earning',
    data: [44, 21, 56, 34, 47]
  },
  {
    name: 'Expense',
    data: [-27, -17, -31, -23, -31]
  }
]

const CrmTotalProfit = () => {
  // ** Hook
  const theme = useTheme()

  const options: ApexOptions = {
    chart: {
      stacked: true,
      parentHeightOffset: 0,
      toolbar: { show: false }
    },
    legend: { show: false },
    dataLabels: { enabled: false },
    colors: [hexToRGBA(theme.palette.secondary.main, 1), hexToRGBA(theme.palette.error.main, 1)],
    plotOptions: {
      bar: {
        borderRadius: 4,
        columnWidth: '21%',
        endingShape: 'rounded',
        startingShape: 'rounded'
      }
    },
    grid: {
      padding: {
        top: -21,
        right: 0,
        left: -17,
        bottom: -16
      },
      yaxis: {
        lines: { show: false }
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
            $88.5k
          </Typography>
          <Typography variant='subtitle2' sx={{ color: 'error.main' }}>
            -18%
          </Typography>
        </Box>
        <Typography variant='body2'>Total Profit</Typography>
        <ReactApexcharts type='bar' height={108} options={options} series={series} />
      </CardContent>
    </Card>
  )
}

export default CrmTotalProfit
