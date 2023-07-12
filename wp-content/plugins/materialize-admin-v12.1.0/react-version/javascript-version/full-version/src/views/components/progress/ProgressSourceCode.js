export const ProgressCircularColorsJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
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
`}</code></pre>) 

export const ProgressCircularControlledUncontrolledJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
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
`}</code></pre>) 

export const ProgressCircularCustomizationJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import Box from '@mui/material/Box'
import { styled } from '@mui/material/styles'
import CircularProgress from '@mui/material/CircularProgress'

const CircularProgressDeterminate = styled(CircularProgress)(({ theme }) => ({
  color: theme.palette.customColors.trackBg
}))

const CircularProgressIndeterminate = styled(CircularProgress)(({ theme }) => ({
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
`}</code></pre>) 

export const ProgressCircularWithLabelJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useEffect, useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'
import CircularProgress from '@mui/material/CircularProgress'

const Progress = props => {
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
          {{Math.round(props.value)}%}
        </Typography>
      </Box>
    </Box>
  )
}

const ProgressCircularWithLabel = () => {
  // ** State
  const [progress, setProgress] = useState(10)
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
`}</code></pre>) 

export const ProgressLinearControlledUncontrolledJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useEffect, useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'
import LinearProgress from '@mui/material/LinearProgress'

const ProgressLinearControlledUncontrolled = () => {
  // ** State
  const [progress, setProgress] = useState(0)
  useEffect(() => {
    const timer = setInterval(() => {
      setProgress(oldProgress => {
        if (oldProgress === 100) {
          return 0
        }
        const diff = Math.random() * 10

        return Math.min(oldProgress + diff, 100)
      })
    }, 500)

    return () => {
      clearInterval(timer)
    }
  }, [])

  return (
    <>
      <Box sx={{ mb: 4 }}>
        <Typography sx={{ fontWeight: 500, mb: 1.5 }}>Uncontrolled Progress</Typography>
        <LinearProgress variant='determinate' value={40} />
      </Box>
      <div>
        <Typography sx={{ fontWeight: 500, mb: 1.5 }}>Controlled Progress</Typography>
        <LinearProgress variant='determinate' value={progress} />
      </div>
    </>
  )
}

export default ProgressLinearControlledUncontrolled
`}</code></pre>) 

export const ProgressLinearBufferJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useEffect, useRef, useState } from 'react'

// ** MUI Imports
import LinearProgress from '@mui/material/LinearProgress'

const ProcessLinearBuffer = () => {
  // ** States
  const [buffer, setBuffer] = useState(10)
  const [progress, setProgress] = useState(0)
  // eslint-disable-next-line
  const progressRef = useRef(() => {})
  useEffect(() => {
    progressRef.current = () => {
      if (progress > 100) {
        setProgress(0)
        setBuffer(10)
      } else {
        const diff = Math.random() * 10
        const diff2 = Math.random() * 10
        setProgress(progress + diff)
        setBuffer(progress + diff + diff2)
      }
    }
  })
  useEffect(() => {
    const timer = setInterval(() => {
      progressRef.current()
    }, 500)

    return () => {
      clearInterval(timer)
    }
  }, [])

  return <LinearProgress variant='buffer' value={progress} valueBuffer={buffer} />
}

export default ProcessLinearBuffer
`}</code></pre>) 

export const ProgressLinearColorsJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
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
`}</code></pre>) 

export const ProgressLinearIndeterminateJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import LinearProgress from '@mui/material/LinearProgress'

const ProgressLinearIndeterminate = () => {
  return <LinearProgress />
}

export default ProgressLinearIndeterminate
`}</code></pre>) 

export const ProgressLinearCustomizationJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import React from 'react'

// ** MUI Imports
import { styled } from '@mui/material/styles'
import LinearProgress, { linearProgressClasses } from '@mui/material/LinearProgress'

const BorderLinearProgress = styled(LinearProgress)(({ theme }) => ({
  height: 10,
  borderRadius: 5,
  [&.{linearProgressClasses.colorPrimary}]: {
    backgroundColor: theme.palette.customColors.trackBg
  },
  [& .{linearProgressClasses.bar}]: {
    borderRadius: 5,
    backgroundColor: theme.palette.mode === 'light' ? '#1a90ff' : '#308fe8'
  }
}))

const ProcessLinearCustomization = () => {
  return <BorderLinearProgress variant='determinate' value={70} />
}

export default ProcessLinearCustomization
`}</code></pre>) 

export const ProgressLinearWithLabelJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useEffect, useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'
import LinearProgress from '@mui/material/LinearProgress'

const LinearProgressWithLabel = props => {
  return (
    <Box sx={{ display: 'flex', alignItems: 'center' }}>
      <Box sx={{ width: '100%', mr: 1 }}>
        <LinearProgress variant='determinate' {...props} />
      </Box>
      <Box sx={{ minWidth: 35 }}>
        <Typography variant='body2' color='text.secondary'>{{Math.round(props.value)}%}</Typography>
      </Box>
    </Box>
  )
}

export default function ProcessLinearWithLabel() {
  // ** State
  const [progress, setProgress] = useState(10)
  useEffect(() => {
    const timer = setInterval(() => {
      setProgress(prevProgress => (prevProgress >= 100 ? 10 : prevProgress + 10))
    }, 800)

    return () => {
      clearInterval(timer)
    }
  }, [])

  return <LinearProgressWithLabel value={progress} />
}
`}</code></pre>) 

export const ProgressCircularIndeterminateJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import CircularProgress from '@mui/material/CircularProgress'

const ProgressCircularIndeterminate = () => {
  return <CircularProgress />
}

export default ProgressCircularIndeterminate
`}</code></pre>) 

