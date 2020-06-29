/**
 * block js set cookies
 *
 * @var ct-ultimate-gdpr-cookie-block.blocked - via wp_localize_script
 *
 **/

if (ct_ultimate_gdpr_cookie_block && ct_ultimate_gdpr_cookie_block.blocked) {

    function ct_should_block_cookie(cookiename) {
        if (cookiename !== 'ct-ultimate-gdpr-cookie' && (ct_ultimate_gdpr_cookie_block.level < 2 || ct_ultimate_gdpr_cookie_block.blocked.indexOf(cookiename) !== -1)) {
            return true;
        }

        return false;
    }

    try {

        var ct_ultimate_gdpr_cookie_setter_original = document.__lookupSetter__("cookie");
        var ct_ultimate_gdpr_cookie_getter_original = document.__lookupGetter__("cookie");
        var old_cookie = document.cookie;

        Object.defineProperty(document, 'cookie', {

            get: function () {
                return (ct_ultimate_gdpr_cookie_getter_original && ct_ultimate_gdpr_cookie_getter_original.apply(document)) ?
                    ct_ultimate_gdpr_cookie_getter_original.apply(document) : this._value;
            },
            set: function (val) {

                if (val && (val.toLowerCase().indexOf('expires=') >= 0 || val.toLowerCase().indexOf('path=') >= 0 || val.indexOf(';') === -1)) {

                    // single cookie with or without an expires parameter
                    var parts = val.split(';');
                    if(parts[0]){
                        var name = parts[0].split('=')[0];
                        var value = parts[0].split('=')[1];

                        if (ct_should_block_cookie(name)) {
                            return;
                        }
                    }

                } else if (val) {

                    // multiple cookies
                    parts = val.split(';');
                    var cookievalue = '';
                    for (var i in parts) {
                        //some cases have values such as "dc=tld;domain=.wpengine.com;" causing the split to have an extra index
                        if(parts[i]){
                            name = parts[i].split('=')[0].trim();
                            value = parts[i].split('=')[1].trim();

                            if (!ct_should_block_cookie(name)) {
                                if (cookievalue.length) {
                                    cookievalue += '; ';
                                }
                                cookievalue += name + "=" + value;
                            }
                        }
                    }

                    val = cookievalue;

                }

                this._value = val;
                ct_ultimate_gdpr_cookie_setter_original && ct_ultimate_gdpr_cookie_setter_original.apply(document, arguments)

            },
            configurable: true
        });

        document.cookie = old_cookie;

    } catch (e) {
        // console.log(e);
    }

}
