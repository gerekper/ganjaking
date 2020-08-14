(function(document,$){var $cache={};function generate_cache(){$cache.document=$(document);$cache.first_payment_date=$('.first-payment-date');$cache.is_variable_subscription=0<$('div.product-type-variable-subscription').length}
function attach_events(){if($cache.is_variable_subscription){$cache.document.on('found_variation',update_first_payment_element);$cache.document.on('reset_data',clear_first_payment_element)}}
function update_first_payment_element(event,variation_data){$cache.first_payment_date.html(variation_data.first_payment_html)}
function clear_first_payment_element(){$cache.first_payment_date.html('')}
function init(){generate_cache();attach_events()}
$(init)})(document,jQuery)