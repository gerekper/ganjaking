// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import { DataGrid } from '@mui/x-data-grid'

// ** Data Import
import { rows } from 'src/@fake-db/table/static-data'

const columns = [
  {
    flex: 0.1,
    field: 'id',
    minWidth: 80,
    headerName: 'ID'
  },
  {
    flex: 0.25,
    minWidth: 200,
    field: 'full_name',
    headerName: 'Name'
  },
  {
    flex: 0.25,
    minWidth: 230,
    field: 'email',
    headerName: 'Email'
  },
  {
    flex: 0.15,
    type: 'date',
    minWidth: 130,
    headerName: 'Date',
    field: 'start_date',
    valueGetter: params => new Date(params.value)
  },
  {
    flex: 0.15,
    minWidth: 120,
    field: 'experience',
    headerName: 'Experience'
  },
  {
    flex: 0.1,
    field: 'age',
    minWidth: 80,
    headerName: 'Age'
  }
]

const TableBasic = () => {
  return (
    <Card>
      <CardHeader title='Basic' />
      <Box sx={{ height: 500 }}>
        <DataGrid columns={columns} rows={rows.slice(0, 10)} />
      </Box>
    </Card>
  )
}

export default TableBasic
