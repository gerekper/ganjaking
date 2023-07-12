// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Box from '@mui/material/Box'
import { Theme } from '@mui/material/styles'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import useMediaQuery from '@mui/material/useMediaQuery'

const LinkStyled = styled(Link)(({ theme }) => ({
  textDecoration: 'none',
  color: theme.palette.primary.main
}))

const FooterContent = () => {
  // ** Var
  const hidden = useMediaQuery((theme: Theme) => theme.breakpoints.down('md'))

  return (
    <Box sx={{ display: 'flex', flexWrap: 'wrap', alignItems: 'center', justifyContent: 'space-between' }}>
      <Typography sx={{ mr: 2 }}>
        {`© ${new Date().getFullYear()}, Made with `}
        <Box component='span' sx={{ color: 'error.main' }}>
          ❤️
        </Box>
        {` by `}
        <LinkStyled target='_blank' href='https://pixinvent.com/'>
          Pixinvent
        </LinkStyled>
      </Typography>
      {hidden ? null : (
        <Box sx={{ display: 'flex', flexWrap: 'wrap', alignItems: 'center', '& :not(:last-child)': { mr: 4 } }}>
          <LinkStyled target='_blank' href='https://themeforest.net/licenses/standard'>
            License
          </LinkStyled>
          <LinkStyled target='_blank' href='https://1.envato.market/pixinvent_portfolio'>
            More Themes
          </LinkStyled>
          <LinkStyled
            target='_blank'
            href='https://demos.pixinvent.com/materialize-nextjs-admin-template/documentation'
          >
            Documentation
          </LinkStyled>
          <LinkStyled target='_blank' href='https://pixinvent.ticksy.com/'>
            Support
          </LinkStyled>
        </Box>
      )}
    </Box>
  )
}

export default FooterContent
