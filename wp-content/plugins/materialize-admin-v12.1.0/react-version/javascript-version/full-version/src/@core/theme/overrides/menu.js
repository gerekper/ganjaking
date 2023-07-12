const Menu = skin => {
  const boxShadow = theme => {
    if (skin === 'bordered') {
      return theme.shadows[0]
    } else if (theme.palette.mode === 'light') {
      return theme.shadows[8]
    } else return theme.shadows[9]
  }

  return {
    MuiMenu: {
      styleOverrides: {
        root: ({ theme }) => ({
          '& .MuiMenu-paper': {
            borderRadius: 5,
            boxShadow: boxShadow(theme),
            ...(skin === 'bordered' && { border: `1px solid ${theme.palette.divider}` })
          }
        })
      }
    }
  }
}

export default Menu
