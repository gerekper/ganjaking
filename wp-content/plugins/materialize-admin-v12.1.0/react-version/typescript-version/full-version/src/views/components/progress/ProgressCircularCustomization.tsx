// ** MUI Imports
import Box from '@mui/material/Box'
import { styled } from '@mui/material/styles'
import CircularProgress, { CircularProgressProps } from '@mui/material/CircularProgress'

const CircularProgressDeterminate = styled(CircularProgress)<CircularProgressProps>(({ theme }) => ({
  color: theme.palette.customColors.trackBg
}))

const CircularProgressIndeterminate = styled(CircularProgress)<CircularProgressProps>(({ theme }) => ({
  left: 0,
  position: 'absolute',
  animationDuration: '550ms',
  color: theme.palette.mode === 'light' ? '#1a90ff' : '#308fe8'
}))

const ProgressCircularCustomization = () => {
  return (
    <Box sx={{ position: 'relative' }}>
      <CircularProgressDeterminate variant='determinate' size={50} thickness={5} value={100} />
      <CircularProgressIndeterminate variant='indeterminate' disableShrink size={50} thickness={5} />
    </Box>
  )
}

export default ProgressCircularCustomization
