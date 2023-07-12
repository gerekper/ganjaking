// ** React Imports
import { useEffect, useState } from 'react'

// ** MUI Imports
import React from 'react'
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'
import CircularProgress from '@mui/material/CircularProgress'

const ProgressCircularControlledUncontrolled = () => {
  // ** State
  const [progress, setProgress] = useState(0)
  useEffect(() => {
    const timer = setInterval(() => {
      setProgress(prevProgress => (prevProgress >= 100 ? 0 : prevProgress + 10))
    }, 800)

    return () => {
      clearInterval(timer)
    }
  }, [])

  return (
    <Grid container spacing={6}>
      <Grid item xs={12} md={6}>
        <Typography sx={{ fontWeight: 500 }}>Uncontrolled Progress</Typography>
        <div className='demo-space-x'>
          <CircularProgress variant='determinate' value={25} />
          <CircularProgress variant='determinate' value={50} />
          <CircularProgress variant='determinate' value={75} />
          <CircularProgress variant='determinate' value={100} />
        </div>
      </Grid>
      <Grid item xs={12} md={6}>
        <Typography sx={{ fontWeight: 500, mb: 4 }}>Controlled Progress</Typography>
        <CircularProgress variant='determinate' value={progress} />
      </Grid>
    </Grid>
  )
}

export default ProgressCircularControlledUncontrolled
