// ** Third Party Imports
import axios from 'axios'

// ** Demo Components Imports
import HelpCenterSubcategory from 'src/views/pages/help-center/subcategory'

const HelpCenterSubcategoryPage = ({ apiData }) => {
  return apiData ? <HelpCenterSubcategory data={apiData.data} activeTab={apiData.activeTab} /> : null
}

export const getStaticPaths = async () => {
  const res = await axios.get('/pages/help-center/subcategory', {
    params: { category: 'getting-started' }
  })
  const apiData = await res.data
  const paths = []
  apiData.categories.forEach(category =>
    category.subCategories.forEach(subcategory => {
      paths.push({ params: { category: `${category.slug}`, subcategory: `${subcategory.slug}` } })
    })
  )

  return {
    paths,
    fallback: false
  }
}

export const getStaticProps = async ({ params }) => {
  const res = await axios.get('/pages/help-center/subcategory', {
    params: { category: params?.category, subcategory: params?.subcategory }
  })
  const apiData = await res.data

  return {
    props: {
      apiData
    }
  }
}

export default HelpCenterSubcategoryPage
