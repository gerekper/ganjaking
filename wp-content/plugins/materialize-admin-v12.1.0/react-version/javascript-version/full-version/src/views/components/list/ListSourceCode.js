export const ListProgressJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import Box from '@mui/material/Box'
import { styled } from '@mui/material/styles'
import ListItem from '@mui/material/ListItem'
import List from '@mui/material/List'
import ListItemText from '@mui/material/ListItemText'
import ListItemAvatar from '@mui/material/ListItemAvatar'
import LinearProgress from '@mui/material/LinearProgress'

// ** Custom Components Imports
import CustomAvatar from 'src/@core/components/mui/avatar'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const StyledList = styled(List)(({ theme }) => ({
  '& .MuiListItem-root': {
    border: 1px solid {theme.palette.divider},
    '&:first-of-type': {
      borderTopLeftRadius: theme.shape.borderRadius,
      borderTopRightRadius: theme.shape.borderRadius
    },
    '&:last-child': {
      borderBottomLeftRadius: theme.shape.borderRadius,
      borderBottomRightRadius: theme.shape.borderRadius
    },
    '&:not(:last-child)': {
      borderBottom: 0
    },
    '& .MuiListItemText-root': {
      margin: theme.spacing(0, 0, 2),
      '& .MuiTypography-root': {
        fontWeight: 500
      }
    }
  }
}))

const ListProgress = () => {
  return (
    <StyledList disablePadding>
      <ListItem>
        <ListItemAvatar>
          <CustomAvatar skin='light' variant='rounded' color='info' sx={{ height: 36, width: 36 }}>
            <Icon icon='mdi:react' />
          </CustomAvatar>
        </ListItemAvatar>
        <Box sx={{ width: '100%' }}>
          <ListItemText primary='React is a JavaScript library for building user interfaces' />
          <LinearProgress color='info' value={90} sx={{ height: 5 }} variant='determinate' />
        </Box>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <CustomAvatar skin='light' variant='rounded' sx={{ height: 36, width: 36 }}>
            <Icon icon='mdi:bootstrap' />
          </CustomAvatar>
        </ListItemAvatar>
        <Box sx={{ width: '100%' }}>
          <ListItemText primary='Bootstrap is an open source toolkit' />
          <LinearProgress value={75} sx={{ height: 5 }} variant='determinate' />
        </Box>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <CustomAvatar skin='light' variant='rounded' color='success' sx={{ height: 36, width: 36 }}>
            <Icon icon='mdi:vuejs' />
          </CustomAvatar>
        </ListItemAvatar>
        <Box sx={{ width: '100%' }}>
          <ListItemText primary='Vue.js is the Progressive JavaScript Framework' />
          <LinearProgress color='success' value={85} sx={{ height: 5 }} variant='determinate' />
        </Box>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <CustomAvatar skin='light' variant='rounded' color='error' sx={{ height: 36, width: 36 }}>
            <Icon icon='mdi:angular' />
          </CustomAvatar>
        </ListItemAvatar>
        <Box sx={{ width: '100%' }}>
          <ListItemText primary='Angular implements Functional Programming concepts' />
          <LinearProgress color='error' value={60} sx={{ height: 5 }} variant='determinate' />
        </Box>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <CustomAvatar skin='light' variant='rounded' color='warning' sx={{ height: 36, width: 36 }}>
            <Icon icon='mdi:language-javascript' />
          </CustomAvatar>
        </ListItemAvatar>
        <Box sx={{ width: '100%' }}>
          <ListItemText primary='JavaScript is the programming language of the Web' />
          <LinearProgress color='warning' value={70} sx={{ height: 5 }} variant='determinate' />
        </Box>
      </ListItem>
    </StyledList>
  )
}

export default ListProgress
`}</code></pre>) 

export const ListStickySubheaderJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import Box from '@mui/material/Box'
import List from '@mui/material/List'
import ListItem from '@mui/material/ListItem'
import ListItemText from '@mui/material/ListItemText'
import ListSubheader from '@mui/material/ListSubheader'

const ListWithSwitch = () => {
  return (
    <List subheader={<li />} sx={{ maxHeight: 300, overflow: 'auto', position: 'relative' }}>
      {[0, 1, 2, 3, 4].map(sectionId => (
        <Box component='li' key={section-{sectionId}} sx={{ backgroundColor: 'background.paper' }}>
          <Box component='ul' sx={{ p: 0, backgroundColor: 'inherit' }}>
            <ListSubheader>{I'm sticky {sectionId}}</ListSubheader>
            {[0, 1, 2].map(item => (
              <ListItem key={item-{sectionId}-{item}}>
                <ListItemText primary={Item {item}} />
              </ListItem>
            ))}
          </Box>
        </Box>
      ))}
    </List>
  )
}

export default ListWithSwitch
`}</code></pre>) 

export const ListSimpleJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { Fragment } from 'react'

// ** MUI Imports
import List from '@mui/material/List'
import Divider from '@mui/material/Divider'
import ListItem from '@mui/material/ListItem'
import ListItemIcon from '@mui/material/ListItemIcon'
import ListItemText from '@mui/material/ListItemText'
import ListItemButton from '@mui/material/ListItemButton'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ListSimple = () => {
  return (
    <Fragment>
      <List component='nav' aria-label='main mailbox'>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:email-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Inbox' />
          </ListItemButton>
        </ListItem>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:content-copy' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Draft' />
          </ListItemButton>
        </ListItem>
      </List>
      <Divider sx={{ m: '0 !important' }} />
      <List component='nav' aria-label='secondary mailbox'>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:clock-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Snoozed' />
          </ListItemButton>
        </ListItem>
        <ListItem disablePadding>
          <ListItemButton component='a' href='#simple-list'>
            <ListItemIcon>
              <Icon icon='mdi:alert-circle-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Spam' />
          </ListItemButton>
        </ListItem>
      </List>
    </Fragment>
  )
}

export default ListSimple
`}</code></pre>) 

export const ListDenseJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { Fragment } from 'react'

// ** MUI Imports
import List from '@mui/material/List'
import Divider from '@mui/material/Divider'
import ListItem from '@mui/material/ListItem'
import ListItemIcon from '@mui/material/ListItemIcon'
import ListItemText from '@mui/material/ListItemText'
import ListItemButton from '@mui/material/ListItemButton'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ListDense = () => {
  return (
    <Fragment>
      <List dense>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:email-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Inbox' />
          </ListItemButton>
        </ListItem>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:content-copy' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Draft' />
          </ListItemButton>
        </ListItem>
      </List>
      <Divider sx={{ m: '0 !important' }} />
      <List dense>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:clock-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Snoozed' />
          </ListItemButton>
        </ListItem>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:alert-circle-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Spam' />
          </ListItemButton>
        </ListItem>
      </List>
    </Fragment>
  )
}

export default ListDense
`}</code></pre>) 

export const ListItemSelectedJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import List from '@mui/material/List'
import Avatar from '@mui/material/Avatar'
import ListItem from '@mui/material/ListItem'
import IconButton from '@mui/material/IconButton'
import ListItemText from '@mui/material/ListItemText'
import ListItemButton from '@mui/material/ListItemButton'
import ListItemAvatar from '@mui/material/ListItemAvatar'
import ListItemSecondaryAction from '@mui/material/ListItemSecondaryAction'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ListItemSelected = () => {
  // ** State
  const [selectedIndex, setSelectedIndex] = useState(1)

  const handleListItemClick = index => {
    setSelectedIndex(index)
  }

  return (
    <List>
      <ListItem disablePadding>
        <ListItemButton selected={selectedIndex === 0} onClick={() => handleListItemClick(0)}>
          <ListItemAvatar>
            <Avatar src='/images/avatars/2.png' alt='Caroline Black' sx={{ height: 32, width: 32 }} />
          </ListItemAvatar>
          <ListItemText primary='Caroline Black' />
          <ListItemSecondaryAction>
            <IconButton edge='end'>
              <Icon icon='mdi:message-text-outline' fontSize={20} />
            </IconButton>
          </ListItemSecondaryAction>
        </ListItemButton>
      </ListItem>
      <ListItem disablePadding>
        <ListItemButton selected={selectedIndex === 1} onClick={() => handleListItemClick(1)}>
          <ListItemAvatar>
            <Avatar src='/images/avatars/1.png' alt='Alfred Copeland' sx={{ height: 32, width: 32 }} />
          </ListItemAvatar>
          <ListItemText primary='Alfred Copeland' />
          <ListItemSecondaryAction>
            <IconButton edge='end'>
              <Icon icon='mdi:message-text-outline' fontSize={20} />
            </IconButton>
          </ListItemSecondaryAction>
        </ListItemButton>
      </ListItem>
      <ListItem disablePadding>
        <ListItemButton selected={selectedIndex === 2} onClick={() => handleListItemClick(2)}>
          <ListItemAvatar>
            <Avatar src='/images/avatars/8.png' alt='Celia Schneider' sx={{ height: 32, width: 32 }} />
          </ListItemAvatar>
          <ListItemText primary='Celia Schneider' />
          <ListItemSecondaryAction>
            <IconButton edge='end'>
              <Icon icon='mdi:message-text-outline' fontSize={20} />
            </IconButton>
          </ListItemSecondaryAction>
        </ListItemButton>
      </ListItem>
    </List>
  )
}

export default ListItemSelected
`}</code></pre>) 

export const ListSecondaryJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import List from '@mui/material/List'
import Avatar from '@mui/material/Avatar'
import ListItem from '@mui/material/ListItem'
import IconButton from '@mui/material/IconButton'
import ListItemText from '@mui/material/ListItemText'
import ListItemAvatar from '@mui/material/ListItemAvatar'
import ListItemSecondaryAction from '@mui/material/ListItemSecondaryAction'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ListSecondary = () => {
  return (
    <List>
      <ListItem>
        <ListItemAvatar>
          <Avatar src='/images/avatars/2.png' alt='Caroline Black' sx={{ height: 36, width: 36 }} />
        </ListItemAvatar>
        <ListItemText primary='Caroline Black' secondary='Sweet dessert brownie.' />
        <ListItemSecondaryAction>
          <IconButton edge='end'>
            <Icon icon='mdi:plus' />
          </IconButton>
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <Avatar src='/images/avatars/1.png' alt='Alfred Copeland' sx={{ height: 36, width: 36 }} />
        </ListItemAvatar>
        <ListItemText primary='Alfred Copeland' secondary='Pudding pie tiramisu.' />
        <ListItemSecondaryAction>
          <IconButton edge='end'>
            <Icon icon='mdi:plus' />
          </IconButton>
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <Avatar src='/images/avatars/8.png' alt='Celia Schneider' sx={{ height: 36, width: 36 }} />
        </ListItemAvatar>
        <ListItemText primary='Celia Schneider' secondary='Muffin pie chupa chups.' />
        <ListItemSecondaryAction>
          <IconButton edge='end'>
            <Icon icon='mdi:plus' />
          </IconButton>
        </ListItemSecondaryAction>
      </ListItem>
    </List>
  )
}

export default ListSecondary
`}</code></pre>) 

export const ListUsersJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import Box from '@mui/material/Box'
import Avatar from '@mui/material/Avatar'
import Button from '@mui/material/Button'
import ListItem from '@mui/material/ListItem'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import List from '@mui/material/List'
import ListItemText from '@mui/material/ListItemText'
import ListItemAvatar from '@mui/material/ListItemAvatar'
import ListItemSecondaryAction from '@mui/material/ListItemSecondaryAction'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const StyledList = styled(List)(({ theme }) => ({
  '& .MuiListItem-container': {
    border: 1px solid {theme.palette.divider},
    '&:first-of-type': {
      borderTopLeftRadius: theme.shape.borderRadius,
      borderTopRightRadius: theme.shape.borderRadius
    },
    '&:last-child': {
      borderBottomLeftRadius: theme.shape.borderRadius,
      borderBottomRightRadius: theme.shape.borderRadius
    },
    '&:not(:last-child)': {
      borderBottom: 0
    },
    '& .MuiListItem-root': {
      paddingRight: theme.spacing(24)
    },
    '& .MuiListItemText-root': {
      marginTop: 0,
      '& .MuiTypography-root': {
        fontWeight: 500
      }
    }
  }
}))

const ListUsers = () => {
  return (
    <StyledList disablePadding>
      <ListItem>
        <ListItemAvatar>
          <Avatar src='/images/avatars/2.png' alt='Caroline Black' />
        </ListItemAvatar>
        <div>
          <ListItemText primary='Caroline Black' />
          <Box sx={{ display: 'flex', alignItems: 'center', flexWrap: 'wrap' }}>
            <Box sx={{ mr: 3, display: 'flex', alignItems: 'center', '& svg': { mr: 1, color: 'success.main' } }}>
              <Icon icon='mdi:circle' fontSize='0.625rem' />
              <Typography variant='caption'>Online</Typography>
            </Box>
            <Typography variant='caption' sx={{ color: 'text.disabled' }}>
              13 minutes ago
            </Typography>
          </Box>
        </div>
        <ListItemSecondaryAction>
          <Button variant='contained' size='small'>
            Add
          </Button>
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <Avatar src='/images/avatars/1.png' alt='Alfred Copeland' />
        </ListItemAvatar>
        <div>
          <ListItemText primary='Alfred Copeland' />
          <Box sx={{ display: 'flex', alignItems: 'center', flexWrap: 'wrap' }}>
            <Box sx={{ mr: 3, display: 'flex', alignItems: 'center', '& svg': { mr: 1, color: 'warning.main' } }}>
              <Icon icon='mdi:circle' fontSize='0.625rem' />
              <Typography variant='caption'>Away</Typography>
            </Box>
            <Typography variant='caption' sx={{ color: 'text.disabled' }}>
              11 minutes ago
            </Typography>
          </Box>
        </div>
        <ListItemSecondaryAction>
          <Button variant='contained' size='small'>
            Add
          </Button>
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <Avatar src='/images/avatars/8.png' alt='Celia Schneider' />
        </ListItemAvatar>
        <div>
          <ListItemText primary='Celia Schneider' />
          <Box sx={{ display: 'flex', alignItems: 'center', flexWrap: 'wrap' }}>
            <Box sx={{ mr: 3, display: 'flex', alignItems: 'center', '& svg': { mr: 1, color: 'secondary.main' } }}>
              <Icon icon='mdi:circle' fontSize='0.625rem' />
              <Typography variant='caption'>Offline</Typography>
            </Box>
            <Typography variant='caption' sx={{ color: 'text.disabled' }}>
              9 minutes ago
            </Typography>
          </Box>
        </div>

        <ListItemSecondaryAction>
          <Button variant='contained' size='small'>
            Add
          </Button>
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemAvatar>
          <Avatar src='/images/avatars/5.png' alt='Celia Schneider' />
        </ListItemAvatar>
        <div>
          <ListItemText primary='Max Rogan' />
          <Box sx={{ display: 'flex', alignItems: 'center', flexWrap: 'wrap' }}>
            <Box sx={{ mr: 3, display: 'flex', alignItems: 'center', '& svg': { mr: 1, color: 'error.main' } }}>
              <Icon icon='mdi:circle' fontSize='0.625rem' />
              <Typography variant='caption'>In Meeting</Typography>
            </Box>
            <Typography variant='caption' sx={{ color: 'text.disabled' }}>
              28 minutes ago
            </Typography>
          </Box>
        </div>

        <ListItemSecondaryAction>
          <Button variant='contained' size='small'>
            Add
          </Button>
        </ListItemSecondaryAction>
      </ListItem>
    </StyledList>
  )
}

export default ListUsers
`}</code></pre>) 

export const ListWithCheckboxJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import List from '@mui/material/List'
import Avatar from '@mui/material/Avatar'
import ListItem from '@mui/material/ListItem'
import Checkbox from '@mui/material/Checkbox'
import ListItemText from '@mui/material/ListItemText'
import ListItemButton from '@mui/material/ListItemButton'
import ListItemAvatar from '@mui/material/ListItemAvatar'
import ListItemSecondaryAction from '@mui/material/ListItemSecondaryAction'

const ListWithCheckbox = () => {
  // ** State
  const [checked, setChecked] = useState([0])

  const handleToggle = value => () => {
    const currentIndex = checked.indexOf(value)
    const newChecked = [...checked]
    if (currentIndex === -1) {
      newChecked.push(value)
    } else {
      newChecked.splice(currentIndex, 1)
    }
    setChecked(newChecked)
  }

  return (
    <List>
      <ListItem disablePadding>
        <ListItemButton onClick={handleToggle(0)}>
          <ListItemAvatar>
            <Avatar src='/images/avatars/2.png' alt='Caroline Black' sx={{ height: 32, width: 32 }} />
          </ListItemAvatar>
          <ListItemText id='checkbox-list-label-0' primary='Caroline Black' />
          <ListItemSecondaryAction>
            <Checkbox
              edge='end'
              tabIndex={-1}
              disableRipple
              onChange={handleToggle(0)}
              checked={checked.indexOf(0) !== -1}
              inputProps={{ 'aria-labelledby': 'checkbox-list-label-0' }}
            />
          </ListItemSecondaryAction>
        </ListItemButton>
      </ListItem>
      <ListItem disablePadding>
        <ListItemButton onClick={handleToggle(1)}>
          <ListItemAvatar>
            <Avatar src='/images/avatars/1.png' alt='Alfred Copeland' sx={{ height: 32, width: 32 }} />
          </ListItemAvatar>
          <ListItemText id='checkbox-list-label-1' primary='Alfred Copeland' />
          <ListItemSecondaryAction>
            <Checkbox
              edge='end'
              tabIndex={-1}
              disableRipple
              onChange={handleToggle(1)}
              checked={checked.indexOf(1) !== -1}
              inputProps={{ 'aria-labelledby': 'checkbox-list-label-1' }}
            />
          </ListItemSecondaryAction>
        </ListItemButton>
      </ListItem>
      <ListItem disablePadding>
        <ListItemButton onClick={handleToggle(2)}>
          <ListItemAvatar>
            <Avatar src='/images/avatars/8.png' alt='Celia Schneider' sx={{ height: 32, width: 32 }} />
          </ListItemAvatar>
          <ListItemText id='checkbox-list-label-2' primary='Celia Schneider' />
          <ListItemSecondaryAction>
            <Checkbox
              edge='end'
              tabIndex={-1}
              disableRipple
              onChange={handleToggle(2)}
              checked={checked.indexOf(2) !== -1}
              inputProps={{ 'aria-labelledby': 'checkbox-list-label-2' }}
            />
          </ListItemSecondaryAction>
        </ListItemButton>
      </ListItem>
    </List>
  )
}

export default ListWithCheckbox
`}</code></pre>) 

export const ListWithSwitchJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import List from '@mui/material/List'
import Switch from '@mui/material/Switch'
import ListItem from '@mui/material/ListItem'
import ListItemText from '@mui/material/ListItemText'
import ListItemIcon from '@mui/material/ListItemIcon'
import ListSubheader from '@mui/material/ListSubheader'
import ListItemSecondaryAction from '@mui/material/ListItemSecondaryAction'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ListWithSwitch = () => {
  // ** State
  const [checked, setChecked] = useState(['wifi', 'location'])

  const handleToggle = value => () => {
    const currentIndex = checked.indexOf(value)
    const newChecked = [...checked]
    if (currentIndex === -1) {
      newChecked.push(value)
    } else {
      newChecked.splice(currentIndex, 1)
    }
    setChecked(newChecked)
  }

  return (
    <List subheader={<ListSubheader>Settings</ListSubheader>}>
      <ListItem>
        <ListItemIcon>
          <Icon icon='mdi:wifi' fontSize={20} />
        </ListItemIcon>
        <ListItemText primary='Wi-Fi' />
        <ListItemSecondaryAction>
          <Switch edge='end' checked={checked.indexOf('wifi') !== -1} onChange={handleToggle('wifi')} />
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemIcon>
          <Icon icon='mdi:bluetooth' fontSize={20} />
        </ListItemIcon>
        <ListItemText primary='Bluetooth' />
        <ListItemSecondaryAction>
          <Switch edge='end' checked={checked.indexOf('bluetooth') !== -1} onChange={handleToggle('bluetooth')} />
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemIcon>
          <Icon icon='mdi:map-marker-outline' fontSize={20} />
        </ListItemIcon>
        <ListItemText primary='Location' />
        <ListItemSecondaryAction>
          <Switch edge='end' checked={checked.indexOf('location') !== -1} onChange={handleToggle('location')} />
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemIcon>
          <Icon icon='mdi:airplane' fontSize={20} />
        </ListItemIcon>
        <ListItemText primary='Airplane Mode' />
        <ListItemSecondaryAction>
          <Switch edge='end' checked={checked.indexOf('airplane') !== -1} onChange={handleToggle('airplane')} />
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemIcon>
          <Icon icon='mdi:broadcast' fontSize={20} />
        </ListItemIcon>
        <ListItemText primary='Hotspot' />
        <ListItemSecondaryAction>
          <Switch edge='end' checked={checked.indexOf('hotspot') !== -1} onChange={handleToggle('hotspot')} />
        </ListItemSecondaryAction>
      </ListItem>
      <ListItem>
        <ListItemIcon>
          <Icon icon='mdi:minus-circle-outline' fontSize={20} />
        </ListItemIcon>
        <ListItemText primary='Do not disturb' />
        <ListItemSecondaryAction>
          <Switch
            edge='end'
            checked={checked.indexOf('do-not-disturb') !== -1}
            onChange={handleToggle('do-not-disturb')}
          />
        </ListItemSecondaryAction>
      </ListItem>
    </List>
  )
}

export default ListWithSwitch
`}</code></pre>) 

export const ListNestedJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { Fragment, useState } from 'react'

// ** MUI Imports
import List from '@mui/material/List'
import Divider from '@mui/material/Divider'
import ListItem from '@mui/material/ListItem'
import Collapse from '@mui/material/Collapse'
import ListItemIcon from '@mui/material/ListItemIcon'
import ListItemText from '@mui/material/ListItemText'
import ListItemButton from '@mui/material/ListItemButton'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ListNested = () => {
  // ** State
  const [open, setOpen] = useState(true)

  const handleClick = () => {
    setOpen(!open)
  }

  return (
    <Fragment>
      <List component='nav' aria-label='main mailbox'>
        <ListItem disablePadding>
          <ListItemButton onClick={handleClick}>
            <ListItemIcon>
              <Icon icon='mdi:email-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Inbox' />
            <Icon icon={open ? 'mdi:chevron-up' : 'mdi:chevron-down'} />
          </ListItemButton>
        </ListItem>
        <Collapse in={open} timeout='auto' unmountOnExit>
          <List component='div' disablePadding>
            <ListItem disablePadding>
              <ListItemButton sx={{ pl: 8 }}>
                <ListItemIcon sx={{ mr: 4 }}>
                  <Icon icon='mdi:send-clock' fontSize={20} />
                </ListItemIcon>
                <ListItemText primary='Scheduled' />
              </ListItemButton>
            </ListItem>
          </List>
        </Collapse>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:content-copy' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Draft' />
          </ListItemButton>
        </ListItem>
      </List>
      <Divider sx={{ m: '0 !important' }} />
      <List component='nav' aria-label='secondary mailbox'>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:clock-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Snoozed' />
          </ListItemButton>
        </ListItem>
        <ListItem disablePadding>
          <ListItemButton>
            <ListItemIcon>
              <Icon icon='mdi:alert-circle-outline' fontSize={20} />
            </ListItemIcon>
            <ListItemText primary='Spam' />
          </ListItemButton>
        </ListItem>
      </List>
    </Fragment>
  )
}

export default ListNested
`}</code></pre>) 

