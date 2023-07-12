// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Table from '@mui/material/Table'
import Avatar from '@mui/material/Avatar'
import TableRow from '@mui/material/TableRow'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import TableHead from '@mui/material/TableHead'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import TableContainer from '@mui/material/TableContainer'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'

const data = [
  {
    title: 'USA',
    trendDir: 'up',
    imgAlt: 'flag-usa',
    subscribers: '22,450',
    trendNumber: '+22.5%',
    imgSrc: '/images/cards/flag-usa.png'
  },
  {
    title: 'India',
    trendDir: 'up',
    imgAlt: 'flag-india',
    subscribers: '18,568',
    trendNumber: '+18.5%',
    imgSrc: '/images/cards/flag-india.png'
  },
  {
    title: 'Brazil',
    trendDir: 'down',
    subscribers: '8,457',
    trendNumber: '-8.3%',
    imgAlt: 'flag-brazil',
    imgSrc: '/images/cards/flag-brazil.png'
  },
  {
    trendDir: 'up',
    title: 'Australia',
    subscribers: '2,850',
    trendNumber: '+15.2%',
    imgAlt: 'flag-australia',
    imgSrc: '/images/cards/flag-australia.png'
  },
  {
    title: 'France',
    trendDir: 'down',
    subscribers: '1,930',
    imgAlt: 'flag-france',
    trendNumber: '-12.6%',
    imgSrc: '/images/cards/flag-france.png'
  },
  {
    title: 'China',
    trendDir: 'down',
    subscribers: '852',
    imgAlt: 'flag-china',
    trendNumber: '-2.4%',
    imgSrc: '/images/cards/flag-china.png'
  }
]

const CardSubscribersByCountries = () => {
  return (
    <Card>
      <CardHeader
        title='Subscribers by Countries'
        titleTypographyProps={{ sx: { lineHeight: '2rem !important', letterSpacing: '0.15px !important' } }}
        action={
          <OptionsMenu
            options={['Last 28 Days', 'Last Month', 'Last Year']}
            iconButtonProps={{ size: 'small', className: 'card-more-options' }}
          />
        }
      />
      <TableContainer>
        <Table>
          <TableHead>
            <TableRow sx={{ '& .MuiTableCell-root': { py: theme => `${theme.spacing(2.5)} !important` } }}>
              <TableCell>
                <Typography variant='subtitle2' sx={{ textTransform: 'capitalize' }}>
                  Countries
                </Typography>
              </TableCell>
              <TableCell>
                <Typography variant='subtitle2' sx={{ textTransform: 'capitalize' }}>
                  Subscribers
                </Typography>
              </TableCell>
              <TableCell align='right'>
                <Typography variant='subtitle2' sx={{ textTransform: 'capitalize' }}>
                  Change
                </Typography>
              </TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {data.map(row => {
              return (
                <TableRow
                  key={row.title}
                  sx={{
                    '& .MuiTableCell-root': {
                      border: 0,
                      py: theme => `${theme.spacing(3.5)} !important`
                    }
                  }}
                >
                  <TableCell>
                    <Box sx={{ display: 'flex', alignItems: 'center' }}>
                      <Avatar alt={row.imgAlt} src={row.imgSrc} sx={{ mr: 3, width: 30, height: 30 }} />
                      <Typography variant='body2' sx={{ fontWeight: 600, color: 'text.primary' }}>
                        {row.title}
                      </Typography>
                    </Box>
                  </TableCell>
                  <TableCell>
                    <Typography variant='body2' sx={{ fontWeight: 600 }}>
                      {row.subscribers}
                    </Typography>
                  </TableCell>
                  <TableCell>
                    <Box
                      sx={{
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'flex-end',
                        '& svg': { color: row.trendDir === 'up' ? 'success.main' : 'error.main' }
                      }}
                    >
                      <Typography
                        variant='body2'
                        sx={{ fontWeight: 600, color: row.trendDir === 'up' ? 'success.main' : 'error.main' }}
                      >
                        {row.trendNumber}
                      </Typography>
                      <Icon icon={row.trendDir === 'up' ? 'mdi:chevron-up' : 'mdi:chevron-down'} />
                    </Box>
                  </TableCell>
                </TableRow>
              )
            })}
          </TableBody>
        </Table>
      </TableContainer>
    </Card>
  )
}

export default CardSubscribersByCountries
