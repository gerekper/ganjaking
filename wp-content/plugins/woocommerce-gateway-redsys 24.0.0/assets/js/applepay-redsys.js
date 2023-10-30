/**
 * Define Constants needed by the Google Pay API
 */
var VarmerchantId        = apple_redsys.merchantId;
var VarmerchantName      = apple_redsys.merchantName;
var VarcountryCode       = apple_redsys.countryCode;
var VarcurrencyCode      = apple_redsys.currencyCode;
var url_site             = apple_redsys.url_site;

function stringToHex(str) {
    var hex = '';
    for(var i = 0; i < str.length; i++) {
        hex += str.charCodeAt(i).toString(16).padStart(2, '0');
    }
    return hex;
}

function check_payment_status(appleRefereciaRedsys, session) {
    var check_status_interval = setInterval(function() {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'check_payment_status',
                apple_referencia_redsys: appleRefereciaRedsys
            },
            success: function(response) {
                if (response && response.status) {
                    clearInterval(check_status_interval);
                    if (response.status === 'processing' || response.status === 'completed') {
                        // Pago correcto.
                        session.completePayment(ApplePaySession.STATUS_SUCCESS);
                    } else {
                        // Pago fallido.
                        session.completePayment(ApplePaySession.STATUS_FAILURE);
                    }
                }
            }
        });
    }, 2000);  // Comprobar cada 2 segundos
}

function getAppleTransactionInfo() {
    return new Promise((resolve, reject) => {
        jQuery.ajax({
            url: url_site + '/?wc-api=WC_Gateway_applepayredsys&checkout-price=true',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && response.total) {
                    resolve(response.total);  // resuelve la promesa con el valor total
                } else {
                    reject(new Error('No se pudo obtener el total del carrito'));
                }
            },
            error: function(error) {
                console.error('Error fetching updated total:', error);
                reject(error);  // rechaza la promesa con el error
            }
        });
    });
}
function onApplePayClicked() {

	if (!ApplePaySession) {
        return;
    }

    // Asumiendo un valor predeterminado, este valor se actualizará más tarde
    var AmountApplePay = '0.00';  

    var request = {
        countryCode: VarcountryCode,
        currencyCode: VarcurrencyCode,
        supportedNetworks: ['visa', 'masterCard', 'amex', 'discover'],
        merchantCapabilities: ['supports3DS'],
        total: { label: VarmerchantName, amount: AmountApplePay },
    };
    
    var session = new ApplePaySession(3, request);
    session.begin();

	session.onvalidatemerchant = event => {
		fetch(ajaxurl, { 
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: `action=validate_merchant&validationURL=${encodeURIComponent(event.validationURL)}`
		})
		.then(response => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		})
		.then(merchantSession => {
			session.completeMerchantValidation(merchantSession);
		})
		.catch(error => console.error('Merchant Validation Error:', error));
	};

	session.onpaymentmethodselected = event => {
		getAppleTransactionInfo().then(function(AmountApplePay) {
	
			const update = {
				newTotal: {
					"label": VarmerchantName,
					"type": "final",
					"amount": AmountApplePay
				}
			};
			session.completePaymentMethodSelection(update);
	
		}).catch(function(error) {
			console.error('Error obteniendo el total del carrito:', error);
		});
	};

	session.onpaymentauthorized = event => {

		var appleRefereciaRedsys = Math.random().toString(36).substring(2, 2 + 9) + '_applepay';
		document.getElementById('apple-referencia-redsys').value = appleRefereciaRedsys;

		var paymentData = event.payment.token.paymentData;
		var paymentDataJsonStr = JSON.stringify(paymentData);
		var paymentDataHexStr = stringToHex(paymentDataJsonStr);
		document.getElementById('apple-token-redsys').value = paymentDataHexStr;
		document.getElementById("place_order").click();
		check_payment_status(appleRefereciaRedsys, session);
	};
		
	session.oncancel = event => {
		console.log('Payment Cancelled:', event);
	}
}
