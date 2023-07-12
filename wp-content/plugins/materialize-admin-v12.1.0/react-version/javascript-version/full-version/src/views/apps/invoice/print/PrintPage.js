// ** React Imports
import { useEffect, useState } from 'react'

// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Grid from '@mui/material/Grid'
import Alert from '@mui/material/Alert'
import Table from '@mui/material/Table'
import Divider from '@mui/material/Divider'
import TableRow from '@mui/material/TableRow'
import TableHead from '@mui/material/TableHead'
import TableBody from '@mui/material/TableBody'
import Typography from '@mui/material/Typography'
import Box from '@mui/material/Box'
import { styled, useTheme } from '@mui/material/styles'
import TableCell from '@mui/material/TableCell'

// ** Third Party Components
import axios from 'axios'

// ** Configs
import themeConfig from 'src/configs/themeConfig'

const CalcWrapper = styled(Box)(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'space-between',
  '&:not(:last-of-type)': {
    marginBottom: theme.spacing(2)
  }
}))

const MUITableCell = styled(TableCell)(({ theme }) => ({
  borderBottom: 0,
  padding: `${theme.spacing(1, 0)} !important`
}))

const InvoicePrint = ({ id }) => {
  // ** State
  const [error, setError] = useState(false)
  const [data, setData] = useState(null)

  // ** Hooks
  const theme = useTheme()
  useEffect(() => {
    setTimeout(() => {
      window.print()
    }, 100)
  }, [])
  useEffect(() => {
    axios
      .get('/apps/invoice/single-invoice', { params: { id } })
      .then(res => {
        setData(res.data)
        setError(false)
      })
      .catch(() => {
        setData(null)
        setError(true)
      })
  }, [id])
  if (data) {
    const { invoice, paymentDetails } = data

    return (
      <Box sx={{ p: 12, pb: 6 }}>
        <Grid container>
          <Grid item xs={8} sx={{ mb: { sm: 0, xs: 4 } }}>
            <Box sx={{ display: 'flex', flexDirection: 'column' }}>
              <Box sx={{ mb: 6, display: 'flex', alignItems: 'center' }}>
                <svg width={40} fill='none' height={22} viewBox='0 0 268 150' xmlns='http://www.w3.org/2000/svg'>
                  <rect
                    rx='25.1443'
                    width='50.2886'
                    height='143.953'
                    fill={theme.palette.primary.main}
                    transform='matrix(-0.865206 0.501417 0.498585 0.866841 195.571 0)'
                  />
                  <rect
                    rx='25.1443'
                    width='50.2886'
                    height='143.953'
                    fillOpacity='0.4'
                    fill='url(#paint0_linear_7821_79167)'
                    transform='matrix(-0.865206 0.501417 0.498585 0.866841 196.084 0)'
                  />
                  <rect
                    rx='25.1443'
                    width='50.2886'
                    height='143.953'
                    fill={theme.palette.primary.main}
                    transform='matrix(0.865206 0.501417 -0.498585 0.866841 173.147 0)'
                  />
                  <rect
                    rx='25.1443'
                    width='50.2886'
                    height='143.953'
                    fill={theme.palette.primary.main}
                    transform='matrix(-0.865206 0.501417 0.498585 0.866841 94.1973 0)'
                  />
                  <rect
                    rx='25.1443'
                    width='50.2886'
                    height='143.953'
                    fillOpacity='0.4'
                    fill='url(#paint1_linear_7821_79167)'
                    transform='matrix(-0.865206 0.501417 0.498585 0.866841 94.1973 0)'
                  />
                  <rect
                    rx='25.1443'
                    width='50.2886'
                    height='143.953'
                    fill={theme.palette.primary.main}
                    transform='matrix(0.865206 0.501417 -0.498585 0.866841 71.7728 0)'
                  />
                  <defs>
                    <linearGradient
                      y1='0'
                      x1='25.1443'
                      x2='25.1443'
                      y2='143.953'
                      id='paint0_linear_7821_79167'
                      gradientUnits='userSpaceOnUse'
                    >
                      <stop />
                      <stop offset='1' stopOpacity='0' />
                    </linearGradient>
                    <linearGradient
                      y1='0'
                      x1='25.1443'
                      x2='25.1443'
                      y2='143.953'
                      id='paint1_linear_7821_79167'
                      gradientUnits='userSpaceOnUse'
                    >
                      <stop />
                      <stop offset='1' stopOpacity='0' />
                    </linearGradient>
                  </defs>
                </svg>
                <Typography variant='h6' sx={{ ml: 2, fontWeight: 700, lineHeight: 1.2 }}>
                  {themeConfig.templateName}
                </Typography>
              </Box>
              <div>
                <Typography variant='body2' sx={{ mb: 1 }}>
                  Office 149, 450 South Brand Brooklyn
                </Typography>
                <Typography variant='body2' sx={{ mb: 1 }}>
                  San Diego County, CA 91905, USA
                </Typography>
                <Typography variant='body2'>+1 (123) 456 7891, +44 (876) 543 2198</Typography>
              </div>
            </Box>
          </Grid>
          <Grid item xs={4}>
            <Box sx={{ display: 'flex', flexDirection: 'column', alignItems: { sm: 'flex-end', xs: 'flex-start' } }}>
              <Typography variant='h6' sx={{ mb: 2 }}>
                {`Invoice #${invoice.id}`}
              </Typography>
              <Box sx={{ mb: 2, display: 'flex' }}>
                <Typography variant='body2' sx={{ mr: 3 }}>
                  Date Issued:
                </Typography>
                <Typography variant='body2' sx={{ fontWeight: 600 }}>
                  {invoice.issuedDate}
                </Typography>
              </Box>
              <Box sx={{ display: 'flex' }}>
                <Typography variant='body2' sx={{ mr: 3 }}>
                  Date Due:
                </Typography>
                <Typography variant='body2' sx={{ fontWeight: 600 }}>
                  {invoice.dueDate}
                </Typography>
              </Box>
            </Box>
          </Grid>
        </Grid>

        <Divider sx={{ my: theme => `${theme.spacing(6)} !important` }} />

        <Grid container>
          <Grid item xs={7} md={8} sx={{ mb: { lg: 0, xs: 4 } }}>
            <Typography variant='body2' sx={{ mb: 3.5, fontWeight: 600 }}>
              Invoice To:
            </Typography>
            <Typography variant='body2' sx={{ mb: 2 }}>
              {invoice.name}
            </Typography>
            <Typography variant='body2' sx={{ mb: 2 }}>
              {invoice.company}
            </Typography>
            <Typography variant='body2' sx={{ mb: 2 }}>
              {invoice.address}
            </Typography>
            <Typography variant='body2' sx={{ mb: 2 }}>
              {invoice.contact}
            </Typography>
            <Typography variant='body2' sx={{ mb: 2 }}>
              {invoice.companyEmail}
            </Typography>
          </Grid>
          <Grid item xs={5} md={4}>
            <Typography variant='body2' sx={{ mb: 3.5, fontWeight: 600 }}>
              Bill To:
            </Typography>
            <Table>
              <TableBody>
                <TableRow>
                  <MUITableCell>Total Due:</MUITableCell>
                  <MUITableCell>{paymentDetails.totalDue}</MUITableCell>
                </TableRow>
                <TableRow>
                  <MUITableCell>Bank name:</MUITableCell>
                  <MUITableCell>{paymentDetails.bankName}</MUITableCell>
                </TableRow>
                <TableRow>
                  <MUITableCell>Country:</MUITableCell>
                  <MUITableCell>{paymentDetails.country}</MUITableCell>
                </TableRow>
                <TableRow>
                  <MUITableCell>IBAN:</MUITableCell>
                  <MUITableCell>{paymentDetails.iban}</MUITableCell>
                </TableRow>
                <TableRow>
                  <MUITableCell>SWIFT code:</MUITableCell>
                  <MUITableCell>{paymentDetails.swiftCode}</MUITableCell>
                </TableRow>
              </TableBody>
            </Table>
          </Grid>
        </Grid>

        <Divider sx={{ mt: theme => `${theme.spacing(6)} !important`, mb: '0 !important' }} />

        <Table sx={{ mb: 6 }}>
          <TableHead>
            <TableRow>
              <TableCell>Item</TableCell>
              <TableCell>Description</TableCell>
              <TableCell>hours</TableCell>
              <TableCell>qty</TableCell>
              <TableCell>Total</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            <TableRow>
              <TableCell>Premium Branding Package</TableCell>
              <TableCell>Branding & Promotion</TableCell>
              <TableCell>48</TableCell>
              <TableCell>1</TableCell>
              <TableCell>$32</TableCell>
            </TableRow>
            <TableRow>
              <TableCell>Social Media</TableCell>
              <TableCell>Social media templates</TableCell>
              <TableCell>42</TableCell>
              <TableCell>1</TableCell>
              <TableCell>$28</TableCell>
            </TableRow>
            <TableRow>
              <TableCell>Web Design</TableCell>
              <TableCell>Web designing package</TableCell>
              <TableCell>46</TableCell>
              <TableCell>1</TableCell>
              <TableCell>$24</TableCell>
            </TableRow>
            <TableRow>
              <TableCell>SEO</TableCell>
              <TableCell>Search engine optimization</TableCell>
              <TableCell>40</TableCell>
              <TableCell>1</TableCell>
              <TableCell>$22</TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <Grid container>
          <Grid item xs={8} sm={7} lg={9}>
            <Box sx={{ mb: 2, display: 'flex', alignItems: 'center' }}>
              <Typography variant='body2' sx={{ mr: 2, fontWeight: 600 }}>
                Salesperson:
              </Typography>
              <Typography variant='body2'>Tommy Shelby</Typography>
            </Box>

            <Typography variant='body2'>Thanks for your business</Typography>
          </Grid>
          <Grid item xs={4} sm={5} lg={3}>
            <CalcWrapper>
              <Typography variant='body2'>Subtotal:</Typography>
              <Typography variant='body2' sx={{ fontWeight: 600 }}>
                $1800
              </Typography>
            </CalcWrapper>
            <CalcWrapper>
              <Typography variant='body2'>Discount:</Typography>
              <Typography variant='body2' sx={{ fontWeight: 600 }}>
                $28
              </Typography>
            </CalcWrapper>
            <CalcWrapper>
              <Typography variant='body2'>Tax:</Typography>
              <Typography variant='body2' sx={{ fontWeight: 600 }}>
                21%
              </Typography>
            </CalcWrapper>
            <Divider />
            <CalcWrapper>
              <Typography variant='body2'>Total:</Typography>
              <Typography variant='body2' sx={{ fontWeight: 600 }}>
                $1690
              </Typography>
            </CalcWrapper>
          </Grid>
        </Grid>

        <Divider sx={{ my: `${theme.spacing(6)} !important` }} />
        <Typography variant='body2'>
          <strong>Note:</strong> It was a pleasure working with you and your team. We hope you will keep us in mind for
          future freelance projects. Thank You!
        </Typography>
      </Box>
    )
  } else if (error) {
    return (
      <Box sx={{ p: 5 }}>
        <Grid container spacing={6}>
          <Grid item xs={12}>
            <Alert severity='error'>
              Invoice with the id: {id} does not exist. Please check the list of invoices:{' '}
              <Link href='/apps/invoice/list'>Invoice List</Link>
            </Alert>
          </Grid>
        </Grid>
      </Box>
    )
  } else {
    return null
  }
}

export default InvoicePrint
