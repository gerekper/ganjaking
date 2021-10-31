function load_wishlist( ajaxurl, nonce ) {
	var xhr = new XMLHttpRequest();
	xhr.open( 'POST', ajaxurl, true );
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.onreadystatechange = function() {
		if ( this.readyState === XMLHttpRequest.DONE && this.status === 200 ) {
			try {
				res = xhr.responseText;
				if ( res ) {
					postMessage( res );
				}
			} catch ( error ) {
				console.warn( error );
			}
		}
	};

	var data = 'action=porto_load_wishlist&nonce=' + nonce;
	xhr.send(data);
}

onmessage = function(e) {
	if ( e.data ) {
		if ( e.data.initWishlist ) {
			setTimeout(function() {
				load_wishlist( e.data.ajaxurl, e.data.nonce );
			}, 300);
		} else if ( e.data.loadWishlist ) {
			load_wishlist( e.data.ajaxurl, e.data.nonce );
		} else if ( e.data.exit ) {
			close();
		}
	}
};
