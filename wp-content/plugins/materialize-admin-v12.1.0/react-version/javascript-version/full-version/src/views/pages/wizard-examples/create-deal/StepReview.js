// ** MUI Imports
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Table from '@mui/material/Table'
import Switch from '@mui/material/Switch'
import TableRow from '@mui/material/TableRow'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import Typography from '@mui/material/Typography'
import TableContainer from '@mui/material/TableContainer'
import FormControlLabel from '@mui/material/FormControlLabel'

// ** Custom Components Imports
import CustomChip from 'src/@core/components/mui/chip'

const ReviewComplete = () => {
  return (
    <Grid container spacing={6}>
      <Grid item xs={12} lg={6} xl={7}>
        <Grid container spacing={2}>
          <Grid item xs={12}>
            <Typography variant='h5' sx={{ mb: 4 }}>
              Almost done! 🚀
            </Typography>
            <Typography sx={{ color: 'text.secondary' }}>
              Confirm your deal details information and submit to create it.
            </Typography>
          </Grid>
          <Grid item xs={12}>
            <TableContainer>
              <Table>
                <TableBody
                  sx={{
                    '& .MuiTableCell-root': {
                      borderBottom: 0,
                      verticalAlign: 'top',
                      '&:last-of-type': { px: '0 !important' },
                      '&:first-of-type': { pl: '0 !important' },
                      py: theme => `${theme.spacing(1)} !important`
                    }
                  }}
                >
                  <TableRow>
                    <TableCell>
                      <Typography noWrap sx={{ fontWeight: 600, color: 'text.secondary' }}>
                        Deal Type
                      </Typography>
                    </TableCell>
                    <TableCell>
                      <Typography sx={{ color: 'text.secondary' }}>Percentage</Typography>
                    </TableCell>
                  </TableRow>
                  <TableRow>
                    <TableCell>
                      <Typography noWrap sx={{ fontWeight: 600, color: 'text.secondary' }}>
                        Amount
                      </Typography>
                    </TableCell>
                    <TableCell>
                      <Typography sx={{ color: 'text.secondary' }}>25%</Typography>
                    </TableCell>
                  </TableRow>
                  <TableRow>
                    <TableCell>
                      <Typography noWrap sx={{ fontWeight: 600, color: 'text.secondary' }}>
                        Deal Code
                      </Typography>
                    </TableCell>
                    <TableCell>
                      <CustomChip size='small' skin='light' color='warning' label='25PEROFF' />
                    </TableCell>
                  </TableRow>
                  <TableRow>
                    <TableCell>
                      <Typography noWrap sx={{ fontWeight: 600, color: 'text.secondary' }}>
                        Deal Title
                      </Typography>
                    </TableCell>
                    <TableCell>
                      <Typography sx={{ color: 'text.secondary' }}>Black friday sale, 25% OFF</Typography>
                    </TableCell>
                  </TableRow>
                  <TableRow>
                    <TableCell>
                      <Typography noWrap sx={{ fontWeight: 600, color: 'text.secondary' }}>
                        Deal Duration
                      </Typography>
                    </TableCell>
                    <TableCell>
                      <Typography sx={{ color: 'text.secondary' }}>2021-07-14 to 2021-07-30</Typography>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </TableContainer>
          </Grid>
          <Grid item xs={12}>
            <FormControlLabel control={<Switch />} label='I have confirmed the deal details.' />
          </Grid>
        </Grid>
      </Grid>
      <Grid
        item
        lg={6}
        xl={5}
        xs={12}
        sx={{ display: 'flex', alignItems: 'center', justifyContent: 'center', '& img': { maxWidth: '100%' } }}
      >
        <Box sx={{ pt: 4.5, px: 4.5, width: '100%', display: 'flex', justifyContent: 'center' }}>
          <img height={300} alt='review-illustration' src='/images/pages/create-deal-review-complete.png' />
        </Box>
      </Grid>
    </Grid>
  )
}

export default ReviewComplete
