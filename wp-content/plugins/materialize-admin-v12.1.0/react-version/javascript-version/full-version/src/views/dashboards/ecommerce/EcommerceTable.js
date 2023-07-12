// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Typography from '@mui/material/Typography'
import { DataGrid } from '@mui/x-data-grid'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components
import CustomChip from 'src/@core/components/mui/chip'
import CustomAvatar from 'src/@core/components/mui/avatar'

// ** Utils Import
import { getInitials } from 'src/@core/utils/get-initials'

const roleObj = {
  admin: {
    icon: (
      <Box component='span' sx={{ mr: 2, display: 'flex', color: 'error.main' }}>
        <Icon icon='mdi:laptop' />
      </Box>
    )
  },
  author: {
    icon: (
      <Box component='span' sx={{ mr: 2, display: 'flex', color: 'warning.main' }}>
        <Icon icon='mdi:cog' />
      </Box>
    )
  },
  maintainer: {
    icon: (
      <Box component='span' sx={{ mr: 2, display: 'flex', color: 'success.main' }}>
        <Icon icon='mdi:chart-donut' />
      </Box>
    )
  },
  editor: {
    icon: (
      <Box component='span' sx={{ mr: 2, display: 'flex', color: 'info.main' }}>
        <Icon icon='mdi:pencil-outline' />
      </Box>
    )
  },
  subscriber: {
    icon: (
      <Box component='span' sx={{ mr: 2, display: 'flex', color: 'primary.main' }}>
        <Icon icon='mdi:account-outline' />
      </Box>
    )
  }
}

const statusObj = {
  active: { color: 'success' },
  pending: { color: 'warning' },
  inactive: { color: 'secondary' }
}

const rows = [
  {
    id: 1,
    role: 'admin',
    status: 'pending',
    name: 'Jordan Stevenson',
    username: '@jstevenson5c',
    email: 'susanna.Lind57@gmail.com',
    avatarSrc: '/images/avatars/1.png'
  },
  {
    id: 2,
    role: 'editor',
    status: 'active',
    name: 'Robert Crawford',
    username: '@rcrawford1d',
    avatarSrc: '/images/avatars/3.png',
    email: 'estelle.Bailey10@gmail.com'
  },
  {
    id: 3,
    role: 'author',
    status: 'inactive',
    name: 'Lydia Reese',
    username: '@lreese3b',
    email: 'milo86@hotmail.com',
    avatarSrc: '/images/avatars/2.png'
  },
  {
    id: 4,
    role: 'editor',
    status: 'pending',
    name: 'Richard Sims',
    username: '@rsims6f',
    email: 'lonnie35@hotmail.com',
    avatarSrc: '/images/avatars/5.png'
  },
  {
    id: 5,
    status: 'active',
    role: 'maintainer',
    name: 'Lucile Young',
    username: '@lyoung4a',
    email: 'ahmad_Collins@yahoo.com',
    avatarSrc: '/images/avatars/4.png'
  },
  {
    id: 6,
    role: 'editor',
    status: 'pending',
    name: 'Francis Frank',
    username: '@ffrank7e',
    avatarSrc: '/images/avatars/7.png',
    email: 'tillman.Gleason68@hotmail.com'
  },
  {
    id: 7,
    role: 'subscriber',
    status: 'inactive',
    name: 'Phoebe Patterson',
    email: 'otho21@gmail.com',
    username: '@ppatterson2g',
    avatarSrc: '/images/avatars/8.png'
  }
]

const renderUserAvatar = row => {
  if (row.avatarSrc) {
    return <CustomAvatar src={row.avatarSrc} sx={{ mr: 3, width: 34, height: 34 }} />
  } else {
    return (
      <CustomAvatar skin='light' sx={{ mr: 3, width: 34, height: 34, fontSize: '.8rem' }}>
        {getInitials(row.name ? row.name : 'John Doe')}
      </CustomAvatar>
    )
  }
}

const columns = [
  {
    flex: 0.25,
    field: 'name',
    minWidth: 200,
    headerName: 'User',
    renderCell: ({ row }) => {
      return (
        <Box sx={{ display: 'flex', alignItems: 'center' }}>
          {renderUserAvatar(row)}
          <Box sx={{ display: 'flex', flexDirection: 'column' }}>
            <Typography variant='subtitle2' sx={{ color: 'text.primary' }}>
              {row.name}
            </Typography>
            <Typography variant='caption' sx={{ lineHeight: 1.6667 }}>
              {row.username}
            </Typography>
          </Box>
        </Box>
      )
    }
  },
  {
    flex: 0.3,
    minWidth: 250,
    field: 'email',
    headerName: 'Email',
    renderCell: ({ row }) => <Typography variant='body2'>{row.email}</Typography>
  },
  {
    flex: 0.2,
    minWidth: 130,
    field: 'role',
    headerName: 'Role',
    renderCell: ({ row }) => (
      <Box sx={{ display: 'flex', alignItems: 'center' }}>
        {roleObj[row.role].icon}
        <Typography sx={{ color: 'text.secondary', textTransform: 'capitalize' }}>{row.role}</Typography>
      </Box>
    )
  },
  {
    flex: 0.15,
    minWidth: 110,
    field: 'status',
    headerName: 'Status',
    renderCell: ({ row }) => (
      <CustomChip
        skin='light'
        size='small'
        label={row.status}
        color={statusObj[row.status].color}
        sx={{ textTransform: 'capitalize', '& .MuiChip-label': { px: 2.5, lineHeight: 1.385 } }}
      />
    )
  }
]

const EcommerceTable = () => {
  return (
    <Card>
      <DataGrid autoHeight hideFooter rows={rows} columns={columns} disableRowSelectionOnClick pagination={undefined} />
    </Card>
  )
}

export default EcommerceTable
