// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
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

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'

// Styled Timeline component
const Timeline = styled(MuiTimeline)({
  paddingLeft: 0,
  paddingRight: 0,
  '& .MuiTimelineItem-root': {
    width: '100%',
    '&:before': {
      display: 'none'
    }
  }
})

const AnalyticsActivityTimeline = () => {
  return (
    <Card>
      <CardHeader
        title='Activity Timeline'
        action={
          <OptionsMenu
            options={['Last 28 Days', 'Last Month', 'Last Year']}
            iconButtonProps={{ size: 'small', className: 'card-more-options' }}
          />
        }
      />
      <CardContent sx={{ pt: theme => `${theme.spacing(2.5)} !important` }}>
        <Timeline sx={{ my: 0, py: 0 }}>
          <TimelineItem>
            <TimelineSeparator>
              <TimelineDot color='error' />
              <TimelineConnector />
            </TimelineSeparator>
            <TimelineContent sx={{ mt: 0, overflow: 'hidden', mb: theme => `${theme.spacing(2)} !important` }}>
              <Box
                sx={{
                  mb: 3,
                  display: 'flex',
                  flexWrap: 'wrap',
                  alignItems: 'center',
                  justifyContent: 'space-between'
                }}
              >
                <Typography sx={{ mr: 2, fontWeight: 600 }}>Create youtube video for next product 😎</Typography>
                <Typography variant='caption' sx={{ color: 'text.disabled' }}>
                  Tomorrow
                </Typography>
              </Box>
              <Typography variant='body2' sx={{ mb: 2 }}>
                Product introduction and details video
              </Typography>
              <Box sx={{ display: 'flex', alignItems: 'center', '& svg': { mr: 2.5, color: 'error.main' } }}>
                <Icon icon='mdi:play-circle' />
                <Typography noWrap variant='subtitle2' sx={{ fontWeight: 600 }}>
                  www.youtube.com/channel/UCuryo5s0CW4aP83itLjIdZg
                </Typography>
              </Box>
            </TimelineContent>
          </TimelineItem>

          <TimelineItem>
            <TimelineSeparator>
              <TimelineDot color='primary' />
              <TimelineConnector />
            </TimelineSeparator>
            <TimelineContent sx={{ mt: 0, mb: theme => `${theme.spacing(2)} !important` }}>
              <Box
                sx={{
                  mb: 3,
                  display: 'flex',
                  flexWrap: 'wrap',
                  alignItems: 'center',
                  justifyContent: 'space-between'
                }}
              >
                <Typography sx={{ mr: 2, fontWeight: 600 }}>Received payment from USA client 😍</Typography>
                <Typography variant='caption' sx={{ color: 'text.disabled' }}>
                  January, 18
                </Typography>
              </Box>
              <Typography variant='body2'>Received payment $1,490 for banking ios app</Typography>
            </TimelineContent>
          </TimelineItem>

          <TimelineItem sx={{ minHeight: 0 }}>
            <TimelineSeparator>
              <TimelineDot color='info' />
              <TimelineConnector sx={{ mb: 4 }} />
            </TimelineSeparator>
            <TimelineContent sx={{ mt: 0, mb: theme => `${theme.spacing(2)} !important` }}>
              <Box
                sx={{
                  mb: 3,
                  display: 'flex',
                  flexWrap: 'wrap',
                  alignItems: 'center',
                  justifyContent: 'space-between'
                }}
              >
                <Typography sx={{ mr: 2, fontWeight: 600 }}>Meeting with joseph morgan for next project</Typography>
                <Typography variant='caption' sx={{ color: 'text.disabled' }}>
                  April, 23
                </Typography>
              </Box>
              <Typography variant='body2' sx={{ mb: 2 }}>
                Meeting Video call on zoom at 9pm
              </Typography>
              <Box sx={{ display: 'flex', alignItems: 'center' }}>
                <img width={24} height={24} alt='invoice.pdf' src='/images/icons/file-icons/pdf.png' />
                <Typography variant='subtitle2' sx={{ ml: 2, fontWeight: 600 }}>
                  presentation.pdf
                </Typography>
              </Box>
            </TimelineContent>
          </TimelineItem>
        </Timeline>
      </CardContent>
    </Card>
  )
}

export default AnalyticsActivityTimeline
