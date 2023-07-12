// ** React Imports
import { useEffect, useRef, useState } from 'react'

// ** MUI Imports
import LinearProgress from '@mui/material/LinearProgress'

const ProcessLinearBuffer = () => {
  // ** States
  const [buffer, setBuffer] = useState<number>(10)
  const [progress, setProgress] = useState<number>(0)

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
