jQuery(document).ready(function($) {
    // Captura los datos aquí. Ejemplo:
    const navegadorBase64 = btoa(navigator.userAgent);
    const idioma = navigator.language;
    const alturaPantalla = window.screen.height;
    const anchuraPantalla = window.screen.width;
    // ... otros datos ...

    // Extraer el order_id del script wc-settings-js-before
    const wcSettingsScript = document.getElementById('wc-settings-js-before')?.textContent;
    let RedsysOrderID = '';
    if (wcSettingsScript) {
        const regex = /order_id%22%3A(\d+)%2C%22/;
        const match = regex.exec(wcSettingsScript);
        if (match) {
            RedsysOrderID = match[1]; // Capturar el order_id encontrado
        } else {
            console.log("Order ID no encontrado.");
        }
    }

    // Función para enviar los datos
    const enviarDatos = () => {
        $.ajax({
            url: redsysAjax.ajaxurl,
            method: 'POST',
            data: {
                'action': 'redsys_check_order_id',
                'nonce': redsysAjax.nonce,
                'order_id': RedsysOrderID,
                'navegadorBase64': navegadorBase64,
                // ... añadir todos los demás datos ...
            },
            success: function(response) {
                console.log('Datos guardados con éxito:', response);
            },
            error: function(error) {
                console.error('Error al enviar datos:', error);
            }
        });
    };
    enviarDatos();
});
