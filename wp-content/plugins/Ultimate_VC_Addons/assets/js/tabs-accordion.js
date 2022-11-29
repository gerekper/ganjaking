jQuery( document ).ready( function ( a ) {
	a( '.cq-accordion' ).each( function () {
		a( this ),
			a( this )
				.find( 'li' )
				.each( function () {
					a( this )
						.find( 'i' )
						.css(
							'margin-top',
							0.5 * ( a( this ).outerHeight() - 9 )
						);
				} );
	} ),
		a( '.ult-tabto-accordion' ).each( function () {
			a( this );
			const c = a( this ).data( 'titlebg' ),
				d = a( this ).data( 'titlecolor' ),
				e = a( this ).data( 'titlehoverbg' ),
				f = a( this ).data( 'titlehovercolor' );
			var act_title = a( this ).data( 'activetitle' ),
				act_icon = a( this ).data( 'activeicon' ),
				scroll_type = a( this ).data( 'scroll' ),
				act_bg = a( this ).data( 'activebg' );
			if ( act_icon == '' ) {
				var act_icon = a( this )
					.find( '.aio-icon' )
					.data( 'iconhover' );
			}

			a( this )
				.find( '.ult-tabto-actitle' )
				.each( function () {
					const iconcolor = a( this )
						.find( '.aio-icon' )
						.data( 'iconcolor' );
					const iconhover = a( this )
						.find( '.aio-icon' )
						.data( 'iconhover' );

					a( this )
						.css( 'background-color', c )
						.on( 'mouseover', function () {
							if (
								a( this ).hasClass( 'ult-tabto-actitleActive' )
							) {
							} else {
								a( this ).css( {
									'background-color': e,
									color: f,
								} ),
									a( this ).find( '.aio-icon' ).css( {
										color: iconhover,
									} );
							}
						} )
						.on( 'mouseleave', function () {
							a( this ).hasClass( 'ult-tabto-actitleActive' ) ||
								a( this ).css( {
									'background-color': c,
									color: d,
								} );
							const flag = a( this ).hasClass(
								'ult-tabto-actitleActive'
							);

							if ( flag == true ) {
							} else {
								a( this ).find( '.aio-icon' ).css( {
									color: iconcolor,
								} );
							}
						} );
				} ),
				a( this ).on( 'click', function ( b ) {
					let c;
					if (
						( ( c = a( b.target ).is( 'i' )
							? a( b.target ).parent()
							: a( b.target ) ),
						c.hasClass( 'ult-tabto-actitle' ) )
					) {
						var d = c.parent().next();

						var animation = c
							.parents( '.ult-tabto-accordion' )
							.data( 'animation' );
						// console.log(d);

						if ( d.nextAll( 'dd' ).hasClass( 'cq-animateIn' ) ) {
							d.nextAll( 'dd' )
								.removeClass( 'cq-animateIn' )
								.addClass(
									' cq-animateOut ult-tabto-accolapsed'
								);
						}
						if ( d.prevAll( 'dd' ).hasClass( 'cq-animateIn' ) ) {
							d.prevAll( 'dd' )
								.removeClass( 'cq-animateIn' )
								.addClass( 'ult-tabto-accolapsed' );
						}

						if (
							d.nextAll( 'dd' ).hasClass( 'ult-ac-slidedown' )
						) {
							d.nextAll( 'dd' )
								.removeClass( 'ult-ac-slidedown' )
								.addClass(
									' ult-ac-slideup ult-tabto-accolapsed'
								);
						}
						if (
							d.prevAll( 'dd' ).hasClass( 'ult-ac-slidedown' )
						) {
							d.prevAll( 'dd' )
								.removeClass( 'ult-ac-slidedown' )
								.addClass( 'ult-tabto-accolapsed' );
						}

						if (
							d
								.prevAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.hasClass( 'ult-tabto-actitleActive' )
						) {
							d.prevAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.removeClass( 'ult-tabto-actitleActive' )
								.addClass( 'ult-acc-normal' );
						}
						if (
							d
								.nextAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.hasClass( 'ult-tabto-actitleActive' )
						) {
							d.nextAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.removeClass( 'ult-tabto-actitleActive' )
								.addClass( 'ult-acc-normal' );
						}

						c.removeClass( 'ult-acc-normal' );
						jQuery( this )
							.find( '.ult-acc-normal' )
							.each( function () {
								const icn = jQuery( this )
									.find( '.aio-icon' )
									.data( 'iconcolor' );
								const ich = jQuery( this )
									.find( '.aio-icon' )
									.data( 'iconhover' );
								const bgcolor = jQuery( this )
									.parents( '.ult-tabto-accordion' )
									.data( 'titlebg' );
								const titlecolor = jQuery( this )
									.parents( '.ult-tabto-accordion' )
									.data( 'titlecolor' );
								jQuery( this ).css( {
									background: bgcolor,
									color: titlecolor,
								} );
								jQuery( this )
									.find( '.aio-icon' )
									.css( { color: icn } );
							} );

						c.css( {
							color: act_title,
							'background-color': act_bg,
						} );
						c.find( '.aio-icon' ).css( {
							color: act_icon,
						} );

						var iconcolor = c
							.find( '.aio-icon' )
							.data( 'iconcolor' );
						var iconhover = c
							.find( '.aio-icon' )
							.data( 'iconhover' );

						if ( animation == 'Fade' ) {
							c.toggleClass( 'ult-tabto-actitleActive' ),
								d.hasClass( 'ult-tabto-accolapsed' )
									? ( d.hasClass( 'cq-animateOut' ) &&
											d.removeClass( 'cq-animateOut' ),
									  d.addClass( 'cq-animateIn' ) )
									: ( d.removeClass( 'cq-animateIn' ),
									  d.addClass( 'cq-animateOut' ) ),
								d.toggleClass( 'ult-tabto-accolapsed' ),
								b.preventDefault();
						} else {
							c.toggleClass( 'ult-tabto-actitleActive' ),
								d.hasClass( 'ult-tabto-accolapsed' )
									? ( d.hasClass( 'ult-ac-slideup' ) &&
											d.removeClass( 'ult-ac-slideup' ),
									  d.addClass( 'ult-ac-slidedown' ) )
									: ( d.removeClass( 'ult-ac-slidedown' ),
									  d.addClass( 'ult-ac-slideup' ) ),
								d.toggleClass( 'ult-tabto-accolapsed' ),
								b.preventDefault();
						}

						if ( d.hasClass( 'ult-tabto-accolapsed' ) ) {
							c.removeClass( 'ult-tabto-actitleActive' );
						}
					} else if (
						( ( c = a( b.target ).is(
							'span.ult-span-text.ult_acordian-text'
						)
							? a( b.target ).parent().parent()
							: a( b.target ) ),
						c.hasClass( 'ult-tabto-actitle' ) )
					) {
						var d = c.parent().next();
						var animation = c
							.parents( '.ult-tabto-accordion' )
							.data( 'animation' );
						//console.log('2');
						if ( d.nextAll( 'dd' ).hasClass( 'cq-animateIn' ) ) {
							d.nextAll( 'dd' )
								.removeClass( 'cq-animateIn' )
								.addClass(
									' ult-ac-slideup ult-tabto-accolapsed'
								);
						}

						if ( d.prevAll( 'dd' ).hasClass( 'cq-animateIn' ) ) {
							d.prevAll( 'dd' )
								.removeClass( 'cq-animateIn' )
								.addClass( 'ult-tabto-accolapsed' );
						}

						if (
							d.nextAll( 'dd' ).hasClass( 'ult-ac-slidedown' )
						) {
							d.nextAll( 'dd' )
								.removeClass( 'ult-ac-slidedown' )
								.addClass(
									' ult-ac-slideup ult-tabto-accolapsed'
								);
						}
						if (
							d.prevAll( 'dd' ).hasClass( 'ult-ac-slidedown' )
						) {
							d.prevAll( 'dd' )
								.removeClass( 'ult-ac-slidedown' )
								.addClass( 'ult-tabto-accolapsed' );
						}

						if (
							d
								.prevAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.hasClass( 'ult-tabto-actitleActive' )
						) {
							d.prevAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.removeClass( 'ult-tabto-actitleActive' )
								.addClass( 'ult-acc-normal' );
						}
						if (
							d
								.nextAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.hasClass( 'ult-tabto-actitleActive' )
						) {
							d.nextAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.removeClass( 'ult-tabto-actitleActive' )
								.addClass( 'ult-acc-normal' );
						}

						c.removeClass( 'ult-acc-normal' );
						jQuery( this )
							.find( '.ult-acc-normal' )
							.each( function () {
								const icn = jQuery( this )
									.find( '.aio-icon' )
									.data( 'iconcolor' );
								const ich = jQuery( this )
									.find( '.aio-icon' )
									.data( 'iconhover' );
								const bgcolor = jQuery( this )
									.parents( '.ult-tabto-accordion' )
									.data( 'titlebg' );
								const titlecolor = jQuery( this )
									.parents( '.ult-tabto-accordion' )
									.data( 'titlecolor' );
								jQuery( this ).css( {
									background: bgcolor,
									color: titlecolor,
								} );
								jQuery( this )
									.find( '.aio-icon' )
									.css( { color: icn } );
							} );

						var iconcolor = c
							.find( '.aio-icon' )
							.data( 'iconcolor' );
						var iconhover = c
							.find( '.aio-icon' )
							.data( 'iconhover' );

						c.css( {
							color: act_title,
							'background-color': act_bg,
						} );
						c.find( '.aio-icon' ).css( {
							color: act_icon,
						} );

						if ( animation == 'Fade' ) {
							c.toggleClass( 'ult-tabto-actitleActive' ),
								d.hasClass( 'ult-tabto-accolapsed' )
									? ( d.hasClass( 'cq-animateOut' ) &&
											d.removeClass( 'cq-animateOut' ),
									  d.addClass( 'cq-animateIn' ) )
									: ( d.removeClass( 'cq-animateIn' ),
									  d.addClass( 'cq-animateOut' ) ),
								d.toggleClass( 'ult-tabto-accolapsed' ),
								b.preventDefault();
						} else {
							c.toggleClass( 'ult-tabto-actitleActive' ),
								d.hasClass( 'ult-tabto-accolapsed' )
									? ( d.hasClass( 'ult-ac-slideup' ) &&
											d.removeClass( 'ult-ac-slideup' ),
									  d.addClass( 'ult-ac-slidedown' ) )
									: ( d.removeClass( 'ult-ac-slidedown' ),
									  d.addClass( 'ult-ac-slideup' ) ),
								d.toggleClass( 'ult-tabto-accolapsed' ),
								b.preventDefault();
						}

						if ( d.hasClass( 'ult-tabto-accolapsed' ) ) {
							c.removeClass( 'ult-tabto-actitleActive' );
						}
					} else if (
						( ( c = a( b.target ).is( 'i' )
							? a( b.target ).parent().parent()
							: a( b.target ) ),
						c.hasClass( 'ult-tabto-actitle' ) )
					) {
						var d = c.parent().next();
						var animation = c
							.parents( '.ult-tabto-accordion' )
							.data( 'animation' );

						if ( d.nextAll( 'dd' ).hasClass( 'cq-animateIn' ) ) {
							d.nextAll( 'dd' )
								.removeClass( 'cq-animateIn' )
								.addClass(
									' cq-animateOut ult-tabto-accolapsed'
								);
						}

						if ( d.prevAll( 'dd' ).hasClass( 'cq-animateIn' ) ) {
							d.prevAll( 'dd' )
								.removeClass( 'cq-animateIn' )
								.addClass( 'ult-tabto-accolapsed' );
						}

						if (
							d.nextAll( 'dd' ).hasClass( 'ult-ac-slidedown' )
						) {
							d.nextAll( 'dd' )
								.removeClass( 'ult-ac-slidedown' )
								.addClass(
									' ult-ac-slideup ult-tabto-accolapsed'
								);
						}
						if (
							d.prevAll( 'dd' ).hasClass( 'ult-ac-slidedown' )
						) {
							d.prevAll( 'dd' )
								.removeClass( 'ult-ac-slidedown' )
								.addClass( 'ult-tabto-accolapsed' );
						}

						if (
							d
								.prevAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.hasClass( 'ult-tabto-actitleActive' )
						) {
							d.prevAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.removeClass( 'ult-tabto-actitleActive' )
								.addClass( 'ult-acc-normal' );
						}
						if (
							d
								.nextAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.hasClass( 'ult-tabto-actitleActive' )
						) {
							d.nextAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.removeClass( 'ult-tabto-actitleActive' )
								.addClass( 'ult-acc-normal' );
						}

						c.removeClass( 'ult-acc-normal' );
						jQuery( this )
							.find( '.ult-acc-normal' )
							.each( function () {
								const icn = jQuery( this )
									.find( '.aio-icon' )
									.data( 'iconcolor' );
								const ich = jQuery( this )
									.find( '.aio-icon' )
									.data( 'iconhover' );
								const bgcolor = jQuery( this )
									.parents( '.ult-tabto-accordion' )
									.data( 'titlebg' );
								const titlecolor = jQuery( this )
									.parents( '.ult-tabto-accordion' )
									.data( 'titlecolor' );
								jQuery( this ).css( {
									background: bgcolor,
									color: titlecolor,
								} );
								jQuery( this )
									.find( '.aio-icon' )
									.css( { color: icn } );
							} );

						var iconcolor = c
							.find( '.aio-icon' )
							.data( 'iconcolor' );
						var iconhover = c
							.find( '.aio-icon' )
							.data( 'iconhover' );

						c.css( {
							color: act_title,
							'background-color': act_bg,
						} );
						c.find( '.aio-icon' ).css( {
							color: act_icon,
						} );

						if ( animation == 'Fade' ) {
							c.toggleClass( 'ult-tabto-actitleActive' ),
								d.hasClass( 'ult-tabto-accolapsed' )
									? ( d.hasClass( 'cq-animateOut' ) &&
											d.removeClass( 'cq-animateOut' ),
									  d.addClass( 'cq-animateIn' ) )
									: ( d.removeClass( 'cq-animateIn' ),
									  d.addClass( 'cq-animateOut' ) ),
								d.toggleClass( 'ult-tabto-accolapsed' ),
								b.preventDefault();
						} else {
							c.toggleClass( 'ult-tabto-actitleActive' ),
								d.hasClass( 'ult-tabto-accolapsed' )
									? ( d.hasClass( 'ult-ac-slideup' ) &&
											d.removeClass( 'ult-ac-slideup' ),
									  d.addClass( 'ult-ac-slidedown' ) )
									: ( d.removeClass( 'ult-ac-slidedown' ),
									  d.addClass( 'ult-ac-slideup' ) ),
								d.toggleClass( 'ult-tabto-accolapsed' ),
								b.preventDefault();
						}

						if ( d.hasClass( 'ult-tabto-accolapsed' ) ) {
							c.removeClass( 'ult-tabto-actitleActive' );
						}
					} else if (
						( ( c = a( b.target ).is( 'i' )
							? a( b.target ).parent().parent().parent()
							: a( b.target ) ),
						c.hasClass( 'ult-tabto-actitle' ) )
					) {
						var d = c.parent().next();
						var animation = c
							.parents( '.ult-tabto-accordion' )
							.data( 'animation' );

						if ( d.nextAll( 'dd' ).hasClass( 'cq-animateIn' ) ) {
							d.nextAll( 'dd' )
								.removeClass( 'cq-animateIn' )
								.addClass(
									' cq-animateOut ult-tabto-accolapsed'
								);
						}

						if ( d.prevAll( 'dd' ).hasClass( 'cq-animateIn' ) ) {
							d.prevAll( 'dd' )
								.removeClass( 'cq-animateIn' )
								.addClass( 'ult-tabto-accolapsed' );
						}

						if (
							d.nextAll( 'dd' ).hasClass( 'ult-ac-slidedown' )
						) {
							d.nextAll( 'dd' )
								.removeClass( 'ult-ac-slidedown' )
								.addClass(
									' ult-ac-slideup ult-tabto-accolapsed'
								);
						}
						if (
							d.prevAll( 'dd' ).hasClass( 'ult-ac-slidedown' )
						) {
							d.prevAll( 'dd' )
								.removeClass( 'ult-ac-slidedown' )
								.addClass( 'ult-tabto-accolapsed' );
						}

						if (
							d
								.prevAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.hasClass( 'ult-tabto-actitleActive' )
						) {
							d.prevAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.removeClass( 'ult-tabto-actitleActive' )
								.addClass( 'ult-acc-normal' );
						}
						if (
							d
								.nextAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.hasClass( 'ult-tabto-actitleActive' )
						) {
							d.nextAll( 'dt' )
								.find( '.ult-tabto-actitle' )
								.removeClass( 'ult-tabto-actitleActive' )
								.addClass( 'ult-acc-normal' );
						}

						c.removeClass( 'ult-acc-normal' );
						jQuery( this )
							.find( '.ult-acc-normal' )
							.each( function () {
								const icn = jQuery( this )
									.find( '.aio-icon' )
									.data( 'iconcolor' );
								const ich = jQuery( this )
									.find( '.aio-icon' )
									.data( 'iconhover' );
								const bgcolor = jQuery( this )
									.parents( '.ult-tabto-accordion' )
									.data( 'titlebg' );
								const titlecolor = jQuery( this )
									.parents( '.ult-tabto-accordion' )
									.data( 'titlecolor' );
								jQuery( this ).css( {
									background: bgcolor,
									color: titlecolor,
								} );
								jQuery( this )
									.find( '.aio-icon' )
									.css( { color: icn } );
							} );

						var iconcolor = c
							.find( '.aio-icon' )
							.data( 'iconcolor' );
						var iconhover = c
							.find( '.aio-icon' )
							.data( 'iconhover' );

						c.css( {
							color: act_title,
							'background-color': act_bg,
						} );
						c.find( '.aio-icon' ).css( {
							color: act_icon,
						} );

						if ( animation == 'Fade' ) {
							c.toggleClass( 'ult-tabto-actitleActive' ),
								d.hasClass( 'ult-tabto-accolapsed' )
									? ( d.hasClass( 'cq-animateOut' ) &&
											d.removeClass( 'cq-animateOut' ),
									  d.addClass( 'cq-animateIn' ) )
									: ( d.removeClass( 'cq-animateIn' ),
									  d.addClass( 'cq-animateOut' ) ),
								d.toggleClass( 'ult-tabto-accolapsed' ),
								b.preventDefault();
						} else {
							c.toggleClass( 'ult-tabto-actitleActive' ),
								d.hasClass( 'ult-tabto-accolapsed' )
									? ( d.hasClass( 'ult-ac-slideup' ) &&
											d.removeClass( 'ult-ac-slideup' ),
									  d.addClass( 'ult-ac-slidedown' ) )
									: ( d.removeClass( 'ult-ac-slidedown' ),
									  d.addClass( 'ult-ac-slideup' ) ),
								d.toggleClass( 'ult-tabto-accolapsed' ),
								b.preventDefault();
						}

						if ( d.hasClass( 'ult-tabto-accolapsed' ) ) {
							c.removeClass( 'ult-tabto-actitleActive' );
						}
					}

					if ( scroll_type == 'on' ) {
						jQuery( 'html, body' ).animate(
							{
								scrollTop: a( this ).offset().top - 100,
							},
							1200
						);
					}
				} );
		} );

	// to open accordion from other pages
	open_accordion();

	//open accordion as per index
	jQuery( '.ult-tabto-accordion' ).each( function () {
		let index = jQuery( this ).data( 'activeindex' );
		index = index - 1;
		if ( index >= 0 ) {
			const current_tab = jQuery( this ).find(
				'dl dt:nth(' + index + ')'
			);
			const id = current_tab.find( '.ult-tabto-actitle' ).attr( 'id' );
			if ( id !== '' && typeof id !== 'undefined' ) {
				open_accordion( id );
			}
		}
	} );

	//open accordion on click of menu or same page link
	jQuery( this )
		.find( 'a' )
		.click( function ( b ) {
			const href = jQuery( this ).attr( 'href' );
			if ( typeof href !== 'undefined' && href.length > 0 ) {
				const class_name = jQuery( this ).hasClass(
					'ult-tabto-actitle'
				);
				const type = escape(
					href.substring( href.indexOf( '#' ) + 1 )
				);
				const maintab = jQuery(
					"a.ult-tabto-actitle[href$='" + type + "']"
				);
				let tabid = maintab.attr( 'href' );
				const titlecolor = maintab
					.parents( '.ult-tabto-accordion' )
					.data( 'titlecolor' );
				const titlebg = maintab
					.parents( '.ult-tabto-accordion' )
					.data( 'titlebg' );

				if ( typeof tabid !== 'undefined' && tabid !== '' ) {
					tabid = tabid.replace( '#', '' );
				}
				if (
					maintab.parents( '.ult-tabto-accordion' ).length > 0 &&
					type == tabid &&
					! class_name
				) {
					maintab
						.parents( '.ult-tabto-accordion' )
						.find( '.ult-tabto-actitle' )
						.each( function ( index, el ) {
							const id = jQuery( this ).attr( 'id' );
							if ( tabid !== id ) {
								jQuery( this )
									.parent()
									.removeClass( 'current' );
								jQuery( this ).removeClass(
									'ult-tabto-actitleActive'
								);
								jQuery( this ).css( {
									background: titlebg,
									color: titlecolor,
								} );
								const icon_color = jQuery( this )
									.find( '.aio-icon' )
									.data( 'iconcolor' );
								jQuery( this )
									.find( '.ult_tab_icon' )
									.css( { color: icon_color } );
								jQuery( this )
									.parents( 'dt' )
									.next( 'dd' )
									.addClass( 'ult-tabto-accolapsed' );
							}
						} );
					open_accordion( tabid );
				}
			}
		} );

	jQuery( this )
		.find( 'a.ult-tabto-actitle' )
		.click( function ( b ) {
			const main_div = jQuery( this ).parents( '.ult-tabto-accordion' );
			const current_id = jQuery( this ).attr( 'id' );
			main_div.find( '.ult-tabto-actitle' ).each( function ( index, el ) {
				const id = jQuery( this ).attr( 'id' );
				if ( current_id !== id ) {
					jQuery( this )
						.parents( 'dt' )
						.next( 'dd' )
						.addClass( 'ult-tabto-accolapsed' );
				}
			} );
		} );

	function open_accordion( id ) {
		let type = escape( window.location.hash.substr( 1 ) );

		if ( id !== '' && typeof id !== 'undefined' ) {
			type = id;
		}

		if ( type != '' ) {
			const current_tab = jQuery( '.ult_acord ' ).find( '#' + type );
			const classm = current_tab.addClass( 'ult-tabto-actitleActive' );
			const bg_color = current_tab
				.parents( '.ult-tabto-accordion' )
				.data( 'activebg' );
			const activetitle = current_tab
				.parents( '.ult-tabto-accordion' )
				.data( 'activetitle' );
			const iconcolor = current_tab
				.find( '.aio-icon' )
				.data( 'iconhover' );
			current_tab.css( 'background-color', bg_color );
			current_tab.css( 'color', activetitle );
			current_tab.find( '.aio-icon' ).css( 'color', iconcolor );
			jQuery( '.ult_acord ' )
				.find( '#' + type )
				.parents( 'dt' )
				.next( 'dd' )
				.removeClass( 'ult-tabto-accolapsed' );
		}
	}
} );
