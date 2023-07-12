// ** React Imports
import { Fragment } from 'react'

// ** MUI Imports
import Fab from '@mui/material/Fab'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ButtonsFabSizes = () => {
  return (
    <Fragment>
      <div className='demo-space-x'>
        <Fab color='primary' aria-label='add' size='small'>
          <Icon icon='mdi:plus' />
        </Fab>
        <Fab color='primary' aria-label='add' size='medium'>
          <Icon icon='mdi:plus' />
        </Fab>
        <Fab color='primary' aria-label='add' size='large'>
          <Icon icon='mdi:plus' />
        </Fab>
      </div>
      <div className='demo-space-x'>
        <Fab variant='extended' size='small' sx={{ '& svg': { mr: 1 } }}>
          <Icon icon='mdi:navigation-outline' />
          Navigate
        </Fab>
        <Fab variant='extended' size='medium' sx={{ '& svg': { mr: 1 } }}>
          <Icon icon='mdi:navigation-outline' />
          Navigate
        </Fab>
        <Fab variant='extended' size='large' sx={{ '& svg': { mr: 1 } }}>
          <Icon icon='mdi:navigation-outline' />
          Navigate
        </Fab>
      </div>
    </Fragment>
  )
}

export default ButtonsFabSizes
