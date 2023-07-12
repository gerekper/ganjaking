// ** React Imports
import { useState } from 'react'

// ** MUI Import
import Box from '@mui/material/Box'
import Tab from '@mui/material/Tab'
import Card from '@mui/material/Card'
import TabList from '@mui/lab/TabList'
import Table from '@mui/material/Table'
import TabPanel from '@mui/lab/TabPanel'
import Avatar from '@mui/material/Avatar'
import TabContext from '@mui/lab/TabContext'
import TableRow from '@mui/material/TableRow'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import TableHead from '@mui/material/TableHead'
import CardHeader from '@mui/material/CardHeader'
import Typography from '@mui/material/Typography'
import TableContainer from '@mui/material/TableContainer'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components
import CustomChip from 'src/@core/components/mui/chip'
import OptionsMenu from 'src/@core/components/option-menu'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const statusObj = {
  'in-stock': { text: 'In Stock', color: 'success' },
  'coming-soon': { text: 'Coming Soon', color: 'warning' },
  'out-of-stock': { text: 'Out of Stock', color: 'primary' }
}

const tabAvatars = [
  {
    imgWidth: 30,
    imgHeight: 58,
    category: 'mobile'
  },
  {
    imgWidth: 52,
    imgHeight: 42,
    category: 'desktop'
  },
  {
    imgWidth: 60,
    imgHeight: 42,
    category: 'console'
  }
]

const tabContentData = {
  mobile: [
    {
      revenue: '$12.5k',
      conversion: '+24',
      imgAlt: 'samsung-s22',
      status: 'out-of-stock',
      product: 'Samsung s22',
      imgSrc: '/images/cards/samsung-s22.png'
    },
    {
      revenue: '$45k',
      conversion: '-18',
      status: 'in-stock',
      imgAlt: 'apple-iPhone-13-pro',
      product: 'Apple iPhone 13 Pro',
      conversionDifference: 'negative',
      imgSrc: '/images/cards/apple-iPhone-13-pro.png'
    },
    {
      revenue: '$98.2k',
      conversion: '+55',
      status: 'coming-soon',
      imgAlt: 'oneplus-9-pro',
      product: 'Oneplus 9 Pro',
      imgSrc: '/images/cards/oneplus-9-pro.png'
    }
  ],
  desktop: [
    {
      revenue: '$94.6k',
      conversion: '+16',
      status: 'in-stock',
      imgAlt: 'apple-mac-mini',
      product: 'Apple Mac Mini',
      imgSrc: '/images/cards/apple-mac-mini.png'
    },
    {
      revenue: '$76.5k',
      conversion: '+27',
      status: 'coming-soon',
      imgAlt: 'hp-envy-x360',
      product: 'Newest HP Envy x360',
      imgSrc: '/images/cards/hp-envy-x360.png'
    },
    {
      revenue: '$69.3k',
      conversion: '-9',
      status: 'out-of-stock',
      imgAlt: 'dell-inspiron-3000',
      product: 'Dell Inspiron 3000',
      conversionDifference: 'negative',
      imgSrc: '/images/cards/dell-inspiron-3000.png'
    }
  ],
  console: [
    {
      revenue: '$18.6k',
      conversion: '+34',
      status: 'coming-soon',
      imgAlt: 'sony-play-station-5',
      product: 'Sony Play Station 5',
      imgSrc: '/images/cards/sony-play-station-5.png'
    },
    {
      revenue: '$29.7k',
      conversion: '-21',
      status: 'out-of-stock',
      imgAlt: 'xbox-series-x',
      product: 'XBOX Series X',
      conversionDifference: 'negative',
      imgSrc: '/images/cards/xbox-series-x.png'
    },
    {
      revenue: '$10.4k',
      conversion: '+38',
      status: 'in-stock',
      imgAlt: 'nintendo-switch',
      product: 'Nintendo Switch',
      imgSrc: '/images/cards/nintendo-switch.png'
    }
  ]
}

const RenderTabContent = ({ data }) => {
  return (
    <TableContainer>
      <Table>
        <TableHead>
          <TableRow sx={{ '& .MuiTableCell-root': { py: theme => `${theme.spacing(2.5)} !important` } }}>
            <TableCell>Image</TableCell>
            <TableCell sx={{ whiteSpace: 'nowrap' }}>Product Name</TableCell>
            <TableCell align='right'>Status</TableCell>
            <TableCell align='right'>Revenue</TableCell>
            <TableCell align='right'>Conversion</TableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          {data.map((row, index) => (
            <TableRow
              key={index}
              sx={{
                '& .MuiTableCell-root': {
                  border: 0,
                  py: theme => `${theme.spacing(1.5)} !important`
                },
                '&:first-child .MuiTableCell-body': {
                  pt: theme => `${theme.spacing(3)} !important`
                },
                '&:last-child .MuiTableCell-body': {
                  pb: theme => `${theme.spacing(3)} !important`
                }
              }}
            >
              <TableCell>
                <Avatar alt={row.imgAlt} src={row.imgSrc} variant='rounded' sx={{ width: 34, height: 34 }} />
              </TableCell>
              <TableCell>
                <Typography variant='body2' sx={{ fontWeight: 600, whiteSpace: 'nowrap', color: 'text.primary' }}>
                  {row.product}
                </Typography>
              </TableCell>
              <TableCell align='right'>
                <CustomChip
                  skin='light'
                  size='small'
                  label={statusObj[row.status].text}
                  color={statusObj[row.status].color}
                  sx={{ height: 20, fontWeight: 500, '& .MuiChip-label': { px: 1.625, lineHeight: 1.539 } }}
                />
              </TableCell>
              <TableCell>
                <Typography
                  variant='body2'
                  sx={{ fontWeight: 600, textAlign: 'right', whiteSpace: 'nowrap', color: 'text.primary' }}
                >
                  {row.revenue}
                </Typography>
              </TableCell>
              <TableCell>
                <Typography
                  variant='body2'
                  sx={{
                    fontWeight: 600,
                    textAlign: 'right',
                    color: row.conversionDifference === 'negative' ? 'error.main' : 'success.main'
                  }}
                >{`${row.conversion}%`}</Typography>
              </TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </TableContainer>
  )
}

const EcommerceSalesOverviewWithTabs = () => {
  // ** State
  const [value, setValue] = useState('mobile')

  const handleChange = (event, newValue) => {
    setValue(newValue)
  }

  const RenderTabAvatar = ({ data }) => (
    <Avatar
      variant='rounded'
      alt={`tabs-${data.category}`}
      src={`/images/cards/tabs-${data.category}.png`}
      sx={{
        width: 100,
        height: 92,
        backgroundColor: 'transparent',
        '& img': { width: data.imgWidth, height: data.imgHeight },
        border: theme =>
          value === data.category ? `2px solid ${theme.palette.primary.main}` : `2px dashed ${theme.palette.divider}`
      }}
    />
  )

  return (
    <Card>
      <CardHeader
        title='Top Referral Sources'
        subheader='82% Activity Growth'
        action={
          <OptionsMenu
            options={['Last 28 Days', 'Last Month', 'Last Year']}
            iconButtonProps={{ size: 'small', className: 'card-more-options' }}
          />
        }
      />
      <TabContext value={value}>
        <TabList
          variant='scrollable'
          scrollButtons='auto'
          onChange={handleChange}
          aria-label='top referral sources tabs'
          sx={{
            mb: 2.5,
            px: 5,
            '& .MuiTab-root:not(:last-child)': { mr: 4 },
            '& .MuiTabs-indicator': { display: 'none' }
          }}
        >
          <Tab value='mobile' sx={{ p: 0 }} label={<RenderTabAvatar data={tabAvatars[0]} />} />
          <Tab value='desktop' sx={{ p: 0 }} label={<RenderTabAvatar data={tabAvatars[1]} />} />
          <Tab value='console' sx={{ p: 0 }} label={<RenderTabAvatar data={tabAvatars[2]} />} />
          <Tab
            disabled
            value='add'
            sx={{ p: 0 }}
            label={
              <Avatar
                variant='rounded'
                sx={{
                  width: 100,
                  height: 92,
                  backgroundColor: 'transparent',
                  border: theme =>
                    value === 'add' ? `2px solid ${theme.palette.primary.main}` : `2px dashed ${theme.palette.divider}`
                }}
              >
                <Box
                  sx={{
                    width: 30,
                    height: 30,
                    display: 'flex',
                    borderRadius: '8px',
                    alignItems: 'center',
                    color: 'action.active',
                    justifyContent: 'center',
                    backgroundColor: theme => hexToRGBA(theme.palette.secondary.main, 0.12)
                  }}
                >
                  <Icon icon='mdi:plus' />
                </Box>
              </Avatar>
            }
          />
        </TabList>

        <TabPanel sx={{ p: 0 }} value='mobile'>
          <RenderTabContent data={tabContentData['mobile']} />
        </TabPanel>
        <TabPanel sx={{ p: 0 }} value='desktop'>
          <RenderTabContent data={tabContentData['desktop']} />
        </TabPanel>
        <TabPanel sx={{ p: 0 }} value='console'>
          <RenderTabContent data={tabContentData['console']} />
        </TabPanel>
      </TabContext>
    </Card>
  )
}

export default EcommerceSalesOverviewWithTabs
