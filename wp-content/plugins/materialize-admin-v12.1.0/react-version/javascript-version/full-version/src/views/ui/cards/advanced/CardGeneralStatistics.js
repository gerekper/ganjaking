// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Table from '@mui/material/Table'
import TableRow from '@mui/material/TableRow'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import LinearProgress from '@mui/material/LinearProgress'
import TableContainer from '@mui/material/TableContainer'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'
import OptionsMenu from 'src/@core/components/option-menu'

const data = [
  {
    title: 'Profit',
    color: 'primary',
    amount: '$54,234',
    trendNumber: '+85%',
    trend: (
      <Box sx={{ color: 'success.main' }}>
        <Icon icon='mdi:chevron-up' />
      </Box>
    )
  },
  {
    title: 'Sales',
    amount: '8,657',
    color: 'warning',
    trendNumber: '+42%',
    trend: (
      <Box sx={{ color: 'success.main' }}>
        <Icon icon='mdi:chevron-up' />
      </Box>
    )
  },
  {
    title: 'User',
    color: 'info',
    amount: '16,456',
    trendNumber: '-12%',
    trend: (
      <Box sx={{ color: 'error.main' }}>
        <Icon icon='mdi:chevron-down' />
      </Box>
    )
  }
]

const CardGeneralStatistics = () => {
  return (
    <Card>
      <CardHeader
        title='General Statistics'
        titleTypographyProps={{ sx: { lineHeight: '2rem !important', letterSpacing: '0.15px !important' } }}
        action={
          <OptionsMenu
            options={['Last 28 Days', 'Last Month', 'Last Year']}
            iconButtonProps={{ size: 'small', className: 'card-more-options' }}
          />
        }
      />
      <CardContent sx={{ pt: theme => `${theme.spacing(2.5)} !important` }}>
        <Box sx={{ mb: 5.75, display: 'flex', alignItems: 'center' }}>
          <CustomAvatar skin='light' variant='rounded' sx={{ mr: 4, width: 50, height: 50 }}>
            <Icon icon='mdi:credit-card' fontSize='2rem' />
          </CustomAvatar>
          <Box sx={{ display: 'flex', flexDirection: 'column' }}>
            <Typography variant='h4'>$89,522</Typography>
            <Typography variant='caption'>Last 6 Month Profit</Typography>
          </Box>
        </Box>

        <Typography sx={{ mb: 1.5, fontWeight: 600 }}>Current Activity</Typography>

        <LinearProgress value={85} sx={{ mb: 4 }} variant='determinate' />

        <TableContainer>
          <Table>
            <TableBody>
              {data.map(row => {
                return (
                  <TableRow
                    key={row.title}
                    sx={{
                      '&:last-of-type td': { border: 0 },
                      '& .MuiTableCell-root': {
                        '&:last-of-type': { pr: 0 },
                        '&:first-of-type': { pl: '0 !important' },
                        py: theme => `${theme.spacing(2.75)} !important`
                      }
                    }}
                  >
                    <TableCell>
                      <Box
                        sx={{ display: 'flex', alignItems: 'center', '& svg': { mr: 1.8, color: `${row.color}.main` } }}
                      >
                        <Icon icon='mdi:circle' fontSize='1rem' />
                        <Typography variant='body2' sx={{ color: 'text.primary' }}>
                          {row.title}
                        </Typography>
                      </Box>
                    </TableCell>
                    <TableCell align='right'>
                      <Typography variant='body2' sx={{ fontWeight: 600, color: 'text.primary' }}>
                        {row.amount}
                      </Typography>
                    </TableCell>
                    <TableCell>
                      <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'flex-end' }}>
                        <Typography variant='body2' sx={{ mr: 1.5, fontWeight: 600, color: 'text.primary' }}>
                          {row.trendNumber}
                        </Typography>
                        {row.trend}
                      </Box>
                    </TableCell>
                  </TableRow>
                )
              })}
            </TableBody>
          </Table>
        </TableContainer>
      </CardContent>
    </Card>
  )
}

export default CardGeneralStatistics
