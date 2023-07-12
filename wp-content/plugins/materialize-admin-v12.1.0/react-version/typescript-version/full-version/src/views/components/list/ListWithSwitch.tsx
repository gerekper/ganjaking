// ** React Imports
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
  const [checked, setChecked] = useState<string[]>(['wifi', 'location'])

  const handleToggle = (value: string) => () => {
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
