<?php
echo "<style>
html, body {
	overflow: hidden !important;
}
#wpadminbar, #wpadminbar .ab-sub-wrapper, #wpadminbar ul, #wpadminbar ul li {
	z-index: 2147483647;
}
#perfmatters-script-manager-wrapper {
	display: none;
	position: fixed;
	z-index: 2147483646;
	top: 32px;
	bottom: 0px;
	left: 0px;
	right: 0px;
	background: rgba(0,0,0,0.5);
	overflow-y: auto;
}
#perfmatters-script-manager {
	display: flex;
	flex-grow: 1;
	position: relative;
	background: #EEF2F5;
	font-size: 14px;
	line-height: 1.5em;
	color: #4a545a;
	min-height: 100%;
	box-sizing: border-box;
	overflow: hidden;
}
#perfmatters-script-manager-wrapper * {
	font-family: -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
}
#perfmatters-script-manager-wrapper .dashicons {
	font-family: dashicons;
}

#perfmatters-script-manager a {
	color: #4A89DD;
	text-decoration: none;
	border: none;
}
#perfmatters-script-manager label {
	float: none;
	opacity: 1;
}
#perfmatters-script-manager-header {
	width: 270px;
	min-width: 270px;
	background: #282E34;
}
#pmsm-header-hero {
	display: flex;
}
#pmsm-menu-toggle {
	cursor: pointer;
}
#pmsm-header-hero .dashicons-menu {
	margin: 20px;
	color: #ffffff;
}
#pmsm-header-hero #perfmatters-logo {
	display: block;
	width: 150px;
}
#perfmatters-script-manager-header h2 {
	font-size: 24px;
	margin: 0px 0px 10px 0px;
	color: #4a545a;
	font-weight: bold;
}
#perfmatters-script-manager-header h2 span {
	background: #ED5464;
	color: #ffffff;
	padding: 5px;
	vertical-align: middle;
	font-size: 10px;
	margin-left: 5px;
}
#perfmatters-script-manager-header p {
	font-size: 14px;
	color: #4a545a;
	font-style: italic;
	margin: 0px auto 15px auto;
}
#perfmatters-script-manager-close {
	position: absolute;
	top: 0px;
	right: 0px;
	height: 26px;
	width: 26px;
}
#perfmatters-script-manager-close img {
	height: 26px;
	width: 26px;
}
#perfmatters-script-manager-tabs button {
	display: flex;
	align-items: center;
	float: left;
	padding: 15px 20px;
	width: 100%;
	font-size: 17px;
	line-height: normal;
	text-align: left;
	background: transparent;
	color: #ffffff;
	font-weight: normal;
	border: none;
	cursor: pointer;
	border-radius: 0px;
	height: 60px;
	text-transform: none;
	text-decoration: none;
	outline: none;
}
#perfmatters-script-manager-tabs {
	overflow: hidden;
}
#perfmatters-script-manager-tabs button span {
	font: 400 20px/1 dashicons;
	margin-right: 20px;
}
#perfmatters-script-manager-tabs button.active {
	background: #4A89DD;
	color: #ffffff;
}
#perfmatters-script-manager-tabs button:hover {
	background: #2f373f;
	color: #ffffff;
}
#perfmatters-script-manager-tabs button.active:hover {
	background: #4A89DD;
	color: #ffffff;
}
#perfmatters-script-manager-header.pmsm-header-minimal {
	width: 60px;
	min-width: 60px;
}
#perfmatters-script-manager-header.pmsm-header-minimal #perfmatters-logo {
	display: none;
}
#perfmatters-script-manager-header.pmsm-header-expanded {
	height: auto;
}
#pmsm-viewport {
	width: 100%;
	overflow-y: scroll;
	padding: 20px 20px 0px 20px;
}
.pmsm-notice {
	background: #ffffff;
	padding: 10px;
	margin: 0px 0px 10px 0px;
	border-radius: 3px;
	border-left: 4px solid #4A89DD; 
}
.pmsm-notice-warning {
	border-color: #FFC14E;
}
#pmsm-disclaimer-close {
	float: right;
	padding: 0px;
  width: 30px;
  height: 30px;
  line-height: 30px;
  font-weight: bold;
  background: none;
  color: #787c82
}
#pmsm-disclaimer-close:hover {
	color: #d63638;
}
#pmsm-notices .pmsm-notice:last-child {
	margin-bottom: 20px;
}
#perfmatters-script-manager-container {
	position: relative;
	max-width: 1000px;
	margin: 0px auto;
}
#perfmatters-script-manager-container .perfmatters-script-manager-title-bar {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 20px;
}
#perfmatters-script-manager-container .perfmatters-script-manager-title-bar h1 {
	font-size: 24px;
	line-height: normal;
	font-weight: 400;
	margin: 0px;
	color: #282E34;
}
#perfmatters-script-manager-container .perfmatters-script-manager-title-bar p {
	margin: 0px;
	color: #282E34;
	font-size: 13px;
}
#perfmatters-script-manager h3 {
	padding: 10px;
	margin: 0px;
	font-size: 18px;
	background: #282E34;
	color: #ffffff;
	text-transform: capitalize;
	font-weight: 400;
}
.perfmatters-script-manager-group {
	box-shadow: 0 1px 2px 0 rgba(40,46,52,.1);
	margin: 0px 0px 20px 0px;
}
.pmsm-group-heading {
	display: flex;
	justify-content: space-between;
	height: 40px;
	padding: 10px;
	background: #edf3f9;
	color: #282E34;
	font-weight: 700;
	box-sizing: content-box;
}
.perfmatters-script-manager-group h4 {
	display: inline-block;
	margin: 0px;
	padding: 0px;
	font-size: 20px;
	line-height: 40px;
	font-weight: 700;
	color: #282E34;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
.perfmatters-script-manager-section {
	padding: 0px 10px;
	background: #ffffff;
	margin: 0px 0px 0px 0px;
}
.perfmatters-script-manager-assets-disabled {
	padding: 0px 0px 10px 0px;
}
#perfmatters-script-manager table {
	table-layout: fixed;
	width: 100%;
	margin: 0px;
	padding: 0px;
	border: none;
	text-align: left;
	font-size: 14px;
	border-collapse: collapse;
}
#perfmatters-script-manager table thead {
	background: none;
	color: #282E34;
	font-weight: bold;
	border: none;
}
#perfmatters-script-manager table thead th {
	font-size: 14px;
	padding: 8px 5px;
	vertical-align: middle;
	border: none;
	background: #ffffff;
	color: #4a545a;
	width: auto;
}
#perfmatters-script-manager .pmsm-column-status {
	width: 120px;
}
#perfmatters-script-manager .pmsm-column-type, #perfmatters-script-manager .pmsm-column-size {
	width: 100px;
	text-align: center;
}
#perfmatters-script-manager table tr {
	border: none;
	border-bottom: 1px solid #EDF3F9;
	background: #ffffff;
	box-sizing: content-box;
}
#perfmatters-script-manager table tbody tr:last-child {
	border-bottom: 0px;
}
#perfmatters-script-manager table td {
	padding: 8px 5px;
	border: none;
	vertical-align: top;
	font-size: 14px;
	background: #ffffff;
	color: #4a545a;
	width: auto;
}
#perfmatters-script-manager table td.perfmatters-script-manager-type {
	font-size: 14px;
	text-align: center;
	padding-top: 16px;
	text-transform: uppercase;
}
#perfmatters-script-manager .pmsm-script-type-js .pmsm-tag {
	background: #FFC14E;
	color: #222;
}
#perfmatters-script-manager .pmsm-script-type-css .pmsm-tag {
	background: #536AD4;
	color: #fff;
}
#perfmatters-script-manager table td.perfmatters-script-manager-size {
	font-size: 14px;
	text-align: center;
	padding-top: 16px;
}
#perfmatters-script-manager table td.perfmatters-script-manager-script a {
	white-space: nowrap;
}
#perfmatters-script-manager .perfmatters-script-manager-script .pmsm-script-handle {
	display: block;
	max-width: 100%;
	overflow: hidden;
	text-overflow: ellipsis;
	font-size: 14px;
	font-weight: bold;
	margin-bottom: 3px;
}
#perfmatters-script-manager .perfmatters-script-manager-script a {
	display: block;
	max-width: 100%;
	overflow: hidden;
	text-overflow: ellipsis;
	font-size: 10px;
	color: #4A89DD;
	line-height: normal;
}
#perfmatters-script-manager .perfmatters-script-manager-disable, #perfmatters-script-manager .perfmatters-script-manager-enable {
	margin: 10px 0px 0px 0px; 
}
#perfmatters-script-manager table .perfmatters-script-manager-disable *:after, #perfmatters-script-manager table .perfmatters-script-manager-disable *:before {
	display: none;
}
#perfmatters-script-manager select {
	display: inline-block;
	position: relative;
	height: auto;
	width: auto;
	background: #ffffff;
	background-color: #ffffff;
	padding: 7px 10px;
	margin: 0px;
	font-size: 14px;
	appearance: menulist;
	-webkit-appearance: menulist;
	-moz-appearance: menulist;
}
#perfmatters-script-manager select.perfmatters-disable-select, #perfmatters-script-manager select.perfmatters-status-select {
	border: 2px solid #27ae60;
}
#perfmatters-script-manager select.perfmatters-disable-select.everywhere, #perfmatters-script-manager select.perfmatters-status-select.disabled {
	border: 2px solid #ED5464;
}
#perfmatters-script-manager select.perfmatters-disable-select.current {
	border: 2px solid #f1c40f;
}
#perfmatters-script-manager select.perfmatters-disable-select.hide {
	display: none;
}
#perfmatters-script-manager .perfmatters-script-manager-enable-placeholder {
	color: #bbbbbb;
	font-style: italic;
	font-size: 14px;
}
#perfmatters-script-manager input[type='radio'] {
	position: static;
	display: inline-block;
	visibility: visible;
	margin: 0px 3px 0px 0px;
	vertical-align: middle;
	opacity: 1;
	z-index: 0;
	appearance: radio;
	-webkit-appearance: radio;
	-moz-appearance: radio;
	vertical-align: baseline;
	height: auto;
	width: auto;
	font-size: 16px;
}
#perfmatters-script-manager input[type='radio']:after {
	display: none;
}
#perfmatters-script-manager .pmsm-checkbox-container {
	display: inline;
}
#perfmatters-script-manager input[type='checkbox'] {
	position: static;
	display: inline-block;
	visibility: visible;
	margin: 0px 3px 0px 0px;
	vertical-align: middle;
	opacity: 1;
	z-index: 0;
	appearance: checkbox;
	-webkit-appearance: checkbox;
	-moz-appearance: checkbox;
	vertical-align: baseline;
	height: auto;
	width: auto;
	font-size: 16px;
	outline: none;
}
#perfmatters-script-manager input[type='checkbox']:before, #perfmatters-script-manager input[type='checkbox']:after {
	display: none;
}
#perfmatters-script-manager .perfmatters-script-manager-controls {
	text-align: left;
}
#perfmatters-script-manager .pmsm-input-group {
	display: flex;
	padding: 3px 0px;
}
#perfmatters-script-manager .pmsm-input-group-label {
	min-width: 70px;
	font-size: 10px;
	font-weight: bold;
	margin: 0px 5px 0px 0px;
	white-space: nowrap;
	color: #282E34;
}
#perfmatters-script-manager .perfmatters-script-manager-controls label {
	display: inline-flex;
	align-items: center;
	margin: 0px 10px 0px 0px;
	width: auto;
	font-size: 12px;
	color: #282E34;
	white-space: nowrap;
}
#perfmatters-script-manager .perfmatters-script-manager-controls input[type='text'], #perfmatters-script-manager .perfmatters-script-manager-controls select {
	padding: 2px;
	width: 100%;
	background: #fff;
	font-size: 12px;
	outline: none;
	border: 1px solid #ccc;
	margin: 0px;
	height: 30px;
	color: #282E34;
	border-radius: unset;
	text-transform: none;
}
#perfmatters-script-manager .pmsm-dependencies {
	font-size: 10px;
	line-height: 14px;
	margin-top: 6px;
}
#perfmatters-script-manager .pmsm-dependencies span {
	font-weight: bold;
}
#perfmatters-script-manager .pmsm-reqs span {
	color: #ED5464;
}
#perfmatters-script-manager .pmsm-group-tag {
	display: inline-block;
    vertical-align: top;
    font-size: 12px;
    background: #fff;
    padding: 2px 6px;
    height: 20px;
    line-height: 20px;
    margin: 8px 7px 8px 0px;
    border-radius: 3px;
}
#perfmatters-script-manager .pmsm-tag {
	display: inline-block;
    font-size: 12px;
    background: #fff;
    padding: 2px 6px;
    border-radius: 3px;
}
#perfmatters-script-manager .pmsm-mu-mode-badge {
	background: #282E34;
	color: #ffffff;
}
#perfmatters-script-manager .perfmatters-script-manager-toolbar {
	position: sticky;
	bottom: 0px;
	left: 0px;
	right: 0px;
	padding: 0px 20px;
	background: #EEF2F5;
	box-sizing: content-box;
	z-index: 2
}
#perfmatters-script-manager .perfmatters-script-manager-toolbar-wrapper {
	position: relative;
	max-width: 1000px;
	margin: 0px auto;
}
#perfmatters-script-manager .perfmatters-script-manager-toolbar-container {
	position: relative;
	background: #EEF2F5;
	display: flex;
	align-items: center;
	justify-content: space-between;
	height: 50px;
	padding: 10px 0px;
	z-index: 2;
}
#perfmatters-script-manager input[type='submit'] {
	background: #4a89dd;
	color: #ffffff;
	cursor: pointer;
	border: none;
	font-size: 14px;
	margin: 0px;
	padding: 0px 20px;
	height: 50px;
	line-height: 50px;
	font-weight: 700;
	width: auto;
	border-radius: 0px;
	text-transform: none;
	outline: none;
}
#perfmatters-script-manager input[type='submit']:hover {
	background: #5A93E0;
}
#perfmatters-script-manager input[type='submit']:disabled {
	opacity: 0.5;
	cursor: default;
}
#script-manager-settings input[type='submit'] {
	float: left;
}
#perfmatters-script-manager input[type='submit'].pmsm-reset {
	float: none;
	background: #ED5464;
	height: 35px;
    line-height: 35px;
    padding: 0px 10px;
    font-size: 12px;
    margin-bottom: 5px;
}
#perfmatters-script-manager input[type='submit'].pmsm-reset:hover {
	background: #c14552;
}
#perfmatters-script-manager .pmsm-message {
	display: block;
	visibility: hidden;
	opacity: 0;
	position: absolute;
    bottom: 00px;
    right: 10px;
    background: #282E34;
    color: #ffffff;
    padding: 10px;
    border-radius: 3px;
    z-index: 1;
	-webkit-transition: all 500ms ease;
	-moz-transition: all 500ms ease;     
	transition: all 500ms ease;
}
#perfmatters-script-manager .pmsm-message.pmsm-fade {
	visibility: visible;
	opacity: 1;
	bottom: 80px;
}
/* On/Off Toggle Switch */
#perfmatters-script-manager .perfmatters-script-manager-switch {
	position: relative;
	display: inline-block;
	width: 76px;
	height: 40px;
	font-size: 1px;
	margin: 0px;
}
#perfmatters-script-manager .perfmatters-script-manager-switch input[type='checkbox'] {
	display: block;
	margin: 0px;
}
#perfmatters-script-manager .perfmatters-script-manager-slider {
	position: absolute;
	cursor: pointer;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #27ae60;
	-webkit-transition: .4s;
	transition: .4s;
}
#perfmatters-script-manager .perfmatters-script-manager-slider:before {
	position: absolute;
	content: '';
	/*height: 20px;
	width: 20px;
	right: 4px;
	bottom: 4px;*/
	width: 30px;
	top: 5px;
	right: 5px;
	bottom: 5px;
	background-color: white;
	-webkit-transition: .4s;
	transition: .4s;
}
#perfmatters-script-manager .perfmatters-script-manager-switch input:checked + .perfmatters-script-manager-slider {
	background-color: #ED5464;
}
#perfmatters-script-manager .perfmatters-script-manager-switch input:focus + .perfmatters-script-manager-slider {
	box-shadow: 0 0 1px #ED5464;
}
#perfmatters-script-manager .perfmatters-script-manager-switch input:checked + .perfmatters-script-manager-slider:before {
	-webkit-transform: translateX(-36px);
	-ms-transform: translateX(-36px);
	transform: translateX(-36px);
}

#perfmatters-script-manager .perfmatters-script-manager-slider:after {
	content:'" . __('ON', 'perfmatters') . "';
	color: white;
	display: block;
	position: absolute;
	transform: translate(-50%,-50%);
	top: 50%;
	left: 27%;
	font-size: 10px;
	font-family: Verdana, sans-serif;
}

#perfmatters-script-manager .perfmatters-script-manager-switch input:checked + .perfmatters-script-manager-slider:after {  
	left: unset;
	right: 0%;
  	content:'" . __('OFF', 'perfmatters') . "';
}

#perfmatters-script-manager .perfmatters-script-manager-assets-disabled p {
	margin: 20px 0px 0px 0px;
	text-align: center;
	font-size: 12px;
	padding: 10px 0px 0px 0px;
	border-top: 1px solid #f8f8f8;
}
/*Global View*/
#pmsm-global-form .perfmatters-script-manager-section tbody tr:hover td {
	background: #EDF3F9;
}
.pmsm-action-button, .pmsm-action-button:hover, .pmsm-action-button:focus, .pmsm-action-button:active {
	background: none;
	border: none;
	padding: 0px;
	margin: 0px;
	height: auto;
	width: auto;
	line-height: normal;
}
.pmsm-action-button .dashicons-trash {
	color: #ED5464;
}
.pmsm-action-button:hover .dashicons-trash {
	color: #c14552;
}
/*Settings View*/
#script-manager-settings table th {
	width: 200px;
	vertical-align: top;
	padding: 8px 5px;
	border: none;
}
#script-manager-settings .perfmatters-switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 28px;
  font-size: 1px;
  margin-bottom: 5px;
}
#script-manager-settings .perfmatters-switch input {
  display: block;
  margin: 0px;
}
#script-manager-settings .perfmatters-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}
#script-manager-settings .perfmatters-slider:before {
  position: absolute;
  content: '';
  height: 20px;
  width: 20px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}
#script-manager-settings input:checked + .perfmatters-slider {
  background-color: #2196F3;
}

#script-manager-settings input:focus + .perfmatters-slider {
  box-shadow: 0 0 1px #2196F3;
}
#script-manager-settings input:checked + .perfmatters-slider:before {
  -webkit-transform: translateX(20px);
  -ms-transform: translateX(20px);
  transform: translateX(20px);
}
.perfmatters-beta {
  background: #ED5464;
  color: #ffffff;
  padding: 5px;
  vertical-align: middle;
  font-size: 10px;
  margin-left: 3px;
}
#script-manager-settings .pmsm-mu-found, #script-manager-settings .pmsm-mu-missing, #script-manager-settings .pmsm-mu-mismatch {
    margin-top: 7px;
    font-weight: bold;
}
#script-manager-settings .pmsm-mu-found {
	color: #27ae60;
}
#script-manager-settings .pmsm-mu-missing {
	color: #ED5464;
}
#script-manager-settings .pmsm-mu-mismatch {
	color: #F6BF27;
}
#jquery-message {
	font-size: 12px;
	font-style: italic;
	color: #27ae60;
	margin-top: 5px;
}
@media (max-width: 1000px) {
	#perfmatters-script-manager-header:not(.pmsm-header-minimal) {
	width: 60px;
		min-width: 60px;
	}
	#perfmatters-script-manager-header:not(.pmsm-header-minimal) #perfmatters-logo {
		display: none;
	}
}
@media (max-width: 782px) {
	#perfmatters-script-manager-wrapper {
		top: 46px;
		padding-top: 60px;
	}

	#perfmatters-script-manager-header {
		width: 100% !important;
	    min-width: 100% !important;
	    position: absolute;
	    top: 0px;
	    z-index: 9999;
	    height: 60px;
	    overflow: hidden;
	}

	#pmsm-header-hero {
		justify-content: space-between;
	}

	#perfmatters-logo {
		display: block !important;
		margin-right: 20px;
	}

	#perfmatters-script-manager-container .perfmatters-script-manager-title-bar {
		flex-direction: column;
	}
	#perfmatters-script-manager-container .perfmatters-script-manager-title-bar h1 {
		margin-bottom: 5px;
	}
	#perfmatters-script-manager .pmsm-column-status {
		width: 90px;
	}
	#perfmatters-script-manager .pmsm-column-type, #perfmatters-script-manager .pmsm-column-size {
		width: 70px;
	}
}

#pmsm-loading-wrapper {
	position: absolute;
    top: 48px;
    bottom: 0;
    padding-top: 200px;
    width: 100%;
    background: rgba(255,255,255,0.75);
    text-align: center;
    z-index: 1;
}
#pmsm-loading-wrapper .pmsm-loading-text {
	font-size: 24px; 
}
.pmsm-spinner {
	background: url(/wp-admin/images/wpspin_light-2x.gif) no-repeat;
    background-size: 16px 16px;
    display: none;
    vertical-align: middle;
    width: 16px;
    height: 16px;
    margin: 0px 0px 0px 5px;
}
#pmsm-loading-wrapper .pmsm-spinner {
	display: inline-block;
    background-size: 32px 32px;
    width: 32px;
    height: 32px;
}
#perfmatters-script-manager .pmsm-hide {
	display: none;
}
</style>";