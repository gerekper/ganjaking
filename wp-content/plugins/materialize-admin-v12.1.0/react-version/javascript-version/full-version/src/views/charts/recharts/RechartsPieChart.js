// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Third Party Imports
import { Tooltip, PieChart, Pie, Cell, ResponsiveContainer } from 'recharts'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const data = [
  { name: 'R&D', value: 50, color: '#00d4bd' },
  { name: 'Operational', value: 85, color: '#ffe700' },
  { name: 'Networking', value: 16, color: '#FFA1A1' },
  { name: 'Hiring', value: 50, color: '#826bf8' }
]
const RADIAN = Math.PI / 180

const renderCustomizedLabel = props => {
  // ** Props
  const { cx, cy, midAngle, innerRadius, outerRadius, percent } = props
  const radius = innerRadius + (outerRadius - innerRadius) * 0.5
  const x = cx + radius * Math.cos(-midAngle * RADIAN)
  const y = cy + radius * Math.sin(-midAngle * RADIAN)

  return (
    <text x={x} y={y} fill='#fff' textAnchor='middle' dominantBaseline='central'>
      {`${(percent * 100).toFixed(0)}%`}
    </text>
  )
}

const RechartsPieChart = () => {
  return (
    <Card>
      <CardHeader
        title='Expense Ratio'
        subheader='Spending on various categories'
        subheaderTypographyProps={{ sx: { color: theme => `${theme.palette.text.disabled} !important` } }}
      />
      <CardContent>
        <Box sx={{ height: 350 }}>
          <ResponsiveContainer>
            <PieChart height={350} style={{ direction: 'ltr' }}>
              <Pie data={data} innerRadius={80} dataKey='value' label={renderCustomizedLabel} labelLine={false}>
                {data.map((entry, index) => (
                  <Cell key={`cell-${index}`} fill={entry.color} />
                ))}
              </Pie>
              <Tooltip />
            </PieChart>
          </ResponsiveContainer>
        </Box>
        <Box sx={{ display: 'flex', flexWrap: 'wrap', mb: 4, justifyContent: 'center' }}>
          <Box
            sx={{
              mr: 6,
              display: 'flex',
              alignItems: 'center',
              '& svg': { mr: 1.5, color: '#00d4bd' }
            }}
          >
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>R&D</Typography>
          </Box>
          <Box
            sx={{
              mr: 6,
              display: 'flex',
              alignItems: 'center',
              '& svg': { mr: 1.5, color: '#ffe700' }
            }}
          >
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>Operational</Typography>
          </Box>
          <Box
            sx={{
              mr: 6,
              display: 'flex',
              alignItems: 'center',
              '& svg': { mr: 1.5, color: '#FFA1A1' }
            }}
          >
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>Networking</Typography>
          </Box>
          <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { mr: 1.5, color: '#826bf8' } }}>
            <Icon icon='mdi:circle' fontSize='0.75rem' />
            <Typography variant='body2'>Hiring</Typography>
          </Box>
        </Box>
      </CardContent>
    </Card>
  )
}

export default RechartsPieChart
