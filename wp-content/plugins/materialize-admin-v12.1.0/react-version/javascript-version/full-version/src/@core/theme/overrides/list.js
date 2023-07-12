const List = () => {
  return {
    MuiListItemIcon: {
      styleOverrides: {
        root: ({ theme }) => ({
          minWidth: '0 !important',
          marginRight: theme.spacing(3),
          color: theme.palette.text.secondary
        })
      }
    },
    MuiListItemAvatar: {
      styleOverrides: {
        root: ({ theme }) => ({
          minWidth: 0,
          marginRight: theme.spacing(4)
        })
      }
    },
    MuiListItemText: {
      styleOverrides: {
        dense: ({ theme }) => ({
          '& .MuiListItemText-primary': {
            color: theme.palette.text.primary
          },
          '& .MuiListItemText-primary, & .MuiListItemText-secondary': {
            lineHeight: 1.43
          }
        })
      }
    },
    MuiListSubheader: {
      styleOverrides: {
        root: ({ theme }) => ({
          fontWeight: 600,
          textTransform: 'uppercase',
          color: theme.palette.text.primary
        })
      }
    }
  }
}

export default List
