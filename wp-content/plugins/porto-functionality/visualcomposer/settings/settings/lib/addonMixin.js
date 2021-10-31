import { getService } from 'vc-cake'
import lodash from 'lodash'

( function () {
	const assetsStorage = getService( 'modernAssetsStorage' )
	const cookService = getService( 'cook' )
	const globalAssetsStorage = assetsStorage.create()


	function getNestedMixinsStyles( cssSettings, cssMixins, elementObject ) {
		const styles = []
		for ( const mixin in cssMixins ) {
			for ( const mixinSelector in cssMixins[ mixin ] ) {
				if ( cssSettings.mixins && cssSettings.mixins[ mixin ] ) {
					styles.push( {
						variables: elementObject.data.tag.indexOf( 'porto' ) > -1 ? Object.assign( {}, cssMixins[ mixin ][ mixinSelector ], { elementId: elementData.id } ) : cssMixins[ mixin ][ mixinSelector ],
						src: cssSettings.mixins[ mixin ].mixin,
						path: elementObject.get( 'metaElementPath' )
					} )
				}
			}
		}

		return styles
	}

	function getMixinStyles( elementData, self ) {
		let styles = []
		const cssMixins = self.getCssMixinsByElement( elementData, {} )
		Object.keys( cssMixins ).forEach( ( tag ) => {
			const elementObject = cookService.get( { tag: tag } )
			if ( !elementObject ) {
				return
			}
			const cssSettings = elementObject.get( 'cssSettings' )
			const mixins = Object.keys( cssMixins[ tag ] )

			mixins.forEach( ( mixin ) => {
				if ( Object.prototype.hasOwnProperty.call( elementData, mixin ) && cssSettings.mixins && !cssSettings.mixins[ mixin ] ) {
					for ( const itemMixinsIndex in cssMixins[ tag ][ mixin ] ) {
						for ( const mixinElementTag in cssMixins[ tag ][ mixin ][ itemMixinsIndex ] ) {
							let mixinStyles = []
							if ( mixinElementTag === 'innerTag' ) {
								mixinStyles = getNestedMixinsStyles( cssSettings, cssMixins[ tag ][ mixin ][ itemMixinsIndex ][ mixinElementTag ], elementObject )
							} else {
								const innerElement = cookService.get( { tag: mixinElementTag } )
								const innerElementCssSettings = innerElement.get( 'cssSettings' )
								mixinStyles = getNestedMixinsStyles( innerElementCssSettings, cssMixins[ tag ][ mixin ][ itemMixinsIndex ][ mixinElementTag ], innerElement )
							}
							styles = styles.concat( mixinStyles )
						}
					}
				} else {
					for ( const selector in cssMixins[ tag ][ mixin ] ) {
						if ( cssSettings.mixins && cssSettings.mixins[ mixin ] ) {
							styles.push( {
								variables: elementData.tag.indexOf( 'porto' ) > -1 ? Object.assign( {}, cssMixins[ tag ][ mixin ][ selector ], { elementId: elementData.id } ) : cssMixins[ tag ][ mixin ][ selector ],
								src: cssSettings.mixins[ mixin ].mixin,
								path: elementObject.get( 'metaElementPath' )
							} )
						}
					}
				}
			} )
		} )
		return styles
	}
	globalAssetsStorage.__proto__.getElementLocalAttributesCssMixins = function ( elementData ) {
		if ( !elementData ) {
			return null
		}
		let styles = []

		// get mixins styles
		styles = styles.concat( getMixinStyles( elementData, this ) )
		return styles;
	}
	globalAssetsStorage.__proto__.getCssDataByElement = function ( elementData, options = {} ) {

		if ( !elementData ) {
			return null
		}
		const defaultOptions = {
			tags: true,
			cssMixins: true,
			attributeMixins: true
		}
		options = lodash.defaults( options, defaultOptions, {} )
		const styles = {
			tags: [],
			cssMixins: [],
			attributeMixins: []
		}
		// get tag styles
		if ( options.tags ) {
			// Tags are recursive function depends also on inner elements
			const tags = this.getElementTagsByData( elementData )
			tags.forEach( ( tag ) => {
				const elementObject = cookService.get( { tag: tag } )
				if ( !elementObject ) {
					return
				}
				const cssSettings = elementObject.get( 'cssSettings' )
				if ( cssSettings.css ) {
					styles.tags.push( {
						src: cssSettings.css,
						path: elementObject.get( 'metaElementPath' )
					} )
				}
			} )
		}
		// get mixins styles
		if ( options.cssMixins ) {
			styles.cssMixins = styles.cssMixins.concat( getMixinStyles( elementData, this ) )
		}
		// get attribute mixins styles
		if ( options.attributeMixins ) {
			const attributesMixins = this.getAttributesMixinsByElement( elementData )
			Object.keys( attributesMixins ).forEach( ( tag ) => {
				Object.keys( attributesMixins[ tag ] ).forEach( ( attribute ) => {
					if ( options.skipOnSave ) {
						if ( !attributesMixins[ tag ][ attribute ].skipOnSave ) {
							styles.attributeMixins.push( attributesMixins[ tag ][ attribute ] )
						}
					} else {
						styles.attributeMixins.push( attributesMixins[ tag ][ attribute ] )
					}
				} )
			} )
		}

		return styles

	}
} )()