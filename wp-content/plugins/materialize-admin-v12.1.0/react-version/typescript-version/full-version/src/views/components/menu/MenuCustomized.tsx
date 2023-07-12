// ** React Imports
import { MouseEvent, useState } from 'react'

// ** MUI Imports
import Button from '@mui/material/Button'
import { styled } from '@mui/material/styles'
import ListItemIcon from '@mui/material/ListItemIcon'
import ListItemText from '@mui/material/ListItemText'
import MuiMenu, { MenuProps } from '@mui/material/Menu'
import MuiMenuItem, { MenuItemProps } from '@mui/material/MenuItem'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// Styled Menu component
const Menu = styled(MuiMenu)<MenuProps>(({ theme }) => ({
  '& .MuiMenu-paper': {
    border: `1px solid ${theme.palette.divider}`
  }
}))

// Styled MenuItem component
const MenuItem = styled(MuiMenuItem)<MenuItemProps>(({ theme }) => ({
  '&:focus': {
    backgroundColor: theme.palette.primary.main,
    '& .MuiListItemIcon-root, & .MuiListItemText-primary': {
      color: theme.palette.common.white
    }
  }
}))

const MenuCustomized = () => {
  // ** State
  const [anchorEl, setAnchorEl] = useState<null | HTMLElement>(null)

  const handleClick = (event: MouseEvent<HTMLElement>) => {
    setAnchorEl(event.currentTarget)
  }

  const handleClose = () => {
    setAnchorEl(null)
  }

  return (
    <div>
      <Button variant='outlined' aria-haspopup='true' onClick={handleClick} aria-controls='customized-menu'>
        Open Menu
      </Button>
      <Menu
        keepMounted
        elevation={0}
        anchorEl={anchorEl}
        id='customized-menu'
        onClose={handleClose}
        open={Boolean(anchorEl)}
        anchorOrigin={{
          vertical: 'bottom',
          horizontal: 'center'
        }}
        transformOrigin={{
          vertical: 'top',
          horizontal: 'center'
        }}
      >
        <MenuItem>
          <ListItemIcon>
            <Icon icon='mdi:send' fontSize={20} />
          </ListItemIcon>
          <ListItemText primary='Sent mail' />
        </MenuItem>
        <MenuItem>
          <ListItemIcon>
            <Icon icon='mdi:email-open' fontSize={20} />
          </ListItemIcon>
          <ListItemText primary='Drafts' />
        </MenuItem>
        <MenuItem>
          <ListItemIcon>
            <Icon icon='mdi:inbox-arrow-down' fontSize={20} />
          </ListItemIcon>
          <ListItemText primary='Inbox' />
        </MenuItem>
      </Menu>
    </div>
  )
}

export default MenuCustomized
