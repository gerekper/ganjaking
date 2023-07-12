// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Third Party Imports
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, TooltipProps } from 'recharts'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomChip from 'src/@core/components/mui/chip'

interface Props {
  direction: 'ltr' | 'rtl'
}

const data = [
  { pv: 280, name: '7/12' },
  { pv: 200, name: '8/12' },
  { pv: 220, name: '9/12' },
  { pv: 180, name: '10/12' },
  { pv: 270, name: '11/12' },
  { pv: 250, name: '12/12' },
  { pv: 70, name: '13/12' },
  { pv: 90, name: '14/12' },
  { pv: 200, name: '15/12' },
  { pv: 150, name: '16/12' },
  { pv: 160, name: '17/12' },
  { pv: 100, name: '18/12' },
  { pv: 150, name: '19/12' },
  { pv: 100, name: '20/12' },
  { pv: 50, name: '21/12' }
]

const CustomTooltip = (props: TooltipProps<any, any>) => {
  // ** Props
  const { active, payload } = props

  if (active && payload) {
    return (
      <div className='recharts-custom-tooltip'>
        <Typography sx={{ fontSize: '0.875rem' }}>{`${payload[0].value}%`}</Typography>
      </div>
    )
  }

  return null
}

const RechartsLineChart = ({ direction }: Props) => {
  return (
    <Card>
      <CardHeader
        title='Balance'
        subheader='Commercial networks & enterprises'
        subheaderTypographyProps={{ sx: { color: theme => `${theme.palette.text.disabled} !important` } }}
        sx={{
          flexDirection: ['column', 'row'],
          alignItems: ['flex-start', 'center'],
          '& .MuiCardHeader-action': { mb: 0 },
          '& .MuiCardHeader-content': { mb: [2, 0] }
        }}
        action={
          <Box sx={{ display: 'flex', alignItems: 'center' }}>
            <Typography variant='h6' sx={{ mr: 5 }}>
              $221,267
            </Typography>
            <CustomChip
              skin='light'
              color='success'
              sx={{ fontWeight: 500, borderRadius: 1, fontSize: '0.875rem' }}
              label={
                <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { mr: 1 } }}>
                  <Icon icon='mdi:arrow-up' fontSize='1rem' />
                  <span>22%</span>
                </Box>
              }
            />
          </Box>
        }
      />
      <CardContent>
        <Box sx={{ height: 350 }}>
          <ResponsiveContainer>
            <LineChart height={350} data={data} style={{ direction }} margin={{ left: -20 }}>
              <CartesianGrid />
              <XAxis dataKey='name' reversed={direction === 'rtl'} />
              <YAxis orientation={direction === 'rtl' ? 'right' : 'left'} />
              <Tooltip content={CustomTooltip} />
              <Line dataKey='pv' stroke='#ff9f43' strokeWidth={3} />
            </LineChart>
          </ResponsiveContainer>
        </Box>
      </CardContent>
    </Card>
  )
}

export default RechartsLineChart
