// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'
import TextField from '@mui/material/TextField'
import { useTheme } from '@mui/material/styles'
import IconButton from '@mui/material/IconButton'
import FormControl from '@mui/material/FormControl'
import InputAdornment from '@mui/material/InputAdornment'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomRadioIcons from 'src/@core/components/custom-radio/icons'

const data = [
  {
    value: 'builder',
    isSelected: true,
    title: 'I am the Builder',
    content: 'List property as Builder, list your project and get highest reach.'
  },
  {
    value: 'owner',
    title: 'I am the Owner',
    content: 'Submit property as an Individual. Lease, Rent or Sell at the best price.'
  },
  {
    value: 'broker',
    title: 'I am the Broker',
    content: 'Earn highest commission by listing your clients properties at the best price.'
  }
]

const StepPersonalDetails = () => {
  const initialIconSelected = data.filter(item => item.isSelected)[data.filter(item => item.isSelected).length - 1]
    .value

  // ** States
  const [showValues, setShowValues] = useState(false)
  const [selectedRadio, setSelectedRadio] = useState(initialIconSelected)

  // ** Hook
  const theme = useTheme()

  const icons = [
    {
      icon: 'mdi:office-building-outline',
      iconProps: { fontSize: '2rem', style: { marginBottom: 4 }, color: theme.palette.text.secondary }
    },
    {
      icon: 'mdi:crown-outline',
      iconProps: { fontSize: '2rem', style: { marginBottom: 4 }, color: theme.palette.text.secondary }
    },
    {
      icon: 'mdi:briefcase-outline',
      iconProps: { fontSize: '2rem', style: { marginBottom: 4 }, color: theme.palette.text.secondary }
    }
  ]

  const handleTogglePasswordView = () => {
    setShowValues(!showValues)
  }

  const handleRadioChange = prop => {
    if (typeof prop === 'string') {
      setSelectedRadio(prop)
    } else {
      setSelectedRadio(prop.target.value)
    }
  }

  return (
    <Grid container spacing={5}>
      {data.map((item, index) => (
        <CustomRadioIcons
          key={index}
          data={data[index]}
          name='custom-radios'
          icon={icons[index].icon}
          selected={selectedRadio}
          gridProps={{ sm: 4, xs: 12 }}
          handleChange={handleRadioChange}
          iconProps={icons[index].iconProps}
        />
      ))}
      <Grid item xs={12} md={6}>
        <FormControl fullWidth>
          <TextField label='First Name' placeholder='John' />
        </FormControl>
      </Grid>
      <Grid item xs={12} md={6}>
        <FormControl fullWidth>
          <TextField label='Last Name' placeholder='Doe' />
        </FormControl>
      </Grid>
      <Grid item xs={12} md={6}>
        <TextField fullWidth label='Username' placeholder='john.doe' />
      </Grid>
      <Grid item xs={12} md={6}>
        <TextField
          fullWidth
          label='Password'
          type={showValues ? 'text' : 'password'}
          InputProps={{
            endAdornment: (
              <InputAdornment position='end'>
                <IconButton
                  edge='end'
                  onClick={handleTogglePasswordView}
                  onMouseDown={e => e.preventDefault()}
                  aria-label='toggle password visibility'
                >
                  <Icon icon={showValues ? 'mdi:eye-outline' : 'mdi:eye-off-outline'} />
                </IconButton>
              </InputAdornment>
            )
          }}
        />
      </Grid>
      <Grid item xs={12} md={6}>
        <TextField fullWidth type='email' label='Email' placeholder='john.doe@email.com' />
      </Grid>
      <Grid item xs={12} md={6}>
        <TextField
          fullWidth
          label='Contact'
          placeholder='202 555 0111'
          InputProps={{
            startAdornment: <InputAdornment position='start'>US (+1)</InputAdornment>
          }}
        />
      </Grid>
    </Grid>
  )
}

export default StepPersonalDetails
