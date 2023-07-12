// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Box from '@mui/material/Box'
import List from '@mui/material/List'
import Button from '@mui/material/Button'
import Drawer from '@mui/material/Drawer'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import ListItemIcon from '@mui/material/ListItemIcon'
import ListItemText from '@mui/material/ListItemText'
import ListItem from '@mui/material/ListItem'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Third Party Imports
import PerfectScrollbar from 'react-perfect-scrollbar'

// ** Custom Components Imports
import CustomBadge from 'src/@core/components/mui/badge'

// ** Styled Components
const ListItemStyled = styled(ListItem)(({ theme }) => ({
  borderLeftWidth: '3px',
  borderLeftStyle: 'solid',
  padding: theme.spacing(0, 5),
  marginBottom: theme.spacing(2)
}))

const ListBadge = styled(CustomBadge)(() => ({
  '& .MuiBadge-badge': {
    height: '18px',
    minWidth: '18px',
    transform: 'none',
    position: 'relative',
    transformOrigin: 'none'
  }
}))

const SidebarLeft = props => {
  // ** Props
  const {
    store,
    hidden,
    lgAbove,
    dispatch,
    leftSidebarOpen,
    leftSidebarWidth,
    toggleComposeOpen,
    setMailDetailsOpen,
    handleSelectAllMail,
    handleLeftSidebarToggle
  } = props

  const RenderBadge = (folder, color) => {
    if (store && store.mailMeta && store.mailMeta[folder] > 0) {
      return <ListBadge skin='light' color={color} sx={{ ml: 2 }} badgeContent={store.mailMeta[folder]} />
    } else {
      return null
    }
  }

  const handleActiveItem = (type, value) => {
    if (store && store.filter[type] !== value) {
      return false
    } else {
      return true
    }
  }

  const handleListItemClick = () => {
    setMailDetailsOpen(false)
    setTimeout(() => dispatch(handleSelectAllMail(false)), 50)
    handleLeftSidebarToggle()
  }

  const activeInboxCondition =
    store && handleActiveItem('folder', 'inbox') && store.filter.folder === 'inbox' && store.filter.label === ''

  const ScrollWrapper = ({ children }) => {
    if (hidden) {
      return <Box sx={{ height: '100%', overflowY: 'auto', overflowX: 'hidden' }}>{children}</Box>
    } else {
      return <PerfectScrollbar options={{ wheelPropagation: false }}>{children}</PerfectScrollbar>
    }
  }

  return (
    <Drawer
      open={leftSidebarOpen}
      onClose={handleLeftSidebarToggle}
      variant={lgAbove ? 'permanent' : 'temporary'}
      ModalProps={{
        disablePortal: true,
        keepMounted: true // Better open performance on mobile.
      }}
      sx={{
        zIndex: 9,
        display: 'block',
        position: lgAbove ? 'static' : 'absolute',
        '& .MuiDrawer-paper': {
          boxShadow: 'none',
          width: leftSidebarWidth,
          zIndex: lgAbove ? 2 : 'drawer',
          position: lgAbove ? 'static' : 'absolute'
        },
        '& .MuiBackdrop-root': {
          position: 'absolute'
        }
      }}
    >
      <Box sx={{ p: 5, overflowY: 'hidden' }}>
        <Button fullWidth variant='contained' onClick={toggleComposeOpen}>
          Compose
        </Button>
      </Box>
      <ScrollWrapper>
        <Box sx={{ pt: 1.25, overflowY: 'hidden' }}>
          <List component='div'>
            <ListItemStyled
              component={Link}
              href='/apps/email/inbox'
              onClick={handleListItemClick}
              sx={{
                pt: 0.5,
                borderLeftColor: theme => (activeInboxCondition ? theme.palette.primary.main : 'transparent')
              }}
            >
              <ListItemIcon sx={{ color: activeInboxCondition ? 'primary.main' : 'text.secondary' }}>
                <Icon icon='mdi:email-outline' fontSize={20} />
              </ListItemIcon>
              <ListItemText
                primary='Inbox'
                primaryTypographyProps={{
                  noWrap: true,
                  sx: { ...(activeInboxCondition && { color: 'primary.main' }) }
                }}
              />
              {RenderBadge('inbox', 'primary')}
            </ListItemStyled>
            <ListItemStyled
              component={Link}
              href='/apps/email/sent'
              onClick={handleListItemClick}
              sx={{
                borderLeftColor: handleActiveItem('folder', 'sent') ? 'primary.main' : 'transparent'
              }}
            >
              <ListItemIcon sx={{ color: handleActiveItem('folder', 'sent') ? 'primary.main' : 'text.secondary' }}>
                <Icon icon='mdi:send-outline' fontSize={20} />
              </ListItemIcon>
              <ListItemText
                primary='Sent'
                primaryTypographyProps={{
                  noWrap: true,
                  sx: { ...(handleActiveItem('folder', 'sent') && { color: 'primary.main' }) }
                }}
              />
            </ListItemStyled>
            <ListItemStyled
              component={Link}
              href='/apps/email/draft'
              onClick={handleListItemClick}
              sx={{
                borderLeftColor: handleActiveItem('folder', 'draft') ? 'primary.main' : 'transparent'
              }}
            >
              <ListItemIcon sx={{ color: handleActiveItem('folder', 'draft') ? 'primary.main' : 'text.secondary' }}>
                <Icon icon='mdi:pencil-outline' fontSize={20} />
              </ListItemIcon>
              <ListItemText
                primary='Draft'
                primaryTypographyProps={{
                  noWrap: true,
                  sx: { ...(handleActiveItem('folder', 'draft') && { color: 'primary.main' }) }
                }}
              />
              {RenderBadge('draft', 'warning')}
            </ListItemStyled>
            <ListItemStyled
              component={Link}
              href='/apps/email/starred'
              onClick={handleListItemClick}
              sx={{
                borderLeftColor: handleActiveItem('folder', 'starred') ? 'primary.main' : 'transparent'
              }}
            >
              <ListItemIcon sx={{ color: handleActiveItem('folder', 'starred') ? 'primary.main' : 'text.secondary' }}>
                <Icon icon='mdi:star-outline' fontSize={20} />
              </ListItemIcon>
              <ListItemText
                primary='Starred'
                primaryTypographyProps={{
                  noWrap: true,
                  sx: { ...(handleActiveItem('folder', 'starred') && { color: 'primary.main' }) }
                }}
              />
            </ListItemStyled>
            <ListItemStyled
              component={Link}
              href='/apps/email/spam'
              onClick={handleListItemClick}
              sx={{
                borderLeftColor: handleActiveItem('folder', 'spam') ? 'primary.main' : 'transparent'
              }}
            >
              <ListItemIcon sx={{ color: handleActiveItem('folder', 'spam') ? 'primary.main' : 'text.secondary' }}>
                <Icon icon='mdi:alert-octagon-outline' fontSize={20} />
              </ListItemIcon>
              <ListItemText
                primary='Spam'
                primaryTypographyProps={{
                  noWrap: true,
                  sx: { ...(handleActiveItem('folder', 'spam') && { color: 'primary.main' }) }
                }}
              />
              {RenderBadge('spam', 'error')}
            </ListItemStyled>
            <ListItemStyled
              component={Link}
              href='/apps/email/trash'
              onClick={handleListItemClick}
              sx={{
                borderLeftColor: handleActiveItem('folder', 'trash') ? 'primary.main' : 'transparent'
              }}
            >
              <ListItemIcon sx={{ color: handleActiveItem('folder', 'trash') ? 'primary.main' : 'text.secondary' }}>
                <Icon icon='mdi:delete-outline' fontSize={20} />
              </ListItemIcon>
              <ListItemText
                primary='Trash'
                primaryTypographyProps={{
                  noWrap: true,
                  sx: { ...(handleActiveItem('folder', 'trash') && { color: 'primary.main' }) }
                }}
              />
            </ListItemStyled>
          </List>
          <Typography
            component='h6'
            variant='caption'
            sx={{
              mx: 6,
              mb: 0,
              mt: 3.5,
              lineHeight: '.95rem',
              color: 'text.disabled',
              letterSpacing: '0.4px',
              textTransform: 'uppercase'
            }}
          >
            Labels
          </Typography>
          <List component='div' sx={{ pt: 1 }}>
            <ListItemStyled
              component={Link}
              onClick={handleListItemClick}
              href='/apps/email/label/personal'
              sx={{
                mb: 1,
                borderLeftColor: handleActiveItem('label', 'personal') ? 'primary.main' : 'transparent'
              }}
            >
              <ListItemIcon sx={{ '& svg': { mr: 1, color: 'success.main' } }}>
                <Icon icon='mdi:circle' fontSize='0.75rem' />
              </ListItemIcon>
              <ListItemText
                primary='Personal'
                primaryTypographyProps={{
                  noWrap: true,
                  sx: { ...(handleActiveItem('label', 'personal') && { color: 'primary.main' }) }
                }}
              />
            </ListItemStyled>
            <ListItemStyled
              component={Link}
              onClick={handleListItemClick}
              href='/apps/email/label/company'
              sx={{
                mb: 1,
                borderLeftColor: handleActiveItem('label', 'company') ? 'primary.main' : 'transparent'
              }}
            >
              <ListItemIcon sx={{ '& svg': { mr: 1, color: 'primary.main' } }}>
                <Icon icon='mdi:circle' fontSize='0.75rem' />
              </ListItemIcon>
              <ListItemText
                primary='Company'
                primaryTypographyProps={{
                  noWrap: true,
                  sx: { ...(handleActiveItem('label', 'company') && { color: 'primary.main' }) }
                }}
              />
            </ListItemStyled>
            <ListItemStyled
              component={Link}
              onClick={handleListItemClick}
              href='/apps/email/label/important'
              sx={{
                mb: 1,
                borderLeftColor: handleActiveItem('label', 'important') ? 'primary.main' : 'transparent'
              }}
            >
              <ListItemIcon sx={{ '& svg': { mr: 1, color: 'warning.main' } }}>
                <Icon icon='mdi:circle' fontSize='0.75rem' />
              </ListItemIcon>
              <ListItemText
                primary='Important'
                primaryTypographyProps={{
                  noWrap: true,
                  sx: { ...(handleActiveItem('label', 'important') && { color: 'primary.main' }) }
                }}
              />
            </ListItemStyled>
            <ListItemStyled
              component={Link}
              onClick={handleListItemClick}
              href='/apps/email/label/private'
              sx={{
                mb: 1,
                borderLeftColor: handleActiveItem('label', 'private') ? 'primary.main' : 'transparent'
              }}
            >
              <ListItemIcon sx={{ '& svg': { mr: 1, color: 'error.main' } }}>
                <Icon icon='mdi:circle' fontSize='0.75rem' />
              </ListItemIcon>
              <ListItemText
                primary='Private'
                primaryTypographyProps={{
                  noWrap: true,
                  sx: { ...(handleActiveItem('label', 'private') && { color: 'primary.main' }) }
                }}
              />
            </ListItemStyled>
          </List>
        </Box>
      </ScrollWrapper>
    </Drawer>
  )
}

export default SidebarLeft
