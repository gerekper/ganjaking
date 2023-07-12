// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'
import Grid from '@mui/material/Grid'
import { styled, useTheme } from '@mui/material/styles'
import MuiCardContent from '@mui/material/CardContent'

// Styled CardContent component
const CardContent = styled(MuiCardContent)(({ theme }) => ({
  padding: `${theme.spacing(7, 7.5)} !important`,
  [theme.breakpoints.down('sm')]: {
    paddingBottom: '0 !important'
  }
}))

// Styled Grid component
const StyledGrid = styled(Grid)(({ theme }) => ({
  [theme.breakpoints.down('sm')]: {
    display: 'flex',
    justifyContent: 'center'
  }
}))

// Styled component for the image
const Img = styled('img')(({ theme }) => ({
  right: 0,
  bottom: 0,
  width: 298,
  position: 'absolute',
  [theme.breakpoints.down('sm')]: {
    width: 250,
    position: 'static'
  }
}))

const CardCongratulationsDaisy = () => {
  // ** Hook
  const theme = useTheme()

  return (
    <Card sx={{ position: 'relative' }}>
      <CardContent sx={{ p: theme => `${theme.spacing(7, 7.5)} !important` }}>
        <Grid container spacing={6}>
          <Grid item xs={12} sm={6}>
            <Typography variant='h5' sx={{ mb: 4.5 }}>
              Congratulations{' '}
              <Box component='span' sx={{ fontWeight: 'bold' }}>
                Daisy
              </Box>
              ! 🎉
            </Typography>
            <Typography variant='body2'>You have done 84% 😍 more task today.</Typography>
            <Typography sx={{ mb: 4.5 }} variant='body2'>
              Check your new badge in your profile.
            </Typography>
            <Button variant='contained'>View Profile</Button>
          </Grid>
          <StyledGrid item xs={12} sm={6}>
            <Img alt='Congratulations Daisy' src={`/images/cards/illustration-daisy-${theme.palette.mode}.png`} />
          </StyledGrid>
        </Grid>
      </CardContent>
    </Card>
  )
}

export default CardCongratulationsDaisy
