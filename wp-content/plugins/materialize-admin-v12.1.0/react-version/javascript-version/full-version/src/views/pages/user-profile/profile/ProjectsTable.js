// ** React Imports
import { useState, useEffect } from 'react'

// ** MUI Components
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import CardHeader from '@mui/material/CardHeader'
import AvatarGroup from '@mui/material/AvatarGroup'
import { DataGrid } from '@mui/x-data-grid'
import LinearProgress from '@mui/material/LinearProgress'

// ** Third Party Imports
import axios from 'axios'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'
import CustomAvatar from 'src/@core/components/mui/avatar'

// ** Utils Import
import { getInitials } from 'src/@core/utils/get-initials'

// ** renders name column
const renderName = row => {
  if (row.avatar) {
    return <CustomAvatar src={row.avatar} sx={{ mr: 2, width: 35, height: 35 }} />
  } else {
    return (
      <CustomAvatar
        skin='light'
        sx={{ mr: 2, width: 35, height: 35, fontSize: '0.875rem' }}
        color={row.avatarColor || 'primary'}
      >
        {getInitials(row.name || 'John Doe')}
      </CustomAvatar>
    )
  }
}

const columns = [
  {
    flex: 0.1,
    field: 'name',
    minWidth: 220,
    headerName: 'Name',
    renderCell: ({ row }) => {
      const { name, date } = row

      return (
        <Box sx={{ display: 'flex', alignItems: 'center' }}>
          {renderName(row)}
          <Box sx={{ display: 'flex', flexDirection: 'column' }}>
            <Typography noWrap sx={{ color: 'text.secondary', fontWeight: 700 }}>
              {name}
            </Typography>
            <Typography noWrap variant='body2' sx={{ color: 'text.disabled', textTransform: 'capitalize' }}>
              {date}
            </Typography>
          </Box>
        </Box>
      )
    }
  },
  {
    flex: 0.1,
    minWidth: 105,
    field: 'leader',
    headerName: 'Leader',
    renderCell: ({ row }) => <Typography sx={{ color: 'text.secondary' }}>{row.leader}</Typography>
  },
  {
    flex: 0.1,
    field: 'team',
    minWidth: 120,
    headerName: 'Team',
    renderCell: ({ row }) => (
      <AvatarGroup className='pull-up'>
        {row.avatarGroup.map((src, index) => (
          <CustomAvatar key={index} src={src} sx={{ height: 26, width: 26 }} />
        ))}
      </AvatarGroup>
    )
  },
  {
    flex: 0.1,
    minWidth: 150,
    field: 'status',
    headerName: 'Status',
    renderCell: ({ row }) => (
      <>
        <LinearProgress
          color='primary'
          value={row.status}
          variant='determinate'
          sx={{
            mr: 4,
            height: 6,
            width: '100%',
            borderRadius: 8,
            backgroundColor: 'background.default',
            '& .MuiLinearProgress-bar': {
              borderRadius: 8
            }
          }}
        />
        <Typography sx={{ color: 'text.secondary' }}>{`${row.status}%`}</Typography>
      </>
    )
  },
  {
    flex: 0.1,
    minWidth: 100,
    field: 'actions',
    headerName: 'Actions',
    renderCell: () => (
      <OptionsMenu
        iconButtonProps={{ size: 'small' }}
        options={[
          'Details',
          'Archive',
          { divider: true },
          { text: 'Delete', menuItemProps: { sx: { color: 'error.main' } } }
        ]}
      />
    )
  }
]

const ProfileTable = () => {
  // ** State
  const [data, setData] = useState([])
  const [value, setValue] = useState('')
  const [paginationModel, setPaginationModel] = useState({ page: 0, pageSize: 7 })
  useEffect(() => {
    axios.get('/pages/profile-table', { params: { q: value } }).then(response => {
      setData(response.data)
    })
  }, [value])

  const handleFilter = val => {
    setValue(val)
  }

  return data ? (
    <Card>
      <CardHeader
        title='Projects'
        action={
          <Box sx={{ display: 'flex', alignItems: 'center' }}>
            <Typography variant='body2' sx={{ mr: 2 }}>
              Search:
            </Typography>
            <TextField size='small' value={value} onChange={e => handleFilter(e.target.value)} />
          </Box>
        }
      />
      <DataGrid
        autoHeight
        pagination
        rows={data}
        columns={columns}
        checkboxSelection
        disableRowSelectionOnClick
        pageSizeOptions={[5, 7, 10]}
        paginationModel={paginationModel}
        onPaginationModelChange={setPaginationModel}
      />
    </Card>
  ) : null
}

export default ProfileTable
