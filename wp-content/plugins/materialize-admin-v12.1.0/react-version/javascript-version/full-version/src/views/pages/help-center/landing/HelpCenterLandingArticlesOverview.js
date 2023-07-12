// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'

const HelpCenterLandingArticlesOverview = props => {
  const { articles } = props

  const renderArticles = () => {
    if (articles && articles.length) {
      return articles.map(article => {
        return (
          <Grid item xs={12} sm={6} md={4} key={article.slug}>
            <Box
              sx={{
                p: 5,
                height: '100%',
                display: 'flex',
                borderRadius: 1,
                textAlign: 'center',
                alignItems: 'center',
                flexDirection: 'column',
                border: theme => `1px solid ${theme.palette.divider}`
              }}
            >
              <Box sx={{ minHeight: 58, display: 'flex' }}>
                <img height='58' src={article.img} alt={article.title} />
              </Box>

              <Typography variant='h6' sx={{ mb: 1.5, fontWeight: 600 }}>
                {article.title}
              </Typography>
              <Typography
                sx={{
                  my: 'auto',
                  overflow: 'hidden',
                  WebkitLineClamp: '2',
                  display: '-webkit-box',
                  color: 'text.secondary',
                  textOverflow: 'ellipsis',
                  WebkitBoxOrient: 'vertical'
                }}
              >
                {article.subtitle}
              </Typography>
              <Button
                sx={{ mt: 4 }}
                component={Link}
                variant='outlined'
                href='/pages/help-center/getting-started/account/changing-your-username'
              >
                Read More
              </Button>
            </Box>
          </Grid>
        )
      })
    } else {
      return null
    }
  }

  return (
    <Grid container spacing={6} sx={{ justifyContent: 'center' }}>
      {renderArticles()}
    </Grid>
  )
}

export default HelpCenterLandingArticlesOverview
