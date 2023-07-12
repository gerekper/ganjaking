// ** Third Party Imports
import axios from 'axios'

// ** Demo Components Imports
import HelpCenterArticle from 'src/views/pages/help-center/article'

const HelpCenterArticlePage = ({ apiData }) => {
  return apiData ? (
    <HelpCenterArticle
      articles={apiData.articles}
      activeArticle={apiData.activeArticle}
      activeSubcategory={apiData.activeSubcategory}
    />
  ) : null
}

export const getStaticPaths = async () => {
  const res = await axios.get('/pages/help-center/article', {
    params: { category: 'getting-started' }
  })
  const apiData = await res.data
  const paths = []
  apiData.categories.forEach(category =>
    category.subCategories.forEach(subcategory =>
      subcategory.articles.forEach(article => {
        paths.push({
          params: { category: `${category.slug}`, subcategory: `${subcategory.slug}`, article: `${article.slug}` }
        })
      })
    )
  )

  return {
    paths,
    fallback: false
  }
}

export const getStaticProps = async ({ params }) => {
  const res = await axios.get('/pages/help-center/article', {
    params: { article: params?.article, category: params?.category, subcategory: params?.subcategory }
  })
  const apiData = await res.data

  return {
    props: {
      apiData
    }
  }
}

export default HelpCenterArticlePage
