// ** React Imports
import { Fragment, useEffect, useState, SyntheticEvent } from 'react'

// ** Next Imports
import { GetStaticProps, InferGetStaticPropsType } from 'next/types'

// ** MUI Imports
import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Third Party Imports
import axios from 'axios'

// ** Types
import { FaqType } from 'src/@fake-db/types'

// ** Demo Imports
import FAQS from 'src/views/pages/faq/Faqs'
import FaqHeader from 'src/views/pages/faq/FaqHeader'
import FaqFooter from 'src/views/pages/faq/FaqFooter'

const FAQ = ({ apiData }: InferGetStaticPropsType<typeof getStaticProps>) => {
  // ** States
  const [data, setData] = useState<{ faqData: FaqType } | null>(null)
  const [searchTerm, setSearchTerm] = useState<string>('')
  const [activeTab, setActiveTab] = useState<string>('payment')

  useEffect(() => {
    if (searchTerm !== '') {
      axios.get('/pages/faqs', { params: { q: searchTerm } }).then(response => {
        if (response.data.faqData && Object.values(response.data.faqData).length) {
          setData(response.data)

          // @ts-ignore
          setActiveTab(Object.values(response.data.faqData)[0].id)
        } else {
          setData(null)
        }
      })
    } else {
      setData(apiData)
    }
  }, [apiData, searchTerm])

  const handleChange = (event: SyntheticEvent, newValue: string) => {
    setActiveTab(newValue)
  }

  const renderNoResult = (
    <Box sx={{ mt: 8, display: 'flex', justifyContent: 'center', alignItems: 'center', '& svg': { mr: 2 } }}>
      <Icon icon='mdi:alert-circle-outline' />
      <Typography variant='h6'>No Results Found!!</Typography>
    </Box>
  )

  return (
    <Fragment>
      <FaqHeader searchTerm={searchTerm} setSearchTerm={setSearchTerm} />
      {data !== null ? <FAQS data={data} activeTab={activeTab} handleChange={handleChange} /> : renderNoResult}
      <FaqFooter />
    </Fragment>
  )
}

export const getStaticProps: GetStaticProps = async () => {
  const res = await axios.get('/pages/faqs')
  const apiData: FaqType = res.data

  return {
    props: {
      apiData
    }
  }
}

export default FAQ
