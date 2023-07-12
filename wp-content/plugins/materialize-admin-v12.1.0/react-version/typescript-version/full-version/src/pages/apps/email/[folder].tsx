// ** Next Import
import { GetStaticPaths, GetStaticProps, GetStaticPropsContext } from 'next/types'

// ** Third Party Imports
import axios from 'axios'

// ** Types
import { MailLayoutType, MailType } from 'src/types/apps/emailTypes'

// ** Demo Components Imports
import Email from 'src/views/apps/email/Email'

const EmailApp = ({ folder }: MailLayoutType) => {
  return <Email folder={folder} />
}

export const getStaticPaths: GetStaticPaths = async () => {
  const res = await axios.get('/apps/email/allEmails')
  const data: MailType[] = await res.data.emails

  const paths = data.map((mail: MailType) => ({
    params: { folder: mail.folder }
  }))

  return {
    paths,
    fallback: false
  }
}

export const getStaticProps: GetStaticProps = ({ params }: GetStaticPropsContext) => {
  return {
    props: {
      folder: params?.folder
    }
  }
}

EmailApp.contentHeightFixed = true

export default EmailApp
