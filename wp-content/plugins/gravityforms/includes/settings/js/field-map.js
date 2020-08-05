/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/src/field-map/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/src/field-map/index.js":
/*!***********************************!*\
  !*** ./js/src/field-map/index.js ***!
  \***********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _mapping__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./mapping */ "./js/src/field-map/mapping.js");
/**
 * WordPress dependencies
 */
const { Component, render } = wp.element;

/**
 * Internal dependencies
 */


class FieldMap extends Component {

	constructor() {

		super(...arguments);

		this.state = {
			mapping: JSON.parse(document.querySelector(`[name="${this.props.input}"]`).value)
		};

		this.addMapping = this.addMapping.bind(this);
		this.deleteMapping = this.deleteMapping.bind(this);
		this.getMapping = this.getMapping.bind(this);
		this.updateMapping = this.updateMapping.bind(this);
	}

	componentDidMount() {

		this.populateRequiredMappings();

		// Ensure there is at least one item.
		if (this.getRequiredChoices().length === 0 && this.getMapping().length < 1) {
			this.addMapping(0);
		}
	}

	// # MAPPING DATA METHODS ------------------------------------------------------------------------------------------

	/**
  * Add a new mapping item.
  *
  * @param {integer} index Index to add item at.
  */
	addMapping(index) {

		const { allow_custom, choices } = this.props.keyField;

		let mapping = this.getMapping(),
		    key = choices.length === 0 && allow_custom ? 'gf_custom' : '';

		mapping.splice(index + 1, 0, {
			key: key,
			custom_key: '',
			value: '',
			custom_value: ''
		});

		this.setMapping(mapping);
	}

	/**
  * Remove a mapping item.
  *
  * @param {integer} index Index of item to remove.
  */
	deleteMapping(index) {

		let mapping = this.getMapping();

		mapping.splice(index, 1);

		this.setMapping(mapping);
	}

	/**
  * Get current mappings.
  *
  * @returns {array}
  */
	getMapping() {

		return this.state.mapping;
	}

	/**
  * Set current mappings.
  *
  * @param {object} mapping Collection of field mappings.
  */
	setMapping(mapping) {

		const { input } = this.props;

		this.setState({ mapping });

		document.querySelector(`[name="${input}"]`).value = JSON.stringify(mapping);
	}

	/**
  * Update a mapping item.
  *
  * @param {object} item Mapping item.
  * @param {integer} index Index of item to update.
  */
	updateMapping(item, index) {

		let mapping = this.getMapping();

		if (!item.key) {
			item.value = '';
		}

		mapping[index] = item;

		this.setMapping(mapping);
	}

	// # CHOICE METHODS ------------------------------------------------------------------------------------------------

	/**
  * Get choice properties by name.
  *
  * @param {string} name Choice name.
  *
  * @returns {boolean|object}
  */
	getChoice(name) {

		const { choices } = this.props.keyField;

		for (let i = 0; i < choices.length; i++) {
			if (choices[i].name === name) {
				return choices[i];
			}
		}

		return false;
	}

	/**
  * Get names of mapped choices.
  */
	getMappedChoices() {

		const mapping = this.getMapping();

		return mapping.filter(m => m.key && m.key !== 'gf_custom').map(m => m.key);
	}

	/**
  * Get names of required choices.
  *
  * @returns {array}
  */
	getRequiredChoices() {

		const { choices, display_all } = this.props.keyField;

		return choices.filter(choice => choice.required || display_all).map(choice => choice.name);
	}

	/**
  * Populate mapping with required choices.
  */
	populateRequiredMappings() {

		const mapping = this.getMapping();
		const requiredChoices = this.getRequiredChoices();

		// Get mapped fields.
		const mappedFields = mapping.map(mapping => mapping.key);

		// Loop through required choices. If not mapped, add to mapping.
		for (let i = 0; i < requiredChoices.length; i++) {

			// If field is mapped, skip.
			if (mappedFields.includes(requiredChoices[i])) {
				continue;
			}

			// Add to mapping.
			mapping.push({
				key: requiredChoices[i],
				custom_key: '',
				value: '',
				custom_value: ''
			});
		}

		// Auto populate default values.
		for (let i = 0; i < mapping.length; i++) {
			// If field have a stored value already, skip.
			if (mapping[i].value !== '') {
				continue;
			}

			let choice = this.getChoice(mapping[i].key);
			// If choice have a default value, get it and set it as value.
			if (choice && 'default_value' in choice) {
				mapping[i].value = choice.default_value;
			}
		}

		this.setMapping(mapping);
	}

	// # RENDER METHODS ------------------------------------------------------------------------------------------------

	render() {

		const { keyField, invalidChoices, limit, valueField } = this.props;
		const mapping = this.getMapping();

		return React.createElement(
			'table',
			{ className: 'gform-settings-generic-map__table', cellSpacing: '0', cellPadding: '0' },
			React.createElement(
				'tbody',
				null,
				mapping.map((m, index) => {

					let selectedChoice = this.getChoice(m.key);

					return React.createElement(_mapping__WEBPACK_IMPORTED_MODULE_0__["default"], {
						key: index,
						mapping: m,
						choice: selectedChoice,
						mappedChoices: this.getMappedChoices(),
						isInvalid: m.key && invalidChoices.includes(m.key),
						keyField: keyField,
						valueField: valueField,
						canAdd: keyField.allow_custom && (limit === 0 || mapping.length <= limit) || !keyField.allow_custom && mapping.length < keyField.choices.length,
						canDelete: mapping.length > 1 && !selectedChoice.required && !keyField.display_all,
						addMapping: this.addMapping,
						deleteMapping: this.deleteMapping,
						updateMapping: this.updateMapping,
						index: index
					});
				})
			)
		);
	}

}

window.initializeFieldMap = (container, props) => {

	render(React.createElement(FieldMap, props), document.getElementById(container));
};

/***/ }),

/***/ "./js/src/field-map/mapping.js":
/*!*************************************!*\
  !*** ./js/src/field-map/mapping.js ***!
  \*************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Mapping; });
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

/**
 * WordPress dependencies
 */
const { Component, Fragment } = wp.element;
const { __ } = wp.i18n;

class Mapping extends Component {

	render() {

		const { isInvalid } = this.props;

		return React.createElement(
			"tr",
			null,
			React.createElement(
				"td",
				{ className: "gform-settings-generic-map__column gform-settings-generic-map__column--key" },
				this.getKeyInput()
			),
			React.createElement(
				"td",
				{ className: "gform-settings-generic-map__column gform-settings-generic-map__column--value" },
				this.getValueInput()
			),
			React.createElement(
				"td",
				{ className: "gform-settings-generic-map__column gform-settings-generic-map__column--error" },
				isInvalid && React.createElement(
					"svg",
					{ width: "22", height: "22", fill: "none", xmlns: "http://www.w3.org/2000/svg" },
					React.createElement("path", { d: "M11 22C4.9249 22 0 17.0751 0 11S4.9249 0 11 0s11 4.9249 11 11-4.9249 11-11 11z", fill: "#E54C3B" }),
					React.createElement("path", { fillRule: "evenodd", clipRule: "evenodd", d: "M9.9317 5.0769a.1911.1911 0 00-.1909.2006l.3708 7.4158a.8895.8895 0 001.7768 0l.3708-7.4158a.1911.1911 0 00-.1909-.2006H9.9317zm2.3375 10.5769c0 .701-.5682 1.2693-1.2692 1.2693-.701 0-1.2692-.5683-1.2692-1.2693 0-.7009.5682-1.2692 1.2692-1.2692.701 0 1.2692.5683 1.2692 1.2692z", fill: "#fff" })
				)
			),
			React.createElement(
				"td",
				{ className: "gform-settings-generic-map__column gform-settings-generic-map__column--buttons" },
				this.getAddButton(),
				this.getDeleteButton()
			)
		);
	}

	// # KEY COLUMN ----------------------------------------------------------------------------------------------------

	/**
  * Prepare input for key column.
  * If choice is required, returns only text.
  *
  * @returns {*}
  */
	getKeyInput() {

		const { choice, index, mapping, updateMapping } = this.props;
		const { choices, display_all, placeholder } = this.props.keyField;

		// If currently selected choice is required or we are displaying all keys, display label.
		if (choice.required || display_all) {
			return React.createElement(
				"label",
				null,
				choice.label,
				" ",
				choice.required ? React.createElement(
					"span",
					{ className: "required" },
					"*"
				) : null
			);
		}

		// If selected choice is custom key, display input.
		if (mapping.key === 'gf_custom') {

			return React.createElement(
				"span",
				{ className: "gform-settings-generic-map__custom" },
				React.createElement("input", { type: "text", value: mapping.custom_key, placeholder: placeholder, onChange: e => updateMapping(_extends({}, mapping, { custom_key: e.target.value }), index) }),
				choices.length > 0 && React.createElement(
					"button",
					{ className: "gform-settings-generic-map__reset", onClick: e => {
							e.preventDefault();updateMapping(_extends({}, mapping, { key: '', custom_key: '' }), index);
						} },
					React.createElement(
						"span",
						{ className: "screen-reader-text" },
						__('Remove Custom Key', 'gravityforms')
					)
				)
			);
		}

		return React.createElement(
			"select",
			{
				value: mapping.key,
				onChange: e => updateMapping(_extends({}, mapping, { key: e.target.value }), index)
			},
			this.getKeyOptions().map(opt => {
				if (opt.choices && opt.choices.length > 0) {
					return React.createElement(
						"optgroup",
						{ key: opt.label, label: opt.label },
						opt.choices.map(o => React.createElement(
							"option",
							{ key: o.value, disabled: opt.disabled, value: o.value },
							o.label
						))
					);
				} else {
					return React.createElement(
						"option",
						{ key: opt.value, disabled: opt.disabled, value: opt.value },
						opt.label
					);
				}
			})
		);
	}

	/**
  * Get options for key drop down.
  *
  * @returns {{label: string, value: string, disabled: boolean}[]}
  */
	getKeyOptions() {

		const { keyField, mappedChoices, mapping } = this.props;
		const { allow_custom, allow_duplicates, choices } = keyField;
		const choiceNames = choices.map(c => c.name || c.value);

		// Initialize options array.
		let options = [];
		if (!choiceNames.includes('')) {
			options.push({
				label: __('Select a Field', 'gravityforms'),
				value: '',
				disabled: false
			});
		}

		// Loop through choices, add as options.
		for (let i = 0; i < choices.length; i++) {

			let choice = choices[i],
			    choice_value = choice.name || choice.value;

			// If this is a required choice, do not add as an option.
			if (choice.required) {
				continue;
			}

			// If choice is already selected, disable it.
			let disabled = mappedChoices.includes(choice_value) && choice_value !== mapping.key && !allow_duplicates;

			options.push({
				label: choice.label,
				value: choice_value,
				choices: choice.choices || [],
				disabled: disabled
			});
		}

		// Add custom key if enabled and is not already present.
		if (allow_custom && !choiceNames.includes('gf_custom')) {
			options.push({
				label: __('Add Custom Key', 'gravityforms'),
				value: 'gf_custom',
				disabled: false
			});
		}

		return options;
	}

	// # VALUE COLUMN --------------------------------------------------------------------------------------------------

	/**
  * Prepare input for value column.
  *
  * @returns {*}
  */
	getValueInput() {

		const { index, isInvalid, mapping, updateMapping, valueField } = this.props;

		// If selected value is custom value, display input.
		if (mapping.value === 'gf_custom') {

			return React.createElement(
				"span",
				{ className: "gform-settings-generic-map__custom" },
				React.createElement("input", { type: "text", value: mapping.custom_value, placeholder: valueField.placeholder, onChange: e => updateMapping(_extends({}, mapping, { custom_value: e.target.value }), index) }),
				React.createElement(
					"button",
					{ className: "gform-settings-generic-map__reset", onClick: e => {
							e.preventDefault();updateMapping(_extends({}, mapping, { value: '', custom_value: '' }), index);
						} },
					React.createElement(
						"span",
						{ className: "screen-reader-text" },
						__('Remove Custom Value', 'gravityforms')
					)
				)
			);
		}

		return React.createElement(
			"select",
			{
				disabled: mapping.key === '' || !mapping.key,
				value: mapping.value,
				onChange: e => updateMapping(_extends({}, mapping, { value: e.target.value }), index),
				className: isInvalid ? 'gform-settings-generic-map__value--invalid' : ''
			},
			this.getValueOptions().map(opt => {
				if (opt.choices && opt.choices.length > 0) {
					return React.createElement(
						"optgroup",
						{ key: opt.label, label: opt.label },
						opt.choices.map(o => React.createElement(
							"option",
							{ key: o.value, value: o.value },
							o.label
						))
					);
				} else {
					return React.createElement(
						"option",
						{ key: opt.value, value: opt.value },
						opt.label
					);
				}
			})
		);
	}

	/**
  * Get options for value drop down.
  *
  * @returns {{label: *, value: boolean}[]}
  */
	getValueOptions() {

		const { choice, valueField } = this.props;
		const { allow_custom } = valueField;
		let choices = choice.choices || valueField.choices;

		let values = choices.map(c => c.value);

		// Add custom key if enabled and is not already present.
		if (allow_custom && !values.includes('gf_custom')) {
			choices.push({
				label: __('Add Custom Value', 'gravityforms'),
				value: 'gf_custom',
				disabled: false
			});
		}

		return choices;
	}

	// # BUTTONS -------------------------------------------------------------------------------------------------------

	/**
  * Get add mapping button.
  *
  * @returns {null|*}
  */
	getAddButton() {

		const { canAdd, addMapping, index } = this.props;

		// If mapping cannot be added, do not show button.
		if (!canAdd) {
			return null;
		}

		return React.createElement(
			"button",
			{ className: "gform-settings-generic-map__button gform-settings-generic-map__button--add", onClick: e => {
					e.preventDefault();addMapping(index);
				} },
			React.createElement(
				"span",
				{ className: "screen-reader-text" },
				__('Add', 'gravityforms')
			)
		);
	}

	/**
  * Get delete mapping button.
  *
  * @returns {null|*}
  */
	getDeleteButton() {

		const { canDelete, deleteMapping, index } = this.props;

		// If mapping cannot be deleted, do not show button.
		if (!canDelete) {
			return null;
		}

		return React.createElement(
			"button",
			{ className: "gform-settings-generic-map__button gform-settings-generic-map__button--delete", onClick: e => {
					e.preventDefault();deleteMapping(index);
				} },
			React.createElement(
				"span",
				{ className: "screen-reader-text" },
				__('Delete', 'gravityforms')
			)
		);
	}

}

/***/ })

/******/ });
//# sourceMappingURL=field-map.js.map