document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', wcml_switch_currency_handler);
});

function wcml_switch_currency_handler(event) {
    var targetElement = event.target;

    if (targetElement.matches('.wcml_currency_switcher a')) {
        event.preventDefault();

        if (targetElement.disabled ||
            targetElement.parentElement.classList.contains('wcml-cs-active-currency') ||
            targetElement.classList.contains('wcml-cs-active-currency')) {
            return;
        }

        document.removeEventListener('click', wcml_switch_currency_handler);
        wcml_load_currency(targetElement.getAttribute('rel'));
    }
}

function wcml_load_currency(currency, force_switch) {
    force_switch = force_switch || 0;

    var ajax_loader = document.createElement('img');
    ajax_loader.className = 'wcml-spinner';
    ajax_loader.width = 16;
    ajax_loader.height = 16;
    ajax_loader.src = wcml_mc_settings.wcml_spinner;

    var switchers = document.querySelectorAll('.wcml_currency_switcher');
    switchers.forEach(function(switcher) {
        switcher.appendChild(ajax_loader);
    });

    var formData = new FormData();
    formData.append('action', 'wcml_switch_currency');
    formData.append('currency', currency);
    formData.append('force_switch', force_switch);
    formData.append('params', window.location.search.substr(1));

    var xhr = new XMLHttpRequest();
    xhr.open('POST', woocommerce_params.ajax_url);
    xhr.responseType = 'json';
    xhr.onload = function() {
        var response = xhr.response;

        if (response.error) {
            alert(response.error);
        } else if (response.data && response.data.prevent_switching) {
            wcml_insert_ajax_html_response_in_dom(response.data.prevent_switching);
        } else {
            var target_location = window.location.href;
            if (target_location.includes('#') || wcml_mc_settings.cache_enabled) {
                var url_dehash = target_location.split('#');
                var hash = url_dehash.length > 1 ? '#' + url_dehash[1] : '';

                target_location = url_dehash[0].replace(/&wcmlc(\=[^&]*)?(?=&|$)|wcmlc(\=[^&]*)?(&|$)/, '').replace(/\?$/, '');
                var url_glue = target_location.includes('?') ? '&' : '?';
                target_location += url_glue + 'wcmlc=' + currency + hash;
            }

            wcml_reset_cart_fragments();

            window.location = wcml_maybe_adjust_widget_price(target_location, response.data);;
        }
    };
    xhr.send(formData);
}

function wcml_insert_ajax_html_response_in_dom(html) {
    // Insert the HTML fragment without the script
    const div = document.createElement('div');
    div.innerHTML = html;
    const script = div.getElementsByTagName('script')[0];
    const scriptContent = script.innerText || script.textContent;

    // Remove the script tags from the div and grab their content
    script.parentNode.removeChild(script);

    // Now insert the cleaned HTML
    document.body.insertAdjacentHTML('beforeend', div.innerHTML);

    // Create a new script element and add the extracted script content
    const scriptToExecute = document.createElement('script');
    scriptToExecute.type = 'text/javascript';
    scriptToExecute.id = 'wcml-cart-dialog-script';
    scriptToExecute.appendChild(document.createTextNode(scriptContent));

    // Append the new script element to the document so it will execute
    document.body.appendChild(scriptToExecute);
}

function wcml_maybe_adjust_widget_price(target_location, response) {
    if (response.min_price) {
        target_location = target_location.replace(/(min_price=)(\d+)/, "$1" + response.min_price);
    }

    if (response.max_price) {
        target_location = target_location.replace(/(max_price=)(\d+)/, "$1" + response.max_price);
    }

    return target_location;
}
