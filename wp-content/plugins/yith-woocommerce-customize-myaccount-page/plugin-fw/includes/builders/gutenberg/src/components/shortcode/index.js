/**
 * Shortcode Component
 */

/**
 * External dependencies
 */
import React, { Component }                        from 'react';
import { isEqual }                                 from 'lodash';

/**
 * WordPress dependencies
 */
import { RawHTML }                                 from '@wordpress/element';
import { Spinner }                                 from '@wordpress/components';
import { doAction, addAction }                     from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import { ajaxFetch, generateShortcode, yith_icon } from '../../common';
import md5                                         from 'md5';
import './style.scss';

const BEFORE_DO_SHORTCODE_ACTION  = 'yith_plugin_fw_gutenberg_before_do_shortcode';
const SUCCESS_DO_SHORTCODE_ACTION = 'yith_plugin_fw_gutenberg_success_do_shortcode';
const AFTER_DO_SHORTCODE_ACTION   = 'yith_plugin_fw_gutenberg_after_do_shortcode';

/**
 * Shortcode Component
 */
export class Shortcode extends Component {
	constructor() {
		super( ...arguments );

		this.state = {
			html         : '',
			shortcode    : '',
			shortcodeHash: '',
			ajaxUpdated  : false,
			ajaxSuccess  : false,
			ajaxResponse : false,
			loading      : false,
			firstLoading : true
		};

		this.ajaxTimeout = false;
	}

	componentDidMount() {
		this.updateShortcode();
	}

	componentDidUpdate( prevProps, prevState, snapshot ) {
		const { shortcode, shortcodeHash, ajaxSuccess, ajaxResponse, ajaxUpdated } = this.state;

		if ( !isEqual( prevProps, this.props ) ) {
			this.updateShortcode();
		}


		if ( this.props.blockArgs.do_shortcode && ajaxUpdated ) {

			if ( ajaxSuccess ) {
				doAction( SUCCESS_DO_SHORTCODE_ACTION, shortcode, shortcodeHash, ajaxResponse );
			}

			doAction( AFTER_DO_SHORTCODE_ACTION, shortcode, shortcodeHash, ajaxResponse );

			this.setState( { ajaxUpdated: false } );
		}
	}

	updateShortcode() {
		const { attributes, blockArgs } = this.props;

		this.setState( { loading: true, ajaxSuccess: false, ajaxResponse: false } );

		const shortcode     = generateShortcode( blockArgs, attributes );
		const shortcodeHash = md5( shortcode );

		if ( blockArgs.do_shortcode ) {
			!!this.ajaxTimeout && clearTimeout( this.ajaxTimeout );

			doAction( BEFORE_DO_SHORTCODE_ACTION, shortcode, shortcodeHash );

			this.ajaxTimeout = setTimeout( () => {
				const ajaxData = {
					action   : 'yith_plugin_fw_gutenberg_do_shortcode',
					shortcode: shortcode,
					context  : { ...( this.props.context ?? {} ), adminPage: window?.adminpage ?? '', pageNow: window?.pagenow ?? '' }
				};

				ajaxFetch( ajaxData ).then( response => {
					this.setState( { loading: false, firstLoading: false, html: response.html, shortcode, shortcodeHash, ajaxSuccess: true, ajaxUpdated: true, ajaxResponse: response } );
				} )
					.catch( error => {
						console.log( { error } );
					} );
			}, 300 );
		} else {
			this.setState( { loading: false, firstLoading: false, html: shortcode, shortcode, shortcodeHash } );
		}
	}

	render() {
		const { html, loading, firstLoading, shortcode, shortcodeHash } = this.state;
		const { blockArgs }                                             = this.props;
		const { do_shortcode, title, empty_message }                    = blockArgs;

		const mainClass = 'block-editor-yith-plugin-fw-shortcode-block';

		let wrapperClasses = [mainClass];
		let type           = do_shortcode ? 'html' : 'shortcode';
		let htmlToShow     = html;
		let message        = '';

		if ( firstLoading && loading ) {
			type = 'first-loading';
		} else if ( do_shortcode && !html ) {
			type       = 'empty-html';
			htmlToShow = shortcode;
			if ( !loading && empty_message ) {
				message = empty_message;
			}
		}

		const showTitle   = ['first-loading', 'empty-html', 'shortcode'].includes( type );
		const showContent = !['first-loading', 'empty-html'].includes( type );
		const showMessage = !!message;

		wrapperClasses.push( `${mainClass}--${type}` );
		wrapperClasses.push( showMessage ? `${mainClass}--has-message` : `${mainClass}--no-message` );
		wrapperClasses.push( `yith_block_${shortcodeHash}` );

		return (
			<>
				<div className={wrapperClasses.join( ' ' )}>
					{!!loading ? <div className={`${mainClass}__spinner-wrap`}><Spinner/></div> : ''}
					{showTitle &&
					 <div className={`${mainClass}__title components-placeholder__label`}>{yith_icon}{title}</div>
					}
					{showMessage &&
					 <RawHTML className={`${mainClass}__message`}>{message}</RawHTML>
					}
					{showContent &&
					 <RawHTML className={`${mainClass}__content`}>{htmlToShow}</RawHTML>
					}
				</div>
			</>
		)
	}
}