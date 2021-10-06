<?php

/**
 * @var string $url
 * @var string $next_url
 * @var bool   $allow_skip
 */
$url = $this->url;
$next_url = $this->next_url;
$allow_skip = $this->allow_skip;

?>
<div class="ac-section-group -start">

	<section class="ac-article">
		<h2 class="ac-article__title"><?php _e( 'Welcome to Admin Columns Pro', 'codepress-admin-columns' ); ?></h2>
		<div class="ac-article__body">
			<p>
				<?php

				$page = __( 'network settings page', 'codepress-admin-columns' );

				if ( $url ) {
					$page = sprintf( '<a href="%s">%s</a>', $url, $page );
				}

				printf( __( 'You can activate Admin Columns Pro on the %s.', 'codepress-admin-columns' ), $page );
				?>
			</p>
			<?php if ( $allow_skip ) : ?>
				<a class="button" href="<?= $next_url; ?>"><?php _e( 'Skip step', 'codepress-admin-columns' ); ?></a>
			<?php endif; ?>

		</div>
	</section>

</div>