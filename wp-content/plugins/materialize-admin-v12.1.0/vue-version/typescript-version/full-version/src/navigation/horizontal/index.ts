import apps from './apps'
import charts from './charts'
import dashboard from './dashboard'
import forms from './forms'
import others from './others'
import pages from './pages'
import tables from './tables'
import uiElements from './ui-elements'
import type { HorizontalNavItems } from '@layouts/types'

export default [...dashboard, ...apps, ...pages, ...uiElements, ...forms, ...tables, ...charts, ...others] as HorizontalNavItems
