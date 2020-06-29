<?php $current_product_id = $_GET['product-id']; ?>

<div class="wrap">
	<h2><?php _e( 'Product Recommendations', 'wc_recommender' ); ?></h2>


	<div>
		<h3>
			<?php
			$label = __( 'Customers also viewed these products', 'wc_recommender' );
			$label = get_option( 'wc_recommender_label_rbpv', $label );
			echo $label;
			?>
		</h3>


		<?php $items = woocommerce_recommender_get_simularity( $current_product_id, array('viewed') ); ?>
		<ul>
			<?php foreach ( $items as $product_id => $score ): ?>
				<li><a href="<?php echo get_permalink( $product_id ); ?>"><?php echo get_the_title( $product_id ); ?></a> <?php echo $score; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>


	<div>
		<h3>
			<?php
			$label = __( 'Customers also purchased these products', 'wc_recommender' );
			$label = get_option( 'wc_recommender_label_rbph', $label );
			echo $label;
			?>
		</h3>

		<?php $items = woocommerce_recommender_get_simularity( $current_product_id, array('completed') ); ?>
		<ul>
			<?php foreach ( $items as $product_id => $score ): ?>
				<li><a href="<?php echo get_permalink( $product_id ); ?>"><?php echo get_the_title( $product_id ); ?></a> <?php echo $score; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	
	
	<div>
		<h3>
			<?php
			$label = __( 'Frequently Purchased Together', 'wc_recommender' );
			$label = get_option( 'wc_recommender_label_fpt', $label );
			echo $label;
			?>
		</h3>

		<?php $items = woocommerce_recommender_get_purchased_together( $current_product_id, array('completed') ); ?>
		<ul>
			<?php foreach ( $items as $product_id => $score ): ?>
				<li><a href="<?php echo get_permalink( $product_id ); ?>"><?php echo get_the_title( $product_id ); ?></a> <?php echo $score; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	
</div>
