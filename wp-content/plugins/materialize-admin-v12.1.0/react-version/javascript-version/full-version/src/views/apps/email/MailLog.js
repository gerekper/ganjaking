// ** React Imports
import { Fragment, useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import List from '@mui/material/List'
import Input from '@mui/material/Input'
import Avatar from '@mui/material/Avatar'
import Divider from '@mui/material/Divider'
import Tooltip from '@mui/material/Tooltip'
import Backdrop from '@mui/material/Backdrop'
import Checkbox from '@mui/material/Checkbox'
import { styled } from '@mui/material/styles'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import InputAdornment from '@mui/material/InputAdornment'
import CircularProgress from '@mui/material/CircularProgress'
import ListItem from '@mui/material/ListItem'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Third Party Imports
import PerfectScrollbar from 'react-perfect-scrollbar'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'

// ** Email App Component Imports
import { setTimeout } from 'timers'
import MailDetails from './MailDetails'

const MailItem = styled(ListItem)(({ theme }) => ({
  cursor: 'pointer',
  paddingTop: theme.spacing(3),
  paddingBottom: theme.spacing(3),
  justifyContent: 'space-between',
  transition: 'border 0.15s ease-in-out, transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out',
  '&:not(:first-child)': {
    borderTop: `1px solid ${theme.palette.divider}`
  },
  '&:hover': {
    zIndex: 2,
    boxShadow: theme.shadows[3],
    transform: 'translateY(-2px)',
    '& .mail-actions': { display: 'flex' },
    '& .mail-info-right': { display: 'none' },
    '& + .MuiListItem-root': { borderColor: 'transparent' }
  },
  [theme.breakpoints.up('xs')]: {
    paddingLeft: theme.spacing(2.5),
    paddingRight: theme.spacing(2.5)
  },
  [theme.breakpoints.up('sm')]: {
    paddingLeft: theme.spacing(5),
    paddingRight: theme.spacing(5)
  }
}))

const ScrollWrapper = ({ children, hidden }) => {
  if (hidden) {
    return <Box sx={{ height: '100%', overflowY: 'auto', overflowX: 'hidden' }}>{children}</Box>
  } else {
    return <PerfectScrollbar options={{ wheelPropagation: false, suppressScrollX: true }}>{children}</PerfectScrollbar>
  }
}

const MailLog = props => {
  // ** Props
  const {
    store,
    query,
    hidden,
    lgAbove,
    dispatch,
    setQuery,
    direction,
    updateMail,
    routeParams,
    labelColors,
    paginateMail,
    getCurrentMail,
    mailDetailsOpen,
    updateMailLabel,
    handleSelectMail,
    setMailDetailsOpen,
    handleSelectAllMail,
    handleLeftSidebarToggle
  } = props

  // ** State
  const [refresh, setRefresh] = useState(false)

  // ** Vars
  const folders = [
    {
      name: 'draft',
      icon: (
        <Box component='span' sx={{ mr: 2, display: 'flex' }}>
          <Icon icon='mdi:pencil-outline' fontSize={20} />
        </Box>
      )
    },
    {
      name: 'spam',
      icon: (
        <Box component='span' sx={{ mr: 2, display: 'flex' }}>
          <Icon icon='mdi:alert-octagon-outline' fontSize={20} />
        </Box>
      )
    },
    {
      name: 'trash',
      icon: (
        <Box component='span' sx={{ mr: 2, display: 'flex' }}>
          <Icon icon='mdi:delete-outline' fontSize={20} />
        </Box>
      )
    },
    {
      name: 'inbox',
      icon: (
        <Box component='span' sx={{ mr: 2, display: 'flex' }}>
          <Icon icon='mdi:email-outline' fontSize={20} />
        </Box>
      )
    }
  ]

  const foldersConfig = {
    draft: {
      name: 'draft',
      icon: (
        <Box component='span' sx={{ mr: 2, display: 'flex' }}>
          <Icon icon='mdi:pencil-outline' fontSize={20} />
        </Box>
      )
    },
    spam: {
      name: 'spam',
      icon: (
        <Box component='span' sx={{ mr: 2, display: 'flex' }}>
          <Icon icon='mdi:alert-octagon-outline' fontSize={20} />
        </Box>
      )
    },
    trash: {
      name: 'trash',
      icon: (
        <Box component='span' sx={{ mr: 2, display: 'flex' }}>
          <Icon icon='mdi:delete-outline' fontSize={20} />
        </Box>
      )
    },
    inbox: {
      name: 'inbox',
      icon: (
        <Box component='span' sx={{ mr: 2, display: 'flex' }}>
          <Icon icon='mdi:email-outline' fontSize={20} />
        </Box>
      )
    }
  }

  const foldersObj = {
    inbox: [foldersConfig.spam, foldersConfig.trash],
    sent: [foldersConfig.trash],
    draft: [foldersConfig.trash],
    spam: [foldersConfig.inbox, foldersConfig.trash],
    trash: [foldersConfig.inbox, foldersConfig.spam]
  }

  const handleMoveToTrash = () => {
    dispatch(updateMail({ emailIds: store.selectedMails, dataToUpdate: { folder: 'trash' } }))
    dispatch(handleSelectAllMail(false))
  }

  const handleStarMail = (e, id, value) => {
    e.stopPropagation()
    dispatch(updateMail({ emailIds: [id], dataToUpdate: { isStarred: value } }))
  }

  const handleReadMail = (id, value) => {
    const arr = Array.isArray(id) ? [...id] : [id]
    dispatch(updateMail({ emailIds: arr, dataToUpdate: { isRead: value } }))
    dispatch(handleSelectAllMail(false))
  }

  const handleLabelUpdate = (id, label) => {
    const arr = Array.isArray(id) ? [...id] : [id]
    dispatch(updateMailLabel({ emailIds: arr, label }))
  }

  const handleFolderUpdate = (id, folder) => {
    const arr = Array.isArray(id) ? [...id] : [id]
    dispatch(updateMail({ emailIds: arr, dataToUpdate: { folder } }))
  }

  const handleRefreshMailsClick = () => {
    setRefresh(true)
    setTimeout(() => setRefresh(false), 1000)
  }

  const handleLabelsMenu = () => {
    const array = []
    Object.entries(labelColors).map(([key, value]) => {
      array.push({
        text: <Typography sx={{ textTransform: 'capitalize' }}>{key}</Typography>,
        icon: (
          <Box component='span' sx={{ mr: 2, color: `${value}.main` }}>
            <Icon icon='mdi:circle' fontSize='0.75rem' />
          </Box>
        ),
        menuItemProps: {
          onClick: () => {
            handleLabelUpdate(store.selectedMails, key)
            dispatch(handleSelectAllMail(false))
          }
        }
      })
    })

    return array
  }

  const handleFoldersMenu = () => {
    const array = []
    if (routeParams && routeParams.folder && !routeParams.label && foldersObj[routeParams.folder]) {
      foldersObj[routeParams.folder].map(folder => {
        array.length = 0
        array.push({
          icon: folder.icon,
          text: <Typography sx={{ textTransform: 'capitalize' }}>{folder.name}</Typography>,
          menuItemProps: {
            onClick: () => {
              handleFolderUpdate(store.selectedMails, folder.name)
              dispatch(handleSelectAllMail(false))
            }
          }
        })
      })
    } else if (routeParams && routeParams.label) {
      folders.map(folder => {
        array.length = 0
        array.push({
          icon: folder.icon,
          text: <Typography sx={{ textTransform: 'capitalize' }}>{folder.name}</Typography>,
          menuItemProps: {
            onClick: () => {
              handleFolderUpdate(store.selectedMails, folder.name)
              dispatch(handleSelectAllMail(false))
            }
          }
        })
      })
    } else {
      foldersObj['inbox'].map(folder => {
        array.length = 0
        array.push({
          icon: folder.icon,
          text: <Typography sx={{ textTransform: 'capitalize' }}>{folder.name}</Typography>,
          menuItemProps: {
            onClick: () => {
              handleFolderUpdate(store.selectedMails, folder.name)
              dispatch(handleSelectAllMail(false))
            }
          }
        })
      })
    }

    return array
  }

  const renderMailLabels = arr => {
    return arr.map((label, index) => {
      return (
        <Box key={index} component='span' sx={{ mr: 3.5, color: `${labelColors[label]}.main` }}>
          <Icon icon='mdi:circle' fontSize='0.625rem' />
        </Box>
      )
    })
  }

  const mailDetailsProps = {
    hidden,
    folders,
    dispatch,
    direction,
    foldersObj,
    updateMail,
    routeParams,
    labelColors,
    paginateMail,
    handleStarMail,
    mailDetailsOpen,
    handleLabelUpdate,
    handleFolderUpdate,
    setMailDetailsOpen,
    mail: store && store.currentMail ? store.currentMail : null
  }

  return (
    <Box sx={{ width: '100%', overflow: 'hidden', position: 'relative', '& .ps__rail-y': { zIndex: 5 } }}>
      <Box sx={{ height: '100%', backgroundColor: 'background.paper' }}>
        <Box sx={{ px: 5, py: 3 }}>
          <Box sx={{ display: 'flex', alignItems: 'center', width: '100%' }}>
            {lgAbove ? null : (
              <IconButton onClick={handleLeftSidebarToggle} sx={{ mr: 1, ml: -2 }}>
                <Icon icon='mdi:menu' fontSize={20} />
              </IconButton>
            )}
            <Input
              value={query}
              placeholder='Search mail'
              onChange={e => setQuery(e.target.value)}
              sx={{ width: '100%', '&:before, &:after': { display: 'none' } }}
              startAdornment={
                <InputAdornment position='start' sx={{ color: 'text.disabled' }}>
                  <Icon icon='mdi:magnify' fontSize='1.375rem' />
                </InputAdornment>
              }
            />
          </Box>
        </Box>
        <Divider sx={{ m: '0 !important' }} />
        <Box sx={{ py: 1.75, px: { xs: 2.5, sm: 5 } }}>
          <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
            <Box sx={{ display: 'flex', alignItems: 'center' }}>
              {store && store.mails && store.selectedMails ? (
                <Checkbox
                  onChange={e => dispatch(handleSelectAllMail(e.target.checked))}
                  checked={(store.mails.length && store.mails.length === store.selectedMails.length) || false}
                  sx={{
                    '& .MuiSvgIcon-root': { fontSize: '1.375rem' },
                    '&:not(.Mui-checked) .MuiSvgIcon-root': { color: 'text.disabled' }
                  }}
                  indeterminate={
                    !!(
                      store.mails.length &&
                      store.selectedMails.length &&
                      store.mails.length !== store.selectedMails.length
                    )
                  }
                />
              ) : null}

              {store && store.selectedMails.length && store.mails && store.mails.length ? (
                <Fragment>
                  {routeParams && routeParams.folder !== 'trash' ? (
                    <IconButton onClick={handleMoveToTrash}>
                      <Icon icon='mdi:delete-outline' />
                    </IconButton>
                  ) : null}
                  <IconButton onClick={() => handleReadMail(store.selectedMails, false)}>
                    <Icon icon='mdi:email-outline' />
                  </IconButton>
                  <OptionsMenu leftAlignMenu options={handleFoldersMenu()} icon={<Icon icon='mdi:folder-outline' />} />
                  <OptionsMenu leftAlignMenu options={handleLabelsMenu()} icon={<Icon icon='mdi:label-outline' />} />
                </Fragment>
              ) : null}
            </Box>
            <Box sx={{ display: 'flex', alignItems: 'center' }}>
              <IconButton size='small' onClick={handleRefreshMailsClick} sx={{ '& svg': { color: 'text.disabled' } }}>
                <Icon icon='mdi:reload' fontSize='1.375rem' />
              </IconButton>
              <IconButton size='small' sx={{ '& svg': { color: 'text.disabled' } }}>
                <Icon icon='mdi:dots-vertical' fontSize='1.375rem' />
              </IconButton>
            </Box>
          </Box>
        </Box>
        <Divider sx={{ m: '0 !important' }} />
        <Box sx={{ p: 0, position: 'relative', overflowX: 'hidden', height: 'calc(100% - 7rem)' }}>
          <ScrollWrapper hidden={hidden}>
            {store && store.mails && store.mails.length ? (
              <List sx={{ p: 0 }}>
                {store.mails.map(mail => {
                  const mailReadToggleIcon = mail.isRead ? 'mdi:email-outline' : 'mdi:email-open-outline'

                  return (
                    <MailItem
                      key={mail.id}
                      sx={{ backgroundColor: mail.isRead ? 'action.hover' : 'background.paper' }}
                      onClick={() => {
                        setMailDetailsOpen(true)
                        dispatch(getCurrentMail(mail.id))
                        dispatch(updateMail({ emailIds: [mail.id], dataToUpdate: { isRead: true } }))
                        setTimeout(() => {
                          dispatch(handleSelectAllMail(false))
                        }, 600)
                      }}
                    >
                      <Box sx={{ mr: 4, display: 'flex', overflow: 'hidden', alignItems: 'center' }}>
                        <Checkbox
                          onClick={e => e.stopPropagation()}
                          onChange={() => dispatch(handleSelectMail(mail.id))}
                          checked={store.selectedMails.includes(mail.id) || false}
                        />
                        <IconButton
                          size='small'
                          onClick={e => handleStarMail(e, mail.id, !mail.isStarred)}
                          sx={{
                            mr: { xs: 0, sm: 3 },
                            color: mail.isStarred ? 'warning.main' : 'text.secondary',
                            '& svg': {
                              display: { xs: 'none', sm: 'block' }
                            }
                          }}
                        >
                          <Icon icon='mdi:star-outline' />
                        </IconButton>
                        <Avatar
                          alt={mail.from.name}
                          src={mail.from.avatar}
                          sx={{ mr: 3, width: '2rem', height: '2rem' }}
                        />
                        <Box
                          sx={{
                            display: 'flex',
                            overflow: 'hidden',
                            flexDirection: { xs: 'column', sm: 'row' },
                            alignItems: { xs: 'flex-start', sm: 'center' }
                          }}
                        >
                          <Typography
                            sx={{
                              mr: 4,
                              fontWeight: 500,
                              whiteSpace: 'nowrap',
                              width: ['100%', 'auto'],
                              overflow: ['hidden', 'unset'],
                              textOverflow: ['ellipsis', 'unset']
                            }}
                          >
                            {mail.from.name}
                          </Typography>
                          <Typography noWrap variant='body2' sx={{ width: '100%' }}>
                            {mail.subject}
                          </Typography>
                        </Box>
                      </Box>
                      <Box
                        className='mail-actions'
                        sx={{ display: 'none', alignItems: 'center', justifyContent: 'flex-end' }}
                      >
                        {routeParams && routeParams.folder !== 'trash' ? (
                          <Tooltip placement='top' title='Delete Mail'>
                            <IconButton
                              onClick={e => {
                                e.stopPropagation()
                                dispatch(updateMail({ emailIds: [mail.id], dataToUpdate: { folder: 'trash' } }))
                              }}
                            >
                              <Icon icon='mdi:delete-outline' />
                            </IconButton>
                          </Tooltip>
                        ) : null}

                        <Tooltip placement='top' title={mail.isRead ? 'Unread Mail' : 'Read Mail'}>
                          <IconButton
                            onClick={e => {
                              e.stopPropagation()
                              handleReadMail([mail.id], !mail.isRead)
                            }}
                          >
                            <Icon icon={mailReadToggleIcon} />
                          </IconButton>
                        </Tooltip>
                        <Tooltip placement='top' title='Move to Spam'>
                          <IconButton
                            onClick={e => {
                              e.stopPropagation()
                              handleFolderUpdate([mail.id], 'spam')
                            }}
                          >
                            <Icon icon='mdi:alert-octagon-outline' />
                          </IconButton>
                        </Tooltip>
                      </Box>
                      <Box
                        className='mail-info-right'
                        sx={{ display: 'flex', alignItems: 'center', justifyContent: 'flex-end' }}
                      >
                        <Box sx={{ display: { xs: 'none', sm: 'flex' } }}>{renderMailLabels(mail.labels)}</Box>
                        <Typography
                          variant='caption'
                          sx={{ minWidth: '50px', textAlign: 'right', whiteSpace: 'nowrap', color: 'text.disabled' }}
                        >
                          {new Date(mail.time).toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                          })}
                        </Typography>
                      </Box>
                    </MailItem>
                  )
                })}
              </List>
            ) : (
              <Box sx={{ mt: 6, display: 'flex', justifyContent: 'center', alignItems: 'center', '& svg': { mr: 2 } }}>
                <Icon icon='mdi:alert-circle-outline' fontSize={20} />
                <Typography>No Mails Found</Typography>
              </Box>
            )}
          </ScrollWrapper>
          <Backdrop
            open={refresh}
            onClick={() => setRefresh(false)}
            sx={{
              zIndex: 5,
              position: 'absolute',
              color: 'common.white',
              backgroundColor: 'action.disabledBackground'
            }}
          >
            <CircularProgress color='inherit' />
          </Backdrop>
        </Box>
      </Box>

      {/* @ts-ignore */}
      <MailDetails {...mailDetailsProps} />
    </Box>
  )
}

export default MailLog
