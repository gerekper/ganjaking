// ** MUI Imports
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

// ** Third Party Imports
import { PolarArea } from 'react-chartjs-2'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'

const ChartjsPolarAreaChart = props => {
  // ** Props
  const { info, grey, green, yellow, primary, warning, legendColor } = props

  const options = {
    responsive: true,
    maintainAspectRatio: false,
    animation: { duration: 500 },
    layout: {
      padding: {
        top: -5,
        bottom: -45
      }
    },
    scales: {
      r: {
        grid: { display: false },
        ticks: { display: false }
      }
    },
    plugins: {
      legend: {
        position: 'right',
        labels: {
          padding: 25,
          boxWidth: 9,
          color: legendColor,
          usePointStyle: true
        }
      }
    }
  }

  const data = {
    labels: ['Africa', 'Asia', 'Europe', 'America', 'Antarctica', 'Australia'],
    datasets: [
      {
        borderWidth: 0,
        label: 'Population (millions)',
        data: [19, 17.5, 15, 13.5, 11, 9],
        backgroundColor: [primary, yellow, warning, info, grey, green]
      }
    ]
  }

  return (
    <Card>
      <CardHeader
        title='Average Skills'
        action={
          <OptionsMenu
            iconProps={{ fontSize: 20 }}
            options={['Refresh', 'Edit', 'Share']}
            iconButtonProps={{ size: 'small', className: 'card-more-options', sx: { color: 'text.secondary' } }}
          />
        }
      />
      <CardContent>
        <PolarArea data={data} height={350} options={options} />
      </CardContent>
    </Card>
  )
}

export default ChartjsPolarAreaChart
