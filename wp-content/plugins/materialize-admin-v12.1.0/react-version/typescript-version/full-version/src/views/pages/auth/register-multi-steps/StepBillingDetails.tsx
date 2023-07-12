// ** React Imports
import { useState, ChangeEvent } from 'react'

// ** MUI Components
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Button from '@mui/material/Button'
import Tooltip from '@mui/material/Tooltip'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import FormControl from '@mui/material/FormControl'
import InputAdornment from '@mui/material/InputAdornment'

// ** Third Party Imports
import Payment from 'payment'

// ** Type Import
import { CustomRadioIconsData } from 'src/@core/components/custom-radio/types'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomRadioIcons from 'src/@core/components/custom-radio/icons'

// ** Util Import
import { formatCVC, formatExpirationDate, formatCreditCardNumber } from 'src/@core/utils/format'

// ** Styles Import
import 'react-credit-cards/es/styles-compiled.css'

const data: CustomRadioIconsData[] = [
  {
    value: 'basic',
    title: <Typography variant='h5'>Basic</Typography>,
    content: (
      <Box sx={{ my: 'auto', display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
        <Typography sx={{ textAlign: 'center', color: 'text.secondary' }}>
          A simple start for start ups & Students
        </Typography>
        <Box sx={{ mt: 2, display: 'flex' }}>
          <Typography component='sup' sx={{ mt: 1.5, color: 'primary.main', alignSelf: 'flex-start' }}>
            $
          </Typography>
          <Typography component='span' sx={{ fontSize: '2rem', color: 'primary.main' }}>
            0
          </Typography>
          <Typography component='sub' sx={{ mb: 1.5, alignSelf: 'flex-end', color: 'text.disabled' }}>
            /month
          </Typography>
        </Box>
      </Box>
    )
  },
  {
    isSelected: true,
    value: 'standard',
    title: <Typography variant='h5'>Standard</Typography>,
    content: (
      <Box sx={{ my: 'auto', display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
        <Typography sx={{ textAlign: 'center', color: 'text.secondary' }}>For small to medium businesses</Typography>
        <Box sx={{ mt: 2, display: 'flex' }}>
          <Typography component='sup' sx={{ mt: 1.5, color: 'primary.main', alignSelf: 'flex-start' }}>
            $
          </Typography>
          <Typography component='span' sx={{ fontSize: '2rem', fontWeight: 500, color: 'primary.main' }}>
            99
          </Typography>
          <Typography component='sub' sx={{ mb: 1.5, alignSelf: 'flex-end', color: 'text.disabled' }}>
            /month
          </Typography>
        </Box>
      </Box>
    )
  },
  {
    value: 'enterprise',
    title: <Typography variant='h5'>Enterprise</Typography>,
    content: (
      <Box sx={{ my: 'auto', display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
        <Typography sx={{ textAlign: 'center', color: 'text.secondary' }}>
          Solution for enterprise & organizations
        </Typography>
        <Box sx={{ mt: 2, display: 'flex' }}>
          <Typography component='sup' sx={{ mt: 1.5, color: 'primary.main', alignSelf: 'flex-start' }}>
            $
          </Typography>
          <Typography component='span' sx={{ fontSize: '2rem', color: 'primary.main' }}>
            499
          </Typography>
          <Typography component='sub' sx={{ mb: 1.5, alignSelf: 'flex-end', color: 'text.disabled' }}>
            /month
          </Typography>
        </Box>
      </Box>
    )
  }
]

const StepBillingDetails = ({ handlePrev }: { handlePrev: () => void }) => {
  const initialSelected: string = data.filter(item => item.isSelected)[data.filter(item => item.isSelected).length - 1]
    .value

  // ** State
  const [cvc, setCvc] = useState<string>('')
  const [name, setName] = useState<string>('')
  const [expiry, setExpiry] = useState<string>('')
  const [cardNumber, setCardNumber] = useState<string>('')
  const [selectedRadio, setSelectedRadio] = useState<string>(initialSelected)

  const handleInputChange = ({ target }: ChangeEvent<HTMLInputElement>) => {
    if (target.name === 'cardNumber') {
      target.value = formatCreditCardNumber(target.value, Payment)
      setCardNumber(target.value)
    } else if (target.name === 'expiry') {
      target.value = formatExpirationDate(target.value)
      setExpiry(target.value)
    } else if (target.name === 'cvc') {
      target.value = formatCVC(target.value, cardNumber, Payment)
      setCvc(target.value)
    }
  }

  const handleRadioChange = (prop: string | ChangeEvent<HTMLInputElement>) => {
    if (typeof prop === 'string') {
      setSelectedRadio(prop)
    } else {
      setSelectedRadio((prop.target as HTMLInputElement).value)
    }
  }

  return (
    <>
      <Box sx={{ mb: 4 }}>
        <Typography variant='h5'>Select Plan</Typography>
        <Typography sx={{ color: 'text.secondary' }}>Select plan as per your requirement</Typography>
      </Box>

      <Grid container spacing={5}>
        {data.map((item, index) => (
          <CustomRadioIcons
            key={index}
            data={data[index]}
            selected={selectedRadio}
            name='custom-radios-plan'
            gridProps={{ sm: 4, xs: 12 }}
            handleChange={handleRadioChange}
          />
        ))}

        <Grid item xs={12} sx={{ pt: theme => `${theme.spacing(11.5)} !important` }}>
          <Typography variant='h5'>Payment Information</Typography>
          <Typography sx={{ color: 'text.secondary' }}>Enter your card information</Typography>
        </Grid>
        <Grid item xs={12}>
          <FormControl fullWidth>
            <TextField
              fullWidth
              name='cardNumber'
              value={cardNumber}
              autoComplete='off'
              label='Card Number'
              onChange={handleInputChange}
              placeholder='0000 0000 0000 0000'
            />
          </FormControl>
        </Grid>
        <Grid item xs={12} sm={6}>
          <TextField
            fullWidth
            name='name'
            value={name}
            autoComplete='off'
            label='Name on Card'
            placeholder='John Doe'
            onChange={e => setName(e.target.value)}
          />
        </Grid>
        <Grid item xs={6} sm={3}>
          <TextField
            fullWidth
            name='expiry'
            label='Expiry'
            value={expiry}
            placeholder='MM/YY'
            onChange={handleInputChange}
            inputProps={{ maxLength: '5' }}
          />
        </Grid>
        <Grid item xs={6} sm={3}>
          <TextField
            fullWidth
            name='cvc'
            label='CVC'
            value={cvc}
            autoComplete='off'
            onChange={handleInputChange}
            InputProps={{
              endAdornment: (
                <InputAdornment position='start' sx={{ '& svg': { cursor: 'pointer' } }}>
                  <Tooltip title='Card Verification Value'>
                    <Box sx={{ display: 'flex' }}>
                      <Icon icon='mdi:help-circle-outline' fontSize={20} />
                    </Box>
                  </Tooltip>
                </InputAdornment>
              )
            }}
          />
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
            <Button color='success' variant='contained' onClick={() => alert('Submitted..!!')}>
              Submit
            </Button>
          </Box>
        </Grid>
      </Grid>
    </>
  )
}

export default StepBillingDetails
