// ** MUI Imports
import Avatar from '@mui/material/Avatar'
import AvatarGroup from '@mui/material/AvatarGroup'

const AvatarsGroupedPullUp = () => {
  return (
    <AvatarGroup className='pull-up' max={4}>
      <Avatar src='/images/avatars/4.png' alt='Olivia Sparks' />
      <Avatar src='/images/avatars/5.png' alt='Howard Lloyd' />
      <Avatar src='/images/avatars/6.png' alt='Hallie Richards' />
      <Avatar src='/images/avatars/8.png' alt='Alice Cobb' />
      <Avatar src='/images/avatars/7.png' alt='Jeffery Warner' />
    </AvatarGroup>
  )
}

export default AvatarsGroupedPullUp
