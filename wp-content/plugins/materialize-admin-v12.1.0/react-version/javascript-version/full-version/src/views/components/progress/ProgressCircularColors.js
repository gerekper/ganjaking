// ** MUI Imports
import CircularProgress from '@mui/material/CircularProgress'

const ProgressCircularColors = () => {
  return (
    <div className='demo-space-x'>
      <CircularProgress color='secondary' />
      <CircularProgress color='success' />
      <CircularProgress color='error' />
      <CircularProgress color='warning' />
      <CircularProgress color='info' />
    </div>
  )
}

export default ProgressCircularColors
