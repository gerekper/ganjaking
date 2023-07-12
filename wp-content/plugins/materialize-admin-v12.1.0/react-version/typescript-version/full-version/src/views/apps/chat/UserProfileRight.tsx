// ** React Imports
import { Fragment, ReactNode } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import List from '@mui/material/List'
import Badge from '@mui/material/Badge'
import MuiAvatar from '@mui/material/Avatar'
import ListItem from '@mui/material/ListItem'
import FormGroup from '@mui/material/FormGroup'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import ListItemIcon from '@mui/material/ListItemIcon'
import ListItemText from '@mui/material/ListItemText'
import ListItemButton from '@mui/material/ListItemButton'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Third Party Components
import PerfectScrollbar from 'react-perfect-scrollbar'

// ** Type
import { UserProfileRightType } from 'src/types/apps/chatTypes'

// ** Custom Component Imports
import Sidebar from 'src/@core/components/sidebar'
import CustomAvatar from 'src/@core/components/mui/avatar'

const UserProfileRight = (props: UserProfileRightType) => {
  const {
    store,
    hidden,
    statusObj,
    getInitials,
    sidebarWidth,
    userProfileRightOpen,
    handleUserProfileRightSidebarToggle
  } = props

  const ScrollWrapper = ({ children }: { children: ReactNode }) => {
    if (hidden) {
      return <Box sx={{ height: '100%', overflowY: 'auto', overflowX: 'hidden' }}>{children}</Box>
    } else {
      return <PerfectScrollbar options={{ wheelPropagation: false }}>{children}</PerfectScrollbar>
    }
  }

  return (
    <Sidebar
      direction='right'
      show={userProfileRightOpen}
      backDropClick={handleUserProfileRightSidebarToggle}
      sx={{
        zIndex: 9,
        height: '100%',
        width: sidebarWidth,
        borderTopRightRadius: theme => theme.shape.borderRadius,
        borderBottomRightRadius: theme => theme.shape.borderRadius,
        '& + .MuiBackdrop-root': {
          zIndex: 8,
          borderRadius: 1
        }
      }}
    >
      {store && store.selectedChat ? (
        <Fragment>
          <Box sx={{ position: 'relative' }}>
            <IconButton
              size='small'
              onClick={handleUserProfileRightSidebarToggle}
              sx={{
                top: '0.7rem',
                right: '0.7rem',
                position: 'absolute',
                color: 'text.secondary',
                '& svg': { color: 'action.active' }
              }}
            >
              <Icon icon='mdi:close' />
            </IconButton>
            <Box sx={{ px: 5, pb: 7, pt: 9.5, display: 'flex', flexDirection: 'column' }}>
              <Box sx={{ mb: 4, display: 'flex', justifyContent: 'center' }}>
                <Badge
                  overlap='circular'
                  anchorOrigin={{
                    vertical: 'bottom',
                    horizontal: 'right'
                  }}
                  badgeContent={
                    <Box
                      component='span'
                      sx={{
                        width: 10,
                        height: 10,
                        borderRadius: '50%',
                        color: `${statusObj[store.selectedChat.contact.status]}.main`,
                        boxShadow: theme => `0 0 0 2px ${theme.palette.background.paper}`,
                        backgroundColor: `${statusObj[store.selectedChat.contact.status]}.main`
                      }}
                    />
                  }
                >
                  {store.selectedChat.contact.avatar ? (
                    <MuiAvatar
                      sx={{ width: '5rem', height: '5rem' }}
                      src={store.selectedChat.contact.avatar}
                      alt={store.selectedChat.contact.fullName}
                    />
                  ) : (
                    <CustomAvatar
                      skin='light'
                      color={store.selectedChat.contact.avatarColor}
                      sx={{ width: '5rem', height: '5rem', fontWeight: 500, fontSize: '2rem' }}
                    >
                      {getInitials(store.selectedChat.contact.fullName)}
                    </CustomAvatar>
                  )}
                </Badge>
              </Box>
              <Typography sx={{ mb: 0.75, fontWeight: 600, textAlign: 'center' }}>
                {store.selectedChat.contact.fullName}
              </Typography>
              <Typography variant='body2' sx={{ textAlign: 'center' }}>
                {store.selectedChat.contact.role}
              </Typography>
            </Box>
          </Box>

          <Box sx={{ height: 'calc(100% - 13.0625rem)' }}>
            <ScrollWrapper>
              <Box sx={{ p: 5 }}>
                <FormGroup sx={{ mb: 6 }}>
                  <Typography variant='body2' sx={{ mb: 1.5, textTransform: 'uppercase' }}>
                    About
                  </Typography>
                  <Typography variant='body2' sx={{ fontSize: '0.875rem' }}>
                    {store.selectedChat.contact.about}
                  </Typography>
                </FormGroup>

                <Box sx={{ mb: 6 }}>
                  <Typography variant='body2' sx={{ mb: 1.5, textTransform: 'uppercase' }}>
                    Personal Information
                  </Typography>
                  <List dense sx={{ p: 0 }}>
                    <ListItem sx={{ px: 2 }}>
                      <ListItemIcon sx={{ mr: 3 }}>
                        <Icon icon='mdi:email-outline' />
                      </ListItemIcon>
                      <ListItemText
                        sx={{ textTransform: 'lowercase' }}
                        secondary={`${store.selectedChat.contact.fullName.replace(/\s/g, '_')}@email.com`}
                      />
                    </ListItem>
                    <ListItem sx={{ px: 2 }}>
                      <ListItemIcon sx={{ mr: 3 }}>
                        <Icon icon='mdi:phone-outline' />
                      </ListItemIcon>
                      <ListItemText secondary='+1(123) 456 - 7890' />
                    </ListItem>
                    <ListItem sx={{ px: 2 }}>
                      <ListItemIcon sx={{ mr: 3 }}>
                        <Icon icon='mdi:clock-time-five-outline' />
                      </ListItemIcon>
                      <ListItemText secondary='Mon - Fri 10AM - 8PM' />
                    </ListItem>
                  </List>
                </Box>

                <div>
                  <Typography variant='body2' sx={{ mb: 1.5, textTransform: 'uppercase' }}>
                    Settings
                  </Typography>
                  <List dense sx={{ p: 0 }}>
                    <ListItem disablePadding>
                      <ListItemButton sx={{ px: 2 }}>
                        <ListItemIcon sx={{ mr: 3 }}>
                          <Icon icon='mdi:bookmark-outline' />
                        </ListItemIcon>
                        <ListItemText secondary='Add Tag' />
                      </ListItemButton>
                    </ListItem>
                    <ListItem disablePadding>
                      <ListItemButton sx={{ px: 2 }}>
                        <ListItemIcon sx={{ mr: 3 }}>
                          <Icon icon='mdi:star-outline' />
                        </ListItemIcon>
                        <ListItemText secondary='Important Contact' />
                      </ListItemButton>
                    </ListItem>
                    <ListItem disablePadding>
                      <ListItemButton sx={{ px: 2 }}>
                        <ListItemIcon sx={{ mr: 3 }}>
                          <Icon icon='mdi:image-outline' />
                        </ListItemIcon>
                        <ListItemText secondary='Shared Media' />
                      </ListItemButton>
                    </ListItem>
                    <ListItem disablePadding>
                      <ListItemButton sx={{ px: 2 }}>
                        <ListItemIcon sx={{ mr: 3 }}>
                          <Icon icon='mdi:delete-outline' />
                        </ListItemIcon>
                        <ListItemText secondary='Delete Contact' />
                      </ListItemButton>
                    </ListItem>
                    <ListItem disablePadding>
                      <ListItemButton sx={{ px: 2 }}>
                        <ListItemIcon sx={{ mr: 2.5, ml: 0.5 }}>
                          <Icon icon='mdi:block-helper' fontSize={20} />
                        </ListItemIcon>
                        <ListItemText secondary='Block Contact' />
                      </ListItemButton>
                    </ListItem>
                  </List>
                </div>
              </Box>
            </ScrollWrapper>
          </Box>
        </Fragment>
      ) : null}
    </Sidebar>
  )
}

export default UserProfileRight
