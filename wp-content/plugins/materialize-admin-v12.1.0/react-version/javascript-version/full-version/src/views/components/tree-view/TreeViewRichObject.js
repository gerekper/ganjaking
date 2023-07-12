// ** MUI Imports
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
