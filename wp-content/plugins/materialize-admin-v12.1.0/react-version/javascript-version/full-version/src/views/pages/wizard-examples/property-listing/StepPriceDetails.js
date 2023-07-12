// ** MUI Imports
import Grid from '@mui/material/Grid'
import Radio from '@mui/material/Radio'
import Select from '@mui/material/Select'
import Checkbox from '@mui/material/Checkbox'
import MenuItem from '@mui/material/MenuItem'
import TextField from '@mui/material/TextField'
import FormLabel from '@mui/material/FormLabel'
import InputLabel from '@mui/material/InputLabel'
import RadioGroup from '@mui/material/RadioGroup'
import FormControl from '@mui/material/FormControl'
import InputAdornment from '@mui/material/InputAdornment'
import FormControlLabel from '@mui/material/FormControlLabel'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const StepPriceDetails = () => {
  return (
    <Grid container spacing={5}>
      <Grid item xs={12} md={6}>
        <TextField
          fullWidth
          type='number'
          placeholder='25,000'
          label='Expected Price'
          InputProps={{
            endAdornment: (
              <InputAdornment position='end'>
                <Icon icon='mdi:currency-usd' />
              </InputAdornment>
            )
          }}
        />
      </Grid>
      <Grid item xs={12} md={6}>
        <TextField
          fullWidth
          type='number'
          placeholder='500'
          label='Price Per SQFT'
          InputProps={{
            endAdornment: (
              <InputAdornment position='end'>
                <Icon icon='mdi:currency-usd' />
              </InputAdornment>
            )
          }}
        />
      </Grid>
      <Grid item xs={12} md={6}>
        <TextField
          fullWidth
          type='number'
          placeholder='50'
          label='Maintenance Charge'
          InputProps={{
            endAdornment: (
              <InputAdornment position='end'>
                <Icon icon='mdi:currency-usd' />
              </InputAdornment>
            )
          }}
        />
      </Grid>

      <Grid item xs={12} md={6}>
        <FormControl fullWidth>
          <InputLabel id='select-maintenance'>Maintenance Period</InputLabel>
          <Select labelId='select-maintenance' label='Maintenance Period' defaultValue=''>
            <MenuItem value='Monthly'>Monthly</MenuItem>
            <MenuItem value='Quarterly'>Quarterly</MenuItem>
            <MenuItem value='Yearly'>Yearly</MenuItem>
            <MenuItem value='One-time'>One-time</MenuItem>
            <MenuItem value='Per Sqft.Monthly'>Per Sqft.Monthly</MenuItem>
          </Select>
        </FormControl>
      </Grid>
      <Grid item xs={12} md={6}>
        <TextField
          fullWidth
          type='number'
          placeholder='250'
          label='Booking/Token Amount'
          InputProps={{
            endAdornment: (
              <InputAdornment position='end'>
                <Icon icon='mdi:currency-usd' />
              </InputAdornment>
            )
          }}
        />
      </Grid>
      <Grid item xs={12} md={6}>
        <TextField
          fullWidth
          type='number'
          placeholder='500'
          label='Other Amount'
          InputProps={{
            endAdornment: (
              <InputAdornment position='end'>
                <Icon icon='mdi:currency-usd' />
              </InputAdornment>
            )
          }}
        />
      </Grid>
      <Grid item xs={12} md={6}>
        <FormControl>
          <FormLabel id='price-radio'>Show Price As</FormLabel>
          <RadioGroup name='price-group' defaultValue='negotiable' aria-labelledby='price-radio'>
            <FormControlLabel value='negotiable' control={<Radio />} label='Negotiable' />
            <FormControlLabel value='call-for-price' control={<Radio />} label='Call For Price' />
          </RadioGroup>
        </FormControl>
      </Grid>
      <Grid item xs={12} md={6}>
        <FormControl component='fieldset'>
          <FormLabel component='legend'>Price Includes</FormLabel>
          <FormControlLabel control={<Checkbox />} label='Car Parking' />
          <FormControlLabel control={<Checkbox />} label='Club Membership' />
        </FormControl>
      </Grid>
    </Grid>
  )
}

export default StepPriceDetails
