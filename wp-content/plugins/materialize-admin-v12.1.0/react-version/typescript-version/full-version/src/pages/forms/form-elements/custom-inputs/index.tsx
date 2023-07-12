// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Custom Components Imports
import CardSnippet from 'src/@core/components/card-snippet'

// ** Demo Components Imports
import CustomRadioImg from 'src/views/forms/form-elements/custom-inputs/CustomRadioImg'
import CustomRadioBasic from 'src/views/forms/form-elements/custom-inputs/CustomRadioBasic'
import CustomRadioIcons from 'src/views/forms/form-elements/custom-inputs/CustomRadioIcons'
import CustomCheckboxImg from 'src/views/forms/form-elements/custom-inputs/CustomCheckboxImg'
import CustomCheckboxBasic from 'src/views/forms/form-elements/custom-inputs/CustomCheckboxBasic'
import CustomCheckboxIcons from 'src/views/forms/form-elements/custom-inputs/CustomCheckboxIcons'

// ** Source code imports
import * as source from 'src/views/forms/form-elements/custom-inputs/CustomInputsSourceCode'

const CustomInputs = () => {
  return (
    <Grid container spacing={6} className='match-height'>
      <Grid item xs={12} lg={6}>
        <CardSnippet
          title='Custom Radios'
          code={{
            tsx: source.CustomRadioBasicTSXCode,
            jsx: source.CustomRadioBasicJSXCode
          }}
        >
          <CustomRadioBasic />
        </CardSnippet>
      </Grid>
      <Grid item xs={12} lg={6}>
        <CardSnippet
          title='Custom Checkboxes'
          code={{
            tsx: source.CustomCheckboxBasicTSXCode,
            jsx: source.CustomCheckboxBasicJSXCode
          }}
        >
          <CustomCheckboxBasic />
        </CardSnippet>
      </Grid>
      <Grid item xs={12} lg={6}>
        <CardSnippet
          title='Custom Radios with Icons'
          code={{
            tsx: source.CustomRadioIconsTSXCode,
            jsx: source.CustomRadioIconsJSXCode
          }}
        >
          <CustomRadioIcons />
        </CardSnippet>
      </Grid>
      <Grid item xs={12} lg={6}>
        <CardSnippet
          title='Custom Checkboxes with Icons'
          code={{
            tsx: source.CustomCheckboxIconsTSXCode,
            jsx: source.CustomCheckboxIconsJSXCode
          }}
        >
          <CustomCheckboxIcons />
        </CardSnippet>
      </Grid>
      <Grid item xs={12} lg={6}>
        <CardSnippet
          title='Custom Radios with Images'
          code={{
            tsx: source.CustomRadioImgTSXCode,
            jsx: source.CustomRadioImgJSXCode
          }}
        >
          <CustomRadioImg />
        </CardSnippet>
      </Grid>
      <Grid item xs={12} lg={6}>
        <CardSnippet
          title='Custom Checkboxes with Images'
          code={{
            tsx: source.CustomCheckboxImgTSXCode,
            jsx: source.CustomCheckboxImgJSXCode
          }}
        >
          <CustomCheckboxImg />
        </CardSnippet>
      </Grid>
    </Grid>
  )
}

export default CustomInputs
