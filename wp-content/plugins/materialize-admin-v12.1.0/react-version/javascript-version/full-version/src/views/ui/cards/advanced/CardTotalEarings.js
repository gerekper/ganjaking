// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import LinearProgress from '@mui/material/LinearProgress'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'

const data = [
  {
    progress: 75,
    title: 'Amazon',
    color: 'primary',
    amount: '$24,453'
  },
  {
    progress: 59,
    color: 'success',
    title: 'Flipkart',
    amount: '$12,763'
  },
  {
    progress: 20,
    title: 'eBay',
    color: 'error',
    amount: '$4,978'
  }
]

const CardTotalEarnings = () => {
  return (
    <Card>
      <CardHeader
        title='Total Earning'
        action={
          <OptionsMenu
            options={['Last 28 Days', 'Last Month', 'Last Year']}
            iconButtonProps={{ size: 'small', sx: { color: 'text.primary' } }}
          />
        }
      />
      <CardContent sx={{ pt: theme => `${theme.spacing(2.5)} !important` }}>
        <Box sx={{ mb: 0.5, display: 'flex', alignItems: 'center' }}>
          <Typography variant='h4' sx={{ mr: 0.5 }}>
            $42,880
          </Typography>
          <Box sx={{ display: 'flex', alignItems: 'center', color: 'success.main', '& svg': { mr: 0.5 } }}>
            <Icon icon='mdi:menu-up' fontSize='1.875rem' />
            <Typography variant='body2' sx={{ fontWeight: 600, color: 'success.main' }}>
              22%
            </Typography>
          </Box>
        </Box>

        <Typography component='p' variant='caption' sx={{ mb: 7.5 }}>
          Compared to $84,325 last year
        </Typography>

        {data.map((item, index) => {
          return (
            <Box key={item.title} sx={{ mb: index !== data.length - 1 ? 6.5 : undefined }}>
              <Box sx={{ mb: 1.5, display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                <Typography sx={{ mr: 2, fontWeight: 600 }}>{item.title}</Typography>
                <Typography variant='body2' sx={{ fontWeight: 600, color: 'text.primary' }}>
                  {item.amount}
                </Typography>
              </Box>
              <LinearProgress color={item.color} value={item.progress} variant='determinate' />
            </Box>
          )
        })}
      </CardContent>
    </Card>
  )
}

export default CardTotalEarnings
