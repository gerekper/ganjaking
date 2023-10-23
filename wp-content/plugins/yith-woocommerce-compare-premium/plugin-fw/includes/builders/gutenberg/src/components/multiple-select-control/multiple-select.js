import classNames                               from 'classnames';
import noop                                     from 'lodash';
import PropTypes                                from 'prop-types';
import { useState, useEffect, useRef, useMemo } from 'react';

import { __, sprintf }   from '@wordpress/i18n';
import { useInstanceId } from '@wordpress/compose';
import { Popover }       from '@wordpress/components';

import MultipleSelectControl from './index';

const defaultMessages = {
	noItems  : __( 'No items found.', 'yith-plugin-fw' ),
	noResults: __( 'No results for "%s"', 'yith-plugin-fw' ),
	search   : __( 'Search for items...', 'yith-plugin-fw' )
};

function useUniqueId( idProp ) {
	const instanceId = useInstanceId( MultipleSelectControl );
	const id         = `inspector-yith-multiple-select-control-${instanceId}`;

	return idProp || id;
}

export default function MultipleSelect( { id: idProp, value, options, onChange, messages: messagesProp } ) {
	const [search, setSearch]                  = useState( '' );
	const [showSuggesions, setShowSuggestions] = useState( false );
	const [width, setWidth]                    = useState( 248 );
	const messages                             = { ...defaultMessages, ...messagesProp };
	const wrapperRef                           = useRef();
	const inputRef                             = useRef();
	const inputContainerRef                    = useRef();
	const popoverRef                           = useRef();
	const suggestionsRef                       = useRef();

	const classes = classNames(
		'yith-fw-components__multiple-select'
	);

	const allowedValues   = useMemo( () => options.map( _ => _.value ), [options] );
	const validValues     = useMemo( () => value.filter( _ => allowedValues.includes( _ ) ), [value, allowedValues] );
	const filteredOptions = useMemo( () => options.filter( ( _ ) => _.label.toLowerCase().indexOf( search.toLowerCase() ) >= 0 ), [options, search] );
	const selected        = useMemo( () => options.filter( ( _ ) => validValues.includes( _.value ) ), [options, validValues] );

	const addItem    = itemValue => {
		onChange( [...validValues, itemValue] );
		setSearch( '' );
	};
	const removeItem = ( itemValue ) => {
		const newSelected = [...validValues].filter( _ => _ !== itemValue );
		onChange( newSelected );
	};
	const focusInput = () => !!inputRef.current && inputRef.current.focus();

	const handleClickOutside = e => {
		const isWrapperClick = wrapperRef?.current && wrapperRef.current.contains( e.target );
		const isPopoverClick = popoverRef?.current && popoverRef.current.contains( e.target );
		if ( !isWrapperClick && !isPopoverClick ) {
			setShowSuggestions( false );
		}
	};

	const handleInputContainerClick = e => {
		const isInputContainerClick = inputContainerRef?.current && inputContainerRef.current === e.target;
		if ( isInputContainerClick ) {
			focusInput();
		}
	};

	useEffect( () => {
		document.addEventListener( 'mousedown', handleClickOutside );
		return () => document.removeEventListener( 'mousedown', handleClickOutside );
	} );

	useEffect( () => {
		const refresh = () => {
			if ( !wrapperRef.current ) {
				return;
			}

			const width = wrapperRef.current.getBoundingClientRect()?.width;
			setWidth( width );
		};

		refresh();
	}, [] );

	return <div className={classes} ref={wrapperRef}>
		<div className="yith-fw-components__multiple-select__input-container" ref={inputContainerRef} onClick={handleInputContainerClick}>
			{selected.map( item => {
				return <span key={item.value} className="yith-fw-components__multiple-select__item">
					<span className="yith-fw-components__multiple-select__item__label">{item.label}</span>
					<i
						className="yith-fw-components__multiple-select__item__remove yith-icon-close-alt"
						onClick={() => {
							removeItem( item.value );
							setShowSuggestions( false );
						}}
					/>
				</span>
			} )}
			<input
				className="yith-fw-components__multiple-select__input"
				id={useUniqueId( idProp )}
				ref={inputRef}
				type="text"
				autoComplete="off"
				placeholder={messages.search}
				onFocus={() => setShowSuggestions( true )}
				value={search}
				onChange={( e ) => setSearch( e.target.value )}
			/>
		</div>
		{showSuggesions && <Popover
			className="yith-fw-components__multiple-select__popover"
			position="bottom"
			offset={20}
			anchorRef={wrapperRef?.current ?? undefined}
			anchorRect={wrapperRef?.current && wrapperRef?.current.getBoundingClientRect()}
			focusOnMount={false}
			ref={popoverRef}
		>
			<div
				className={classNames( 'yith-fw-components__multiple-select__suggestions', !filteredOptions.length && 'no-results' )}
				ref={suggestionsRef}
				style={{ width }}
			>
				{!!options.length && !!filteredOptions.length ?
				 (
					 filteredOptions.map(
						 item => {
							 const isSelected = validValues.includes( item.value );

							 return <div
								 key={item.value}
								 className={classNames( 'yith-fw-components__multiple-select__suggestion', isSelected && 'selected' )}
								 onClick={() => {
									 if ( !isSelected ) {
										 addItem( item.value );
										 focusInput();
									 }
								 }}
							 >
								 <div className="yith-fw-components__multiple-select__suggestion__label">{item.label}</div>
								 {isSelected && <i
									 className="yith-fw-components__multiple-select__suggestion__remove yith-icon yith-icon-close-alt"
									 onClick={() => {
										 removeItem( item.value );
										 focusInput();
									 }}/>}
							 </div>
						 }
					 )
				 ) :
				 <div className="yith-fw-components__multiple-select__suggestions__message">
					 {!options.length || !search ? messages.noItems : sprintf( messages.noResults, search )}
				 </div>
				}
			</div>
		</Popover>}
	</div>
}

MultipleSelect.propTypes = {
	id       : PropTypes.string,
	className: PropTypes.string,
	value    : PropTypes.array,
	options  : PropTypes.arrayOf(
		PropTypes.shape(
			{
				label: PropTypes.string,
				value: PropTypes.oneOfType( [PropTypes.string, PropTypes.number] )
			}
		)
	),
	onChange : PropTypes.func

};

MultipleSelect.defaultProps = {
	id       : '',
	value    : [],
	className: '',
	options  : [],
	onChange : noop
};