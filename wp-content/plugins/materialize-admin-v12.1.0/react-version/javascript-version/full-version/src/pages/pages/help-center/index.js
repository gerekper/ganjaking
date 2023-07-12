// ** MUI Imports
import Card from '@mui/material/Card'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Third Party Imports
import axios from 'axios'

// ** Demo Imports
import HelpCenterLandingHeader from 'src/views/pages/help-center/landing/HelpCenterLandingHeader'
import HelpCenterLandingFooter from 'src/views/pages/help-center/landing/HelpCenterLandingFooter'
import HelpCenterLandingKnowledgeBase from 'src/views/pages/help-center/landing/HelpCenterLandingKnowledgeBase'
import HelpCenterLandingArticlesOverview from 'src/views/pages/help-center/landing/HelpCenterLandingArticlesOverview'

const StyledCardContent = styled(CardContent)(({ theme }) => ({
  paddingTop: `${theme.spacing(20)} !important`,
  paddingBottom: `${theme.spacing(20)} !important`,
  [theme.breakpoints.up('sm')]: {
    paddingLeft: `${theme.spacing(20)} !important`,
    paddingRight: `${theme.spacing(20)} !important`
  }
}))

const HelpCenter = ({ apiData }) => {
  return (
    <Card>
      {apiData !== null ? (
        <>
          <HelpCenterLandingHeader data={apiData.categories} allArticles={apiData.allArticles} />
          <StyledCardContent>
            <Typography variant='h5' sx={{ mb: 6, fontWeight: 600, textAlign: 'center' }}>
              Popular Articles
            </Typography>
            <HelpCenterLandingArticlesOverview articles={apiData.popularArticles} />
          </StyledCardContent>
          <StyledCardContent sx={{ backgroundColor: 'action.hover' }}>
            <Typography variant='h5' sx={{ mb: 6, fontWeight: 600, textAlign: 'center' }}>
              Knowledge Base
            </Typography>
            <HelpCenterLandingKnowledgeBase categories={apiData.categories} />
          </StyledCardContent>
          <StyledCardContent>
            <Typography variant='h5' sx={{ mb: 6, fontWeight: 600, textAlign: 'center' }}>
              Keep Learning
            </Typography>
            <HelpCenterLandingArticlesOverview articles={apiData.keepLearning} />
          </StyledCardContent>
          <StyledCardContent sx={{ textAlign: 'center', backgroundColor: 'action.hover' }}>
            <HelpCenterLandingFooter />
          </StyledCardContent>
        </>
      ) : null}
    </Card>
  )
}

export const getStaticProps = async () => {
  const res = await axios.get('/pages/help-center/landing')
  const apiData = res.data

  return {
    props: {
      apiData
    }
  }
}

export default HelpCenter
