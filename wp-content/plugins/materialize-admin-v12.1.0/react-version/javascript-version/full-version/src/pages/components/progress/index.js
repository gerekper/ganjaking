// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Custom Components Imports
import Typography from '@mui/material/Typography'
import CardSnippet from 'src/@core/components/card-snippet'

// ** Demo Components Imports
import ProgressLinearBuffer from 'src/views/components/progress/ProgressLinearBuffer'
import ProgressLinearColors from 'src/views/components/progress/ProgressLinearColors'
import ProgressCircularColors from 'src/views/components/progress/ProgressCircularColors'
import ProgressLinearWithLabel from 'src/views/components/progress/ProgressLinearWithLabel'
import ProgressCustomization from 'src/views/components/progress/ProgressCircularCustomization'
import ProgressCircularWithLabel from 'src/views/components/progress/ProgressCircularWithLabel'
import ProgressLinearCustomization from 'src/views/components/progress/ProgressLinearCustomization'
import ProgressLinearIndeterminate from 'src/views/components/progress/ProgressLinearIndeterminate'
import ProgressCircularIndeterminate from 'src/views/components/progress/ProgressCircularIndeterminate'
import ProgressLinearControlledUncontrolled from 'src/views/components/progress/ProgressLinearControlledUncontrolled'
import ProgressCircularControlledUncontrolled from 'src/views/components/progress/ProgressCircularControlledUncontrolled'

// ** Source code imports
import * as source from 'src/views/components/progress/ProgressSourceCode'

const Progress = () => {
  return (
    <Grid container spacing={6} className='match-height'>
      <Grid item xs={12}>
        <Typography variant='h5'>Circular</Typography>
      </Grid>
      <Grid item lg={6} xs={12}>
        <CardSnippet
          title='Indeterminate'
          code={{
            tsx: null,
            jsx: source.ProgressCircularIndeterminateJSXCode
          }}
        >
          <Typography sx={{ mb: 4 }}>
            Use <code>CircularProgress</code> component for simple circular progress.
          </Typography>
          <ProgressCircularIndeterminate />
        </CardSnippet>
      </Grid>
      <Grid item lg={6} xs={12}>
        <CardSnippet
          title='Colors'
          code={{
            tsx: null,
            jsx: source.ProgressCircularColorsJSXCode
          }}
        >
          <Typography sx={{ mb: 4 }}>
            Use <code>color</code> prop for different colored circular progress.
          </Typography>
          <ProgressCircularColors />
        </CardSnippet>
      </Grid>
      <Grid item lg={6} xs={12}>
        <CardSnippet
          title='With label'
          code={{
            tsx: null,
            jsx: source.ProgressCircularWithLabelJSXCode
          }}
        >
          <Typography sx={{ mb: 4 }}>
            Use <code>variant='determinate'</code> and <code>value</code> props with the help of a state for circular
            progress with label.
          </Typography>
          <ProgressCircularWithLabel />
        </CardSnippet>
      </Grid>
      <Grid item lg={6} xs={12}>
        <CardSnippet
          title='Customization'
          code={{
            tsx: null,
            jsx: source.ProgressCircularCustomizationJSXCode
          }}
        >
          <Typography sx={{ mb: 4 }}>
            Use <code>styled</code> hook to customize your circular progress.
          </Typography>
          <ProgressCustomization />
        </CardSnippet>
      </Grid>
      <Grid item xs={12}>
        <CardSnippet
          title='Controlled and Uncontrolled'
          code={{
            tsx: null,
            jsx: source.ProgressCircularControlledUncontrolledJSXCode
          }}
        >
          <Typography sx={{ mb: 4 }}>
            Manage <code>value</code> prop with the help of a state for controlled circular progress.
          </Typography>
          <ProgressCircularControlledUncontrolled />
        </CardSnippet>
      </Grid>
      <Grid item xs={12}>
        <Typography variant='h5'>Linear</Typography>
      </Grid>
      <Grid item lg={6} xs={12}>
        <CardSnippet
          title='Indeterminate'
          code={{
            tsx: null,
            jsx: source.ProgressLinearIndeterminateJSXCode
          }}
        >
          <Typography sx={{ mb: 4 }}>
            Use <code>LinearProgress</code> component for simple linear progress.
          </Typography>
          <ProgressLinearIndeterminate />
        </CardSnippet>
      </Grid>
      <Grid item lg={6} xs={12}>
        <CardSnippet
          title='Buffer'
          code={{
            tsx: null,
            jsx: source.ProgressLinearBufferJSXCode
          }}
        >
          <Typography sx={{ mb: 4 }}>
            Use <code>variant='buffer'</code> and <code>valueBuffer</code> props with the help of a state for linear
            progress with buffer.
          </Typography>
          <ProgressLinearBuffer />
        </CardSnippet>
      </Grid>
      <Grid item lg={6} xs={12}>
        <CardSnippet
          title='Colors'
          code={{
            tsx: null,
            jsx: source.ProgressLinearColorsJSXCode
          }}
        >
          <Typography sx={{ mb: 4 }}>
            Use <code>color</code> prop for different colored linear progress.
          </Typography>
          <ProgressLinearColors />
        </CardSnippet>
      </Grid>
      <Grid item lg={6} xs={12}>
        <CardSnippet
          title='Controlled and Uncontrolled'
          code={{
            tsx: null,
            jsx: source.ProgressLinearControlledUncontrolledJSXCode
          }}
        >
          <Typography sx={{ mb: 4 }}>
            Manage <code>value</code> prop with the help of a state for controlled linear progress.
          </Typography>
          <ProgressLinearControlledUncontrolled />
        </CardSnippet>
      </Grid>
      <Grid item lg={6} xs={12}>
        <CardSnippet
          title='With Label'
          code={{
            tsx: null,
            jsx: source.ProgressLinearWithLabelJSXCode
          }}
        >
          <Typography sx={{ mb: 4 }}>
            Use <code>variant='determinate'</code> and <code>value</code> props with the help of a state for linear
            progress with label.
          </Typography>
          <ProgressLinearWithLabel />
        </CardSnippet>
      </Grid>
      <Grid item lg={6} xs={12}>
        <CardSnippet
          title='Customization'
          code={{
            tsx: null,
            jsx: source.ProgressLinearCustomizationJSXCode
          }}
        >
          <Typography sx={{ mb: 4 }}>
            Use <code>styled</code> hook to customize your Linear progress.
          </Typography>
          <ProgressLinearCustomization />
        </CardSnippet>
      </Grid>
    </Grid>
  )
}

export default Progress
