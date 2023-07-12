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
import TableContainer from '@mui/material/TableContainer'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

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
  },
  {
    imgWidth: 44,
    imgHeight: 50,
    category: 'watch'
  }
]

const tabContentData = {
  mobile: [
    {
      quantity: 2,
      price: '$849',
      total: '$1,698',
      imgAlt: 'samsung-s22',
      product: 'Samsung s22',
      imgSrc: '/images/cards/samsung-s22.png'
    },
    {
      quantity: 1,
      price: '$599',
      total: '$599',
      imgAlt: 'apple-iPhone-13-pro',
      product: 'Apple iPhone 13 Pro',
      imgSrc: '/images/cards/apple-iPhone-13-pro.png'
    },
    {
      quantity: 4,
      price: '$399',
      total: '$1,596',
      imgAlt: 'oneplus-9-pro',
      product: 'Oneplus 9 Pro',
      imgSrc: '/images/cards/oneplus-9-pro.png'
    },
    {
      quantity: 3,
      price: '$450',
      total: '$1,350',
      imgAlt: 'google-pixel-6',
      product: 'Google Pixel 6',
      imgSrc: '/images/cards/google-pixel-6.png'
    }
  ],
  desktop: [
    {
      quantity: 2,
      price: '$849',
      total: '$1,698',
      imgAlt: 'apple-mac-mini',
      product: 'Apple Mac Mini',
      imgSrc: '/images/cards/apple-mac-mini.png'
    },
    {
      quantity: 4,
      price: '$599',
      total: '$2,396',
      imgAlt: 'hp-envy-x360',
      product: 'Newest HP Envy x360',
      imgSrc: '/images/cards/hp-envy-x360.png'
    },
    {
      quantity: 1,
      price: '$399',
      total: '$399',
      imgAlt: 'dell-inspiron-3000',
      product: 'Dell Inspiron 3000',
      imgSrc: '/images/cards/dell-inspiron-3000.png'
    },
    {
      quantity: 3,
      price: '$450',
      total: '$1,350',
      imgAlt: 'apple-iMac-4k',
      product: 'Apple iMac 4k',
      imgSrc: '/images/cards/apple-iMac-4k.png'
    }
  ],
  console: [
    {
      quantity: 1,
      price: '$599',
      total: '$599',
      imgAlt: 'sony-play-station-5',
      product: 'Sony Play Station 5',
      imgSrc: '/images/cards/sony-play-station-5.png'
    },
    {
      quantity: 3,
      price: '$489',
      total: '$1,467',
      imgAlt: 'xbox-series-x',
      product: 'XBOX Series X',
      imgSrc: '/images/cards/xbox-series-x.png'
    },
    {
      quantity: 4,
      price: '$335',
      total: '$1,340',
      imgAlt: 'nintendo-switch',
      product: 'Nintendo Switch',
      imgSrc: '/images/cards/nintendo-switch.png'
    },
    {
      quantity: 8,
      price: '$14',
      total: '$112',
      imgAlt: 'sup-game-box-400',
      product: 'SUP Game Box 400',
      imgSrc: '/images/cards/sup-game-box-400.png'
    }
  ],
  watch: [
    {
      quantity: 2,
      price: '$202',
      total: '$404',
      imgAlt: 'samsung-watch-4',
      product: 'Samsung Watch 4',
      imgSrc: '/images/cards/samsung-watch-4.png'
    },
    {
      quantity: 1,
      price: '$399',
      total: '$399',
      imgAlt: 'apple-watch-7',
      product: 'Apple Watch 7',
      imgSrc: '/images/cards/apple-watch-7.png'
    },
    {
      quantity: 3,
      price: '$59',
      total: '$177',
      imgAlt: 'amazon-echo-dot',
      product: 'Amazon Echo Dot',
      imgSrc: '/images/cards/amazon-echo-dot.png'
    },
    {
      quantity: 1,
      price: '$139',
      total: '$139',
      imgAlt: 'gramin-verve',
      product: 'Gramin Verve',
      imgSrc: '/images/cards/gramin-verve.png'
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
            <TableCell align='right'>Qty</TableCell>
            <TableCell align='right'>Price</TableCell>
            <TableCell align='right'>Total</TableCell>
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
              <TableCell sx={{ whiteSpace: 'nowrap' }}>{row.product}</TableCell>
              <TableCell align='right'>{row.quantity}</TableCell>
              <TableCell align='right'>{row.price}</TableCell>
              <TableCell align='right'>{row.total}</TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </TableContainer>
  )
}

const CardTopReferralSources = () => {
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
          <Tab value='watch' sx={{ p: 0 }} label={<RenderTabAvatar data={tabAvatars[3]} />} />
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
        <TabPanel sx={{ p: 0 }} value='watch'>
          <RenderTabContent data={tabContentData['watch']} />
        </TabPanel>
      </TabContext>
    </Card>
  )
}

export default CardTopReferralSources
