(function() {
	'use strict';
	tinymce.create('tinymce.plugins.ShortcodeMce', {
		init : function(ed, url){
			tinymce.plugins.ShortcodeMce.theurl = url;
		},
		createControl : function(btn, e) {
			if ( btn == "porto_shortcodes_button" ) {
				var a = this;
				var btn = e.createSplitButton('button', {
					title: "Porto Shortcodes",
					image: tinymce.plugins.ShortcodeMce.theurl +"/shortcodes.png",
					icons: false
				});
				btn.onRenderMenu.add(function (c, b) {
					a.render( b, "Block", "porto_block" );
					a.render( b, "Container", "porto_container" );
					a.render( b, "Animation", "porto_animation" );
					a.render( b, "Testimonial", "porto_testimonial" );
					a.render( b, "Blockquote", "porto_blockquote" );
					a.render( b, "Content Box", "porto_content_box" );
					a.render( b, "History", "porto_history" );
					a.render( b, "Grid Container", "porto_grid_container" );
					a.render( b, "Grid Item", "porto_grid_item" );
					a.render( b, "Links Block", "porto_links_block" );
					a.render( b, "Links Item", "porto_links_item" );
					a.render( b, "Recent Posts", "porto_recent_posts" );
					a.render( b, "Recent Portfolios", "porto_recent_portfolios" );
					a.render( b, "Recent Members", "porto_recent_members" );
					a.render( b, "Blog", "porto_blog" );
					a.render( b, "Portfolios", "porto_portfolios" );
					a.render( b, "FAQs", "porto_faqs" );
					a.render( b, "Members", "porto_members" );
					a.render( b, "Concept", "porto_concept" );
					a.render( b, "Map Section", "porto_map_section" );
					a.render( b, "Recent Products", "porto_recent_products" );
					a.render( b, "Featured Products", "porto_featured_products" );
					a.render( b, "Sale Products", "porto_sale_products" );
					a.render( b, "Best Selling Products", "porto_best_selling_products" );
					a.render( b, "Top Rated Products", "porto_top_rated_products" );
					a.render( b, "Products", "porto_products" );
					a.render( b, "Product Category", "porto_product_category" );
					a.render( b, "Product Attribute", "porto_product_attribute" );
					a.render( b, "Product", "porto_product" );
					a.render( b, "Product Categories", "porto_product_categories" );
					a.render( b, "Widget Woocommerce Products", "porto_widget_woo_products" );
					a.render( b, "Widget Woocommerce Top Rated Products", "porto_widget_woo_top_rated_products" );
					a.render( b, "Widget Woocommerce Recently Viewed", "porto_widget_woo_recently_viewed" );
					a.render( b, "Widget Woocommerce Recent Reviews", "porto_widget_woo_recent_reviews" );
					a.render( b, "Widget Woocommerce Product Tags", "porto_widget_woo_product_tags" );
				});

				return btn;
			}
			return null;
		},
		render : function(ed, title, id) {
			ed.add({
				title: title,
				onclick: function () {
					porto_shortcode_open(title, id);
					return false;
				}
			})
		}

	});
	tinymce.PluginManager.add("shortcodes", tinymce.plugins.ShortcodeMce);

})();