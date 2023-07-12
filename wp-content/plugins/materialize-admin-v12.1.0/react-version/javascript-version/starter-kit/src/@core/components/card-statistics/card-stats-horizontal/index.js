// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Styled Avatar component
const Avatar = styled(CustomAvatar)(({ theme }) => ({
  width: 40,
  height: 40,
  marginRight: theme.spacing(4)
}))

const CardStatsHorizontal = props => {
  // ** Props
  const { title, icon, stats, trendNumber, color = 'primary', trend = 'positive' } = props

  return (
    <Card>
      <CardContent sx={{ py: theme => `${theme.spacing(4.125)} !important` }}>
        <Box sx={{ display: 'flex', alignItems: 'center' }}>
          <Avatar skin='light' color={color} variant='rounded'>
            {icon}
          </Avatar>
          <Box sx={{ display: 'flex', flexDirection: 'column' }}>
            <Box sx={{ display: 'flex', flexWrap: 'wrap', alignItems: 'center' }}>
              <Typography variant='h6'>{stats}</Typography>
              <Box sx={{ display: 'flex', alignItems: 'center' }}>
                <Box sx={{ display: 'inline-flex', color: trend === 'positive' ? 'success.main' : 'error.main' }}>
                  <Icon icon={trend === 'positive' ? 'mdi:chevron-up' : 'mdi:chevron-down'} />
                </Box>
                <Typography variant='caption' sx={{ color: trend === 'positive' ? 'success.main' : 'error.main' }}>
                  {trendNumber}
                </Typography>
              </Box>
            </Box>
            <Typography variant='caption'>{title}</Typography>
          </Box>
        </Box>
      </CardContent>
    </Card>
  )
}

export default CardStatsHorizontal
