class AdvancedSearch {
	constructor() {
		// register hooks
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/eael-advanced-search.default",
			this.initFrontend.bind( this )
		);
		this.searchText = null;
		this.offset     = 0;
		this.catId      = null;
		this.allPostsCount  = 0;
	}
	
	// init frontend features
	initFrontend( $scope, $ ) {
		ea.getToken();
		this.scope        = $scope;
		this.search       = $scope[0].querySelector( ".eael-advanced-search" );
		this.searchForm   = $scope[0].querySelector( ".eael-advanced-search-form" );
		this.settingsData = JSON.parse( this.searchForm.dataset.settings )
		this.$            = $;
		this.showSearchResult();
		this.SearchByText();
		this.searchByKeyword();
		this.hideContainer( $scope );
		this.cateOnChange();
		this.onButtonClick();
		this.loadMoreData();
		this.clearData( $scope );
	}
	
	showSearchResult() {
		
		if ( !this.search ) {
			return false;
		}
		const $scope = this.scope;
		this.search.addEventListener( 'focus', this.inputSearchOnFocusBind.bind( this, $scope ) )
	}
	
	/**
	 * inputSearchOnFocusBind
	 * @param $scope
	 * @param event
	 */
	inputSearchOnFocusBind( $scope, event ) {
		
		if ( !$scope[0]?.querySelector( '.eael-advanced-search' ).value ) {
			return false;
		}
		
		const searchContainer = $scope[0].querySelector( '.eael-advanced-search-result' );
		if ( searchContainer.querySelector( '.eael-advanced-search-content' ).innerHTML.trim() !== '' ) {
			searchContainer.style.display = 'block'
			this.popularkeyWordDispaly( false, $scope );
		}
	}
	
	/**
	 * SearchByText
	 * @constructor
	 */
	SearchByText() {
		let timeOutRef = null;
		const $scope   = this.scope
		
		$scope[0].querySelector( '.eael-advanced-search' ).addEventListener( 'keyup', ( e ) => {

			let isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
			if (isMobile) {
				if (e.keyCode === 32 || e.keyCode === 91) {
					return;
				}
			} else {
				if (e.isComposing || e.keyCode === 229 || e.keyCode === 32 || e.keyCode === 91) {
					return;
				}
			}
			
			const searchText     = e.target.value.trim();
			this.searchContainer = $scope[0].querySelector( '.eael-advanced-search-result' );
			
			this.searchText = searchText;
			if ( searchText.length < 1 ) {
				this.clearOldData( this.searchContainer, $scope )
				this.searchContainer.style.display = 'none';
				this.popularkeyWordDispaly( true, $scope );
				this.customTriggerEvent( 'advSearchClear', { $scope } )
				return false;
			}
			
			this.searchForm   = $scope[0].querySelector( ".eael-advanced-search-form" );
			this.settingsData = JSON.parse( this.searchForm.dataset.settings )
			const $data       = {
				action: "fetch_search_result",
				s: searchText,
				settings: { ...this.settingsData },
				nonce: localize.nonce,
			}
			
			this.loader = $scope[0].querySelector( '.eael-adv-search-loader' );
			const catId = $scope[0].querySelector( '.eael-adv-search-cate' )?.value?.trim();
			
			if ( parseInt( catId ) > 0 ) {
				$data.settings.cat_id = catId;
			}
			
			let popularKeyword = sessionStorage.getItem( 'eael_popular_keyword' )
			if ( popularKeyword && this.searchText.length < 3 ) {
				delete $data.settings.show_popular_keyword;
			}
			
			//need to delay ajax request when typing
			clearTimeout( timeOutRef );
			timeOutRef = setTimeout( () => {
				timeOutRef = null
				this.makeAjaxRequest( $data, $scope )
				
			}, 500 );
			
		} )
	}
	
	searchByKeyword() {
		document.addEventListener( 'click', this.searchByKeywordEventBind.bind( this ), false );
	}
	
	/**
	 *
	 * @returns {boolean}
	 * @param event
	 */
	searchByKeywordEventBind( event ) {
		if ( event.target.className !== 'eael-popular-keyword-item' ) {
			return false;
		}
		this.searchText = event.target.dataset.keyword;
		this.triggerKeyupEvent( event )
	}
	
	/**
	 * cateOnChange
	 * @returns {boolean}
	 */
	cateOnChange() {
		const categorySelector = this.searchForm.querySelector( '.eael-adv-search-cate' )
		if ( !categorySelector ) {
			return false;
		}
		const $scope = this.scope
		categorySelector.addEventListener( 'change', this.categoryOnChangeEvent.bind( this, $scope ), false );
	}
	
	/**
	 *
	 * @param $scope
	 * @param event
	 */
	categoryOnChangeEvent( $scope, event ) {
		this.searchText = $scope[0].querySelector( '.eael-advanced-search' ).value;
		this.catId      = event.target.value;
		if ( this.searchText ) {
			this.triggerKeyupEvent( event )
		}
	}
	
	/**
	 * onButtonClick
	 * @returns {boolean}
	 */
	onButtonClick() {
		const searchButton = this.searchForm.querySelector( '.eael-advanced-search-button' )
		if ( !searchButton ) {
			return false;
		}
		const $scope = this.scope
		searchButton.addEventListener( 'click', this.searchButtonClickBind.bind( this, $scope ), false );
	}
	
	/**
	 * searchButtonClickBind
	 * @param $scope
	 * @param event
	 */
	searchButtonClickBind( $scope, event ) {
		event.preventDefault();
		if ( this.searchText ) {
			const newText = $scope[0].querySelector( '.eael-advanced-search' ).value;
			if ( this.searchText !== newText ) {
				this.searchText = newText
				this.triggerKeyupEvent( event );
			} else {
				const searchContainer         = $scope[0].querySelector( '.eael-advanced-search-result' );
				searchContainer.style.display = 'block';
				this.popularkeyWordDispaly( false, $scope );
			}
		}
	}
	
	loadMoreData() {
		const $scope = this.scope
		$scope[0].querySelector( '.eael-advanced-search-load-more-button' ).addEventListener( 'click', this.loadMoreDataBind.bind( this, $scope ), false );
	}
	
	loadMoreDataBind( $scope, event ) {
		event.preventDefault();

		if( event.target.disabled ){
			return;
		}
		event.target.disabled = true;

		this.searchForm   = $scope[0].querySelector( ".eael-advanced-search-form" );
		this.settingsData = JSON.parse( this.searchForm.dataset.settings )
		this.offset = parseInt( this.offset ) + parseInt( this.settingsData.post_per_page )
		
		const $data = {
			action: "fetch_search_result",
			s: this.searchText,
			settings: { ...this.settingsData, "offset": this.offset, 'cat_id': this.catId },
			nonce: localize.nonce,
		}
		delete $data.settings.show_category
		delete $data.settings.show_popular_keyword
		
		this.$.ajax( {
			             url: localize.ajaxurl,
			             type: "post",
			             data: $data,
			             context: this,
			             success: function ( response ) {
				             event.target.style.display = response.data?.more_data ? 'block' : 'none';
				             if ( response.data?.post_lists ) {
					             $scope[0].querySelector( '.eael-advanced-search-result' ).querySelector( '.eael-advanced-search-content' ).insertAdjacentHTML( 'beforeend', response.data.post_lists );
								 this.allPostsCount = response.data.all_posts_count;
				             }
							let hideAllPostsCount = response.data?.post_lists ? false : true;
							this.renderAllPostsCountContent( $scope, hideAllPostsCount );
							event.target.disabled = false;
			             },
			             error: function ( response ) {
				             event.target.style.display = 'none';
							this.renderAllPostsCountContent( $scope, true );
							event.target.disabled = false;
			             },
		             } )
	}
	
	/**
	 * manageRendering
	 *
	 * @param data
	 * @param selector
	 * @param $scope
	 */
	manageRendering( data, selector, $scope ) {
		selector.style.display = 'block'
		this.contentNotFound   = true;
		this.offset = 0;
		this.renderPopularKeyword( data, selector )
		this.renderCategory( data, selector );
		this.renderContent( data, selector, $scope );
		this.contentNotFoundRender( $scope );
		this.popularkeyWordDispaly( false, $scope );
		const searchTextlength                                            = $scope[0].querySelector( '.eael-advanced-search' ).value.length
		$scope[0].querySelector( '.eael-adv-search-close' ).style.display = searchTextlength > 0 ? 'block' : 'none';
	}
	
	contentNotFoundRender( $scope ) {
		$scope[0].querySelector( '.eael-advanced-search-not-found' ).style.display = this.contentNotFound ? 'block' : 'none';
		$scope[0].querySelector( '.eael-advanced-search-result' ).style.maxHeight  = this.contentNotFound ? 'inherit' : '';
	}
	
	clearData( $scope ) {
		const $this = this;
		$scope[0].querySelector( '.eael-adv-search-close' ).addEventListener( 'click', ( event ) => {
			event.preventDefault();
			$scope[0].querySelector( '.eael-adv-search-close' ).style.display       = 'none';
			$scope[0].querySelector( '.eael-advanced-search' ).value                = '';
			$scope[0].querySelector( '.eael-advanced-search-result' ).style.display = 'none';
			$this.search                                                            = '';
			this.popularkeyWordDispaly( true, $scope );
		} );
	}
	
	/**
	 * triggerKeyupEvent
	 *
	 * @param e
	 */
	triggerKeyupEvent( e ) {
		const closestElement = e.target.closest( '.elementor-widget-eael-advanced-search' ),
		      Input          = closestElement.querySelector( '.eael-advanced-search' ),
		      eventCreate    = document.createEvent( 'HTMLEvents' );
		Input.value          = this.searchText;
		eventCreate.initEvent( 'keyup', false, true );
		Input.dispatchEvent( eventCreate );
	}
	
	/**
	 * Create Custom event and dispatch
	 * @param eventName
	 * @param data
	 */
	customTriggerEvent( eventName, data ) {
		const event = new CustomEvent( eventName, { detail: { ...data } } )
		document.dispatchEvent( event );
	}
	
	/**
	 * renderPopularKeyword
	 *
	 * @param data
	 * @param selector
	 */
	renderPopularKeyword( data, selector ) {
		let keyword = selector.querySelector( '.eael-advanced-search-popular-keyword > .eael-popular-keyword-content' );
		if ( this.settingsData.show_popular_keyword ) {
			if ( keyword.innerHTML == '' ) {
				let popularKeyword = sessionStorage.getItem( 'eael_popular_keyword' )
				if ( data?.popular_keyword ) {
					popularKeyword = data.popular_keyword
					
					sessionStorage.setItem( 'eael_popular_keyword', popularKeyword );
				}
				
				if ( popularKeyword ) {
					keyword.parentElement.style.display = 'flex';
					keyword.innerHTML                   = popularKeyword;
					this.contentNotFound                = false;
				} else {
					keyword.parentElement.style.display = 'none';
				}
				
			}
		} else {
			keyword.parentElement.style.display = 'none';
		}
	}
	
	/**
	 * renderCategory
	 * @param data
	 * @param selector
	 */
	renderCategory( data, selector ) {
		let category = selector.querySelector( '.eael-advanced-search-category .eael-popular-category-content' );
		if ( data?.cate_lists ) {
			this.contentNotFound                 = false;
			category.parentElement.style.display = 'block';
			category.innerHTML                   = data.cate_lists;
		} else {
			category.parentElement.style.display = 'none';
		}
	}
	
	/**
	 * renderContent
	 * @param data
	 * @param selector
	 * @param $scope
	 */
	renderContent( data, selector, $scope ) {
		let content                                                                       = selector.querySelector( '.eael-advanced-search-content' );
		let loadmoreButton															      = $scope[0].querySelector( '.eael-advanced-search-load-more-button' );
		loadmoreButton.style.display 													  = data?.more_data ? 'block' : 'none';
		let hideAllPostsCount = true;
		if ( data?.post_lists ) {
			this.contentNotFound  = false;
			content.style.display = 'block';
			content.innerHTML     = data.post_lists;
			this.highlightSearchText( content, $scope );
			hideAllPostsCount = false;
		} else {
			this.contentNotFound  = true;
			content.innerHTML     = '';
			content.style.display = 'none';
			if(this.allPostsCount > 0){
				hideAllPostsCount = false;
			}
		}

		this.allPostsCount = data.all_posts_count;

		this.renderAllPostsCountContent( $scope, hideAllPostsCount );
	}
	
	/**
	 * hideContainer
	 */
	hideContainer( scope ) {
		document.addEventListener( 'click', ( e ) => {
			const status = e.target.closest( '.eael-advanced-search-widget' );
			if ( !status ) {
				let searchBox           = scope[0].querySelector( '.eael-advanced-search-result' );
				searchBox.style.display = 'none'
				this.popularkeyWordDispaly( true, scope );
			}
		} );
	}
	
	/**
	 * clearOldData
	 *
	 * @param searchContainer
	 * @param $scope
	 */
	clearOldData( searchContainer, $scope ) {
		searchContainer.querySelector( '.eael-popular-keyword-content' ).innerHTML  = '';
		searchContainer.querySelector( '.eael-popular-category-content' ).innerHTML = '';
		searchContainer.querySelector( '.eael-advanced-search-content' ).innerHTML  = '';
		$scope[0].querySelector( '.eael-adv-search-close' ).style.display           = 'none';
	}
	
	makeAjaxRequest( data, $scope ) {
		this.$.ajax( {
			             url: localize.ajaxurl,
			             type: "post",
			             data: data,
			             context: this,
			             beforeSend: function () {
				             this.loader.style.display                                         = 'block'
				             $scope[0].querySelector( '.eael-adv-search-close' ).style.display = 'none';
			             },
			             success: function ( response ) {
				             this.loader.style.display = 'none'
				             this.manageRendering( response.data, this.searchContainer, $scope )
			             },
			             error: function ( response ) {
				             this.loader.style.display = 'none'
			             },
		             } )
	}
	
	popularkeyWordDispaly( status, $scope ) {
		const view = $scope[0].querySelector( '.eael-after-adv-search' );
		if ( view ) {
			view.style.display = ( status ) ? 'flex' : 'none';
		}
	}
	
	renderAllPostsCountContent( $scope, hideAllPostsCount = false ) {
		let allPostsCountWrap		= $scope[0].querySelector( '.eael-advanced-search-total-results-wrap' );
		let allPostsCountText		= $scope[0].querySelector( '.eael-advanced-search-total-results-count' );
		
		if ( this.allPostsCount ) {
			if( allPostsCountText ) { 
				allPostsCountText.innerHTML = this.allPostsCount 
			}

			if( allPostsCountWrap ){
				allPostsCountWrap.style.display = 'block';
				allPostsCountWrap.parentNode.style.marginBottom = '20px';
			}
		} else {
			if( allPostsCountText ) { 
				allPostsCountText.innerHTML 	= '0';
			}

			if( allPostsCountWrap ){
				allPostsCountWrap.style.display = 'none';
				allPostsCountWrap.parentNode.style.marginBottom = 0;
			}
		}

		if( hideAllPostsCount && this.allPostsCount === 0 ){
			if( allPostsCountWrap ){
				allPostsCountWrap.style.display = 'none';
				allPostsCountWrap.parentNode.style.marginBottom = 0;
			}
		}
	}

	highlightSearchText( content, $scope ) {
		if ( this.searchText ) {
			let searchTexts = content.querySelectorAll( '.eael-search-text-highlight' );
			searchTexts.forEach( ( item ) => {
				let text = content.innerHTML;
				let regex = new RegExp( searchText, 'gi' );
				let result = text.replace( regex, `<span class="eael-search-text-highlight">${searchText}</span>` );
				item.innerHTML = result;
			} );
		}
	}
}

ea.hooks.addAction( "init", "ea", () => {
	new AdvancedSearch();
} );
