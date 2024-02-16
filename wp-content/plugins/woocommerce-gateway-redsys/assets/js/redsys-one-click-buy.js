jQuery(document).ready(function($) {
    $('#one-click-buy-button').on('click', function() {
        // Obtener el ID del producto y la cantidad deseada
        console.log(redsys_pay_one);
        var productId = redsys_pay_one.product_id;
        var qty = $('input.qty').val() || 1;  // Asume 1 si no se puede obtener la cantidad
        console.log('productId: ' + productId );
        console.log('qty: ' + qty );
        
        // Obtener el ID de la variación si es un producto variable
        var variationId = $('input[name="variation_id"]').val();
        
        // Si es un producto variable, usar el ID de la variación en lugar del ID del producto
        if (variationId) {
            console.log('variationId: ' + variationId );
            productId = variationId;
        }
        
        // Obtener el token ID desde el campo oculto
        var tokenId = $('#redsys_token_id').val();
        var billingAgenteNavegador = $('#billing_agente_navegador').val();
        var billingIdiomaNavegador = $('#billing_idioma_navegador').val();
        var billingAlturaPantalla = $('#billing_altura_pantalla').val();
        var billingAnchuraPantalla = $('#billing_anchura_pantalla').val();
        var billingProfundidadColor = $('#billing_profundidad_color').val();
        var billingDiferenciaHoraria = $('#billing_diferencia_horaria').val();
        var billingHttpAcceptHeaders = $('#billing_http_accept_headers').val();
        var billingTzHoraria = $('#billing_tz_horaria').val();
        var billingJsEnabledNavegador = $('#billing_js_enabled_navegador').val();
        var RedsysTokenType = $('#redsys_token_type').val();
        var shippingMethod = '';
        if ($('#one-click-shipping-method').length > 0) {
            // Obtener el valor del método de envío seleccionado
            shippingMethod = $('#one-click-shipping-method').val();
        }

        console.log('tokenId: ' + tokenId );
        console.log("Método de envío seleccionado:", $('#one-click-shipping-method').val());

        
        // Verificar si se obtuvo el ID del producto y el token ID antes de proceder
        if (!productId || !tokenId) {
            alert('No se pudo obtener la información necesaria del producto.');
            return;
        }
        // Hacer la solicitud AJAX para crear la orden
        $.post(
            redsys_pay_one.ajax_url,
            {
                action: 'redsys_one_click_buy',
                product_id: productId,
                qty: qty,
                token_id: tokenId,
                billing_agente_navegador: billingAgenteNavegador,
                billing_idioma_navegador: billingIdiomaNavegador,
                billing_altura_pantalla: billingAlturaPantalla,
                billing_anchura_pantalla: billingAnchuraPantalla,
                billing_profundidad_color: billingProfundidadColor,
                billing_diferencia_horaria: billingDiferenciaHoraria,
                billing_http_accept_headers: billingHttpAcceptHeaders,
                billing_tz_horaria: billingTzHoraria,
                billing_js_enabled_navegador: billingJsEnabledNavegador,
                shipping_method: shippingMethod,
                redsys_token_type: RedsysTokenType,
            },
            function(response) {
                if (response.success) {
                    window.location.href = response.data.redirect_url;
                } else {
                    alert('Hubo un problema al crear el pedido.');
                }
            }
        );
    });
});
