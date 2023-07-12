export const TreeViewBasicJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import TreeView from '@mui/lab/TreeView'
import TreeItem from '@mui/lab/TreeItem'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const TreeViewBasic = ({ direction }) => {
  const ExpandIcon = direction === 'rtl' ? 'mdi:chevron-left' : 'mdi:chevron-right'

  return (
    <TreeView
      sx={{ minHeight: 240 }}
      defaultExpandIcon={<Icon icon={ExpandIcon} />}
      defaultCollapseIcon={<Icon icon='mdi:chevron-down' />}
    >
      <TreeItem nodeId='1' label='Applications'>
        <TreeItem nodeId='2' label='Calendar' />
        <TreeItem nodeId='3' label='Chrome' />
        <TreeItem nodeId='4' label='Webstorm' />
      </TreeItem>
      <TreeItem nodeId='5' label='Documents'>
        <TreeItem nodeId='10' label='OSS' />
        <TreeItem nodeId='6' label='MUI'>
          <TreeItem nodeId='7' label='src'>
            <TreeItem nodeId='8' label='index.js' />
            <TreeItem nodeId='9' label='tree-view.js' />
          </TreeItem>
        </TreeItem>
      </TreeItem>
    </TreeView>
  )
}

export default TreeViewBasic
`}</code></pre>) 

export const TreeViewControlledJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import TreeView from '@mui/lab/TreeView'
import TreeItem from '@mui/lab/TreeItem'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const TreeViewControlled = ({ direction }) => {
  // ** States
  const [expanded, setExpanded] = useState([])
  const [selected, setSelected] = useState([])

  const handleToggle = (event, nodeIds) => {
    setExpanded(nodeIds)
  }

  const handleSelect = (event, nodeIds) => {
    setSelected(nodeIds)
  }
  const ExpandIcon = direction === 'rtl' ? 'mdi:chevron-left' : 'mdi:chevron-right'

  return (
    <TreeView
      expanded={expanded}
      selected={selected}
      sx={{ minHeight: 240 }}
      onNodeToggle={handleToggle}
      onNodeSelect={handleSelect}
      defaultExpandIcon={<Icon icon={ExpandIcon} />}
      defaultCollapseIcon={<Icon icon='mdi:chevron-down' />}
    >
      <TreeItem nodeId='1' label='Applications'>
        <TreeItem nodeId='2' label='Calendar' />
        <TreeItem nodeId='3' label='Chrome' />
        <TreeItem nodeId='4' label='Webstorm' />
      </TreeItem>
      <TreeItem nodeId='5' label='Documents'>
        <TreeItem nodeId='10' label='OSS' />
        <TreeItem nodeId='6' label='MUI'>
          <TreeItem nodeId='7' label='src'>
            <TreeItem nodeId='8' label='index.js' />
            <TreeItem nodeId='9' label='tree-view.js' />
          </TreeItem>
        </TreeItem>
      </TreeItem>
    </TreeView>
  )
}

export default TreeViewControlled
`}</code></pre>) 

export const TreeViewMultiSelectionJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import TreeView from '@mui/lab/TreeView'
import TreeItem from '@mui/lab/TreeItem'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const TreeViewMultiSelection = ({ direction }) => {
  const ExpandIcon = direction === 'rtl' ? 'mdi:chevron-left' : 'mdi:chevron-right'

  return (
    <TreeView
      multiSelect
      sx={{ minHeight: 240 }}
      defaultExpandIcon={<Icon icon={ExpandIcon} />}
      defaultCollapseIcon={<Icon icon='mdi:chevron-down' />}
    >
      <TreeItem nodeId='1' label='Applications'>
        <TreeItem nodeId='2' label='Calendar' />
        <TreeItem nodeId='3' label='Chrome' />
        <TreeItem nodeId='4' label='Webstorm' />
      </TreeItem>
      <TreeItem nodeId='5' label='Documents'>
        <TreeItem nodeId='10' label='OSS' />
        <TreeItem nodeId='6' label='MUI'>
          <TreeItem nodeId='7' label='src'>
            <TreeItem nodeId='8' label='index.js' />
            <TreeItem nodeId='9' label='tree-view.js' />
          </TreeItem>
        </TreeItem>
      </TreeItem>
    </TreeView>
  )
}

export default TreeViewMultiSelection
`}</code></pre>) 

export const TreeViewRichObjectJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import TreeView from '@mui/lab/TreeView'
import TreeItem from '@mui/lab/TreeItem'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

const data = {
  id: 'root',
  name: 'Parent',
  children: [
    {
      id: '1',
      name: 'Child - 1'
    },
    {
      id: '3',
      name: 'Child - 3',
      children: [
        {
          id: '4',
          name: 'Child - 4'
        }
      ]
    }
  ]
}

const TreeViewRichObject = ({ direction }) => {
  const renderTree = nodes => (
    <TreeItem key={nodes.id} nodeId={nodes.id} label={nodes.name}>
      {Array.isArray(nodes.children) ? nodes.children.map(node => renderTree(node)) : null}
    </TreeItem>
  )
  const ExpandIcon = direction === 'rtl' ? 'mdi:chevron-left' : 'mdi:chevron-right'

  return (
    <TreeView
      sx={{ minHeight: 240 }}
      defaultExpanded={['root']}
      defaultExpandIcon={<Icon icon={ExpandIcon} />}
      defaultCollapseIcon={<Icon icon='mdi:chevron-down' />}
    >
      {renderTree(data)}
    </TreeView>
  )
}

export default TreeViewRichObject
`}</code></pre>) 

export const TreeViewGmailCloneJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import Box from '@mui/material/Box'
import TreeView from '@mui/lab/TreeView'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import TreeItem from '@mui/lab/TreeItem'

// ** Custom Icon Import
import Icon from 'src/@core/components/icon'

// Styled TreeItem component
const StyledTreeItemRoot = styled(TreeItem)(({ theme }) => ({
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

const StyledTreeItem = props => {
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

const TreeViewGmailClone = ({ direction }) => {
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
`}</code></pre>) 

export const TreeViewCustomizedJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** MUI Imports
import TreeItem from '@mui/lab/TreeItem'
import { alpha, styled } from '@mui/material/styles'
import MuiTreeView from '@mui/lab/TreeView'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// Styled TreeView component
const TreeView = styled(MuiTreeView)(({ theme }) => ({
  minHeight: 264,
  '& .MuiTreeItem-iconContainer .close': {
    opacity: 0.3
  },
  '& .MuiTreeItem-group': {
    marginLeft: 15,
    paddingLeft: 18,
    borderLeft: 1px dashed {alpha(theme.palette.text.primary, 0.4)}
  }
}))

const TreeViewCustomized = () => {
  return (
    <TreeView
      defaultExpanded={['1']}
      defaultExpandIcon={<Icon icon='mdi:plus-box-outline' />}
      defaultCollapseIcon={<Icon icon='mdi:minus-box-outline' />}
      defaultEndIcon={<Icon icon='mdi:close-box-outline' className='close' />}
    >
      <TreeItem nodeId='1' label='Main'>
        <TreeItem nodeId='2' label='Hello' />
        <TreeItem nodeId='3' label='Subtree with children'>
          <TreeItem nodeId='6' label='Hello' />
          <TreeItem nodeId='7' label='Sub-subtree with children'>
            <TreeItem nodeId='9' label='Child 1' />
            <TreeItem nodeId='10' label='Child 2' />
            <TreeItem nodeId='11' label='Child 3' />
          </TreeItem>
          <TreeItem nodeId='8' label='Hello' />
        </TreeItem>
        <TreeItem nodeId='4' label='World' />
        <TreeItem nodeId='5' label='Something something' />
      </TreeItem>
    </TreeView>
  )
}

export default TreeViewCustomized
`}</code></pre>) 

