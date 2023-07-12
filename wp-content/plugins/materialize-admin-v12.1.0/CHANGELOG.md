# Changelog

All notable changes to this template will be documented in this file.

## v12.0.0 (2023-05-10)

### HTML

##### Fixed

- Fixed treeview images
- Dropzone minor fixes
- Without customizer Layout fix
- Minor bugfixes
  
### HTML + Laravel

##### Added

- Initial Version

### ASP.NET Core

##### Added

- Initial Version

## v11.0.0 (2023-03-30)

### HTML

##### Added

- Initial Version
  
## v10.1.0 (2023-03-22)

### React

##### Added

- Added .gitattributes file
- Added Progress component page
- Added necessary props for the UserLayout
- Added ability to access vertical or horizontal layout with menu without logging in
- Added custom variable `avatarBg` for Avatar background-color in the MUI's palette
- Added custom variable avatarBg for Avatar background-color in the MUI's paletteAdded articles on how to migrate from JWT Auth to [NextAuth](https://next-auth.js.org/), NextAuth with [CredentialsProvider](https://next-auth.js.org/configuration/providers/credentials) and NextAuth with [GoogleProvider](https://next-auth.js.org/providers/google) & [PrismaAdapter](https://next-auth.js.org/adapters/prisma)

##### Updated

- Updated docs
- Updated FAQs on the Pricing page
- Changed MUI Links to Next.js Links
- Updated `trackBg` colors in the theme
- Updated examples on the Dialog pages
- Changed method of implementing semi-dark skin
- Updated input mask labels on the Input Mask page
- Updated bg-colors of social cards on the Card Basic page
- Updated MUI's theme structure which is in the `src/@core` folder
- Update `lang` attribute on the `<html>` tag when the language is changed
- Updated structure of points in the Knowledge Base section on the Help Center page
- Updated UI of avatar, breadcrumb, chip, dataGrid, list, tooltip, snackbar components
- Updated all the packages (Refer to the official docs for the migration of `@mui/x-data-grid` from [here](https://mui.com/x/migration/migration-data-grid-v5/))

##### Fixed

- Fixed types
- Fixed vertical menu re-renders on hover
- Fixed transition issue on pull-up Avatars
- Fixed Alert snackbar shadow in bordered skin
- Fixed shadows in the whole template when the skin is semi-dark
- Fixed `react-hot-toast`'s z-index when its position is on the left side
- Fixed bugs in the Chat, Calendar, Invoice, User apps and User Profile page
- Fixed shadow between nav header and nav items in the Vertical navigation menu in RTL

##### Removed

- Removed unnecessary images
- Removed validation on the Register page
- Removed all the files and guides related to Firebase (in favour of NextAuth)

## v10.0.0 (2023-01-20)

### VueJS

##### Added

- Initial Version

### VueJS + Laravel

##### Added

- Initial Version

### HTML & Laravel

##### Removed

- Removed HTML version
- Removed HTML + Laravel version

## v9.0.0 (2022-11-11)

### Figma

##### Added

- Added authentication pages viz. Verify Email, Two Steps and Multi Step Register
- Added User Profile and Help Center pages
- Added real-life Form Wizard examples
- Added shortcut dropdown in the Navbar (AppBar)

##### Updated

- Updated Account Settings, Pricing, FAQ pages and User app
- Updated the variant of the Notification badge in the Navbar (AppBar)

##### Removed

- Removed Knowledge Base page

### React

##### Added

- Added Iconify icons
- Added npm and nvm config files
- Added real-life Form Wizard examples
- Added User Profile and Help Center pages
- Added a custom component to use Iconify icons
- Added shortcut dropdown in the Navbar (AppBar)
- Added rounded variant for the Chip component
- Added new examples on the List component page
- Added pull-up variant for the Avatar component
- Added functionality to 'Remember Me' while logging in
- Added options to override styling and props on the layout
- Added a utility function to convert an RGBA color to a Hex color
- Added functionality to log out and refresh the token on expiration
- Added a custom component to open a menu on an IconButton component
- Added custom components for radios and checkboxes with different variants
- Added authentication pages viz. Verify Email, Two Steps and Multi Step Register

##### Updated

- Updated docs
- Updated footer styles
- Updated Next.js to v13
- Optimized all the images
- Updated colors in all the charts
- Moved JWT configs to the .env file
- Updated semi-dark from skin to mode
- Updated all the dependencies to the latest versions
- Updated color opacities according to the MUI's Theme
- Updated Account Settings, Pricing, FAQ pages and User app
- Updated the variant of the Notification badge in the Navbar (AppBar)
- Updated fallback to false in the getStaticPaths method on all dynamic pages

##### Fixed

- Fixed minor bugs
- Fixed react-datepicker styling
- Fixed styles of Accordion component
- Fixed the search dialog's unexpected behavior
- Fixed popper-placement of react-datepicker in RTL
- Fixed z-index of react-hot-toast in smaller screens
- Fixed third-level menu and undefined links in the Horizontal navigation menu
- Fixed a few re-renders in the Vertical navigation menu and in the Vertical layout
- Fixed content area scroll issue due to spinner when a user logs in for the first time
- Fixed Email and Chat apps' height by adding the contentHeightFixed method on the page component
- Fixed border-radius & spacing issues in Table & DataGrid and overriding row height issue in DataGrid

##### Removed

- Removed Knowledge Base page
- Removed validations from some pages
- Removed portal of react-datepicker
- Removed all the examples of MUI's pickers
- Removed Material Design Icons (by Community)
- Removed documentation of Auth0 and AWS Amplify
- Removed download functionality from the Invoice Preview page

## v8.1 (2022-05-27)

### React

##### Updated

- Updated docs
- Updated some image paths
- Updated the shadow below Vertical Navigation Header and above Vertical Navigation

##### Fixed

- Fixed the build errors
- Fixed the UI of the search
- Fixed width of all columns in all DataGrids
- Fixed ACL visibility of sub to sub menu group

## v8.0 (2022-05-20)

### React

##### Added

- Initial Version

### HTML

##### Updated

- Updated node dependencies
  
### Laravel

##### Updated

- Updated Laravel Framework composer dependencies

## v7.4 (2022-03-24)

### Laravel

##### Updated

- Laravel Framework updated to Laravel 9

##### Added

- Added Support for BrowserSync

## v7.3 (2021-02-25)

### Laravel

- Updated Laravel framework to latest
- Updated Laravel folder structure according to the latest one
- Updated Laravel passport documentation
- Added Laravel sanctum documentation
- Added Laravel Sail(Docker) documentation

## v7.2 (2020-12-02)

### Laravel

##### Updated

- Updated to latest Laravel 8
- Updated Laravel UI
- Updated Laravel Passport

## v7.1 (2020-04-03)

### Laravel

##### Updated

- Updated to latest Laravel 7

## v7.0 (2020-01-30)

### HTML

##### Updated

- Improved folder structure

##### Fixed

- Fixed search issue on small screen
- Fixed horizontal menu dropdown click for iPad
- Fixed iPhone menu open issue for new iOS update

### Laravel

- Initial Release

## v6.0 (2019-11-02)

HTML

##### Added

- RTL Support for all layouts & starter kit
- Multi language support enable
- Quick search
- Kanban (scrum board) App
- File manager App
- Invoice App
- User list, view & edit page
- Timeline page
- Account Settings page
- Component: Select2
- Component: Rating
- Component: Tour
- Component: Quill Editor

##### Updated

- Updated all libs & package to the latest version
- Updated documentation

##### Fixed

- Vertical navigation sub-menu open effect
- Horizontal menu navbar dropdown z-index issue
- Form select overflowed the card box
- Vertical navigation menu item flashing issue on collapsibles page
- Bordered table
- Firefox & IE left sidenav scrolling issue

------------

## v5.1 (2019-02-19)

##### Fixed

- Fixed BreadCrumbs in IE
- Fixed Right slide out issue of IE
- Fixed Todo Select All IE issue
- Fixed Chat image IE issue
- Fixed Datatable View in IE
- Fixed Collapse Icon of menu for IE
- Fixed Pricing page icon issue in IE
- Fixed Modal popup for IE
- Fixed Fullscreen issue from IE

## v5.0 (2019-02-11)

##### Added

- Added new vertical modern & dark menu templates
- Added theme customizer for customize & preview template in real time
- Added new vertical dark menu template
- Added new menu active style
- Added modern dashboard with clean and modern look & feel
- Added chat, todo, contact & ecommerce applications
- Added search, knowledge base & faq pages
- Added new advanced & extended cards
- Added all the framework components useful html,css & js code
- Added new components & options as per Materializecss framework
- Added form wizard with vertical & horizontal example
- Added intro section for landing page of admin template

##### Updated

- Materializecss framework upgraded to v1.0.0
- Improved folder structure to make it easy to utilize
- Upgraded all the third party vendors & library
- Improved Email & Calendar app UI and functionality
- Improved user profile & error pages design
- Improved contact page UI
- Improved right slide out UI and added chat support
- Improved chart library and upgrade all the charts
- Improve the document for upgraded folder structure usage and customization
- Improve gulp tasks for the new folder structure

##### Fixed

- Fixed swipe to open menu in small screen
- Fixed chartjs issues by upgrading it to latest stable version
- Fixed form elements, validation & input mask related issues

##### Removed

- Removed vertical overlay & fixed menu template
- Removed grunt task runner support
- Removed jsGrid, floatThead tables
- Removed advance-ui-scrollfire as per framework change
- Removed Morris, xChart & Flot charts
- Removed AngularJS directive support (Angular 1.0)

## v4.0 (2017-10-18)

##### Added

- New Themes : Collapsible Menu, Collapsible Semi Dark Menu
- New Dashboard - dashboard-ecommerce.html
- Advanced cards - advanced-cards.html
- Advanced ui pulse - css-pulse.html
- Feature discovery - ui-feature-discovery.html
- CSS Transition - css-transitions.html
- Carousel - ui-carousel.html
- JS Transition - advanced-ui-transitions.html
- UI Advanced Buttons - ui-buttons-extended.html
- Dropdown - advance-ui-dropdown.html
- Scrollfire - advance-ui-scrollfire.html
- Scrollspy - advance-ui-scrollspy.html
- Add new layouts pages for light, dark, semi-dark, fixed footer and collapsible menu options
- Added support for jQuery 3
- Added Time Picker
- Added XL breakpoint
- Added Pulse CSS effect
- Added Feature Discovery component
- Added FABs in image cards
- Added swipeable tabs
- Added autocomplete integration with chips
- Added class method to dismiss all toasts
- Added callbacks to side nav
- Added instance method to remove specific toasts
- Added ability to remove autocompelete data
- Added container option for time picker
- Tab accessibility for date picker
- Validation styling support added for many form components
- Added carousel destroy
- Added close on select option for date picker
- Added support for custom active elements in scrollspy
- Basic horizontal cards
- Sidenav and Modals no longer cause flicker with scrollbar
- Materialbox overflow and z-index issues fixed
- Added new option for Card actions within a Card reveal

##### Updated

- Materializecss framework upgraded to v0.100.1
- Updated styling for Date Picker
- Updated styling for switches
- Autocomplete: renamed and moved options to autocompleteOptions
- Range slider supports keyboard navigation
- Upgraded noUiSlider to version 9 with support for vertical sliders
- Improved tabs compatibility with cards
- Refactored Modal plugin
- Tabs now supported in navbar
- Chips data can now be reinitiailized
- Updated sidenav styles and new component
- Changed /font directory to /fonts
- Datepicker and ScrollSpy now compatible with jQuery 2.2.x
- css-icons.html --> ui-icons.html
- advanced-ui-chips.html - > ui-chips.html
- ui-toasts.html -> advance-ui-toasts.html
- ui-tooltip.html -> advance-ui-tooltip.html
- ui-waves.html -> advance-ui-waves.html
- ui-tabs.html -> advance-ui-tabs.html
- ui-collapsibles.html -> advance-ui-collapsibles.html
- ui-carousel.html -> advance-ui-carousel.html
- ui-transitions.html -> advanced-ui-transitions.html
- Renamed Plugins folder to Vendors and moved to root folder

##### Fixed

- Fixed bug where modal triggers could not contain child elements
- Fixed bug with right alignment option for dropdown
- Added fix for validation messages being mispositioned when input is empty
- Allow select native browser validation error messages
- Modal open no longer initializes plugin
- Fixed bug where modal open did not use initialized options
- Modal-trigger class required for modal trigger elements
- Fixed waves persisting bug
- Waves no longer throws error on svg elements
- Fixed side nav callback bugs
- Fixed carousel image loading bug
- Full width carousel now resizes height on resize
- Fixed multiple bugs with jQuery outerWidth on Linux
- Fixed cursor blinking on select on iOS
- Fixed search form styling in navbar
- Fixed label animation on date picker
- Browser errors now show up on radio buttons and checkboxes
- Fixed dynamic textarea resize bug
- Fixed collapsible preselect bug
- Fixed dropdown event bubbling bug
- Fixed range position inaccuracies
- Fixed feature discovery mobile styles
- Fixed carousel reinitialize bugs
- Fixed grid offset bug
- Fixed various select bugs on mobile devices
- Fixed small sideNav overlay bugs
- Fixed carousel resizing bug
- Fixed materialbox callback bug
- Fixed carousel misalignment when switching quickly
- Fixed carousel resize bug where slide widths wouldn't change when changing window size
- Fixed bug where using backspace to delete chips would navigate back in certain browsers
- Fixed dropdown options bug
- Carousel bug fixes and new features
- Responsive tables now work with empty cells
- Added focus states to checkboxes, switches, and radio buttons

##### Removed

- Removed deprecated material icons from project

## v3.1 (2016-01-28)

##### Added

- Added a custom.scss file for making custom changes using SCSS.

##### Fixed

- Fixed navigation bar issue on small screen for Horizontal & full-screen layout
- Fixed card reveal effect and make made it more smooth
- Fixed chartist error on single pages like login, register etc..

## v3.0 (2015-12-12)

##### Added

- Ready to use number of Angular directives, are handled by Angular in the right way.
(angular-materialize.html)
- Language Translation (advanced-ui-translation.html)
- Sweetalert for beautiful Modal popup (advanced-ui-sweetalert.html)
- Shortable & Nestable (advanced-ui-nestable.html)
- Range Selector (advanced-ui-range-slider.html)
- Syntext Highlight (advanced-ui-highlight.html)
- Advanced form validations (form-validation.html)
- Input ttype mask (form-masks.html)
- Drag & Drop file upload with customizable drop zone (form-file-uploads.html)
- Updated new form elements (form-elements.html)
- jsGrid - Lightweight Grid jQuery Plugin (table-jsgrid.html)
- editableTable - turns any table into an editable spreadsheet. (table-editable.html)
- floatThead - fixed table header plugin that requires no special CSS and supports (table-floatThead.html)
- Material Chips (ui-chips.html)
- Alerts & Warnings (ui-alerts.html)
- Breadcrumbs (ui-breadcrumbs.html)
- Accordions page change to Collapsible with new popout feature.
 (ui-accordions.html - > ui-collapsible.html )
- CSS Animations (css-animations.html)
- Hoverable healpers (css-helpers.html)

##### Updated

- Updated Material FAB Buttons (ui-buttons.html)

##### Fixed

- Fixed popup close issue on click of popup out side (body) area.
- Fixed the datatables row display select dropdown hide issue.
- Fixed the floating button hover issue.
- Fixed sparkline chaer hover stats background display issue.
- Fixed the dount chart stats position.
- Added the open close state menu icons and color.
- Added notification dropdown and county flag dropdown for translation.
- Fixed chart lag, scrollbar and map marker issues.

## v2.3 (2015-09-25)

##### Added

- Update materialize the SASS files

##### Fixed

- Fixed Firefox dropdown menu support issues
- e-Commerce product page fix

## v2.2 (2015-08-18)

##### Updated

- Improved Charts
- Update materialize the SASS files

##### Fixed

- Fixed IE and Firefox support issues
- Dashboard search fix and updates

## v2.1 (2015-06-11)

##### Added

- 2 New layout added - Horizontal & Full Screen
- New Page - Profile Page
- New Page - Contact Page
- New Page - ToDos Page
- New Page - Blog Type 1 & 2 Page
- New Page - Products Page
- New Page - Pricing Page
- New Page - Gallaery Page
- New Page - Image Hover Page

##### Updated

- Updated Material Search
- Update materialize the SASS files

##### Fixed

Fixed perfect scroll bar select to scroll issue

## v1.1 (2015-06-06)

##### Added

- Add new floating action button

##### Updated

- Update materialize the SASS files

##### Fixed

- Fix the trending chart lagging issue

## v1.0 (2015-03-20):  Initial Release
