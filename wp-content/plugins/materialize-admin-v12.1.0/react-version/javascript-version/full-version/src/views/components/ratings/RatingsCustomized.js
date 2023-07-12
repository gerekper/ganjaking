// ** MUI Imports
import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'
import Rating from '@mui/material/Rating'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const customIcons = {
  1: {
    label: 'Very Dissatisfied',
    icon: 'mdi:emoticon-sad-outline'
  },
  2: {
    label: 'Neutral',
    icon: 'mdi:emoticon-neutral-outline'
  },
  3: {
    label: 'Satisfied',
    icon: 'mdi:emoticon-happy-outline'
  },
  4: {
    label: 'Very Satisfied',
    icon: 'mdi:emoticon-outline'
  }
}

const IconContainer = props => {
  const { value } = props

  return (
    <span {...props}>
      <Icon icon={customIcons[value].icon} />
    </span>
  )
}

const RatingsCustomized = () => {
  return (
    <div>
      <Box sx={{ mb: 3 }}>
        <Typography sx={{ fontWeight: 500 }}>Custom empty icon</Typography>
        <Rating name='customized-empty' defaultValue={2} precision={0.5} emptyIcon={<Icon icon='mdi:star' />} />
      </Box>
      <Box sx={{ mb: 3 }}>
        <Typography sx={{ fontWeight: 500 }}>Custom icon and color</Typography>
        <Rating
          precision={0.5}
          defaultValue={3}
          name='customized-color'
          sx={{ color: 'error.main' }}
          icon={<Icon icon='mdi:heart' />}
          emptyIcon={<Icon icon='mdi:heart' />}
        />
      </Box>
      <Box sx={{ mb: 3 }}>
        <Typography sx={{ fontWeight: 500 }}>10 stars</Typography>
        <Rating name='customized-10' defaultValue={7} max={10} />
      </Box>
      <div>
        <Typography sx={{ fontWeight: 500 }}>Custom icon set</Typography>
        <Rating name='customized-icons' defaultValue={2} max={4} IconContainerComponent={IconContainer} />
      </div>
    </div>
  )
}

export default RatingsCustomized
