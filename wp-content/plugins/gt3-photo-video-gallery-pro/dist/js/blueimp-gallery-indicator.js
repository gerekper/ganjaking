/*!
 * Gallery Indicator JS
 *
 * Copyright 2017, GT3 Theme
 */

/* global define, window, document */

;(function (factory) {
	'use strict'
	window.blueimp && window.blueimp.Gallery &&
	factory(window.jQuery, window.blueimp.Gallery)
})(function ($, Gallery) {
	'use strict'

	$.extend(Gallery.prototype.options, {
		// The tag name, Id, element or querySelector of the indicator container:
		indicatorContainer: '.gt3pg_thumbnails',
		// The class for the active indicator:
		activeIndicatorClass: 'active',
		// The list object property (or data attribute) with the thumbnail URL,
		// used as alternative to a thumbnail child element:
		thumbnailProperty: 'thumbnail',
		// Defines if the gallery indicators should display a thumbnail:
		thumbnailIndicators: true
	})

	var initSlides = Gallery.prototype.initSlides
	var initWidget = Gallery.prototype.initWidget
	var addSlide = Gallery.prototype.addSlide
	var resetSlides = Gallery.prototype.resetSlides
	var handleClick = Gallery.prototype.handleClick
	var handleSlide = Gallery.prototype.handleSlide
	var handleClose = Gallery.prototype.handleClose
	var destroyEventListeners = Gallery.prototype.destroyEventListeners

	$.extend(Gallery.prototype, {
		createIndicator: function (obj) {
			var indicator = this.indicatorPrototype.cloneNode(false)
			var title = this.getItemProperty(obj, this.options.titleProperty)
			var thumbnailProperty = this.options.thumbnailProperty
			var thumbnailUrl
			var thumbnail
			if (this.options.thumbnailIndicators) {
				if (thumbnailProperty) {
					thumbnailUrl = this.getItemProperty(obj, thumbnailProperty)
				}
				if (thumbnailUrl === undefined) {
					thumbnail = obj.getElementsByTagName && $(obj).find('img')[0]
					if (thumbnail) {
						thumbnailUrl = thumbnail.src
					}
				}
				if (thumbnailUrl) {
					indicator.style.backgroundImage = 'url("' + thumbnailUrl + '")'
				}
			}
			if (title) {
				indicator.title = title
			}
			return indicator
		},

		addIndicator: function (index) {
			if (this.indicatorContainer.length) {
				var indicator = this.createIndicator(this.list[index])
				indicator.setAttribute('data-index', index)
				this.indicatorContainer[0].appendChild(indicator)
				this.indicators.push(indicator)
			}
		},
		initWidget: function () {
			initWidget.call(this)
			if (this.is_slick === true) {
				var slick_options = {
					accessibility: false,
					adaptiveHeight: true,
					dots: false,
					arrows: false,
					TouchMove: true,
					infinite: true,
					variableWidth: true,
					centerPadding: '0',
					autoplay: false,
					speed: this.options.transitionSpeed,
					slidesToShow: 2,
					swipeToSlide: false,
					centerMode: true,
					focusOnSelect: true,
					draggable: false,
					waitForAnimate: false,
					cssEase: 'linear',
					initialSlide: this.index
				}
				// if (!this.options.continuous) slick_options.infinite = false;
				jQuery(this.indicatorContainer).slick(slick_options)
			}


			this.fromSlick = false
		},

		destroyEventListeners: function () {
			destroyEventListeners.call(this)
			if (this.is_slick === true) {
				this.indicatorContainer.off('beforeChange', this.slickBeforeSlide)
			}
		},

		initSlides: function (reload) {
			var that = this

			function slickBeforeSlide(event, slick, currentSlide, nextSlide) {
				if (currentSlide !== nextSlide && nextSlide !== that.index) {
					that.fromSlick = true
					that.slide(nextSlide)
				}
			}

			if (!reload) {
				this.indicatorContainer = this.container.find(this.options.indicatorContainer)
				if (this.indicatorContainer.length) {
					this.indicatorPrototype = document.createElement('div')
					this.indicators = this.indicatorContainer[0].children

					this.is_slick = true


					this.indicatorContainer.on('beforeChange', slickBeforeSlide)
					this.slickBeforeSlide = slickBeforeSlide

				} else this.is_slick = false
			}

			initSlides.call(this, reload)


		},

		addSlide: function (index) {
			addSlide.call(this, index)
			this.addIndicator(index)
		},

		resetSlides: function () {
			resetSlides.call(this)
			this.indicatorContainer.empty()
			this.indicators = []
		},

		handleClick: function (event) {
			var target = event.target || event.srcElement
			var parent = target.parentNode
			if (parent.parentNode.parentNode === this.indicatorContainer[0]) {
				// console.log('Click on indicator element')
				// Click on indicator element
				this.preventDefault(event)
				// this.slide(this.getNodeIndex(target))
			} else if (parent.parentNode === this.indicatorContainer[0]) {
				// console.log('Click on indicator child element')
				// Click on indicator child element
				this.preventDefault(event)
				// this.slide(this.getNodeIndex(parent))
			} else {
				return handleClick.call(this, event)
			}
		},
		indicatorNext: function () {
			if (this.is_slick === true) {
				this.indicatorContainer[0].slick.slickNext()
			}
		},
		indicatorPrev: function () {
			if (this.is_slick === true) {
				this.indicatorContainer[0].slick.slickPrev()
			}
		},
		indicatorSlide: function (index) {
			if (this.is_slick === true) {
				this.indicatorContainer[0].slick.slickGoTo(index)
			}
		},

		handleSlide: function (index) {
			handleSlide.call(this, index)
			// this.setActiveIndicator(index)
			var diff = index - this.prevIndex
			var max = this.num - 1
			if (this.fromSlick == false) {
				// console.log(index, this.prevIndex, diff, max, this.fromSlick)
				if (index === 0) {
					if (diff !== 0) {
						if (this.prevIndex === max) {
							// console.log('next')
							this.indicatorNext()
						} else {
							// console.log('prev')
							this.indicatorPrev()
						}
					}
				} else if (index === max) {
					if (diff !== 0) {
						if (this.prevIndex === 0) {
							// console.log('prev')
							this.indicatorPrev()
						} else {
							// console.log('next')
							this.indicatorNext()
						}
					}
				} else {
					if (diff > 0) {
						// console.log('next')
						this.indicatorNext()
					} else if (diff < 0) {
						// console.log('prev')
						this.indicatorPrev()
					}
				}


				//////
				/*				switch (index) {
									case 0:
									if (Math.abs(diff) === 1 || Math.abs(diff) === max) {
										if (this.prevIndex === max) {
											this.indicatorNext();
										} else {
											this.indicatorPrev();
										}
									} else {
										this.indicatorSlide(index)
									}
									break;
									case max:
										if (Math.abs(diff) === 1 || Math.abs(diff) === max) {
											if (this.prevIndex === 0 || this.prevIndex === max) {
												this.indicatorPrev();
											} else {
												this.indicatorNext();
											}
										} else {
											this.indicatorSlide(index)
										}
										break;
									default:
										if (Math.abs(diff) === 1) {
											if (diff > 0) {
												this.indicatorNext();
											} else if (diff < 0) {
												this.indicatorPrev();
											}
										} else {
											this.indicatorSlide(index)
										}
										break;
								}*/
				////////

			}
			// this.indicatorContainer[0].slick.slickGoTo(index)
			this.fromSlick = false
		}
		,

		handleClose: function () {
			if (this.activeIndicator) {
				this.activeIndicator.removeClass(this.options.activeIndicatorClass)
			}
			handleClose.call(this)
			if (this.is_slick === true) {
				this.indicatorContainer[0].slick.unslick()
			}
		}
		,
	})

	return Gallery
})
