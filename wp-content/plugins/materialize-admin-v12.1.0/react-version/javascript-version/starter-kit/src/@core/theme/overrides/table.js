const Table = () => {
  return {
    MuiTableContainer: {
      styleOverrides: {
        root: ({ theme }) => ({
          boxShadow: theme.shadows[0],
          borderTopColor: theme.palette.divider
        })
      }
    },
    MuiTableHead: {
      styleOverrides: {
        root: {
          textTransform: 'uppercase',
          '& .MuiTableCell-head': {
            fontWeight: 500,
            fontSize: '0.75rem',
            lineHeight: '1.959rem',
            letterSpacing: '0.17px'
          }
        }
      }
    },
    MuiTableBody: {
      styleOverrides: {
        root: ({ theme }) => ({
          '& .MuiTableCell-body': {
            fontWeight: 400,
            fontSize: '0.875rem',
            lineHeight: '1.358rem',
            letterSpacing: '0.15px',
            '&:not(.MuiTableCell-sizeSmall):not(.MuiTableCell-paddingCheckbox):not(.MuiTableCell-paddingNone)': {
              paddingTop: theme.spacing(4),
              paddingBottom: theme.spacing(4)
            }
          }
        })
      }
    },
    MuiTableRow: {
      styleOverrides: {
        root: ({ theme }) => ({
          '& .MuiTableCell-head:not(.MuiTableCell-paddingCheckbox):first-child, & .MuiTableCell-root:not(.MuiTableCell-paddingCheckbox):first-child ':
            {
              paddingLeft: theme.spacing(5)
            },
          '& .MuiTableCell-head:last-child, & .MuiTableCell-root:last-child': {
            paddingRight: theme.spacing(5)
          }
        })
      }
    },
    MuiTableCell: {
      styleOverrides: {
        root: ({ theme }) => ({
          borderBottom: `1px solid ${theme.palette.divider}`
        }),
        paddingCheckbox: ({ theme }) => ({
          paddingLeft: theme.spacing(2)
        }),
        stickyHeader: ({ theme }) => ({
          backgroundColor: theme.palette.customColors.tableHeaderBg
        })
      }
    },
    MuiTablePagination: {
      styleOverrides: {
        root: ({ theme }) => ({
          '& .MuiIconButton-root.Mui-disabled': {
            color: theme.palette.action.active
          }
        }),
        displayedRows: ({ theme }) => ({
          color: theme.palette.text.primary
        })
      }
    }
  }
}

export default Table
