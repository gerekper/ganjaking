// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Grid from '@mui/material/Grid'
import { styled } from '@mui/material/styles'
import { useTheme } from '@mui/material/styles'
import Typography from '@mui/material/Typography'

// ** Custom Components Imports
import PageHeader from 'src/@core/components/page-header'
import CardSnippet from 'src/@core/components/card-snippet'

// ** Styled Component
import DatePickerWrapper from 'src/@core/styles/libs/react-datepicker'

// ** Demo Components Imports
import PickersTime from 'src/views/forms/form-elements/pickers/PickersTime'
import PickersBasic from 'src/views/forms/form-elements/pickers/PickersBasic'
import PickersRange from 'src/views/forms/form-elements/pickers/PickersRange'
import PickersMinMax from 'src/views/forms/form-elements/pickers/PickersMinMax'
import PickersLocale from 'src/views/forms/form-elements/pickers/PickersLocale'
import PickersOptions from 'src/views/forms/form-elements/pickers/PickersOptions'
import PickersCallbacks from 'src/views/forms/form-elements/pickers/PickersCallbacks'
import PickersSpecificRange from 'src/views/forms/form-elements/pickers/PickersSpecificRange'
import PickersCustomization from 'src/views/forms/form-elements/pickers/PickersCustomization'
import PickersIncludeExclude from 'src/views/forms/form-elements/pickers/PickersIncludeExclude'
import PickersMonthYearQuarter from 'src/views/forms/form-elements/pickers/PickersMonthYearQuarter'
import PickersMonthYearDropdowns from 'src/views/forms/form-elements/pickers/PickersMonthYearDropdowns'

// ** Source code imports
import * as source from 'src/views/forms/form-elements/pickers/PickersSourceCode'

const LinkStyled = styled(Link)(({ theme }) => ({
  textDecoration: 'none',
  color: theme.palette.primary.main
}))

const ReactDatePicker = () => {
  // ** Hook
  const theme = useTheme()
  const { direction } = theme
  const popperPlacement = direction === 'ltr' ? 'bottom-start' : 'bottom-end'

  return (
    <DatePickerWrapper>
      <Grid container spacing={6}>
        <PageHeader
          subtitle={<Typography variant='body2'>A simple and reusable datepicker component for React</Typography>}
          title={
            <Typography variant='h5'>
              <LinkStyled href='https://github.com/Hacker0x01/react-datepicker/' target='_blank'>
                React DatePicker
              </LinkStyled>
            </Typography>
          }
        />
        <Grid item xs={12}>
          <CardSnippet title='Date Pickers' code={{ tsx: null, jsx: source.PickersBasicJSXCode }}>
            <PickersBasic popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
        <Grid item xs={12}>
          <CardSnippet title='Time Pickers' code={{ tsx: null, jsx: source.PickersTimeJSXCode }}>
            <PickersTime popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
        <Grid item xs={12}>
          <CardSnippet
            title='Min & Max Pickers'
            code={{ tsx: null, jsx: source.PickersMinMaxJSXCode }}
          >
            <PickersMinMax popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
        <Grid item xs={12}>
          <CardSnippet
            title='Date Range Pickers'
            code={{ tsx: null, jsx: source.PickersRangeJSXCode }}
          >
            <PickersRange popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
        <Grid item xs={12}>
          <CardSnippet
            title='Specific Range'
            code={{ tsx: null, jsx: source.PickersSpecificRangeJSXCode }}
          >
            <PickersSpecificRange popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
        <Grid item xs={12}>
          <CardSnippet
            title='Callbacks'
            code={{ tsx: null, jsx: source.PickersCallbacksJSXCode }}
          >
            <PickersCallbacks popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
        <Grid item xs={12}>
          <CardSnippet
            title='Customization'
            code={{ tsx: null, jsx: source.PickersCustomizationJSXCode }}
          >
            <PickersCustomization popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
        <Grid item xs={12}>
          <CardSnippet
            title='Include Exclude'
            code={{ tsx: null, jsx: source.PickersIncludeExcludeJSXCode }}
          >
            <PickersIncludeExclude popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
        <Grid item xs={12}>
          <CardSnippet title='Locale' code={{ tsx: null, jsx: source.PickersLocaleJSXCode }}>
            <PickersLocale popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
        <Grid item xs={12}>
          <CardSnippet
            title='Month & Year Dropdowns'
            code={{ tsx: null, jsx: source.PickersMonthYearDropdownsJSXCode }}
          >
            <PickersMonthYearDropdowns popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
        <Grid item xs={12}>
          <CardSnippet
            title='Month, Year & Quarter'
            code={{ tsx: null, jsx: source.PickersMonthYearQuarterJSXCode }}
          >
            <PickersMonthYearQuarter popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
        <Grid item xs={12}>
          <CardSnippet title='Options' code={{ tsx: null, jsx: source.PickersOptionsJSXCode }}>
            <PickersOptions popperPlacement={popperPlacement} />
          </CardSnippet>
        </Grid>
      </Grid>
    </DatePickerWrapper>
  )
}

export default ReactDatePicker
