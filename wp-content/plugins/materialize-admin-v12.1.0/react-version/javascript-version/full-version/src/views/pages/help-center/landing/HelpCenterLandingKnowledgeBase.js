// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Import
import CustomAvatar from 'src/@core/components/mui/avatar'

const HelpCenterLandingKnowledgeBase = ({ categories }) => {
  const renderCategories = () => {
    if (categories && categories.length) {
      return categories.map(category => {
        const totalArticles = category.subCategories
          .map(subCategory => subCategory.articles.length)
          .reduce((partialSum, a) => partialSum + a, 0)

        return (
          <Grid item xs={12} sm={6} md={4} key={category.slug}>
            <Box
              sx={{
                p: 5,
                boxShadow: 6,
                height: '100%',
                display: 'flex',
                borderRadius: 1,
                flexDirection: 'column',
                alignItems: 'flex-start',
                backgroundColor: 'background.paper'
              }}
            >
              <Box sx={{ mb: 5, display: 'flex', alignItems: 'center' }}>
                <CustomAvatar
                  skin='light'
                  variant='rounded'
                  color={category.avatarColor}
                  sx={{ mr: 3, height: 34, width: 34 }}
                >
                  <Icon icon={category.icon} />
                </CustomAvatar>
                <Typography
                  variant='h6'
                  component={Link}
                  href={`/pages/help-center/${category.slug}/${category.subCategories[0].slug}`}
                  sx={{ fontWeight: 600, textDecoration: 'none', '&:hover': { color: 'primary.main' } }}
                >
                  {category.title}
                </Typography>
              </Box>
              <Box component='ul' sx={{ mt: 0, mb: 5, pl: 6.75, '& li': { mb: 2, color: 'primary.main' } }}>
                {category.subCategories.map(subcategory => (
                  <li key={subcategory.title}>
                    <Typography
                      component={Link}
                      sx={{ color: 'inherit', textDecoration: 'none' }}
                      href={`/pages/help-center/${category.slug}/${subcategory.slug}`}
                    >
                      {subcategory.title}
                    </Typography>
                  </li>
                ))}
              </Box>
              <Typography
                component={Link}
                href={`/pages/help-center/${category.slug}/${category.subCategories[0].slug}`}
                sx={{ mt: 'auto', textDecoration: 'none', '&:hover': { color: 'primary.main' } }}
              >
                {`${totalArticles} Articles`}
              </Typography>
            </Box>
          </Grid>
        )
      })
    } else {
      return null
    }
  }

  return (
    <Grid container spacing={6}>
      {renderCategories()}
    </Grid>
  )
}

export default HelpCenterLandingKnowledgeBase
