// ** MUI Imports
import Pagination from '@mui/material/Pagination'

const PaginationSizes = () => {
  return (
    <div className='demo-space-y'>
      <Pagination count={10} size='small' />
      <Pagination count={10} color='primary' />
      <Pagination count={10} size='large' color='secondary' />
    </div>
  )
}

export default PaginationSizes
