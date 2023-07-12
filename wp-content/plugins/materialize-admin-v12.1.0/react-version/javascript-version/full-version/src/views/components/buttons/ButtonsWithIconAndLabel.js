// ** MUI Imports
import Button from '@mui/material/Button'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ButtonsWithIconAndLabel = () => {
  return (
    <div className='demo-space-x'>
      <Button variant='contained' endIcon={<Icon icon='mdi:send' />}>
        Send
      </Button>
      <Button variant='contained' color='secondary' startIcon={<Icon icon='mdi:delete-outline' />}>
        Delete
      </Button>
    </div>
  )
}

export default ButtonsWithIconAndLabel
