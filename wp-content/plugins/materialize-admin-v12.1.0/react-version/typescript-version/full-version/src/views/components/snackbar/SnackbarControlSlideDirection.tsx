// ** React Imports
import { ComponentType, Fragment, useState } from 'react'

// ** MUI Imports
import Button from '@mui/material/Button'
import Snackbar from '@mui/material/Snackbar'
import Slide, { SlideProps } from '@mui/material/Slide'

type TransitionProps = Omit<SlideProps, 'direction'>

const TransitionLeft = (props: TransitionProps) => {
  return <Slide {...props} direction='left' />
}

const TransitionUp = (props: TransitionProps) => {
  return <Slide {...props} direction='up' />
}

const TransitionRight = (props: TransitionProps) => {
  return <Slide {...props} direction='right' />
}

const TransitionDown = (props: TransitionProps) => {
  return <Slide {...props} direction='down' />
}

const SnackbarControlSlideDirection = () => {
  // ** States
  const [open, setOpen] = useState<boolean>(false)
  const [transition, setTransition] = useState<ComponentType<TransitionProps> | undefined>(undefined)

  const handleClick = (Transition: ComponentType<TransitionProps>) => () => {
    setTransition(() => Transition)
    setOpen(true)
  }

  const handleClose = () => {
    setOpen(false)
  }

  return (
    <Fragment>
      <div className='demo-space-x'>
        <Button variant='outlined' onClick={handleClick(TransitionLeft)}>
          Right
        </Button>
        <Button variant='outlined' onClick={handleClick(TransitionUp)}>
          Up
        </Button>
        <Button variant='outlined' onClick={handleClick(TransitionRight)}>
          Left
        </Button>
        <Button variant='outlined' onClick={handleClick(TransitionDown)}>
          Down
        </Button>
      </div>
      <Snackbar
        open={open}
        onClose={handleClose}
        message='I love snacks'
        autoHideDuration={3000}
        TransitionComponent={transition}
        key={transition ? transition.name : ''}
      />
    </Fragment>
  )
}

export default SnackbarControlSlideDirection
