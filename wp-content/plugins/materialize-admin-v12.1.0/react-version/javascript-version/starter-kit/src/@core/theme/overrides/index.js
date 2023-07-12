// ** Overrides Imports
import MuiFab from './fab'
import MuiCard from './card'
import MuiChip from './chip'
import MuiLink from './link'
import MuiList from './list'
import MuiMenu from './menu'
import MuiTabs from './tabs'
import MuiInput from './input'
import MuiPaper from './paper'
import MuiTable from './table'
import MuiAlerts from './alerts'
import MuiButton from './button'
import MuiDialog from './dialog'
import MuiRating from './rating'
import MuiSelect from './select'
import MuiAvatar from './avatars'
import Progress from './progress'
import MuiDivider from './divider'
import MuiPopover from './popover'
import MuiTooltip from './tooltip'
import MuiBackdrop from './backdrop'
import MuiDataGrid from './dataGrid'
import MuiSnackbar from './snackbar'
import MuiSwitches from './switches'
import MuiTimeline from './timeline'
import MuiAccordion from './accordion'
import MuiPagination from './pagination'
import MuiTypography from './typography'
import MuiBreadcrumb from './breadcrumbs'
import MuiButtonGroup from './buttonGroup'
import MuiAutocomplete from './autocomplete'
import MuiToggleButton from './toggleButton'

const Overrides = settings => {
  const { skin, mode } = settings
  const fab = MuiFab()
  const chip = MuiChip()
  const list = MuiList()
  const tabs = MuiTabs()
  const input = MuiInput()
  const tables = MuiTable()
  const menu = MuiMenu(skin)
  const button = MuiButton()
  const rating = MuiRating()
  const select = MuiSelect()
  const cards = MuiCard(skin)
  const avatars = MuiAvatar()
  const progress = Progress()
  const divider = MuiDivider()
  const tooltip = MuiTooltip()
  const alerts = MuiAlerts(mode)
  const dialog = MuiDialog(skin)
  const backdrop = MuiBackdrop()
  const dataGrid = MuiDataGrid()
  const switches = MuiSwitches()
  const timeline = MuiTimeline()
  const popover = MuiPopover(skin)
  const accordion = MuiAccordion()
  const snackbar = MuiSnackbar(skin)
  const pagination = MuiPagination()
  const autocomplete = MuiAutocomplete(skin)

  return Object.assign(
    fab,
    chip,
    list,
    menu,
    tabs,
    cards,
    input,
    select,
    alerts,
    button,
    dialog,
    rating,
    tables,
    avatars,
    divider,
    popover,
    tooltip,
    MuiLink,
    backdrop,
    dataGrid,
    MuiPaper,
    progress,
    snackbar,
    switches,
    timeline,
    accordion,
    pagination,
    autocomplete,
    MuiTypography,
    MuiBreadcrumb,
    MuiButtonGroup,
    MuiToggleButton
  )
}

export default Overrides
