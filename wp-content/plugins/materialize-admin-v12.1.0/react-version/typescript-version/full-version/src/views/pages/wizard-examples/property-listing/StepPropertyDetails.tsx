// ** React Imports
import { ChangeEvent, useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'
import Select from '@mui/material/Select'
import MenuItem from '@mui/material/MenuItem'
import TextField from '@mui/material/TextField'
import { useTheme } from '@mui/material/styles'
import InputLabel from '@mui/material/InputLabel'
import Typography from '@mui/material/Typography'
import FormControl from '@mui/material/FormControl'

// ** Type Imports
import { CustomRadioIconsData, CustomRadioIconsProps } from 'src/@core/components/custom-radio/types'

// ** Custom Components Imports
import CustomRadioIcons from 'src/@core/components/custom-radio/icons'

interface IconType {
  icon: CustomRadioIconsProps['icon']
  iconProps: CustomRadioIconsProps['iconProps']
}

const data: CustomRadioIconsData[] = [
  {
    value: 'sale',
    isSelected: true,
    title: 'Sell the property',
    content: (
      <Typography variant='body2' sx={{ my: 'auto', textAlign: 'center' }}>
        Post your property for sale.
        <br />
        Unlimited free listing.
      </Typography>
    )
  },
  {
    value: 'rent',
    title: 'Rent the property',
    content: (
      <Typography variant='body2' sx={{ my: 'auto', textAlign: 'center' }}>
        Post your property for rent.
        <br />
        Unlimited free listing.
      </Typography>
    )
  }
]

const StepPropertyDetails = () => {
  const initialIconSelected: string = data.filter(item => item.isSelected)[
    data.filter(item => item.isSelected).length - 1
  ].value

  // ** State
  const [selectedRadio, setSelectedRadio] = useState<string>(initialIconSelected)

  // ** Hook
  const theme = useTheme()

  const icons: IconType[] = [
    {
      icon: 'mdi:home-outline',
      iconProps: { fontSize: '2rem', style: { marginBottom: 4 }, color: theme.palette.text.secondary }
    },
    {
      icon: 'mdi:wallet-outline',
      iconProps: { fontSize: '2rem', style: { marginBottom: 4 }, color: theme.palette.text.secondary }
    }
  ]

  const handleRadioChange = (prop: string | ChangeEvent<HTMLInputElement>) => {
    if (typeof prop === 'string') {
      setSelectedRadio(prop)
    } else {
      setSelectedRadio((prop.target as HTMLInputElement).value)
    }
  }

  return (
    <Grid container spacing={5}>
      {data.map((item, index) => (
        <CustomRadioIcons
          key={index}
          data={data[index]}
          icon={icons[index].icon}
          selected={selectedRadio}
          name='custom-radios-property'
          gridProps={{ sm: 6, xs: 12 }}
          handleChange={handleRadioChange}
          iconProps={icons[index].iconProps}
        />
      ))}
      <Grid item xs={12} md={6}>
        <FormControl fullWidth>
          <InputLabel htmlFor='validation-property-select'>Property Type</InputLabel>
          <Select label='Property Type' labelId='validation-property-select' defaultValue=''>
            <MenuItem value='Residential'>Residential</MenuItem>
            <MenuItem value='Commercial'>Commercial</MenuItem>
          </Select>
        </FormControl>
      </Grid>
      <Grid item xs={12} md={6}>
        <FormControl fullWidth>
          <TextField type='number' label='Zip Code' placeholder='99950' aria-describedby='validation-zip-code' />
        </FormControl>
      </Grid>
      <Grid item xs={12} md={6}>
        <FormControl fullWidth>
          <InputLabel htmlFor='country-select'>Country</InputLabel>
          <Select label='Country' labelId='country-select' aria-describedby='country-select' defaultValue=''>
            <MenuItem value='UK'>UK</MenuItem>
            <MenuItem value='USA'>USA</MenuItem>
            <MenuItem value='India'>India</MenuItem>
            <MenuItem value='Australia'>Australia</MenuItem>
            <MenuItem value='Germany'>Germany</MenuItem>
          </Select>
        </FormControl>
      </Grid>
      <Grid item xs={12} md={6}>
        <TextField fullWidth label='State' placeholder='California' />
      </Grid>
      <Grid item xs={12} md={6}>
        <TextField fullWidth label='City' placeholder='Los Angeles' />
      </Grid>
      <Grid item xs={12} md={6}>
        <TextField fullWidth label='Landmark' placeholder='Nr. Hard Rock Cafe' />
      </Grid>
      <Grid item xs={12}>
        <TextField fullWidth multiline minRows={2} label='Address' />
      </Grid>
    </Grid>
  )
}

export default StepPropertyDetails
