// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Collapse from '@mui/material/Collapse'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const CardActionCollapse = () => {
  // ** State
  const [collapsed, setCollapsed] = useState<boolean>(true)

  return (
    <Card>
      <CardHeader
        title='Collapsible'
        action={
          <IconButton
            size='small'
            aria-label='collapse'
            sx={{ color: 'text.secondary' }}
            onClick={() => setCollapsed(!collapsed)}
          >
            <Icon fontSize={20} icon={!collapsed ? 'mdi:chevron-down' : 'mdi:chevron-up'} />
          </IconButton>
        }
      />
      <Collapse in={collapsed}>
        <CardContent>
          <Typography variant='body2'>
            You can specifically add collapsible action using <code>actionCollapse</code> prop Click on{' '}
            <Box component='span' sx={{ verticalAlign: 'top' }}>
              <Icon icon='mdi:chevron-up' fontSize={20} />
            </Box>{' '}
            icon to see it in action
          </Typography>
        </CardContent>
      </Collapse>
    </Card>
  )
}

export default CardActionCollapse
