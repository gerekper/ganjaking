var debounce = function (func, wait, immediate) {
    // 'private' variable for instance
    // The returned function will be able to reference this due to closure.
    // Each call to the returned function will share this common timer.
    var timeout;

    // Calling debounce returns a new anonymous function
    return function () {
        // reference the context and args for the setTimeout function
        var context = this,
            args = arguments;

        // Should the function be called now? If immediate is true
        //   and not already in a timeout then the answer is: Yes
        var callNow = immediate && !timeout;

        // This is the basic debounce behaviour where you can call this
        //   function several times, but it will only execute once
        //   [before or after imposing a delay].
        //   Each time the returned function is called, the timer starts over.
        clearTimeout(timeout);

        // Set the new timeout
        timeout = setTimeout(function () {

            // Inside the timeout function, clear the timeout variable
            // which will let the next execution run when in 'immediate' mode
            timeout = null;

            // Check if the function already ran with the immediate flag
            if (!immediate) {
                // Call the original function with apply
                // apply lets you define the 'this' object as well as the arguments
                //    (both captured before setTimeout)
                func.apply(context, args);
            }
        }, wait);

        // Immediate mode and no wait timer? Execute the function..
        if (callNow) func.apply(context, args);
    };
};

/** 
 * Start used on Social Share
 */

jQuery(document).ready(function() {
    jQuery(".bdt-ss-link").on("click", function() {
        var $temp = jQuery("<input>");
        jQuery("body").append($temp);
        $temp.val(jQuery(this).data("url")).select();
        document.execCommand("copy");
        $temp.remove();

        // Update the text to indicate that it has been copied
        jQuery(this).find('.bdt-social-share-title').html(jQuery(this).data('copied'));

        // Reset the text after a delay (e.g., 5 seconds)
        setTimeout(() => {
            jQuery(this).find('.bdt-social-share-title').html(jQuery(this).data('orginal'));
        }, 5000);
    });
});

/** 
 * end Social Share
 */

/**
 * Start Crypto Currency
 */

function returnCurrencySymbol(currency = null) {
    if (currency === null) return "";
    let currency_symbols = {
        USD: "$", // US Dollar
        EUR: "€", // Euro
        CRC: "₡", // Costa Rican Colón
        GBP: "£", // British Pound Sterling
        ILS: "₪", // Israeli New Sheqel
        INR: "₹", // Indian Rupee
        JPY: "¥", // Japanese Yen
        KRW: "₩", // South Korean Won
        NGN: "₦", // Nigerian Naira
        PHP: "₱", // Philippine Peso
        PLN: "zł", // Polish Zloty
        PYG: "₲", // Paraguayan Guarani
        THB: "฿", // Thai Baht
        UAH: "₴", // Ukrainian Hryvnia
        VND: "₫", // Vietnamese Dong
    };
    if (currency_symbols[currency] !== undefined) {
        return currency_symbols[currency];
    } else {
        return ""; // this is means there is not any
    }
}

/**
 * End Crypto Currency
 */

/**
 * Open Offcanvas on Mini Cart Update
 */

jQuery(document).ajaxComplete(function (event, request, settings) {
    if (request.responseJSON && typeof request.responseJSON.cart_hash !== 'undefined' && request.responseJSON.cart_hash) {
        if (jQuery('.bdt-offcanvas').hasClass('__update_cart')) {
            let id = jQuery('.bdt-offcanvas.__update_cart').attr('id');
            bdtUIkit.util.ready(function () {
                bdtUIkit.offcanvas('#' + id).show();
            });
        }
    }
});

/**
 * /Open Offcanvas on Mini Cart Update
 */