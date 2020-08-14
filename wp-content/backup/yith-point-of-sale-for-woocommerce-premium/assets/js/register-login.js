( function ( $ ) {
    var storeRegisterForm = $( '#yith-pos-store-register-form' );

    if ( storeRegisterForm.length ) {
        var stores      = storeRegisterForm.data( 'stores' ) || {},
            store       = $( '#yith-pos-store-register-form__store' ),
            register    = $( '#yith-pos-store-register-form__register' ),
            emptyOption = register.find( 'option' ).first().clone();


        store.on( 'change', function () {
            var storeID = $( this ).val();

            if ( storeID ) {
                var currentStore = stores[ storeID ],
                    registers    = currentStore.registers || [],
                    i;

                register.html( '' );
                register.append( emptyOption.clone() );

                for ( i in registers ) {
                    var currentRegister = registers[ i ],
                        option          = new Option( currentRegister.name, currentRegister.id, false, false );
                    register.append( option );
                }
            }
        } ).trigger( 'change' );
    }

} )( jQuery );