export const ChipsAvatarJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import Chip from '@mui/material/Chip'
import Avatar from '@mui/material/Avatar'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ChipsAvatar = () => {
  return (
    <div className='demo-space-x'>
      <Chip label='Default' avatar={<Avatar />} />
      <Chip label='Howard Paul' avatar={<Avatar src='/images/avatars/7.png' alt='User Avatar' />} />
      <Chip label='Maurice Bell' avatar={<Avatar>M</Avatar>} />
      <Chip
        label='Archived'
        avatar={
          <Avatar>
            <Icon icon='mdi:archive-outline' fontSize={20} />
          </Avatar>
        }
      />
    </div>
  )
}

export default ChipsAvatar
`}</code></pre>) 

export const ChipsArrayJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Chip from '@mui/material/Chip'
import Avatar from '@mui/material/Avatar'

const ChipsArray = () => {
  // ** State
  const [chipData, setChipData] = useState([
    { key: 0, avatar: '/images/avatars/1.png', avatarAlt: 'User Avatar', label: 'Norman Santiago' },
    { key: 1, avatar: '/images/avatars/2.png', avatarAlt: 'User Avatar', label: 'Cecelia Tucker' },
    { key: 2, label: 'Max Burns' },
    { key: 3, avatar: '/images/avatars/4.png', avatarAlt: 'User Avatar', label: 'Ellen Nguyen' },
    { key: 4, avatar: '/images/avatars/5.png', avatarAlt: 'User Avatar', label: 'Edward Francis' }
  ])

  const handleDelete = chipToDelete => () => {
    setChipData(chips => chips.filter(chip => chip.key !== chipToDelete.key))
  }

  return (
    <div className='demo-space-x'>
      {chipData.map(data => (
        <Chip
          key={data.key}
          label={data.label}
          avatar={<Avatar src={data.avatar} alt={data.avatarAlt} />}
          onDelete={data.key === 2 ? undefined : handleDelete(data)}
        />
      ))}
    </div>
  )
}

export default ChipsArray
`}</code></pre>) 

export const ChipsClickableJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import Chip from '@mui/material/Chip'

const ChipsClickable = () => {
  const handleClick = () => {
    console.info('You clicked the Chip.')
  }

  return (
    <div className='demo-space-x'>
      <Chip label='Clickable' onClick={handleClick} />
      <Chip label='Clickable Link' component='a' href='https://pixinvent.com/' target='_blank' clickable />
    </div>
  )
}

export default ChipsClickable
`}</code></pre>) 

export const ChipsDisabledJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import Chip from '@mui/material/Chip'

const ChipsDisabled = () => {
  return (
    <div className='demo-space-x'>
      <Chip label='Basic' disabled />
      <Chip label='Outlined' variant='outlined' disabled />
    </div>
  )
}

export default ChipsDisabled
`}</code></pre>) 

export const ChipsLightJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** Custom Components Imports
import CustomChip from 'src/@core/components/mui/chip'

const ChipsCustomized = () => {
  return (
    <div className='demo-space-x'>
      <CustomChip label='Primary' skin='light' color='primary' />
      <CustomChip label='Secondary' skin='light' color='secondary' />
      <CustomChip label='Success' skin='light' color='success' />
      <CustomChip label='Error' skin='light' color='error' />
      <CustomChip label='Warning' skin='light' color='warning' />
      <CustomChip label='Info' skin='light' color='info' />
    </div>
  )
}

export default ChipsCustomized
`}</code></pre>) 

export const ChipsColorsJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { Fragment } from 'react'

// ** MUI Imports
import Chip from '@mui/material/Chip'
import Typography from '@mui/material/Typography'

const ChipsColors = () => {
  return (
    <Fragment>
      <Typography sx={{ fontWeight: 500 }}>Filled Chips</Typography>
      <div className='demo-space-x'>
        <Chip label='Primary' color='primary' />
        <Chip label='Secondary' color='secondary' />
        <Chip label='Success' color='success' />
        <Chip label='Error' color='error' />
        <Chip label='Warning' color='warning' />
        <Chip label='Info' color='info' />
      </div>
      <Typography sx={{ mt: 4, fontWeight: 500 }}>Outlined Chips</Typography>
      <div className='demo-space-x'>
        <Chip label='Primary' color='primary' variant='outlined' />
        <Chip label='Secondary' color='secondary' variant='outlined' />
        <Chip label='Success' color='success' variant='outlined' />
        <Chip label='Error' color='error' variant='outlined' />
        <Chip label='Warning' color='warning' variant='outlined' />
        <Chip label='Info' color='info' variant='outlined' />
      </div>
    </Fragment>
  )
}

export default ChipsColors
`}</code></pre>) 

export const ChipsIconJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import Chip from '@mui/material/Chip'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ChipsIcon = () => {
  return (
    <div className='demo-space-x'>
      <Chip label='Previous' icon={<Icon icon='mdi:arrow-left-thin-circle-outline' fontSize={20} />} />
      <Chip
        label='Next'
        color='primary'
        variant='outlined'
        icon={<Icon icon='mdi:arrow-right-thin-circle-outline' fontSize={20} />}
      />
    </div>
  )
}

export default ChipsIcon
`}</code></pre>) 

export const ChipsSizesJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import Chip from '@mui/material/Chip'

const ChipsSizes = () => {
  return (
    <div className='demo-space-x'>
      <Chip label='Default' />
      <Chip label='Small' size='small' />
    </div>
  )
}

export default ChipsSizes
`}</code></pre>) 

export const ChipsVariantsJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import Chip from '@mui/material/Chip'

const ChipsVariants = () => {
  return (
    <div className='demo-space-x'>
      <Chip label='Basic' />
      <Chip label='Outlined' variant='outlined' />
    </div>
  )
}

export default ChipsVariants
`}</code></pre>) 

export const ChipsRoundedJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** Custom Components Imports
import CustomChip from 'src/@core/components/mui/chip'

const ChipsRounded = () => {
  return (
    <div className='demo-space-x'>
      <CustomChip rounded label='Primary' skin='light' color='primary' />
      <CustomChip rounded label='Secondary' skin='light' color='secondary' />
      <CustomChip rounded label='Success' skin='light' color='success' />
      <CustomChip rounded label='Error' skin='light' color='error' />
      <CustomChip rounded label='Warning' skin='light' color='warning' />
      <CustomChip rounded label='Info' skin='light' color='info' />
    </div>
  )
}

export default ChipsRounded
`}</code></pre>) 

export const ChipsOnDeleteJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { Fragment } from 'react'

// ** MUI Imports
import Chip from '@mui/material/Chip'
import Typography from '@mui/material/Typography'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const ChipsOnDelete = () => {
  const handleDelete = () => {
    console.info('You clicked the delete icon.')
  }

  return (
    <Fragment>
      <Typography sx={{ fontWeight: 500 }}>Default</Typography>
      <div className='demo-space-x'>
        <Chip label='Basic' variant='outlined' onDelete={handleDelete} />
        <Chip label='Primary' color='primary' variant='outlined' onDelete={handleDelete} />
        <Chip label='Secondary' color='secondary' variant='outlined' onDelete={handleDelete} />
      </div>
      <Typography sx={{ mt: 4, fontWeight: 500 }}>Custom</Typography>
      <div className='demo-space-x'>
        <Chip label='Basic' onDelete={handleDelete} deleteIcon={<Icon icon='mdi:delete-outline' />} />
        <Chip label='Primary' color='primary' onDelete={handleDelete} deleteIcon={<Icon icon='mdi:delete-outline' />} />
        <Chip
          label='Secondary'
          color='secondary'
          onDelete={handleDelete}
          deleteIcon={<Icon icon='mdi:delete-outline' />}
        />
      </div>
    </Fragment>
  )
}

export default ChipsOnDelete
`}</code></pre>) 

