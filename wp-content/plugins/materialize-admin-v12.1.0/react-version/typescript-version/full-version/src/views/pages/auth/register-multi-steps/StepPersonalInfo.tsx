// ** MUI Components
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Button from '@mui/material/Button'
import Select from '@mui/material/Select'
import MenuItem from '@mui/material/MenuItem'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import InputLabel from '@mui/material/InputLabel'
import FormControl from '@mui/material/FormControl'
import InputAdornment from '@mui/material/InputAdornment'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const StepPersonalDetails = ({ handleNext, handlePrev }: { [key: string]: () => void }) => {
  return (
    <>
      <Box sx={{ mb: 4 }}>
        <Typography variant='h5'>Personal Information</Typography>
        <Typography sx={{ color: 'text.secondary' }}>Enter Your Personal Information</Typography>
      </Box>

      <Grid container spacing={5}>
        <Grid item xs={12} sm={6}>
          <TextField fullWidth placeholder='john' label='First Name' />
        </Grid>

        <Grid item xs={12} sm={6}>
          <TextField fullWidth label='Last Name' placeholder='Doe' />
        </Grid>
        <Grid item xs={12} sm={6}>
          <TextField
            fullWidth
            label='Mobile'
            placeholder='202 555 0111'
            InputProps={{
              startAdornment: <InputAdornment position='start'>US (+1)</InputAdornment>
            }}
          />
        </Grid>
        <Grid item xs={12} sm={6}>
          <TextField fullWidth type='number' label='Pincode' placeholder='689421' />
        </Grid>
        <Grid item xs={12}>
          <FormControl fullWidth>
            <TextField label='Address' placeholder='7777, Mendez Plains, Florida' />
          </FormControl>
        </Grid>
        <Grid item xs={12}>
          <TextField fullWidth label='Landmark' placeholder='Mendez Plains' />
        </Grid>
        <Grid item xs={12} sm={6}>
          <TextField fullWidth label='City' placeholder='Miami' />
        </Grid>
        <Grid item xs={12} sm={6}>
          <FormControl fullWidth>
            <InputLabel id='state-select'>State</InputLabel>
            <Select labelId='state-select' label='State' defaultValue='New York'>
              <MenuItem value='New York'>New York</MenuItem>
              <MenuItem value='California'>California</MenuItem>
              <MenuItem value='Florida'>Florida</MenuItem>
              <MenuItem value='Washington'>Washington</MenuItem>
              <MenuItem value='Texas'>Texas</MenuItem>
            </Select>
          </FormControl>
        </Grid>
        <Grid item xs={12}>
          <Box sx={{ display: 'flex', justifyContent: 'space-between' }}>
            <Button
              color='secondary'
              variant='contained'
              onClick={handlePrev}
              startIcon={<Icon icon='mdi:chevron-left' fontSize={20} />}
            >
              Previous
            </Button>
            <Button variant='contained' onClick={handleNext} endIcon={<Icon icon='mdi:chevron-right' fontSize={20} />}>
              Next
            </Button>
          </Box>
        </Grid>
      </Grid>
    </>
  )
}

export default StepPersonalDetails
