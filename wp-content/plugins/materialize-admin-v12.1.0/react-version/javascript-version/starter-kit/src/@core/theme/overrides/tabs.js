const Tabs = () => {
  return {
    MuiTabs: {
      styleOverrides: {
        vertical: ({ theme }) => ({
          minWidth: 130,
          marginRight: theme.spacing(4),
          borderRight: `1px solid ${theme.palette.divider}`,
          '& .MuiTab-root': {
            minWidth: 130
          }
        })
      }
    },
    MuiTab: {
      styleOverrides: {
        root: {
          lineHeight: 1.5
        },
        textColorSecondary: ({ theme }) => ({
          '&.Mui-selected': {
            color: theme.palette.text.secondary
          }
        })
      }
    }
  }
}

export default Tabs
