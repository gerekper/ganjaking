// ** MUI Imports
import Button from '@mui/material/Button'
import Drawer from '@mui/material/Drawer'
import Checkbox from '@mui/material/Checkbox'
import Typography from '@mui/material/Typography'
import FormControlLabel from '@mui/material/FormControlLabel'

// ** Types
import { ThemeColor } from 'src/@core/layouts/types'
import { SidebarLeftType, CalendarFiltersType } from 'src/types/apps/calendarTypes'

const SidebarLeft = (props: SidebarLeftType) => {
  const {
    store,
    mdAbove,
    dispatch,
    calendarsColor,
    leftSidebarOpen,
    leftSidebarWidth,
    handleSelectEvent,
    handleAllCalendars,
    handleCalendarsUpdate,
    handleLeftSidebarToggle,
    handleAddEventSidebarToggle
  } = props

  const colorsArr = calendarsColor ? Object.entries(calendarsColor) : []

  const renderFilters = colorsArr.length
    ? colorsArr.map(([key, value]: string[]) => {
        return (
          <FormControlLabel
            key={key}
            label={key}
            sx={{ mb: 0.5 }}
            control={
              <Checkbox
                color={value as ThemeColor}
                checked={store.selectedCalendars.includes(key as CalendarFiltersType)}
                onChange={() => dispatch(handleCalendarsUpdate(key as CalendarFiltersType))}
              />
            }
          />
        )
      })
    : null

  const handleSidebarToggleSidebar = () => {
    handleAddEventSidebarToggle()
    dispatch(handleSelectEvent(null))
  }

  if (renderFilters) {
    return (
      <Drawer
        open={leftSidebarOpen}
        onClose={handleLeftSidebarToggle}
        variant={mdAbove ? 'permanent' : 'temporary'}
        ModalProps={{
          disablePortal: true,
          disableAutoFocus: true,
          disableScrollLock: true,
          keepMounted: true // Better open performance on mobile.
        }}
        sx={{
          zIndex: 2,
          display: 'block',
          position: mdAbove ? 'static' : 'absolute',
          '& .MuiDrawer-paper': {
            borderRadius: 1,
            boxShadow: 'none',
            width: leftSidebarWidth,
            borderTopRightRadius: 0,
            alignItems: 'flex-start',
            borderBottomRightRadius: 0,
            p: theme => theme.spacing(5),
            zIndex: mdAbove ? 2 : 'drawer',
            position: mdAbove ? 'static' : 'absolute'
          },
          '& .MuiBackdrop-root': {
            borderRadius: 1,
            position: 'absolute'
          }
        }}
      >
        <Button fullWidth variant='contained' onClick={handleSidebarToggleSidebar}>
          Add Event
        </Button>

        <Typography variant='body2' sx={{ mt: 7, mb: 2.5, textTransform: 'uppercase' }}>
          Calendars
        </Typography>
        <FormControlLabel
          label='View All'
          sx={{ mr: 0, mb: 0.5 }}
          control={
            <Checkbox
              color='secondary'
              checked={store.selectedCalendars.length === colorsArr.length}
              onChange={e => dispatch(handleAllCalendars(e.target.checked))}
            />
          }
        />
        {renderFilters}
      </Drawer>
    )
  } else {
    return null
  }
}

export default SidebarLeft
