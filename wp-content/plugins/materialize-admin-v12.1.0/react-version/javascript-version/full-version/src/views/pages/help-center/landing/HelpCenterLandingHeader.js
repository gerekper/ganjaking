// ** React Imports
import { useState } from 'react'

// ** Next Import
import { useRouter } from 'next/router'

// ** MUI Imports
import { styled } from '@mui/material/styles'
import ListItem from '@mui/material/ListItem'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import ListItemButton from '@mui/material/ListItemButton'
import InputAdornment from '@mui/material/InputAdornment'
import MuiAutocomplete from '@mui/material/Autocomplete'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// Styled Autocomplete component
const Autocomplete = styled(MuiAutocomplete)(({ theme }) => ({
  '& .MuiOutlinedInput-root': {
    paddingLeft: theme.spacing(3.5),
    backgroundColor: theme.palette.background.paper
  },
  [theme.breakpoints.up('md')]: {
    width: '55%'
  },
  [theme.breakpoints.up('xl')]: {
    width: '45%'
  },
  [theme.breakpoints.down('md')]: {
    width: '75%'
  },
  [theme.breakpoints.down('sm')]: {
    width: '100%'
  }
}))

const HelpCenterLandingHeader = ({ data, allArticles }) => {
  // ** States
  const [value, setValue] = useState('')
  const [open, setOpen] = useState(false)

  // ** Hooks & Vars
  const router = useRouter()

  const handleRedirection = option => {
    setOpen(false)
    setValue(option.title)
    let currentSubcategory = null

    const currentCategory = data.find(category =>
      category.subCategories.find(subcategory =>
        subcategory.articles.find(article => {
          if (option.slug === article.slug) {
            currentSubcategory = subcategory
          }

          return option.slug === article.slug
        })
      )
    )
    if (currentSubcategory !== null) {
      router.push(`/pages/help-center/${currentCategory?.slug}/${currentSubcategory.slug}/${option.slug}`)
    }
  }

  return (
    <CardContent
      sx={{
        display: 'flex',
        textAlign: 'center',
        alignItems: 'center',
        flexDirection: 'column',
        backgroundSize: 'cover',
        py: theme => `${theme.spacing(25)} !important`,
        backgroundImage: theme => `url(/images/pages/pages-header-bg-${theme.palette.mode}.png)`
      }}
    >
      <Typography variant='h5' sx={{ fontWeight: 600, fontSize: '1.5rem !important' }}>
        Hello, how can we help?
      </Typography>

      <Autocomplete
        open={open}
        disablePortal
        inputValue={value}
        options={allArticles}
        onClose={() => setOpen(false)}
        sx={{ my: 4, '& + .MuiAutocomplete-popper .MuiAutocomplete-listbox': { maxHeight: 250 } }}
        getOptionLabel={option => option.title || ''}
        isOptionEqualToValue={(option, value) => value === option}
        onChange={(event, option) => handleRedirection(option)}
        onInputChange={(event, value) => {
          setValue(value)
          setOpen(!!event.target.value)
        }}
        renderInput={params => (
          <TextField
            {...params}
            value={value}
            placeholder='Search a question...'
            onChange={event => setValue(event.target.value)}
            InputProps={{
              ...params.InputProps,
              startAdornment: (
                <InputAdornment position='start' sx={{ color: 'text.secondary' }}>
                  <Icon icon='mdi:magnify' />
                </InputAdornment>
              )
            }}
          />
        )}
        renderOption={(props, option) => {
          return value.length ? (
            <ListItem {...props} sx={{ p: '0 !important' }} key={option.slug} onClick={() => handleRedirection(option)}>
              <ListItemButton sx={{ py: 1.5 }}>{option.title}</ListItemButton>
            </ListItem>
          ) : null
        }}
      />

      <Typography variant='body2'>Common troubleshooting topics: eCommerce, Blogging to payment</Typography>
    </CardContent>
  )
}

export default HelpCenterLandingHeader
