<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load class collection files

require_once 'rightpress-meta.class.php';

require_once 'object-admin/rightpress-object-admin.class.php';
require_once 'object-admin/rightpress-wc-custom-order-admin.class.php';
require_once 'object-admin/rightpress-wc-custom-order-object-admin.class.php';
require_once 'object-admin/rightpress-wc-object-admin.class.php';
require_once 'object-admin/rightpress-wc-product-object-admin.class.php';
require_once 'object-admin/rightpress-wp-custom-object-admin.class.php';
require_once 'object-admin/rightpress-wp-custom-post-object-admin.class.php';
require_once 'object-admin/rightpress-wp-log-entry-admin.class.php';
require_once 'object-admin/rightpress-wp-object-admin.class.php';
require_once 'object-admin/rightpress-wp-post-object-admin.class.php';

require_once 'object-admin/interfaces/rightpress-object-admin-interface.php';
require_once 'object-admin/interfaces/rightpress-wc-product-object-admin-interface.php';
require_once 'object-admin/interfaces/rightpress-wp-custom-post-object-admin-interface.php';
require_once 'object-admin/interfaces/rightpress-wp-post-object-admin-interface.php';

require_once 'object-admin/list-tables/rightpress-wp-list-table.class.php';

require_once 'object-controllers/rightpress-object-controller.class.php';
require_once 'object-controllers/rightpress-wc-custom-order-controller.class.php';
require_once 'object-controllers/rightpress-wc-custom-order-object-controller.class.php';
require_once 'object-controllers/rightpress-wc-object-controller.class.php';
require_once 'object-controllers/rightpress-wc-product-object-controller.class.php';
require_once 'object-controllers/rightpress-wp-custom-object-controller.class.php';
require_once 'object-controllers/rightpress-wp-custom-post-object-controller.class.php';
require_once 'object-controllers/rightpress-wp-log-entry-controller.class.php';
require_once 'object-controllers/rightpress-wp-object-controller.class.php';

require_once 'object-controllers/interfaces/rightpress-object-controller-interface.php';
require_once 'object-controllers/interfaces/rightpress-wc-custom-order-controller-interface.php';
require_once 'object-controllers/interfaces/rightpress-wc-custom-order-object-controller-interface.php';
require_once 'object-controllers/interfaces/rightpress-wc-product-object-controller-interface.php';
require_once 'object-controllers/interfaces/rightpress-wp-custom-object-controller-interface.php';
require_once 'object-controllers/interfaces/rightpress-wp-custom-post-object-controller-interface.php';
require_once 'object-controllers/interfaces/rightpress-wp-object-controller-interface.php';

require_once 'object-data-stores/rightpress-object-data-store.class.php';
require_once 'object-data-stores/rightpress-wc-custom-order-data-store.class.php';
require_once 'object-data-stores/rightpress-wc-custom-order-object-data-store.class.php';
require_once 'object-data-stores/rightpress-wc-object-data-store.class.php';
require_once 'object-data-stores/rightpress-wc-product-object-data-store.class.php';
require_once 'object-data-stores/rightpress-wp-custom-object-data-store.class.php';
require_once 'object-data-stores/rightpress-wp-custom-post-object-data-store.class.php';
require_once 'object-data-stores/rightpress-wp-log-entry-data-store.class.php';
require_once 'object-data-stores/rightpress-wp-object-data-store.class.php';

require_once 'object-data-stores/interfaces/rightpress-object-data-store-interface.php';
require_once 'object-data-stores/interfaces/rightpress-wc-custom-order-data-store-interface.php';
require_once 'object-data-stores/interfaces/rightpress-wp-custom-object-data-store-interface.php';
require_once 'object-data-stores/interfaces/rightpress-wp-custom-post-object-data-store-interface.php';
require_once 'object-data-stores/interfaces/rightpress-wp-object-data-store-interface.php';

require_once 'objects/rightpress-object.class.php';
require_once 'objects/rightpress-wc-custom-order-object.class.php';
require_once 'objects/rightpress-wc-custom-order.class.php';
require_once 'objects/rightpress-wc-object.class.php';
require_once 'objects/rightpress-wc-product-object.class.php';
require_once 'objects/rightpress-wp-custom-object.class.php';
require_once 'objects/rightpress-wp-custom-post-object.class.php';
require_once 'objects/rightpress-wp-log-entry.class.php';
require_once 'objects/rightpress-wp-object.class.php';

require_once 'objects/interfaces/rightpress-object-interface.php';
require_once 'objects/interfaces/rightpress-wp-object-interface.php';
