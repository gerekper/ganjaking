var products = [], load_first = true, try_count = 0, current_index = 0;

function load_products(sales_vars, ajaxurl, nonce) {
	if (load_first || 'real' == sales_vars.type) {
		var xhr = new XMLHttpRequest();
		xhr.open('POST', ajaxurl, true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function() {
			if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
				try {
					res = JSON.parse( xhr.responseText );
					if ( res.length ) {
						if (products.length) {
							products = res.concat( products );
							var index = products.length - 1;
							for ( ; index > 0; index-- ) {
								if ( products.findIndex( function ( product ) {
									return product.id == products[ index ].id
								} ) < index ) {
									products.splice( index, 1 );
								}
							}
							products.splice( sales_vars.limit );
						} else {
							products = res;
						}
						current_index = 0;
						postMessage(products[0]);
					}
				} catch ( error ) {
					console.warn( error );
				}
			}
		};

		var data = 'action=porto_recent_sale_products&nonce=' + nonce;
		if (load_first) {
			data += '&load_first=1';
			load_first = false;
		}
		xhr.send(data);
	} else if (products.length) {
		current_index = ( current_index + 1 ) % products.length;
		postMessage(products[current_index]);
	}

	if ('real' == sales_vars.type || products.length || try_count < 3) {
		setTimeout( function () {
			try_count++;
			load_products(sales_vars, ajaxurl, nonce);
		}, sales_vars.interval ? parseInt(sales_vars.interval, 10) * 1000 : 60000 );
	}
}

onmessage = function(e) {
	if (e.data) {
		if (e.data.init) {
			setTimeout(function() {
				load_products(e.data.vars, e.data.ajaxurl, e.data.nonce);
			}, e.data.vars.start ? parseInt(e.data.vars.start, 10) * 1000 : 5000);
		} else if (e.data.exit) {
			close();
		}
	}
};
