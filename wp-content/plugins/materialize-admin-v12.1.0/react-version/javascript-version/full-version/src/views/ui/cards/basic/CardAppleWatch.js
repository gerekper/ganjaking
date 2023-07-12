// ** MUI Imports
import Card from '@mui/material/Card'
import Button from '@mui/material/Button'
import CardMedia from '@mui/material/CardMedia'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

const CardAppleWatch = () => {
  return (
    <Card>
      <CardMedia sx={{ height: 140 }} image='/images/cards/apple-watch.png' />
      <CardContent sx={{ p: theme => `${theme.spacing(4, 5)} !important` }}>
        <Typography variant='h6' sx={{ mb: 2 }}>
          Apple Watch
        </Typography>
        <Typography sx={{ mb: 2 }}>$249.40</Typography>
        <Typography variant='body2'>
          3.1GHz 6-core 10th-generation Intel Core i5 processor, Turbo Boost up to 4.5GHz
        </Typography>
      </CardContent>
      <Button size='large' variant='contained' sx={{ width: '100%', borderTopLeftRadius: 0, borderTopRightRadius: 0 }}>
        Add To Cart
      </Button>
    </Card>
  )
}

export default CardAppleWatch
