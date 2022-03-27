import deburr from 'lodash/deburr';
import Promise from 'promise-polyfill';
import scrollparent from 'scrollparent';

const { jQuery: $ } = window;

/* Polyfills */
if (!Object.entries) {
	Object.entries = function (obj: any) {
		var ownProps = Object.keys(obj),
			i = ownProps.length,
			resArray = new Array(i); // preallocate the Array
		while (i--)
			resArray[i] = [ownProps[i], obj[ownProps[i]]];

		return resArray;
	};
}

// Callback to be fired by the Google <script> when the Places API and Maps JavaScript API are ready.
window.gpaaInit = () => {
	const event = new CustomEvent('gpaa_api_ready');
	document.dispatchEvent(event);

	window.gpaaReady = true;
}

document.addEventListener('gpaa_api_ready', () => {
	console.debug('GP Address Autocomplete: Google Maps API ready.');
});

interface GPAAParsedAdrMicroformat {
	'post-office-box'?: string;
	'extended-address'?: string;
	'street-address'?: string;
	'locality': string;
	'region'?: string;
	'postal-code'?: string;
	'country-name': string;
}

interface GPAAInputsSelectors {
	autocomplete: string; // Defaults to be the same as Address Line 1
	address1: string;
	address2: string | undefined;
	postalCode: string | undefined;
	city: string | undefined;
	stateProvince: string | undefined;
	country: string | undefined;
}

interface GPAAInputs {
	autocomplete: HTMLInputElement; // Defaults to be the same as Address Line 1
	address1: HTMLInputElement;
	address2: HTMLInputElement | undefined;
	postalCode: HTMLInputElement | undefined;
	city: HTMLInputElement | undefined;
	stateProvince: HTMLInputElement | undefined;
	country: HTMLSelectElement | undefined;
}

class GP_Address_Autocomplete {
	public autocomplete: google.maps.places.Autocomplete | undefined;
	public autocompleteListener: google.maps.MapsEventListener | undefined;
	public pacContainer: HTMLElement | undefined;
	public pacContainerStyleObserver: MutationObserver | undefined;

	public formId: number;
	public fieldId: number;
	public inputSelectors: GPAAInputsSelectors;
	public inputs: GPAAInputs;
	public addressType: string;
	public interval: NodeJS.Timer | undefined;

	/**
	 * Key/value store of any GPAA instances on the page. Useful for re-initializing using existing instances and
	 * preventing pac-containers from doubling-up in use-cases such as GPNF when re-opening the modal multiple times.
	 */
	public static instances: { [formIdFieldId: string]: GP_Address_Autocomplete } = {};

	/**
	 * Lockout flag to be used amongst all instances to only initialize one Address Autocomplete at a time.
	 *
	 * The sole reason for this lockout is due to us needing to use a MutationObserver to get the .pac-container div
	 * that gets created when the Places Autocomplete is initialized. If multiple Address Autocompletes are initializing
	 * at the same time without this lockout, there's a high likelihood that pacContainer will get set incorrectly.
	 *
	 * I tried various ways of trying to get the
	 * .pac-container out of the google.maps.places.Autocomplete instances including recursively looping to no avail.
	 */
	public static multiInstanceLockout: boolean = false;

	constructor(opts: Pick<GP_Address_Autocomplete, 'formId' | 'fieldId' | 'addressType'> & { inputSelectors: GPAAInputsSelectors }) {
		this.formId = opts.formId;
		this.fieldId = opts.fieldId;
		this.inputSelectors = opts.inputSelectors;
		this.inputs = this.getInputEls(this.inputSelectors); // we also get the inputEls in init()
		this.addressType = opts.addressType;

		if (!window.gpaaReady) {
			document.addEventListener('gpaa_api_ready', () => this.queueInit());

			return;
		}

		this.queueInit();
	}

	/**
	 * Use an interval to try initializing every 25ms until multiInstanceLockout is set to false.
	 *
	 * @see multiInstanceLockout
	 */
	queueInit = () => {
		this.interval = setInterval(() => this.init(), 25);
	};

	init() {
		if (GP_Address_Autocomplete.multiInstanceLockout) {
			return;
		}

		clearInterval(this.interval!);

		/**
		 * multiInstanceLockout is set to false after the pacContainer is set in initPac.
		 *
		 * @see initPac
		 */
		GP_Address_Autocomplete.multiInstanceLockout = true;

		/* Re-use existing instance if its present. */
		if (typeof GP_Address_Autocomplete.instances[`${this.formId}-${this.fieldId}`] !== 'undefined') {
			GP_Address_Autocomplete.instances[`${this.formId}-${this.fieldId}`].initPac();
			return;
		}

		GP_Address_Autocomplete.instances[`${this.formId}-${this.fieldId}`] = this;

		this.initPac();
		this.bindGPPAListener();

		/**
		 * Action that fires after Address Autocomplete has been initialized on the frontend.
		 *
		 * @param {GP_Address_Autocomplete} instance Current instance of the class.
		 * @param {int} formId The current form ID.
		 * @param {int} fieldId The current field ID.
		 *
		 * @since 1.0
		 */
		window.gform.doAction('gpaa_init', this, this.formId, this.fieldId);
	}

	/**
	 * Bind a listener to gppa_updated_batch_fields jQuery event that will reinitialize Address Autocomplete when
	 * the Address field markup is replaced.
	 */
	bindGPPAListener() {
		$(document).on('gppa_updated_batch_fields', (event, formId, updatedFieldIds) => {
			if (parseInt(formId) !== this.formId) {
				return;
			}

			updatedFieldIds = updatedFieldIds.map((fieldId: string) => parseInt(fieldId));

			if (updatedFieldIds.indexOf(this.fieldId) === -1) {
				return;
			}

			this.init();
		});
	}

	getInputEls(selectors: GPAAInputsSelectors) : GPAAInputs {
		const inputs: { [inputName: string]: any } = {};

		for ( const [inputName, selector] of Object.entries(selectors) ) {
			inputs[inputName] = document.querySelector(selector);
		}

		return inputs as GPAAInputs;
	}

	get autocompleteOptions() : google.maps.places.AutocompleteOptions {
		const { allowed_countries: allowedCountries } = window.GP_ADDRESS_AUTOCOMPLETE_CONSTANTS;

		const autocompleteOptions: google.maps.places.AutocompleteOptions = {
			componentRestrictions: undefined,
			fields: [
				'address_components', // Individual components of the address to be populated into address field inputs
				'formatted_address', // String representation of the address
				'geometry', // Coordinates
				'adr_address', // Similar to formatted_address but uses ADR microformat which we can use to extract components.
			],
			types: ['address'],
		};

		// Restrict country to Canada if Address Field type is configured to United States
		if (this.addressType === 'us') {
			autocompleteOptions.componentRestrictions = {
				country: ['us'],
			}
		// Restrict country to Canada if Address Field type is configured to Canadian
		} else if ( this.addressType === 'canadian') {
			autocompleteOptions.componentRestrictions = {
				country: ['ca'],
			}
		// Restrict to countries set in settings
		} else if ( allowedCountries?.length ) {
			autocompleteOptions.componentRestrictions = {
				country: allowedCountries,
			}
		}

		/**
		 * Filter to change the autocomplete options used to initialize Google Places API Autocomplete.
		 *
		 * @param {google.maps.places.AutocompleteOptions} autocompleteOptions Options used to initialize Places API Autocomplete.
		 * @param {GP_Address_Autocomplete} instance Current instance of the class.
		 * @param {int} formId The current form ID.
		 * @param {int} fieldId The current field ID.
		 *
		 * @returns {google.maps.places.AutocompleteOptions} Filtered options used to initialized Places API Autocomplete.
		 *
		 * @since 1.0
		 */
		return window.gform.applyFilters('gpaa_autocomplete_options', autocompleteOptions, this, this.formId, this.fieldId);
	}

	initPac = () : void => {
		// Remove existing pac-container since a new one will be created
		this.pacContainer?.remove();

		// Get fresh input elements in case this method is called in the future and the input els were replaced (GPPA).
		this.inputs = this.getInputEls(this.inputSelectors);

		if (this.autocomplete) {
			google.maps.event.clearInstanceListeners(this.autocomplete);
		}

		if (this.autocompleteListener) {
			google.maps.event.removeListener(this.autocompleteListener);
		}

		this.waitForPacContainer()
			.then(() => {
				// Now that we have the pacContainer from the MutationObserver, remove the lockout so other GPAA
				// instances can initialize.
				GP_Address_Autocomplete.multiInstanceLockout = false;

				// Add class to pac-container to prevent future MutationObservers from accidentally using it.
				this.pacContainer?.classList.add('pac-container-gpaa');

				// Add fixed positioner to handle reposition the PAC container if it's in a fixed div
				// such as GPNF Tingle.
				this.addFixedPositioner();
			});

		this.autocomplete = new google.maps.places.Autocomplete(this.inputs.autocomplete, this.autocompleteOptions);

		// Prevent enter from submitting the form when pressing enter in auto complete.
		window.google.maps.event.addDomListener(this.inputs.autocomplete, 'keydown', (e: KeyboardEvent) => {
			let pacContainerVisible = false;

			document.querySelectorAll('.pac-container').forEach((container) => {
				if (!(window.getComputedStyle(container).display === 'none')) {
					pacContainerVisible = true;
				}
			});

			if ((e.code === 'Enter' || e.keyCode === 13) && pacContainerVisible) {
				e.preventDefault();
			}
		});

		// When the user selects an address, populate the address inputs in the field.
		this.autocompleteListener = this.autocomplete.addListener('place_changed', this.fillFields);

		// Store coordinates in hidden input. Utilize action so this can be unhooked if desired.
		window.gform.addAction('gpaa_fields_filled', (place: google.maps.places.PlaceResult, instance: GP_Address_Autocomplete) => {
			if (instance !== this) {
				return;
			}

			this.fillCoordinatesMetaInput(place);
		});
	};

	/**
	 * Add a mutation observer to detect when the pac-container is added by Google Places Autocomplete.
	 *
	 * This is needed as it's nearly impossible to reliably pull the pac-container element out of the autocomplete
	 * instance.
	 */
	waitForPacContainer = () : Promise<HTMLElement> => {
		return new Promise((resolve: (value: any) => void, reject: (reason?: any) => void) => {
			const observer = new MutationObserver((mutations) => {
				mutations.forEach((mutation) => {
					mutation.addedNodes.forEach((addedNode) => {
						if (!(addedNode instanceof HTMLElement)) {
							return;
						}

						if (addedNode.classList.contains('pac-container') && !addedNode.classList.contains('pac-container-gpaa')) {
							this.pacContainer = addedNode;
							resolve(addedNode);

							observer.disconnect();
						}
					});
				});
			});

			/* Give up on finding the container after 1 second. */
			setTimeout(() => {
				reject(new Error('GPAA: pac-container not found.'));
			}, 1000);

			observer.observe(document, {attributes: false, childList: true, characterData: false, subtree:true});
		});
	}

	/**
	 * Google PAC does not work well if the autocomplete input is in any offset parent outside of the root document.
	 *
	 * This method will move the pacContainer to the offset parent (Tingle in the case of GPNF) and adds
	 * the following:
	 *  * Mutation Observer to watch when the style of the PAC container changes and auto-positions when it does
	 *  * Binds to the scroll event of the scroll parent and document and auto repositions when they scroll
	 */
	addFixedPositioner = () : void => {
		const scrollParent = scrollparent(this.inputs.autocomplete)!;

		if ($(scrollParent).is('html')) {
			return;
		}

		$(this.pacContainer!).appendTo(scrollParent);

		if (this.pacContainerStyleObserver) {
			this.pacContainerStyleObserver.disconnect();
		}

		/**
		 * Flag to lockout the mutation observer from repositioning when the page/scrollparent is scrolled. Without
		 * this, an infinite loop will ensue.
		 */
		let mutationObserverLockout: boolean = false;

		this.pacContainerStyleObserver = new MutationObserver((mutations) => {
			if (mutationObserverLockout) {
				return;
			}

			this.positionPacContainer();
		});

		scrollParent.addEventListener('scroll', () => {
			mutationObserverLockout = true;
			this.positionPacContainer();
			mutationObserverLockout = false;
		});

		document.addEventListener('scroll', () => {
			mutationObserverLockout = true;
			this.positionPacContainer();
			mutationObserverLockout = false;
		});

		this.pacContainerStyleObserver.observe(this.pacContainer as Node, {
			attributes: true,
			attributeFilter: ['style'],
		});
	}

	/**
	 * Position the pac-container to be directly below the autocomplete input.
	 * The default position behavior does not work if the autocomplete dropdown is in a modal.
	 */
	positionPacContainer = () : void => {
		if (!this.pacContainer) {
			return;
		}

		const inputOffset = $(this.inputs.autocomplete).offset();
		const inputHeight = $(this.inputs.autocomplete).outerHeight();

		if (window.getComputedStyle(this.pacContainer).display === 'none') {
			return;
		}

		this.pacContainerStyleObserver?.disconnect();

		this.pacContainer.style.setProperty('position', 'fixed', 'important');
		this.pacContainer.style.top = (inputOffset!.top + inputHeight!) + 'px';
		this.pacContainer.style.left = (inputOffset!.left) + 'px';

		setTimeout(() => {
			this.pacContainerStyleObserver?.observe(this.pacContainer as Node, {
				attributes: true,
				attributeFilter: ['style'],
			});
		}, 50);
	};

	fillFields = () : void => {
		if (!this.autocomplete) {
			console.warn('GP Address Autocomplete: Google API not ready.')
			return;
		}

		// Get the place details from the autocomplete object.
		const place: google.maps.places.PlaceResult = this.autocomplete.getPlace();
		const adrParsed = this.parseAdrAddressHTML(place.adr_address!);

		// Use parsed adr_address for most of the components as it respects the local formatting of addresses much
		// better than trying to piece together the individual address_components which differ from region-to-region.
		let values = {
			address1: adrParsed["street-address"],
			address2: adrParsed["extended-address"],
			postcode: adrParsed["postal-code"],
			city: adrParsed.locality,
			stateProvince: adrParsed.region,
			country: '',
			autocomplete: undefined, // Intended to be populated using the gpaa_values JS filter.
		}

		// Augment the parsed ADR address with the address components as some of the items in the ADR address come through
		// as the short_name instead of long_name.
		for (const component of place.address_components as google.maps.GeocoderAddressComponent[]) {
			const componentType = component.types[0];

			switch (componentType) {
				// Use locality as city if the city is not present in the ADR. This is necessary for Singapore.
				case 'locality':
					if (!values.city) {
						values.city = component.long_name;
					}
					break;

				case 'administrative_area_level_2':
				case 'administrative_area_level_1':
					if (component.short_name === values.stateProvince) {
						values.stateProvince = component.long_name;

						/*
						 * Try various formats of the State/Province as the default Gravity Forms province selector for
						 * Canada is ASCII / basic Latin only.
						 */
						if (this.inputs.stateProvince?.tagName.toLowerCase() === 'select') {
							if (!this.inputs.stateProvince.querySelector(`option[value="${values.stateProvince}"]`)) {
								values.stateProvince = deburr(values.stateProvince);
							}
						}
					}
					break;

				case 'country':
					/*
					 * The long_name of the country may not always match up with what Gravity Forms is outputting.
					 * Reasons include the browser being set to a language that isn't the same as the website.
					 *
					 * To work around this, we take the short_name (abbreviation) of the country and find the
					 * long_name in Gravity Forms' countries array.
					 */
					const { countries } = window.GP_ADDRESS_AUTOCOMPLETE_CONSTANTS;

					/**
					 * Depending on the website setup, the values of the select can be abbreviations rather than the
					 * long name.
					 *
					 * Go through and try populating with each.
					 */
					const googleShortName = component.short_name;
					const googleLongName = component.long_name;
					const gravityFormsLongName = countries?.[component.short_name];

					if (this.inputs.country) {
						if (this.inputs.country.querySelector(`option[value="${googleShortName}"]`)) {
							values.country = googleShortName;
						} else if (this.inputs.country.querySelector(`option[value="${googleLongName}"]`)) {
							values.country = googleLongName;
						} else if (this.inputs.country.querySelector(`option[value="${gravityFormsLongName}"]`)) {
							values.country = gravityFormsLongName;
						}
					}

					if (!values.country) {
						values.country = gravityFormsLongName;
					}

					break;
			}
		}

		/**
		 * Filter the formatted values after a place has been selected. Use this to change the format of individual
		 * values such as Address 1, City, etc.
		 *
		 * @param {Object} values The values to be populated into address inputs.
		 * @param {google.maps.places.PlaceResult} place The place selected.
		 * @param {GP_Address_Autocomplete} instance Current instance of the class.
		 * @param {int} formId The current form ID.
		 * @param {int} fieldId The current field ID.
		 *
		 * @returns {Object} The filtered values to be populated into address inputs.
		 *
		 * @since 1.0
		 */
		values = window.gform.applyFilters('gpaa_values', values, place, this, this.formId, this.fieldId);

		if (this.inputs.autocomplete) {
			this.inputs.autocomplete.value = values.autocomplete ?? '';
			this.triggerChange(this.inputs.autocomplete);
		}

		if (this.inputs.address1) {
			this.inputs.address1.value = values.address1 ?? '';
			this.triggerChange(this.inputs.address1);
		}

		if (this.inputs.address2) {
			this.inputs.address2.value = values.address2 ?? '';
			this.triggerChange(this.inputs.address2);
		}

		if (this.inputs.city) {
			this.inputs.city.value = values.city ?? '';
			this.triggerChange(this.inputs.city);
		}

		if (this.inputs.stateProvince) {
			this.inputs.stateProvince.value = values.stateProvince ?? '';
			this.triggerChange(this.inputs.stateProvince);
		}

		if (this.inputs.postalCode) {
			this.inputs.postalCode.value = values.postcode ?? '';
			this.triggerChange(this.inputs.postalCode);
		}

		if (this.inputs.country) {
			this.inputs.country.value = values.country ?? '';
			this.triggerChange(this.inputs.country);
		}

		/**
		 * Action that fires after a place is selected and Address Autocomplete has filled the fields.
		 *
		 * @param {google.maps.places.PlaceResult} place The place selected.
		 * @param {GP_Address_Autocomplete} instance Current instance of the class.
		 * @param {int} formId The current form ID.
		 * @param {int} fieldId The current field ID.
		 *
		 * @since 1.0
		 */
		window.gform.doAction('gpaa_fields_filled', place, this, this.formId, this.fieldId);
	}

	fillCoordinatesMetaInput(place: google.maps.places.PlaceResult) {
		const $form = document.querySelector<HTMLFormElement>(`#gform_${this.formId}`);
		let $input  = $form?.querySelector<HTMLInputElement>(`input[name="gpaa_coords_${this.fieldId}"]`);

		if (!$input) {
			return;
		}

		if (place.geometry) {
			$input.value = JSON.stringify(place.geometry.location);
		} else {
			$input.value == '';
		}
	}

	triggerChange(input: HTMLInputElement | HTMLSelectElement) : void {
		input.dispatchEvent(new Event('change'));

		$(input).trigger('change');
	}

	parseAdrAddressHTML(html: string) : GPAAParsedAdrMicroformat {
		const parsedAddress: Partial<GPAAParsedAdrMicroformat> = {};
		const adrPattern = /<(?:span|div) class="(post-office-box|extended-address|street-address|locality|region|postal-code|country-name)">(.*?)<\/(?:span|div)>/gm;

		let m;

		// I would prefer to use String.matchAll, but the Can I Use stat is 92% and the polyfills take the frontend JS
		// from 3-4KB to 20KB at a minimum.
		while ((m = adrPattern.exec(html)) !== null) {
			// This is necessary to avoid infinite loops with zero-width matches
			if (m.index === adrPattern.lastIndex) {
				adrPattern.lastIndex++;
			}

			// The result can be accessed through the `m`-variable.
			parsedAddress[m[1] as keyof GPAAParsedAdrMicroformat] = this.decodeHTMLEntities(m[2]);
		}

		return parsedAddress as GPAAParsedAdrMicroformat;
	}

	/**
	 * @credit https://stackoverflow.com/a/1395954
	 */
	decodeHTMLEntities(html: string) : string {
		var textarea = document.createElement('textarea');
		textarea.innerHTML = html;
		const value = textarea.value;
		textarea.remove();

		return value;
	}
}

// @ts-ignore
window.GP_Address_Autocomplete = GP_Address_Autocomplete;
