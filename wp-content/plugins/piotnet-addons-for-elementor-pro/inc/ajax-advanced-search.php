<?php
	add_action( 'wp_ajax_pafe_advanced_search', 'pafe_advanced_search' );
	add_action( 'wp_ajax_nopriv_pafe_advanced_search', 'pafe_advanced_search' );
	add_action( 'wp_ajax_pafe_advanced_search_tax', 'pafe_advanced_search_tax' );
	add_action( 'wp_ajax_nopriv_pafe_advanced_search_tax', 'pafe_advanced_search_tax' );

	function pafe_advanced_search() {

		if (isset($_POST['taxonomy'])) {
			$term = isset($_POST['term']) ? $_POST['term'] : '';
			$taxonomy = isset($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
			$product_check = false;
			$posts_per_page = $_POST['posts_per_page'];
			$noresult = $_POST['no_result'];
			$result_footer = $_POST['result_footer'];

			if (strpos($taxonomy, 'product_') !== false) {
	          $product_check = true;
	       	}


			if (strpos($term, '|')) {
				$term = explode('|', $term);
				$term = $term[0];
			}

			if ($term != 'all') {
						$paged = ( isset( $_POST['paged']) ) ? $_POST['paged'] : "";
							$args = array(
							    's' => $_POST['keywords'],
							    'tax_query' => array(                    
				    				'relation' => 'AND',                      
								      array(
								        'taxonomy' => $_POST['taxonomy'],               
								        'field' => 'slug',                    
								        'terms' => $term,    
								        'include_children' => false,          
								        'operator' => 'IN' 
								      ),
								 ),     
								 'posts_per_page' => $posts_per_page,
							);

						if(!empty($paged)) { 
						 	$args['paged'] = $paged;
						}
						
						$the_query = new WP_Query($args);
				?>

				<div class="pafe-advanced-search__result-wrapper">
				<?php
					if (function_exists("pafe_pagination")) {
		            	pafe_pagination($the_query->max_num_pages,2,$paged);
					}

					if( $the_query->have_posts() ) : while( $the_query->have_posts() ) : $the_query->the_post();
		                   global $product;
				?>
								<div class="pafe-advanced-search__results-item">
									<a class="pafe-advanced-search__results-item-link" href="<?php the_permalink();?>">
										<div class="pafe-advanced-search__results-item-thumbnail">
											<img src='<?php the_post_thumbnail_url();?>' class='pafe-advanced-search__results-item-thumbnail-image' alt>
										</div>
										<div class="pafe-advanced-search__results-item-content-wrapper">
											<div class="pafe-advanced-search__results-item-title">
												<?php echo get_the_title();?>
											</div>
											<?php if($product_check) :?>
											 <div class="pafe-single-price floatright">
	                                         <h2>
	                                         	<span>Price : </span><?php echo $product->get_price_html(); ?>
	                                         </h2>
	                                         </div>
	                                        <?php endif;?>
											<div class="pafe-advanced-search__results-item-content">
												<?php 
												$content = get_the_content();
												echo wp_trim_words( $content, $num_words = 20, $more = null );?>
											</div>		
										</div>
									</a>	
								</div>
							<?php endwhile;
							else: ?> <div class="pafe-advanced-search__results-no-result-message"><?php echo __( $noresult, 'pafe' ); ?></div>
						<?php endif;
				if( $the_query->have_posts() ) :?>							
								<div class="pafe-advanced-search__results-footer">
									<button class="pafe-advanced-search__results-full"><?php echo __( $result_footer, 'pafe' ); ?></button>
								</div><?php
				endif;
				?></div><?php
						wp_reset_postdata();
				wp_die();

			} elseif ($term == 'all') {
				$taxonomy = $_POST['taxonomy'];
				$terms = get_terms($taxonomy); 
				$posts_per_page = $_POST['posts_per_page'];
				$noresult = $_POST['no_result'];
				$result_footer = $_POST['result_footer'];
				$term_ids = wp_list_pluck( $terms, 'term_id' );
				$paged = ( isset( $_POST['paged']) ) ? $_POST['paged'] : "";
				$args = array(
				    's' => $_POST['keywords'],
				    'tax_query' => array(                    
	    				'relation' => 'AND',                      
					      array(
					        'taxonomy' => $_POST['taxonomy'],               
					        'field' => 'term_id',                    
					        'terms' => $term_ids,    
					        'include_children' => false,          
					        'operator' => 'IN' 
					      )
					 ),  
					 'posts_per_page' => $posts_per_page,
				);
				
				if(!empty($paged)) {
					$args['paged'] = $paged;
				}

				$the_query = new WP_Query($args);
				?>
				<div class="pafe-advanced-search__result-wrapper">
				<?php

				if (function_exists("pafe_pagination")) {
	                      pafe_pagination($the_query->max_num_pages,2,$paged);
				}

				if( $the_query->have_posts() ) : while( $the_query->have_posts() ) : $the_query->the_post();
					global $product; ?>
								<div class="pafe-advanced-search__results-item">
									<a class="pafe-advanced-search__results-item-link" href="<?php the_permalink();?>">
										<div class="pafe-advanced-search__results-item-thumbnail">
											<img src='<?php the_post_thumbnail_url();?>' class='pafe-advanced-search__results-item-thumbnail-image' alt>
										</div>
										<div class="pafe-advanced-search__results-item-content-wrapper">
											<div class="pafe-advanced-search__results-item-title">
												<?php echo get_the_title();?>
											</div>

											<?php  if($product_check) : ?>
	                                       	<div class="pafe-single-price floatright">
		                                       	<h2>
		                            	          <span> Price : </span>
		                            	           <?php echo $product->get_price_html(); ?>
		                            	       	</h2>
	                                      	</div>
	                                      <?php endif;?>

											<div class="pafe-advanced-search__results-item-content">
												<?php 
												$content = get_the_content();
												echo wp_trim_words( $content, $num_words = 20, $more = null );?>
											</div>		
										</div>
									</a>	
								</div>

							<?php endwhile;
							else: ?> <div class="pafe-advanced-search__results-no-result-message"><?php echo __( $noresult, 'pafe' ); ?></div>
						<?php endif;
				if( $the_query->have_posts() ) :?>							
								<div class="pafe-advanced-search__results-footer">
									<button type="submit" class="pafe-advanced-search__results-full"><?php echo __( $result_footer, 'pafe' ); ?></button>
								</div><?php
				endif;
				?></div><?php
						wp_reset_postdata();
				wp_die();
			};
		} else {
			$term = isset($_POST['term']) ? $_POST['term'] : '';
			$taxonomy = isset($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
			$product_check = false;
			$posts_per_page = $_POST['posts_per_page'];
			$noresult = $_POST['no_result'];
			$result_footer = $_POST['result_footer'];
			if (strpos($taxonomy, 'product_') !== false) {
	          $product_check = true;
	       	}

			if (strpos($term, '|')) {
				$term = explode('|', $term);
				$term = $term[0];
			}
					$paged = ( isset( $_POST['paged']) ) ? $_POST['paged'] : "";
							$args = array(
							    's' => $_POST['keywords'],
								 'posts_per_page' => $posts_per_page,
							);

						if(!empty($paged)) { 
						 	$args['paged'] = $paged;
						}
						
						$the_query = new WP_Query($args);
				?>

				<div class="pafe-advanced-search__result-wrapper">
				<?php
					if (function_exists("pafe_pagination")) {
		            	pafe_pagination($the_query->max_num_pages,2,$paged);
					}

					if( $the_query->have_posts() ) : while( $the_query->have_posts() ) : $the_query->the_post();
		                   global $product;
				?>
								<div class="pafe-advanced-search__results-item">
									<a class="pafe-advanced-search__results-item-link" href="<?php the_permalink();?>">
										<div class="pafe-advanced-search__results-item-thumbnail">
											<img src='<?php the_post_thumbnail_url();?>' class='pafe-advanced-search__results-item-thumbnail-image' alt>
										</div>
										<div class="pafe-advanced-search__results-item-content-wrapper">
											<div class="pafe-advanced-search__results-item-title">
												<?php echo get_the_title();?>
											</div>
											<?php if($product_check) :?>
											 <div class="pafe-single-price floatright">
	                                         <h2>
	                                         	<span>Price : </span><?php echo $product->get_price_html(); ?>
	                                         </h2>
	                                         </div>
	                                        <?php endif;?>
											<div class="pafe-advanced-search__results-item-content">
												<?php 
												$content = get_the_content();
												echo wp_trim_words( $content, $num_words = 20, $more = null );?>
											</div>		
										</div>
									</a>	
								</div>
							<?php endwhile;
							else: ?> <div class="pafe-advanced-search__results-no-result-message"><?php echo __( $noresult, 'pafe' ); ?></div>
						<?php endif;
				if( $the_query->have_posts() ) :?>							
								<div class="pafe-advanced-search__results-footer">
									<button type="submit" class="pafe-advanced-search__results-full"><?php echo __( $result_footer, 'pafe' ); ?></button>
								</div><?php
				endif;
				?></div><?php
						wp_reset_postdata();
				wp_die();
		}	
	}

	function pafe_advanced_search_tax() {
		$term = $_POST['term'];
		$taxonomy = $_POST['taxonomy'];
		$product_check = false;
		$posts_per_page = $_POST['posts_per_page'];
		$noresult = $_POST['no_result'];
		$result_footer = $_POST['result_footer'];

		 if (strpos($taxonomy, 'product_') !== false) {
         	$product_check = true;
       	 }

		if (strpos($term, '|')) {
			$term = explode('|', $term);
			$term = $term[0];
		}

		if ($term != 'all') {
			$paged = ( isset( $_POST['paged']) ) ? $_POST['paged'] : "";
			$args = array(
			    's' => $_POST['keywords'],
			    'tax_query' => array(                    
    				'relation' => 'AND',                      
				      array(
				        'taxonomy' => $_POST['taxonomy'],               
				        'field' => 'slug',                    
				        'terms' => $term,    
				        'include_children' => false,          
				        'operator' => 'IN' 
				      )
				 ),
				 'posts_per_page' => $posts_per_page,     
			);

			if(!empty($paged)) {
				$args['paged'] = $paged;
			}
			
			$the_query = new WP_Query($args);
			?>
			<div class="pafe-advanced-search__result-wrapper">
			<?php

			if (function_exists("pafe_pagination")) {
                      pafe_pagination($the_query->max_num_pages,2,$paged);
			}
			
			if( $the_query->have_posts() ) : while( $the_query->have_posts() ) : $the_query->the_post();
				global $product; ?>
							<div class="pafe-advanced-search__results-item">
								<a class="pafe-advanced-search__results-item-link" href="<?php the_permalink();?>">
									<div class="pafe-advanced-search__results-item-thumbnail">
										<img src='<?php the_post_thumbnail_url();?>' class='pafe-advanced-search__results-item-thumbnail-image' alt>
									</div>
									<div class="pafe-advanced-search__results-item-content-wrapper">
										<div class="pafe-advanced-search__results-item-title">
											<?php echo get_the_title();?>
										</div>

										<?php  if($product_check) : ?>
                                       	<div class="pafe-single-price floatright">
	                                       	<h2>
	                            	          <span> Price : </span>
	                            	           <?php echo $product->get_price_html(); ?>
	                            	       	</h2>
                                      	</div>
                                      <?php endif;?>

										<div class="pafe-advanced-search__results-item-content">
											<?php 
											$content = get_the_content();
											echo wp_trim_words( $content, $num_words = 20, $more = null );?>
										</div>		
									</div>
								</a>	
							</div>
						<?php endwhile;
						else: ?> <div class="pafe-advanced-search__results-no-result-message"><?php echo __( $noresult, 'pafe' ); ?></div>
					<?php endif;
			if( $the_query->have_posts() ) :?>							
							<div class="pafe-advanced-search__results-footer">
								<button type="submit" class="pafe-advanced-search__results-full"><?php echo __( $result_footer, 'pafe' ); ?></button>
							</div><?php
			endif;
			?></div><?php
					wp_reset_postdata();
			wp_die();

		} elseif ($term == 'all') {
			$taxonomy = $_POST['taxonomy'];
			$posts_per_page = $_POST['posts_per_page'];
			$noresult = $_POST['no_result'];
			$result_footer = $_POST['result_footer'];
			$terms = get_terms($taxonomy); 
			$term_ids = wp_list_pluck( $terms, 'term_id' );
			$paged = ( isset( $_POST['paged']) ) ? $_POST['paged'] : "";
			$args = array(
			    's' => $_POST['keywords'],
			    'tax_query' => array(                    
    				'relation' => 'AND',                      
				      array(
				        'taxonomy' => $_POST['taxonomy'],               
				        'field' => 'term_id',                    
				        'terms' => $term_ids,    
				        'include_children' => false,          
				        'operator' => 'IN' 
				      )
				 ),  
				 'posts_per_page' => $posts_per_page,
			);

			if(!empty($paged)) {
				$args['paged'] = $paged;
			}

			$the_query = new WP_Query($args);
			?>
			<div class="pafe-advanced-search__result-wrapper">
			<?php

			if (function_exists("pafe_pagination")) {
                      pafe_pagination($the_query->max_num_pages,2,$paged);
			}

			if( $the_query->have_posts() ) : while( $the_query->have_posts() ) : $the_query->the_post();
				global $product; ?>
							<div class="pafe-advanced-search__results-item">
								<a class="pafe-advanced-search__results-item-link" href="<?php the_permalink();?>">
									<div class="pafe-advanced-search__results-item-thumbnail">
										<img src='<?php the_post_thumbnail_url();?>' class='pafe-advanced-search__results-item-thumbnail-image' alt>
									</div>
									<div class="pafe-advanced-search__results-item-content-wrapper">
										<div class="pafe-advanced-search__results-item-title">
											<?php echo get_the_title();?>
										</div>

										<?php  if($product_check) : ?>
                                       	<div class="pafe-single-price floatright">
	                                       	<h2>
	                            	          <span> Price : </span>
	                            	           <?php echo $product->get_price_html(); ?>
	                            	       	</h2>
                                      	</div>
                                      <?php endif;?>

										<div class="pafe-advanced-search__results-item-content">
											<?php 
											$content = get_the_content();
											echo wp_trim_words( $content, $num_words = 20, $more = null );?>
										</div>		
									</div>
								</a>	
							</div>
						<?php endwhile;
						else: ?> <div class="pafe-advanced-search__results-no-result-message"><?php echo __( $noresult, 'pafe' ); ?></div>
					<?php endif;
			if( $the_query->have_posts() ) :?>							
							<div class="pafe-advanced-search__results-footer">
								<button type="submit" class="pafe-advanced-search__results-full"><?php echo __( $result_footer, 'pafe' ); ?></button>
							</div><?php
			endif;
			?></div><?php
					wp_reset_postdata();
			wp_die();
		};
	}	
function pafe_pagination($pages = '',$range = 2, $paged=1)
{
    $showitems = ($range * 2)+1;

    if(empty($paged)) $paged = 1;

    if($pages == '')
    {
        global $wp_query;

        $pages = $wp_query->max_num_pages;

        if(!$pages)
        {
            $pages = 1;
        }
    }

    if($pages !=1)
    {
      
        echo "<div class='pagination'> <ul class='pafe_pagination'>";

        if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<li class='page-item'><span data-advanced-search-paged='" . 1 . "'>&laquo;</span></li>";

        for ($i=1; $i <= $pages; $i++)
        {
            if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
            {
                echo ($paged == $i)? "<li class=\"page-item active\"><span class='page-link page-link-active'>".$i."</span></li>":"<li class='page-item'> <span data-advanced-search-paged='" . $i . "' class=\"page-link\">".$i."</span></li>";
            }
        }

        if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo " <li class='page-item'><span data-advanced-search-paged='" . $pages . "'>&raquo;</span></li>";
        echo "</ul></div>\n";
    }
}