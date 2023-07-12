// ** MUI Imports
import Box from '@mui/material/Box'
import TreeView from '@mui/lab/TreeView'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import TreeItem, { TreeItemProps } from '@mui/lab/TreeItem'

// ** Custom Icon Import
import Icon from 'src/@core/components/icon'

interface Props {
  direction: 'ltr' | 'rtl'
}

type StyledTreeItemProps = TreeItemProps & {
  labelText: string
  labelIcon: string
  labelInfo?: string
}

// Styled TreeItem component
const StyledTreeItemRoot = styled(TreeItem)<TreeItemProps>(({ theme }) => ({
  '&:hover > .MuiTreeItem-content:not(.Mui-selected)': {
    backgroundColor: theme.palette.action.hover
  },
  '& .MuiTreeItem-content': {
    paddingRight: theme.spacing(3),
    borderTopRightRadius: theme.spacing(4),
    borderBottomRightRadius: theme.spacing(4),
    fontWeight: theme.typography.fontWeightMedium
  },
  '& .MuiTreeItem-label': {
    fontWeight: 'inherit',
    paddingRight: theme.spacing(3)
  },
  '& .MuiTreeItem-group': {
    marginLeft: 0,
    '& .MuiTreeItem-content': {
      paddingLeft: theme.spacing(4),
      fontWeight: theme.typography.fontWeightRegular
    }
  }
}))

const StyledTreeItem = (props: StyledTreeItemProps) => {
  // ** Props
  const { labelText, labelIcon, labelInfo, ...other } = props

  return (
    <StyledTreeItemRoot
      {...other}
      label={
        <Box sx={{ py: 1, display: 'flex', alignItems: 'center', '& svg': { mr: 1 } }}>
          <Icon icon={labelIcon} color='inherit' />
          <Typography variant='body2' sx={{ flexGrow: 1, fontWeight: 'inherit' }}>
            {labelText}
          </Typography>
          {labelInfo ? (
            <Typography variant='caption' color='inherit'>
              {labelInfo}
            </Typography>
          ) : null}
        </Box>
      }
    />
  )
}

const TreeViewGmailClone = ({ direction }: Props) => {
  const ExpandIcon = <Icon icon={direction === 'rtl' ? 'mdi:chevron-left' : 'mdi:chevron-right'} />

  return (
    <TreeView
      sx={{ minHeight: 240 }}
      defaultExpanded={['3']}
      defaultExpandIcon={ExpandIcon}
      defaultCollapseIcon={<Icon icon='mdi:chevron-down' />}
    >
      <StyledTreeItem nodeId='1' labelText='All Mail' labelIcon='mdi:email-outline' />
      <StyledTreeItem nodeId='2' labelText='Trash' labelIcon='mdi:delete-outline' />
      <StyledTreeItem nodeId='3' labelText='Categories' labelIcon='mdi:label-outline'>
        <StyledTreeItem nodeId='5' labelInfo='90' labelText='Social' labelIcon='mdi:account-supervisor-outline' />
        <StyledTreeItem nodeId='6' labelInfo='2,294' labelText='Updates' labelIcon='mdi:information-outline' />
        <StyledTreeItem nodeId='7' labelInfo='3,566' labelText='Forums' labelIcon='mdi:forum-outline' />
        <StyledTreeItem nodeId='8' labelInfo='733' labelText='Promotions' labelIcon='mdi:tag-outline' />
      </StyledTreeItem>
      <StyledTreeItem nodeId='4' labelText='History' labelIcon='mdi:label-outline' />
    </TreeView>
  )
}

export default TreeViewGmailClone
