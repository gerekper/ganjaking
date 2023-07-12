// ** MUI Components
import Button from '@mui/material/Button'
import { styled } from '@mui/material/styles'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import Box from '@mui/material/Box'

// ** Layout Import
import BlankLayout from 'src/@core/layouts/BlankLayout'

// ** Demo Imports
import FooterIllustrations from 'src/views/pages/misc/FooterIllustrations'

// ** Styled Components
const BoxWrapper = styled(Box)(({ theme }) => ({
  [theme.breakpoints.down('md')]: {
    width: '90vw'
  }
}))

const Img = styled('img')(({ theme }) => ({
  marginTop: theme.spacing(15),
  [theme.breakpoints.down('lg')]: {
    height: 450,
    marginTop: theme.spacing(10)
  },
  [theme.breakpoints.down('md')]: {
    height: 400
  }
}))

const ComingSoon = () => {
  return (
    <Box className='content-center'>
      <Box sx={{ p: 5, display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
        <BoxWrapper>
          <Box sx={{ mb: 10, textAlign: 'center' }}>
            <Typography variant='h5' sx={{ mb: 2.5, fontSize: '1.5rem !important' }}>
              We are launching soon 🚀
            </Typography>
            <Typography variant='body2'>
              Our website is opening soon. Please register to get notified when it&prime;s ready!
            </Typography>
          </Box>
          <form noValidate autoComplete='off' onSubmit={e => e.preventDefault()}>
            <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
              <TextField autoFocus size='small' type='email' sx={{ mr: 6 }} placeholder='Enter your email' />
              <Button type='submit' variant='contained'>
                Notify
              </Button>
            </Box>
          </form>
        </BoxWrapper>
        <Img alt='coming-soon-illustration' src='/images/pages/misc-coming-soon.png' />
      </Box>
      <FooterIllustrations />
    </Box>
  )
}
ComingSoon.getLayout = page => <BlankLayout>{page}</BlankLayout>

export default ComingSoon
