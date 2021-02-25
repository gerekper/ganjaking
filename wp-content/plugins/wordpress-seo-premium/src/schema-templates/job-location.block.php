<?php // phpcs:ignore Internal.NoCodeFound ?>
{{block name="yoast/job-location" title="<?php esc_attr_e( 'Location', 'wordpress-seo-premium' ); ?>" category="common" parent=[ "yoast/job-posting" ] supports={"multiple": false} }}
<div class={{class-name}}>
	{{variation name="office-location" title="<?php esc_attr_e( 'Office location', 'wordpress-seo-premium' ); ?>" description="<?php esc_attr_e( 'Address where the office is located', 'wordpress-seo-premium' ); ?>" scope=[ "block" ] icon="<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='36' height='36'><path d='M12 4L4 7.9V20h16V7.9L12 4zm6.5 14.5H14V13h-4v5.5H5.5V8.8L12 5.7l6.5 3.1v9.7z'></path></svg>" innerBlocks=[ { "name": "yoast/office-location", "attributes": { "address-title": "<?php esc_attr_e( 'Address', 'wordpress-seo-premium' ); ?>", "postal-code-title": "<?php esc_attr_e( 'Postal code', 'wordpress-seo-premium' ); ?>", "city-title": "<?php esc_attr_e( 'City', 'wordpress-seo-premium' ); ?>", "region-title": "<?php esc_attr_e( 'Region', 'wordpress-seo-premium' ); ?>", "country-title": "<?php esc_attr_e( 'Country', 'wordpress-seo-premium' ); ?>" } }] }}
	{{variation name="remote-location" title="<?php esc_attr_e( 'Remote job', 'wordpress-seo-premium' ); ?>" description="<?php esc_attr_e( 'This job is 100% remote', 'wordpress-seo-premium' ); ?>" scope=[ "block" ] icon="<svg xmlns='http://www.w3.org/2000/svg' viewBox='-3.2 -3.2 24 24' width='36' height='36'><path d='M9 0C4.03 0 0 4.03 0 9s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zM1.11 9.68h2.51c.04.91.167 1.814.38 2.7H1.84c-.403-.85-.65-1.764-.73-2.7zm8.57-5.4V1.19c.964.366 1.756 1.08 2.22 2 .205.347.386.708.54 1.08l-2.76.01zm3.22 1.35c.232.883.37 1.788.41 2.7H9.68v-2.7h3.22zM8.32 1.19v3.09H5.56c.154-.372.335-.733.54-1.08.462-.924 1.255-1.64 2.22-2.01zm0 4.44v2.7H4.7c.04-.912.178-1.817.41-2.7h3.21zm-4.7 2.69H1.11c.08-.936.327-1.85.73-2.7H4c-.213.886-.34 1.79-.38 2.7zM4.7 9.68h3.62v2.7H5.11c-.232-.883-.37-1.788-.41-2.7zm3.63 4v3.09c-.964-.366-1.756-1.08-2.22-2-.205-.347-.386-.708-.54-1.08l2.76-.01zm1.35 3.09v-3.04h2.76c-.154.372-.335.733-.54 1.08-.464.92-1.256 1.634-2.22 2v-.04zm0-4.44v-2.7h3.62c-.04.912-.178 1.817-.41 2.7H9.68zm4.71-2.7h2.51c-.08.936-.327 1.85-.73 2.7H14c.21-.87.337-1.757.38-2.65l.01-.05zm0-1.35c-.046-.894-.176-1.78-.39-2.65h2.16c.403.85.65 1.764.73 2.7l-2.5-.05zm1-4H13.6c-.324-.91-.793-1.76-1.39-2.52 1.244.56 2.325 1.426 3.14 2.52h.04zm-9.6-2.52c-.597.76-1.066 1.61-1.39 2.52H2.65c.815-1.094 1.896-1.96 3.14-2.52zm-3.15 12H4.4c.324.91.793 1.76 1.39 2.52-1.248-.567-2.33-1.445-3.14-2.55l-.01.03zm9.56 2.52c.597-.76 1.066-1.61 1.39-2.52h1.76c-.82 1.08-1.9 1.933-3.14 2.48l-.01.04z'></path></svg>" innerBlocks=[ { "name": "yoast/remote-location" } ]}}
	{{inner-blocks appender=false}}
	{{variation-picker}}
</div>
