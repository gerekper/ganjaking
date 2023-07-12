// ** React Imports
import { useEffect, useState } from 'react'

// ** Next Imports
import Link from 'next/link'
import { useRouter } from 'next/router'

// ** MUI Imports
import Box from '@mui/material/Box'
import Tab from '@mui/material/Tab'
import Card from '@mui/material/Card'
import TabPanel from '@mui/lab/TabPanel'
import Button from '@mui/material/Button'
import TabContext from '@mui/lab/TabContext'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import MuiTabList from '@mui/lab/TabList'
import CircularProgress from '@mui/material/CircularProgress'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Import
import CustomAvatar from 'src/@core/components/mui/avatar'

const TabList = styled(MuiTabList)(({ theme }) => ({
  border: 0,
  marginRight: 0,
  overflow: 'visible',
  '& .MuiTabs-flexContainer': {
    flexDirection: 'column'
  },
  '& .MuiTabs-indicator': {
    display: 'none'
  },
  '& .Mui-selected': {
    backgroundColor: theme.palette.primary.main,
    color: `${theme.palette.common.white} !important`
  },
  '& .MuiTab-root': {
    minHeight: 40,
    minWidth: 300,
    maxWidth: 300,
    textAlign: 'start',
    flexDirection: 'row',
    justifyContent: 'flex-start',
    borderRadius: theme.shape.borderRadius,
    '& svg': {
      marginBottom: 0,
      marginRight: theme.spacing(1)
    },
    [theme.breakpoints.down('md')]: {
      minWidth: '100%',
      maxWidth: '100%'
    }
  }
}))

const HelpCenterSubcategory = ({ data, activeTab }) => {
  // ** State
  const [isLoading, setIsLoading] = useState(false)
  const [tabValue, setTabValue] = useState(activeTab)

  // ** Hook
  const router = useRouter()

  const handleChange = (event, newValue) => {
    setIsLoading(true)
    router.push(`/pages/help-center/${data.slug}/${newValue}`).then(() => setIsLoading(false))
  }
  useEffect(() => {
    if (activeTab && activeTab !== tabValue) {
      setTabValue(activeTab)
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [activeTab])

  const renderTabs = () => {
    return data && data.subCategories.map(tab => <Tab key={tab.slug} value={tab.slug} label={tab.title} />)
  }

  const renderContent = () => {
    const dataToRender = data.subCategories.filter(item => item.slug === tabValue)[0]

    return (
      <TabPanel value={tabValue} sx={{ p: 0, width: '100%' }}>
        <Card>
          <CardContent>
            <Box sx={{ mb: 7, display: 'flex', alignItems: 'center' }}>
              <CustomAvatar skin='light' variant='rounded' sx={{ mr: 3 }}>
                <Icon icon={dataToRender.icon} />
              </CustomAvatar>
              <Typography variant='h5' sx={{ fontWeight: 600 }}>
                {dataToRender.title}
              </Typography>
            </Box>

            <Box sx={{ mb: 6 }}>
              {dataToRender.articles.map(article => {
                return (
                  <Box
                    key={article.title}
                    sx={{
                      display: 'flex',
                      alignItems: 'center',
                      '&:not(:last-of-type)': { mb: 4 },
                      '& svg': { color: 'text.disabled' }
                    }}
                  >
                    <Icon icon='mdi:chevron-right' />
                    <Typography
                      component={Link}
                      sx={{ ml: 1.5, color: 'primary.main', textDecoration: 'none' }}
                      href={`/pages/help-center/${data.slug}/${activeTab}/${article.slug}`}
                    >
                      {article.title}
                    </Typography>
                  </Box>
                )
              })}
            </Box>

            <Button
              component={Link}
              variant='outlined'
              href='/pages/help-center'
              startIcon={<Icon icon='mdi:chevron-left' />}
            >
              Back to help center
            </Button>
          </CardContent>
        </Card>
      </TabPanel>
    )
  }

  return (
    <TabContext value={tabValue}>
      <Box sx={{ display: 'flex', flexDirection: ['column', 'column', 'row'] }}>
        <Box sx={{ mr: [0, 0, 5], mb: [5, 5, 0], display: 'flex', flexDirection: 'column' }}>
          <Typography variant='h6' sx={{ mb: 4, fontWeight: 600 }}>
            {data.title}
          </Typography>
          <TabList orientation='vertical' onChange={handleChange} aria-label='vertical tabs example'>
            {renderTabs()}
          </TabList>
        </Box>
        {isLoading ? (
          <Box sx={{ mt: 11, width: '100%', display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
            <CircularProgress sx={{ mb: 4 }} />
            <Typography>Loading...</Typography>
          </Box>
        ) : (
          renderContent()
        )}
      </Box>
    </TabContext>
  )
}

export default HelpCenterSubcategory
