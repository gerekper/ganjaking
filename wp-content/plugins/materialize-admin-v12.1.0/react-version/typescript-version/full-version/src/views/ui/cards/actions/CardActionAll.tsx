// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Fade from '@mui/material/Fade'
import Card from '@mui/material/Card'
import Collapse from '@mui/material/Collapse'
import Backdrop from '@mui/material/Backdrop'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'
import CircularProgress from '@mui/material/CircularProgress'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const CardActionAll = () => {
  // ** States
  const [reload, setReload] = useState<boolean>(false)
  const [collapsed, setCollapsed] = useState<boolean>(true)
  const [visibility, setVisibility] = useState<boolean>(true)

  const handleBackDrop = () => {
    setReload(true)

    setTimeout(() => {
      setReload(false)
    }, 2000)
  }

  return (
    <Fade in={visibility} timeout={300}>
      <Card sx={{ position: 'relative' }}>
        <CardHeader
          title='All Actions'
          action={
            <Box sx={{ display: 'flex', alignItems: 'center' }}>
              <IconButton
                size='small'
                aria-label='collapse'
                sx={{ mr: 2, color: 'text.secondary' }}
                onClick={() => setCollapsed(!collapsed)}
              >
                <Icon fontSize={20} icon={!collapsed ? 'mdi:chevron-down' : 'mdi:chevron-up'} />
              </IconButton>
              <IconButton
                size='small'
                aria-label='reload'
                onClick={() => handleBackDrop()}
                sx={{ mr: 2, color: 'text.secondary' }}
              >
                <Icon icon='mdi:refresh' fontSize={20} />
              </IconButton>
              <IconButton
                size='small'
                aria-label='close'
                sx={{ color: 'text.secondary' }}
                onClick={() => setVisibility(false)}
              >
                <Icon icon='mdi:close' fontSize={20} />
              </IconButton>
            </Box>
          }
        />
        <Collapse in={collapsed}>
          <CardContent>
            <Typography variant='body2'>
              You can specifically add remove action using <code>actionRemove</code> prop Click on{' '}
              <Box component='span' sx={{ verticalAlign: 'top' }}>
                <Icon icon='mdi:close' fontSize={20} />{' '}
              </Box>
              icon to see it in action
            </Typography>
          </CardContent>

          <Backdrop
            open={reload}
            sx={{
              position: 'absolute',
              color: 'common.white',
              zIndex: theme => theme.zIndex.mobileStepper - 1
            }}
          >
            <CircularProgress color='inherit' />
          </Backdrop>
        </Collapse>
      </Card>
    </Fade>
  )
}

export default CardActionAll
