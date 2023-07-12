// ** MUI Imports
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'

const HelpCenterLandingFooter = () => {
  return (
    <>
      <Typography variant='h5' sx={{ mb: 4, fontWeight: 600, fontSize: '1.5rem !important' }}>
        Still need help?
      </Typography>
      <Typography sx={{ color: 'text.secondary' }}>Our specialists are always happy to help.</Typography>
      <Typography sx={{ mb: 4, color: 'text.secondary' }}>
        Contact us during standard business hours or email us 24/7, and we'll get back to you.
      </Typography>
      <Box sx={{ gap: 4, display: 'flex', flexWrap: 'wrap', alignItems: 'center', justifyContent: 'center' }}>
        <Button variant='contained'>Visit our community</Button>
        <Button variant='contained'>Contact us</Button>
      </Box>
    </>
  )
}

export default HelpCenterLandingFooter
