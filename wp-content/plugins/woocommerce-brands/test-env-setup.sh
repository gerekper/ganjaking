#!/usr/bin/env bash

ENABLE_HPOS="${ENABLE_HPOS:-0}"
ENABLE_NEW_PRODUCT_EDITOR="${ENABLE_NEW_PRODUCT_EDITOR:-0}"
ENABLE_TRACKING="${ENABLE_TRACKING:-0}"

echo -e 'Normalize permissions for wp-content directory \n'
docker-compose -f $(wp-env install-path)/docker-compose.yml run --rm -u www-data -e HOME=/tmp tests-wordpress sh -c "chmod -c ugo+w /var/www/html/wp-config.php \
&& chmod -c ugo+w /var/www/html/wp-content \
&& chmod -c ugo+w /var/www/html/wp-content/themes \
&& chmod -c ugo+w /var/www/html/wp-content/plugins"

docker-compose -f $(wp-env install-path)/docker-compose.yml run --rm -u www-data -e HOME=/tmp tests-cli sh -c "ls \
&& wp theme install storefront --activate \
&& wp plugin install https://github.com/WP-API/Basic-Auth/archive/master.zip --activate \
&& wp plugin install wp-mail-logging --activate \
&& wp plugin install wordpress-importer --activate \
&& wp plugin install https://github.com/woocommerce/woocommerce-reset/archive/refs/heads/trunk.zip --activate \
&& wp rewrite structure '/%postname%/' --hard \
&& wp user create customer customer@woocommercecoree2etestsuite.com \
	--user_pass=password \
	--role=subscriber \
	--first_name='Jane' \
	--last_name='Smith' \
	--user_registered='2022-01-01 12:23:45'"

echo -e 'Import WooCommerce sample product data using wordpress-importer plugin \n'
docker-compose -f $(wp-env install-path)/docker-compose.yml run --rm -u www-data -e HOME=/tmp tests-cli sh -c "wp import wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors=skip"

echo -e 'Set Basic WooCommerce Settings \n'
docker-compose -f $(wp-env install-path)/docker-compose.yml run --rm -u www-data -e HOME=/tmp tests-cli sh -c "wp option set woocommerce_store_address '60 29th Street' \
&& wp option set woocommerce_store_address_2 '#343' \
&& wp option set woocommerce_store_city 'San Francisco' \
&& wp option set woocommerce_default_country 'US:CA' \
&& wp option set woocommerce_store_postcode '94110' \
&& wp option set woocommerce_currency 'USD' \
&& wp option set woocommerce_product_type 'both' \
&& wp option set woocommerce_allow_tracking "no" \
&& wp option set --format=json woocommerce_stripe_settings '{\"enabled\":\"no\",\"create_account\":false,\"email\":false}' \
&& wp option set --format=json woocommerce_ppec_paypal_settings '{\"reroute_requests\":false,\"email\":false}' \
&& wp option set --format=json woocommerce_cheque_settings '{\"enabled\":\"no\"}' \
&& wp option set --format=json woocommerce_bacs_settings '{\"enabled\":\"no\"}' \
&& wp option set --format=json woocommerce_cod_settings '{\"enabled\":\"yes\"}'"

echo -e 'Setup WooCommerce Shop Pages \n'
docker-compose -f $(wp-env install-path)/docker-compose.yml run --rm -u www-data -e HOME=/tmp tests-cli sh -c "wp wc tool run install_pages --user=admin"

if [ $ENABLE_HPOS == 1 ]; then
	echo 'Enable the COT feature'
	docker-compose -f $(wp-env install-path)/docker-compose.yml run --rm -u www-data -e HOME=/tmp tests-cli sh -c "wp plugin install https://gist.github.com/vedanshujain/564afec8f5e9235a1257994ed39b1449/archive/b031465052fc3e04b17624acbeeb2569ef4d5301.zip --activate"
fi

if [ $ENABLE_NEW_PRODUCT_EDITOR == 1 ]; then
	echo 'Enable the new product editor feature'
	docker-compose -f $(wp-env install-path)/docker-compose.yml run --rm -u www-data -e HOME=/tmp tests-cli sh -c "wp plugin install https://github.com/woocommerce/woocommerce-experimental-enable-new-product-editor/releases/download/0.1.0/woocommerce-experimental-enable-new-product-editor.zip --activate"
fi

if [ $ENABLE_TRACKING == 1 ]; then
	echo 'Enable tracking'
	docker-compose -f $(wp-env install-path)/docker-compose.yml run --rm -u $(id -u) -e HOME=/tmp tests-cli sh -c "wp option update woocommerce_allow_tracking 'yes'"
fi
