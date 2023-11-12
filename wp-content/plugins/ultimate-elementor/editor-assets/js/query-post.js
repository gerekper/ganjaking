( function( $ ) {

	jQuery( window ).on( 'elementor:init', function() {
		var ControlQueryPostSearch = elementor.modules.controls.BaseData.extend( {
			
			isPostSearchReady: false,

			getPostTitlesbyID: function() {
				var self = this,
					ids = this.getControlValue();
				
				if ( ! ids ) {
					return;
				}

				if ( ! _.isArray( ids ) ) {
					ids = [ ids ];
				}

				self.addControlSpinner();
				
				$.ajax ({
					url: ajaxurl,
					type:'POST',
					data: {
						action: 'uael_get_posts_title_by_id',
						id: ids,
						nonce: uael_query_script.get_post_nonce,
					},

					success:function(results) {
						
						self.isPostSearchReady = true;

						self.model.set( 'options', results );

						self.render();
					}
				});
			},

			addControlSpinner: function() {
				this.ui.select.prop( 'disabled', true );
				this.$el.find( '.elementor-control-title' ).after( '<span class="elementor-control-spinner">&nbsp;<i class="fa fa-spinner fa-spin"></i>&nbsp;</span>' );
			},
			onReady: function() {
				var self = this;

				this.ui.select.select2({

					placeholder: 'Search',
					allowClear: true,
					minimumInputLength: 2,

					ajax: {
					    url: ajaxurl,
					    dataType: 'json',
					    method: 'post',
					    delay: 250,
					    data: function (params) {
					      	return {
					        	q: params.term, // search term
								post_type: self.model.get( 'post_type' ),
						        action: 'uael_get_posts_by_query',
						        nonce: uael_query_script.get_post_by_query
					    	};
						},
						processResults: function (data) {
				            // parse the results into the format expected by Select2.
				            // since we are using custom formatting functions we do not need to
				            // alter the remote JSON data

				            return {
				                results: data
				            };
				        },
					    cache: true
					},
				});

				
				if ( ! this.isPostSearchReady ) {
					this.getPostTitlesbyID();
				}
			},
			onBeforeDestroy: function() {
				if ( this.ui.select.data( 'select2' ) ) {
					this.ui.select.select2( 'destroy' );
				}

				this.$el.remove();
			}
		} );
		elementor.addControlView( 'uael-query-posts', ControlQueryPostSearch );
	} );


} )( jQuery ); 