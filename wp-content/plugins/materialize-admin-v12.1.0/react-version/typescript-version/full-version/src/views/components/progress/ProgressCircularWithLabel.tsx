// ** React Imports
import { useEffect, useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'
import CircularProgress, { CircularProgressProps } from '@mui/material/CircularProgress'

const Progress = (props: CircularProgressProps) => {
  return (
    <Box sx={{ position: 'relative', display: 'inline-flex' }}>
      <CircularProgress variant='determinate' {...props} size={50} />
      <Box
        sx={{
          top: 0,
          left: 0,
          right: 0,
          bottom: 0,
          display: 'flex',
          position: 'absolute',
          alignItems: 'center',
          justifyContent: 'center'
        }}
      >
        <Typography variant='caption' component='div' color='text.secondary'>
          {`${Math.round(props.value as number)}%`}
        </Typography>
      </Box>
    </Box>
  )
}

const ProgressCircularWithLabel = () => {
  // ** State
  const [progress, setProgress] = useState<number>(10)

  useEffect(() => {
    const timer = setInterval(() => {
      setProgress(prevProgress => (prevProgress >= 100 ? 0 : prevProgress + 10))
    }, 800)

    return () => {
      clearInterval(timer)
    }
  }, [])

  return <Progress value={progress} />
}

export default ProgressCircularWithLabel
