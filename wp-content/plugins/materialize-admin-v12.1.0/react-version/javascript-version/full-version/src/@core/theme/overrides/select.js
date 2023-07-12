const select = () => {
  return {
    MuiSelect: {
      styleOverrides: {
        select: ({ theme }) => ({
          minWidth: '6rem !important',
          '&.MuiTablePagination-select': {
            minWidth: '1.5rem !important'
          },
          '&.Mui-disabled ~ .MuiOutlinedInput-notchedOutline': {
            borderColor: `rgba(${theme.palette.customColors.main}, 0.22)`
          }
        })
      }
    }
  }
}

export default select
