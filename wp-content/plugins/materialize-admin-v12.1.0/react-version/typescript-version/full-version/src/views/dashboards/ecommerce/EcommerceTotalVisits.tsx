// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Divider from '@mui/material/Divider'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import LinearProgress from '@mui/material/LinearProgress'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const EcommerceTotalVisits = () => {
  return (
    <Card>
      <CardContent>
        <Box sx={{ mb: 6.5, display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between' }}>
          <Box>
            <Typography variant='body2'>Total Visits</Typography>
            <Typography variant='h6'>42.5k</Typography>
          </Box>
          <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { color: 'success.main' } }}>
            <Typography variant='subtitle2' sx={{ color: 'success.main' }}>
              +18.4%
            </Typography>
            <Icon icon='mdi:chevron-up' />
          </Box>
        </Box>
        <Box sx={{ mb: 5, display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
          <Box sx={{ display: 'flex', flexDirection: 'column' }}>
            <Box sx={{ mb: 2.5, display: 'flex', alignItems: 'center' }}>
              <CustomAvatar
                skin='light'
                color='warning'
                variant='rounded'
                sx={{ mr: 1.5, height: 24, width: 24, borderRadius: '6px' }}
              >
                <Icon icon='mdi:cellphone' fontSize='0.875rem' />
              </CustomAvatar>
              <Typography variant='body2'>Mobile</Typography>
            </Box>
            <Typography variant='h6'>23.5%</Typography>
            <Typography variant='caption' sx={{ color: 'text.disabled' }}>
              2,890
            </Typography>
          </Box>
          <Divider flexItem sx={{ m: 0 }} orientation='vertical'>
            <CustomAvatar
              skin='light'
              color='secondary'
              sx={{ height: 24, width: 24, fontSize: '0.6875rem', color: 'text.secondary' }}
            >
              VS
            </CustomAvatar>
          </Divider>
          <Box sx={{ display: 'flex', alignItems: 'flex-end', flexDirection: 'column' }}>
            <Box sx={{ mb: 2.5, display: 'flex', alignItems: 'center' }}>
              <Typography sx={{ mr: 1.5 }} variant='body2'>
                Desktop
              </Typography>
              <CustomAvatar skin='light' variant='rounded' sx={{ height: 24, width: 24, borderRadius: '6px' }}>
                <Icon icon='mdi:monitor' fontSize='0.875rem' />
              </CustomAvatar>
            </Box>
            <Typography variant='h6'>76.5%</Typography>
            <Typography variant='caption' sx={{ color: 'text.disabled' }}>
              22,465
            </Typography>
          </Box>
        </Box>
        <LinearProgress
          value={24}
          variant='determinate'
          sx={{
            height: 10,
            '&.MuiLinearProgress-colorPrimary': { backgroundColor: 'primary.main' },
            '& .MuiLinearProgress-bar': {
              borderTopRightRadius: 0,
              borderBottomRightRadius: 0,
              backgroundColor: 'warning.main'
            }
          }}
        />
      </CardContent>
    </Card>
  )
}

export default EcommerceTotalVisits
