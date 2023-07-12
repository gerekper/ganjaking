// ** React Imports
import { useState, forwardRef } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Tab from '@mui/material/Tab'
import Card from '@mui/material/Card'
import TabList from '@mui/lab/TabList'
import TabPanel from '@mui/lab/TabPanel'
import Avatar from '@mui/material/Avatar'
import Dialog from '@mui/material/Dialog'
import Button from '@mui/material/Button'
import TabContext from '@mui/lab/TabContext'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import Fade from '@mui/material/Fade'
import DialogContent from '@mui/material/DialogContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Hook Imports
import { useSettings } from 'src/@core/hooks/useSettings'

// ** Tab Content Imports
import DialogTabDetails from 'src/views/pages/dialog-examples/create-app-tabs/DialogTabDetails'
import DialogTabBilling from 'src/views/pages/dialog-examples/create-app-tabs/DialogTabBilling'
import DialogTabDatabase from 'src/views/pages/dialog-examples/create-app-tabs/DialogTabDatabase'
import DialogTabFramework from 'src/views/pages/dialog-examples/create-app-tabs/DialogTabFramework'

const Transition = forwardRef(function Transition(props, ref) {
  return <Fade ref={ref} {...props} />
})

const TabLabel = props => {
  const { icon, title, subtitle, active } = props

  return (
    <div>
      <Box sx={{ display: 'flex', alignItems: 'center' }}>
        <Avatar
          variant='rounded'
          sx={{
            mr: 3.5,
            ...(active ? { color: 'common.white', backgroundColor: 'primary.main' } : { color: 'text.primary' })
          }}
        >
          {icon}
        </Avatar>
        <Box sx={{ textAlign: 'left' }}>
          <Typography variant='body2'>{title}</Typography>
          <Typography variant='caption' sx={{ color: 'text.disabled', textTransform: 'none' }}>
            {subtitle}
          </Typography>
        </Box>
      </Box>
    </div>
  )
}
const tabsArr = ['detailsTab', 'frameworkTab', 'DatabaseTab', 'paymentTab', 'submitTab']

const DialogCreateApp = () => {
  // ** States
  const [show, setShow] = useState(false)
  const [activeTab, setActiveTab] = useState('detailsTab')

  // ** Hook
  const { settings } = useSettings()

  // ** Var
  const { direction } = settings

  const handleClose = () => {
    setShow(false)
    setActiveTab('detailsTab')
  }
  const nextArrow = direction === 'ltr' ? 'mdi:arrow-right' : 'mdi:arrow-left'
  const previousArrow = direction === 'ltr' ? 'mdi:arrow-left' : 'mdi:arrow-right'

  const renderTabFooter = () => {
    const prevTab = tabsArr[tabsArr.indexOf(activeTab) - 1]
    const nextTab = tabsArr[tabsArr.indexOf(activeTab) + 1]

    return (
      <Box sx={{ mt: 8.5, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <Button
          variant='outlined'
          color='secondary'
          disabled={activeTab === 'detailsTab'}
          onClick={() => setActiveTab(prevTab)}
          startIcon={<Icon icon={previousArrow} />}
        >
          Previous
        </Button>
        <Button
          variant='contained'
          color={activeTab === 'submitTab' ? 'success' : 'primary'}
          endIcon={<Icon icon={activeTab === 'submitTab' ? 'mdi:check' : nextArrow} />}
          onClick={() => {
            if (activeTab !== 'submitTab') {
              setActiveTab(nextTab)
            } else {
              handleClose()
            }
          }}
        >
          {activeTab === 'submitTab' ? 'Submit' : 'Next'}
        </Button>
      </Box>
    )
  }

  return (
    <Card>
      <CardContent sx={{ textAlign: 'center', '& svg': { mb: 2 } }}>
        <Icon icon='mdi:cube-outline' fontSize='2rem' />
        <Typography variant='h6' sx={{ mb: 4 }}>
          Create App
        </Typography>
        <Typography sx={{ mb: 3 }}>
          Provide application data with this form to create the app dialog popup example, easy to use in any page.
        </Typography>
        <Button variant='contained' onClick={() => setShow(true)}>
          Show
        </Button>
      </CardContent>
      <Dialog
        fullWidth
        open={show}
        scroll='body'
        maxWidth='md'
        onClose={handleClose}
        onBackdropClick={handleClose}
        TransitionComponent={Transition}
      >
        <DialogContent
          sx={{
            position: 'relative',
            pr: theme => [`${theme.spacing(5)} !important`, `${theme.spacing(15)} !important`],
            pl: theme => [`${theme.spacing(5)} !important`, `${theme.spacing(11)} !important`],
            py: theme => [`${theme.spacing(8)} !important`, `${theme.spacing(12.5)} !important`]
          }}
        >
          <IconButton size='small' onClick={handleClose} sx={{ position: 'absolute', right: '1rem', top: '1rem' }}>
            <Icon icon='mdi:close' />
          </IconButton>
          <Box sx={{ mb: 3, textAlign: 'center' }}>
            <Typography variant='h5' sx={{ mb: 3, lineHeight: '2rem' }}>
              Create App
            </Typography>
            <Typography variant='body2'>Provide data with this form to create your app.</Typography>
          </Box>
          <Box sx={{ display: 'flex', flexWrap: { xs: 'wrap', md: 'nowrap' } }}>
            <TabContext value={activeTab}>
              <TabList
                orientation='vertical'
                onChange={(e, newValue) => setActiveTab(newValue)}
                sx={{
                  border: 0,
                  minWidth: 200,
                  '& .MuiTabs-indicator': { display: 'none' },
                  '& .MuiTabs-flexContainer': {
                    alignItems: 'flex-start',
                    '& .MuiTab-root': {
                      width: '100%',
                      alignItems: 'flex-start'
                    }
                  }
                }}
              >
                <Tab
                  disableRipple
                  value='detailsTab'
                  label={
                    <TabLabel
                      title='Details'
                      subtitle='Enter Details'
                      icon={<Icon icon='mdi:clipboard-outline' />}
                      active={activeTab === 'detailsTab'}
                    />
                  }
                />
                <Tab
                  disableRipple
                  value='frameworkTab'
                  label={
                    <TabLabel
                      title='Frameworks'
                      icon={<Icon icon='mdi:star-outline' />}
                      subtitle='Select Framework'
                      active={activeTab === 'frameworkTab'}
                    />
                  }
                />
                <Tab
                  disableRipple
                  value='DatabaseTab'
                  label={
                    <TabLabel
                      title='Database'
                      active={activeTab === 'DatabaseTab'}
                      subtitle='Select Database'
                      icon={<Icon icon='mdi:chart-donut' />}
                    />
                  }
                />
                <Tab
                  disableRipple
                  value='paymentTab'
                  label={
                    <TabLabel
                      title='Billing'
                      active={activeTab === 'paymentTab'}
                      subtitle='Payment details'
                      icon={<Icon icon='mdi:credit-card-outline' />}
                    />
                  }
                />
                <Tab
                  disableRipple
                  value='submitTab'
                  label={
                    <TabLabel
                      title='Submit'
                      subtitle='Submit'
                      icon={<Icon icon='mdi:check' />}
                      active={activeTab === 'submitTab'}
                    />
                  }
                />
              </TabList>
              <TabPanel value='detailsTab' sx={{ flexGrow: 1, p: '0 !important' }}>
                <DialogTabDetails />
                {renderTabFooter()}
              </TabPanel>
              <TabPanel value='frameworkTab' sx={{ flexGrow: 1, p: '0 !important' }}>
                <DialogTabFramework />
                {renderTabFooter()}
              </TabPanel>
              <TabPanel value='DatabaseTab' sx={{ flexGrow: 1, p: '0 !important' }}>
                <DialogTabDatabase />
                {renderTabFooter()}
              </TabPanel>
              <TabPanel value='paymentTab' sx={{ flexGrow: 1, p: '0 !important' }}>
                <DialogTabBilling />
                {renderTabFooter()}
              </TabPanel>
              <TabPanel value='submitTab' sx={{ flexGrow: 1, p: '0 !important' }}>
                <Box sx={{ textAlign: 'center' }}>
                  <Typography variant='h6'>Submit</Typography>
                  <Typography variant='body2'>Submit to kickstart your project.</Typography>

                  <Box sx={{ mt: 5, display: 'flex', justifyContent: 'center' }}>
                    <img alt='submit-img' src={`/images/pages/create-app-dialog-illustration-${settings.mode}.png`} />
                  </Box>
                </Box>
                {renderTabFooter()}
              </TabPanel>
            </TabContext>
          </Box>
        </DialogContent>
      </Dialog>
    </Card>
  )
}

export default DialogCreateApp
