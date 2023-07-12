// ** MUI Imports
import Stack from '@mui/material/Stack'
import LinearProgress from '@mui/material/LinearProgress'

const ProgressLinearColors = () => {
  return (
    <Stack spacing={4} sx={{ width: '100%' }}>
      <LinearProgress color='secondary' />
      <LinearProgress color='success' />
      <LinearProgress color='error' />
      <LinearProgress color='warning' />
      <LinearProgress color='info' />
    </Stack>
  )
}

export default ProgressLinearColors
