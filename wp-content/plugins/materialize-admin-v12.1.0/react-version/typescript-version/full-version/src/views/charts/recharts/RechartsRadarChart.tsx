// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Divider from '@mui/material/Divider'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Third Party Imports
import {
  Radar,
  Tooltip,
  PolarGrid,
  RadarChart,
  TooltipProps,
  PolarAngleAxis,
  PolarRadiusAxis,
  ResponsiveContainer
} from 'recharts'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const data = [
  {
    subject: 'Battery',
    'iPhone 11': 41,
    'Samsung s20': 65
  },
  {
    subject: 'Brand',
    'iPhone 11': 64,
    'Samsung s20': 46
  },
  {
    subject: 'Camera',
    'iPhone 11': 81,
    'Samsung s20': 42
  },
  {
    subject: 'Memory',
    'iPhone 11': 60,
    'Samsung s20': 25
  },
  {
    subject: 'Storage',
    'iPhone 11': 42,
    'Samsung s20': 58
  },
  {
    subject: 'Display',
    'iPhone 11': 42,
    'Samsung s20': 63
  },
  {
    subject: 'OS',
    'iPhone 11': 33,
    'Samsung s20': 76
  },
  {
    subject: 'Price',
    'iPhone 11': 23,
    'Samsung s20': 43
  }
]

const CustomTooltip = (data: TooltipProps<any, any>) => {
  const { active, payload } = data

  if (active && payload) {
    return (
      <div className='recharts-custom-tooltip'>
        <Typography>{data.label}</Typography>
        <Divider />
        {data &&
          data.payload &&
          data.payload.map((i: any) => {
            return (
              <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { color: i.fill, mr: 2.5 } }} key={i.dataKey}>
                <Icon icon='mdi:circle' fontSize='0.6rem' />
                <Typography variant='body2'>{`${i.dataKey} : ${i.payload[i.dataKey]}`}</Typography>
              </Box>
            )
          })}
      </div>
    )
  }

  return null
}

const RechartsRadarChart = () => {
  return (
    <Card>
      <CardHeader title='Mobile Comparison' />
      <CardContent>
        <Box sx={{ height: 350 }}>
          <ResponsiveContainer>
            <RadarChart cx='50%' cy='50%' height={350} data={data} style={{ direction: 'ltr' }}>
              <PolarGrid />
              <PolarAngleAxis dataKey='subject' />
              <PolarRadiusAxis />
              <Tooltip content={CustomTooltip} />
              <Radar dataKey='iPhone 11' stroke='#fde802' fill='#fde802' fillOpacity={1} />
              <Radar dataKey='Samsung s20' stroke='#9b88fa' fill='#9b88fa' fillOpacity={0.8} />
            </RadarChart>
          </ResponsiveContainer>
        </Box>
        <Box sx={{ display: 'flex', mb: 4, justifyContent: 'center' }}>
          <Box sx={{ mr: 6, display: 'flex', alignItems: 'center', '& svg': { mr: 1.5, color: '#fde802' } }}>
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>iPhone 11</Typography>
          </Box>
          <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { mr: 1.5, color: '#9b88fa' } }}>
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>Samsung s20</Typography>
          </Box>
        </Box>
      </CardContent>
    </Card>
  )
}

export default RechartsRadarChart
