// ** MUI Imports
import Drawer from '@mui/material/Drawer'
import Button from '@mui/material/Button'
import Select from '@mui/material/Select'
import MenuItem from '@mui/material/MenuItem'
import { styled } from '@mui/material/styles'
import TextField from '@mui/material/TextField'
import InputLabel from '@mui/material/InputLabel'
import IconButton from '@mui/material/IconButton'
import Box, { BoxProps } from '@mui/material/Box'
import Typography from '@mui/material/Typography'
import FormControl from '@mui/material/FormControl'
import FormHelperText from '@mui/material/FormHelperText'

// ** Third Party Imports
import * as yup from 'yup'
import { yupResolver } from '@hookform/resolvers/yup'
import { useForm, Controller } from 'react-hook-form'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Types
import { InvoiceClientType } from 'src/types/apps/invoiceTypes'

interface Props {
  open: boolean
  toggle: () => void
  clients: InvoiceClientType[] | undefined
  setClients: (val: InvoiceClientType[]) => void
  setSelectedClient: (val: InvoiceClientType) => void
}

interface FormData {
  name: string
  email: string
  company: string
  address: string
  country: string
  contact: string
}

const Header = styled(Box)<BoxProps>(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  padding: theme.spacing(3, 4),
  justifyContent: 'space-between',
  backgroundColor: theme.palette.background.default
}))

const schema = yup.object().shape({
  name: yup.string().required(),
  email: yup.string().email().required(),
  company: yup.string().required(),
  contact: yup.string().min(10).max(10).required(),
  address: yup.string().max(120).required()
})

const AddNewCustomer = ({ open, toggle, setSelectedClient, clients, setClients }: Props) => {
  const {
    reset,
    control,
    handleSubmit,
    formState: { errors }
  } = useForm({
    resolver: yupResolver(schema),
    defaultValues: { name: '', email: '', company: '', address: '', country: 'USA', contact: '' }
  })

  const onSubmit = (data: FormData) => {
    const { address, company, contact, country, email, name } = data
    const finalData = {
      name,
      country,
      contact,
      company,
      address,
      companyEmail: email
    }
    if (clients !== undefined) {
      setClients([...clients, finalData])
    }
    setSelectedClient(finalData)

    toggle()
    reset({ name: '', email: '', company: '', address: '', country: 'USA', contact: '' })
  }

  const handleDrawerClose = () => {
    toggle()
    reset({ name: '', email: '', company: '', address: '', country: 'USA', contact: '' })
  }

  return (
    <Drawer
      open={open}
      anchor='right'
      variant='temporary'
      onClose={handleDrawerClose}
      ModalProps={{ keepMounted: true }}
      sx={{ '& .MuiDrawer-paper': { width: [300, 400] } }}
    >
      <Header>
        <Typography variant='h6'>Add New Customer</Typography>
        <IconButton size='small' onClick={toggle} sx={{ color: 'text.primary' }}>
          <Icon icon='mdi:close' fontSize={20} />
        </IconButton>
      </Header>
      <Box component='form' sx={{ p: 5 }} onSubmit={handleSubmit(onSubmit)}>
        <FormControl fullWidth sx={{ mb: 6 }}>
          <Controller
            name='name'
            control={control}
            rules={{ required: true }}
            render={({ field: { value, onChange } }) => (
              <TextField
                label='Name'
                value={value}
                variant='outlined'
                onChange={onChange}
                error={Boolean(errors.name)}
              />
            )}
          />
          {errors.name && (
            <FormHelperText sx={{ color: 'error.main' }} id='invoice-name-error'>
              {errors.name.message}
            </FormHelperText>
          )}
        </FormControl>
        <FormControl fullWidth sx={{ mb: 6 }}>
          <Controller
            name='company'
            control={control}
            rules={{ required: true }}
            render={({ field: { value, onChange } }) => (
              <TextField
                value={value}
                label='Company'
                variant='outlined'
                onChange={onChange}
                error={Boolean(errors.company)}
              />
            )}
          />
          {errors.company && (
            <FormHelperText sx={{ color: 'error.main' }} id='invoice-company-error'>
              {errors.company.message}
            </FormHelperText>
          )}
        </FormControl>
        <FormControl fullWidth sx={{ mb: 6 }}>
          <Controller
            name='email'
            control={control}
            rules={{ required: true }}
            render={({ field: { value, onChange } }) => (
              <TextField
                type='email'
                label='Email'
                value={value}
                variant='outlined'
                onChange={onChange}
                error={Boolean(errors.email)}
              />
            )}
          />
          {errors.email && (
            <FormHelperText sx={{ color: 'error.main' }} id='invoice-email-error'>
              {errors.email.message}
            </FormHelperText>
          )}
        </FormControl>
        <FormControl fullWidth sx={{ mb: 6 }}>
          <Controller
            name='address'
            control={control}
            rules={{ required: true }}
            render={({ field: { value, onChange } }) => (
              <TextField
                rows={6}
                multiline
                value={value}
                label='Address'
                variant='outlined'
                onChange={onChange}
                error={Boolean(errors.address)}
                placeholder='1037 Lady Bug  Drive New York'
              />
            )}
          />
          {errors.address && (
            <FormHelperText sx={{ color: 'error.main' }} id='invoice-address-error'>
              {errors.address.message}
            </FormHelperText>
          )}
        </FormControl>
        <FormControl fullWidth sx={{ mb: 6 }}>
          <InputLabel id='invoice-country'>Country</InputLabel>

          <Controller
            name='country'
            control={control}
            rules={{ required: true }}
            render={({ field: { value, onChange } }) => (
              <Select
                label='Country'
                value={value}
                onChange={onChange}
                labelId='invoice-country'
                error={Boolean(errors.country)}
              >
                <MenuItem value='USA'>USA</MenuItem>
                <MenuItem value='UK'>UK</MenuItem>
                <MenuItem value='Russia'>Russia</MenuItem>
                <MenuItem value='Australia'>Australia</MenuItem>
                <MenuItem value='Canada'>Canada</MenuItem>
              </Select>
            )}
          />
          {errors.country && (
            <FormHelperText sx={{ color: 'error.main' }} id='invoice-country-error'>
              {errors.country.message}
            </FormHelperText>
          )}
        </FormControl>
        <FormControl fullWidth sx={{ mb: 6 }}>
          <Controller
            name='contact'
            control={control}
            rules={{ required: true }}
            render={({ field: { value, onChange } }) => (
              <TextField
                type='number'
                value={value}
                variant='outlined'
                onChange={onChange}
                label='Contact Number'
                placeholder='763-242-9206'
                error={Boolean(errors.contact)}
              />
            )}
          />
          {errors.contact && (
            <FormHelperText sx={{ color: 'error.main' }} id='invoice-contact-error'>
              {errors.contact.message}
            </FormHelperText>
          )}
        </FormControl>
        <div>
          <Button size='large' type='submit' variant='contained' sx={{ mr: 4 }}>
            Add
          </Button>
          <Button size='large' variant='outlined' color='secondary' onClick={handleDrawerClose}>
            Cancel
          </Button>
        </div>
      </Box>
    </Drawer>
  )
}

export default AddNewCustomer
