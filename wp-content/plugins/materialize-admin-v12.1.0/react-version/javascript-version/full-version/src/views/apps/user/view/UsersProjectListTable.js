// ** React Imports
import { useState, useEffect } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import { styled } from '@mui/material/styles'
import TextField from '@mui/material/TextField'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import { DataGrid } from '@mui/x-data-grid'
import LinearProgress from '@mui/material/LinearProgress'

// ** Third Party Imports
import axios from 'axios'

const Img = styled('img')(({ theme }) => ({
  width: 32,
  height: 32,
  borderRadius: '50%',
  marginRight: theme.spacing(3)
}))

const columns = [
  {
    flex: 0.3,
    minWidth: 230,
    field: 'projectTitle',
    headerName: 'Project',
    renderCell: ({ row }) => (
      <Box sx={{ display: 'flex', alignItems: 'center' }}>
        <Img src={row.img} alt={`project-${row.projectTitle}`} />
        <Box sx={{ display: 'flex', flexDirection: 'column' }}>
          <Typography sx={{ fontWeight: 500, fontSize: '0.875rem' }}>{row.projectTitle}</Typography>
          <Typography variant='caption' sx={{ color: 'text.disabled' }}>
            {row.projectType}
          </Typography>
        </Box>
      </Box>
    )
  },
  {
    flex: 0.15,
    minWidth: 100,
    field: 'totalTask',
    headerName: 'Total Tasks',
    renderCell: ({ row }) => <Typography variant='body2'>{row.totalTask}</Typography>
  },
  {
    flex: 0.15,
    minWidth: 200,
    headerName: 'Progress',
    field: 'progressValue',
    renderCell: ({ row }) => (
      <Box sx={{ width: '100%' }}>
        <Typography variant='body2'>{row.progressValue}%</Typography>
        <LinearProgress
          variant='determinate'
          value={row.progressValue}
          color={row.progressColor}
          sx={{ height: 6, mt: 1, borderRadius: '5px' }}
        />
      </Box>
    )
  },
  {
    flex: 0.15,
    minWidth: 100,
    field: 'hours',
    headerName: 'Hours',
    renderCell: ({ row }) => <Typography variant='body2'>{row.hours}</Typography>
  }
]

const InvoiceListTable = () => {
  // ** State
  const [value, setValue] = useState('')
  const [data, setData] = useState([])
  const [paginationModel, setPaginationModel] = useState({ page: 0, pageSize: 7 })
  useEffect(() => {
    axios
      .get('/apps/users/project-list', {
        params: {
          q: value
        }
      })
      .then(res => setData(res.data))
  }, [value])

  return (
    <Card>
      <CardHeader title="User's Projects List" />
      <CardContent>
        <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'flex-end' }}>
          <Typography variant='body2' sx={{ mr: 2 }}>
            Search:
          </Typography>
          <TextField size='small' placeholder='Search Project' value={value} onChange={e => setValue(e.target.value)} />
        </Box>
      </CardContent>
      <DataGrid
        autoHeight
        rows={data}
        columns={columns}
        disableRowSelectionOnClick
        pageSizeOptions={[7, 10, 25, 50]}
        paginationModel={paginationModel}
        onPaginationModelChange={setPaginationModel}
      />
    </Card>
  )
}

export default InvoiceListTable
