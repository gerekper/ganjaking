// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Grid from '@mui/material/Grid'
import Avatar from '@mui/material/Avatar'
import { styled } from '@mui/material/styles'
import TimelineDot from '@mui/lab/TimelineDot'
import TimelineItem from '@mui/lab/TimelineItem'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import TimelineContent from '@mui/lab/TimelineContent'
import TimelineSeparator from '@mui/lab/TimelineSeparator'
import TimelineConnector from '@mui/lab/TimelineConnector'
import MuiTimeline from '@mui/lab/Timeline'

// ** Demo Component Imports
import UsersInvoiceListTable from 'src/views/apps/user/view/UsersInvoiceListTable'
import UsersProjectListTable from 'src/views/apps/user/view/UsersProjectListTable'

// Styled Timeline component
const Timeline = styled(MuiTimeline)(({ theme }) => ({
  margin: 0,
  padding: 0,
  marginLeft: theme.spacing(0.75),
  '& .MuiTimelineItem-root': {
    '&:before': {
      display: 'none'
    },
    '&:last-child': {
      minHeight: 60
    }
  }
}))

const UserViewOverview = ({ invoiceData }) => {
  return (
    <Grid container spacing={6}>
      <Grid item xs={12}>
        <UsersProjectListTable />
      </Grid>
      <Grid item xs={12}>
        <Card>
          <CardHeader title='User Activity Timeline' />
          <CardContent>
            <Timeline>
              <TimelineItem>
                <TimelineSeparator>
                  <TimelineDot color='error' />
                  <TimelineConnector />
                </TimelineSeparator>
                <TimelineContent>
                  <Box
                    sx={{
                      mb: 2,
                      display: 'flex',
                      flexWrap: 'wrap',
                      alignItems: 'center',
                      justifyContent: 'space-between'
                    }}
                  >
                    <Typography variant='body2' sx={{ mr: 2, fontWeight: 600, color: 'text.primary' }}>
                      User login
                    </Typography>
                    <Typography variant='caption'>12 min ago</Typography>
                  </Box>
                  <Typography variant='body2'>User login at 2:12pm</Typography>
                </TimelineContent>
              </TimelineItem>

              <TimelineItem>
                <TimelineSeparator>
                  <TimelineDot color='primary' />
                  <TimelineConnector />
                </TimelineSeparator>
                <TimelineContent>
                  <Box
                    sx={{
                      mb: 2,
                      display: 'flex',
                      flexWrap: 'wrap',
                      alignItems: 'center',
                      justifyContent: 'space-between'
                    }}
                  >
                    <Typography variant='body2' sx={{ mr: 2, fontWeight: 600, color: 'text.primary' }}>
                      Meeting with John
                    </Typography>
                    <Typography variant='caption'>45 min ago</Typography>
                  </Box>
                  <Typography variant='body2' sx={{ mb: 2 }}>
                    React Project meeting with John @10:15am
                  </Typography>
                  <Box sx={{ display: 'flex', alignItems: 'center' }}>
                    <Avatar alt='Avatar' src='/images/avatars/2.png' sx={{ width: 40, height: 40, mr: 2 }} />
                    <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                      <Typography variant='body2' sx={{ fontWeight: 600, color: 'text.primary' }}>
                        Leona Watkins (Client)
                      </Typography>
                      <Typography variant='body2'>CEO of Watkins Group</Typography>
                    </Box>
                  </Box>
                </TimelineContent>
              </TimelineItem>

              <TimelineItem>
                <TimelineSeparator>
                  <TimelineDot color='info' />
                  <TimelineConnector />
                </TimelineSeparator>
                <TimelineContent>
                  <Box
                    sx={{
                      mb: 2,
                      display: 'flex',
                      flexWrap: 'wrap',
                      alignItems: 'center',
                      justifyContent: 'space-between'
                    }}
                  >
                    <Typography variant='body2' sx={{ mr: 2, fontWeight: 600, color: 'text.primary' }}>
                      Create a new react project for client
                    </Typography>
                    <Typography variant='caption'>2 day ago</Typography>
                  </Box>
                  <Typography variant='body2'>Add files to new design folder</Typography>
                </TimelineContent>
              </TimelineItem>

              <TimelineItem>
                <TimelineSeparator>
                  <TimelineDot color='success' />
                  <TimelineConnector />
                </TimelineSeparator>
                <TimelineContent>
                  <Box
                    sx={{
                      mb: 2,
                      display: 'flex',
                      flexWrap: 'wrap',
                      alignItems: 'center',
                      justifyContent: 'space-between'
                    }}
                  >
                    <Typography variant='body2' sx={{ mr: 2, fontWeight: 600, color: 'text.primary' }}>
                      Create invoices for client
                    </Typography>
                    <Typography variant='caption'>12 min ago</Typography>
                  </Box>
                  <Typography variant='body2'>Create new invoices and send to Leona Watkins</Typography>
                  <Box sx={{ mt: 2, display: 'flex', alignItems: 'center' }}>
                    <Box sx={{ width: 28, height: 'auto' }}>
                      <img width={28} height={28} alt='invoice.pdf' src='/images/icons/file-icons/pdf.png' />
                    </Box>
                    <Typography variant='subtitle2' sx={{ ml: 2, fontWeight: 600 }}>
                      invoice.pdf
                    </Typography>
                  </Box>
                </TimelineContent>
              </TimelineItem>
            </Timeline>
          </CardContent>
        </Card>
      </Grid>

      <Grid item xs={12}>
        <UsersInvoiceListTable invoiceData={invoiceData} />
      </Grid>
    </Grid>
  )
}

export default UserViewOverview
