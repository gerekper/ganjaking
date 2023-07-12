// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Third Party Components
import axios from 'axios'

// ** Demo Components Imports
import AddCard from 'src/views/apps/invoice/add/AddCard'
import AddActions from 'src/views/apps/invoice/add/AddActions'
import AddNewCustomers from 'src/views/apps/invoice/add/AddNewCustomer'

// ** Styled Component
import DatePickerWrapper from 'src/@core/styles/libs/react-datepicker'

const InvoiceAdd = ({ apiClientData, invoiceNumber }) => {
  // ** State
  const [addCustomerOpen, setAddCustomerOpen] = useState(false)
  const [selectedClient, setSelectedClient] = useState(null)
  const [clients, setClients] = useState(apiClientData)
  const toggleAddCustomerDrawer = () => setAddCustomerOpen(!addCustomerOpen)

  return (
    <DatePickerWrapper sx={{ '& .react-datepicker-wrapper': { width: 'auto' } }}>
      <Grid container spacing={6}>
        <Grid item xl={9} md={8} xs={12}>
          <AddCard
            clients={clients}
            invoiceNumber={invoiceNumber}
            selectedClient={selectedClient}
            setSelectedClient={setSelectedClient}
            toggleAddCustomerDrawer={toggleAddCustomerDrawer}
          />
        </Grid>
        <Grid item xl={3} md={4} xs={12}>
          <AddActions />
        </Grid>
      </Grid>
      <AddNewCustomers
        clients={clients}
        open={addCustomerOpen}
        setClients={setClients}
        toggle={toggleAddCustomerDrawer}
        setSelectedClient={setSelectedClient}
      />
    </DatePickerWrapper>
  )
}

export const getStaticProps = async () => {
  const clientResponse = await axios.get('/apps/invoice/clients')
  const apiClientData = clientResponse.data
  const allInvoicesResponse = await axios.get('/apps/invoice/invoices', { params: { q: '', status: '' } })
  const lastInvoiceNumber = Math.max(...allInvoicesResponse.data.allData.map(i => i.id))

  return {
    props: {
      apiClientData,
      invoiceNumber: lastInvoiceNumber + 1
    }
  }
}

export default InvoiceAdd
