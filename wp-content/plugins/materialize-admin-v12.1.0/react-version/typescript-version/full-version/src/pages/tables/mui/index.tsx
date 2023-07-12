// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Grid from '@mui/material/Grid'
import Card from '@mui/material/Card'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'

// ** Custom Components Imports
import PageHeader from 'src/@core/components/page-header'

// ** Demo Components Imports
import TableBasic from 'src/views/table/mui/TableBasic'
import TableDense from 'src/views/table/mui/TableDense'
import TableSpanning from 'src/views/table/mui/TableSpanning'
import TableCustomized from 'src/views/table/mui/TableCustomized'
import TableSortSelect from 'src/views/table/mui/TableSortSelect'
import TableCollapsible from 'src/views/table/mui/TableCollapsible'
import TableStickyHeader from 'src/views/table/mui/TableStickyHeader'

const LinkStyled = styled(Link)(({ theme }) => ({
  textDecoration: 'none',
  color: theme.palette.primary.main
}))

const MUITable = () => {
  return (
    <Grid container spacing={6}>
      <PageHeader
        title={
          <Typography variant='h5'>
            <LinkStyled href='https://mui.com/material-ui/react-table/' target='_blank'>
              MUI Tables
            </LinkStyled>
          </Typography>
        }
        subtitle={<Typography variant='body2'>Tables display sets of data. They can be fully customized</Typography>}
      />
      <Grid item xs={12}>
        <Card>
          <CardHeader title='Basic Table' />
          <TableBasic />
        </Card>
      </Grid>
      <Grid item xs={12}>
        <Card>
          <CardHeader title='Dense Table' />
          <TableDense />
        </Card>
      </Grid>
      <Grid item xs={12}>
        <Card>
          <CardHeader title='Sticky Header' />
          <TableStickyHeader />
        </Card>
      </Grid>
      <Grid item xs={12}>
        <Card>
          <CardHeader title='Collapsible Table' />
          <TableCollapsible />
        </Card>
      </Grid>
      <Grid item xs={12}>
        <Card>
          <CardHeader title='Spanning Table' />
          <TableSpanning />
        </Card>
      </Grid>
      <Grid item xs={12}>
        <Card>
          <CardHeader title='Customized Table' />
          <TableCustomized />
        </Card>
      </Grid>
      <Grid item xs={12}>
        <Card>
          <TableSortSelect />
        </Card>
      </Grid>
    </Grid>
  )
}

export default MUITable
