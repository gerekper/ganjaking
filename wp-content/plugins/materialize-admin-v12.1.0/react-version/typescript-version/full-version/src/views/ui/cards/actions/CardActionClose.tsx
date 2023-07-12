// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Fade from '@mui/material/Fade'
import Card from '@mui/material/Card'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const CardActionClose = () => {
  // ** State
  const [visibility, setVisibility] = useState<boolean>(true)

  return (
    <Fade in={visibility} timeout={300}>
      <Card>
        <CardHeader
          title='Remove Card'
          action={
            <IconButton
              size='small'
              aria-label='collapse'
              sx={{ color: 'text.secondary' }}
              onClick={() => setVisibility(false)}
            >
              <Icon icon='mdi:close' fontSize={20} />
            </IconButton>
          }
        />
        <CardContent>
          <Typography variant='body2'>
            You can specifically add remove action using <code>actionRemove</code> prop Click on{' '}
            <Box component='span' sx={{ verticalAlign: 'top' }}>
              <Icon icon='mdi:close' fontSize={20} />
            </Box>{' '}
            icon to see it in action
          </Typography>
        </CardContent>
      </Card>
    </Fade>
  )
}

export default CardActionClose
