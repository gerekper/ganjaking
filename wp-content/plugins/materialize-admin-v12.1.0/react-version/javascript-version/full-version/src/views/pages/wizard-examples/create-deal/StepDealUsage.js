// ** MUI Imports
import Grid from '@mui/material/Grid'
import Select from '@mui/material/Select'
import Switch from '@mui/material/Switch'
import MenuItem from '@mui/material/MenuItem'
import TextField from '@mui/material/TextField'
import InputLabel from '@mui/material/InputLabel'
import FormControl from '@mui/material/FormControl'
import FormControlLabel from '@mui/material/FormControlLabel'

const StepDealUsage = () => {
  return (
    <Grid container spacing={5}>
      <Grid item xs={12} sm={6}>
        <FormControl fullWidth>
          <InputLabel id='select-user-type'>User Type</InputLabel>
          <Select labelId='select-user-type' label='User Type' defaultValue=''>
            <MenuItem value='all'>All</MenuItem>
            <MenuItem value='registered'>Registered</MenuItem>
            <MenuItem value='unregistered'>Unregistered</MenuItem>
            <MenuItem value='prime-members'>Prime Members</MenuItem>
          </Select>
        </FormControl>
      </Grid>
      <Grid item xs={12} sm={6}>
        <TextField fullWidth type='number' label='Max Users' placeholder='500' />
      </Grid>
      <Grid item xs={12} sm={6}>
        <TextField fullWidth type='number' label='Minimum Cart Amount' placeholder='$99' />
      </Grid>
      <Grid item xs={12} sm={6}>
        <TextField fullWidth type='number' label='Promotion Fee' placeholder='$9' />
      </Grid>
      <Grid item xs={12} sm={6}>
        <FormControl fullWidth>
          <InputLabel id='select-payment-method'>Payment Method</InputLabel>
          <Select labelId='select-payment-method' label='Payment Method' defaultValue=''>
            <MenuItem value='any'>any</MenuItem>
            <MenuItem value='credit-card'>Credit Card</MenuItem>
            <MenuItem value='net-banking'>Net Banking</MenuItem>
            <MenuItem value='wallet'>Wallet</MenuItem>
          </Select>
        </FormControl>
      </Grid>
      <Grid item xs={12} sm={6}>
        <FormControl fullWidth>
          <InputLabel id='select-deal-status'>Deal Status</InputLabel>
          <Select labelId='select-deal-status' label='Deal Status' defaultValue=''>
            <MenuItem value='active'>Active</MenuItem>
            <MenuItem value='inactive'>Inactive</MenuItem>
            <MenuItem value='suspended'>Suspended</MenuItem>
            <MenuItem value='abandoned'>Abandoned</MenuItem>
          </Select>
        </FormControl>
      </Grid>
      <Grid item xs={12}>
        <FormControlLabel control={<Switch />} label='Limit this discount to a single-use per customer?' />
      </Grid>
    </Grid>
  )
}

export default StepDealUsage
