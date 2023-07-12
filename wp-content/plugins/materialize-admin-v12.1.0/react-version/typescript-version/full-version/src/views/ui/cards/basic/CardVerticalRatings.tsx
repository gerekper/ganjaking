// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Rating from '@mui/material/Rating'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import CardActions from '@mui/material/CardActions'

const CardVerticalRatings = () => {
  return (
    <Card>
      <CardContent>
        <Typography variant='h6' sx={{ mb: 2 }}>
          The Best Answers
        </Typography>
        <Box sx={{ py: 1, mb: 2, display: 'flex', flexWrap: 'wrap', alignItems: 'center' }}>
          <Rating readOnly value={4} name='read-only' sx={{ mr: 2 }} />
          <Typography variant='body2'>4 Star | 98 reviews</Typography>
        </Box>
        <Typography variant='body2' sx={{ mb: 2 }}>
          If you are looking for a new way to promote your business that won’t cost you more money, maybe printing is
          one of the options you won’t resist.
        </Typography>
        <Typography variant='body2'>
          Printing is a widely use process in making printed materials that are used for advertising. It become fast,
          easy and simple.
        </Typography>
      </CardContent>
      <CardActions className='card-action-dense'>
        <Button>Location</Button>
        <Button>Reviews</Button>
      </CardActions>
    </Card>
  )
}

export default CardVerticalRatings
