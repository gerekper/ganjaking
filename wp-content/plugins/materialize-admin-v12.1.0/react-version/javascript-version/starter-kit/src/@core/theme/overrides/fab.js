const Button = () => {
  return {
    MuiFab: {
      styleOverrides: {
        root: ({ theme }) => ({
          boxShadow: theme.shadows[5]
        })
      }
    }
  }
}

export default Button
