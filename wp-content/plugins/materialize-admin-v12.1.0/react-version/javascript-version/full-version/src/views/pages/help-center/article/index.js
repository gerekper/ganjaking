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
import Divider from '@mui/material/Divider'
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

const HelpCenterArticle = ({ articles, activeArticle, activeSubcategory }) => {
  // ** State
  const [isLoading, setIsLoading] = useState(false)
  const [tabValue, setTabValue] = useState(activeArticle.slug)

  // ** Hooks
  const router = useRouter()

  const handleChange = (event, newValue) => {
    setIsLoading(true)
    router
      .push({ pathname: `/pages/help-center/${router.query.category}/${router.query.subcategory}/${newValue}` })
      .then(() => setIsLoading(false))
  }
  useEffect(() => {
    if (activeArticle && activeArticle.slug !== tabValue) {
      setTabValue(activeArticle.slug)
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [activeArticle])

  const renderTabs = () => {
    return articles && articles.map(article => <Tab key={article.slug} value={article.slug} label={article.title} />)
  }

  const renderContent = () => (
    <TabPanel value={tabValue} sx={{ p: 0, width: '100%' }}>
      <Card>
        <CardContent>
          <Button
            component={Link}
            variant='outlined'
            startIcon={<Icon icon='mdi:chevron-left' />}
            href={`/pages/help-center/${router.query.category}/${router.query.subcategory}`}
          >
            Back to Categories
          </Button>

          <Box sx={{ mt: 6, mb: 6.5, display: 'flex', alignItems: 'center' }}>
            <CustomAvatar skin='light' variant='rounded' color='secondary' sx={{ mr: 3 }}>
              <Icon icon={activeSubcategory.icon} />
            </CustomAvatar>
            <Typography variant='h6'>{activeArticle.title}</Typography>
          </Box>

          <Box
            sx={{ '& p': { color: 'text.secondary' } }}
            dangerouslySetInnerHTML={{ __html: activeArticle.content }}
          />
        </CardContent>
        <Divider sx={{ m: '0 !important' }} />
        <CardContent
          sx={{
            gap: 4,
            display: 'flex',
            flexWrap: 'wrap',
            alignItems: 'flex-start',
            justifyContent: 'space-between'
          }}
        >
          <div>
            <Typography variant='h6' sx={{ fontWeight: 600 }}>
              {activeArticle.title}
            </Typography>
            <Typography sx={{ mb: 4, color: 'text.secondary' }}>55 People found this helpful</Typography>
            <div>
              <Button variant='outlined' sx={{ mr: 2.5, p: 1.5, minWidth: 32 }}>
                <Icon fontSize={18} icon='mdi:thumbs-up-outline' />
              </Button>
              <Button variant='outlined' sx={{ p: 1.5, minWidth: 32 }}>
                <Icon fontSize={18} icon='mdi:thumbs-down-outline' />
              </Button>
            </div>
          </div>
          <Box sx={{ display: 'flex' }}>
            <Typography sx={{ mr: 1, fontWeight: 600 }}>Still need help?</Typography>
            <Typography
              href='/'
              component={Link}
              onClick={e => e.preventDefault()}
              sx={{ fontWeight: 600, color: 'primary.main', textDecoration: 'none' }}
            >
              Contact us?
            </Typography>
          </Box>
        </CardContent>
      </Card>
    </TabPanel>
  )

  return (
    <TabContext value={tabValue}>
      <Box sx={{ display: 'flex', flexDirection: ['column', 'column', 'row'] }}>
        <Box sx={{ mr: [0, 0, 5], mb: [5, 5, 0], display: 'flex', flexDirection: 'column' }}>
          <Typography variant='h6' sx={{ mb: 4, fontWeight: 600 }}>
            {activeSubcategory.title}
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

export default HelpCenterArticle
