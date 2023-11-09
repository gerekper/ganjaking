/**
 * Unity Dashboard
 *
 * @param window.mdpDashboard
 */
( function () {

    document.readyState === 'loading' ?
        document.addEventListener( 'DOMContentLoaded', initUnityDashboard ) :
        initUnityDashboard();

    const { restBase, nonce, translation } = window.mdpDashboard;

    /**
     * Get async data about plugin for dashboard
     * @param pluginName
     * @param ask
     */
    function getAsyncValue( pluginName, ask ) {

        const xHttp     = new XMLHttpRequest();

        xHttp.open( 'POST', `${ restBase }ungrabber/v2/dashboard/?nonce=${ nonce }&plugin=${ pluginName }`, true);
        xHttp.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded');

        xHttp.onload = () => {

            if ( xHttp.status !== 200 ) {
                renderLicenseStatus( pluginName );
                return;
            }

            switch ( ask ) {

                case 'license':
                    renderLicenseStatus( pluginName, JSON.parse( xHttp.response ) );
                    break;

                case 'update':
                    renderUpdateStatus( pluginName, JSON.parse( xHttp.response ) );
                    break;

            }

        };

        xHttp.send(`ask=${ ask }` );

    }

    /**
     * Async plugin activation
     * @param target
     */
    function activatePlugin( target ) {

        const buttonText = target.innerHTML;

        // Make button disabled
        target.classList.add( 'button-disabled' );

        // Add Waiting...
        target.innerHTML = translation.wait;
        let dots = 0;
        const waiting = setInterval( () => {
            target.innerHTML += '.';
            dots++;
            if ( dots === 3 ) {
                dots = 0;
                target.innerHTML = translation.wait;
            }
        }, 200 );

        // Work only once
        if ( target.href === ''  ) { return; }
        const url = target.href;
        target.href = '';

        const xHttp     = new XMLHttpRequest();

        xHttp.open( 'POST', url, true);
        xHttp.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded');

        xHttp.onload = () => {

            // Remove Waiting...
            clearInterval( waiting );
            target.innerHTML = buttonText;

            // Make button disabled
            target.classList.remove( 'button-disabled' );

            if ( xHttp.status === 200 ) {

                target.closest( '.mdp-dashboard__buttons' ).querySelectorAll( '.button' ).forEach( ( button ) => {
                    button.removeAttribute( 'style' );
                } );
                target.remove();

            } else {

                location.href = url;

            }

        };

        xHttp.send('' );

    }

    /**
     * Render license activation status
     * @param pluginName
     * @param isLicenseValid
     * @param translation.licenseActive
     * @param translation.licenseInactive
     * @param translation.licenseUnknown
     * @param translation.latestVersion
     * @param translation.update
     */
    function renderLicenseStatus( pluginName, isLicenseValid = '' ) {

        const $status = document.querySelector( `div[data-plugin="${ pluginName }"] .mdp-dashboard__status__license a` );

        if ( isLicenseValid === true ) {

            $status.closest( '.mdp-dashboard__status__license' ).className += `--valid`;
            $status.innerHTML += translation.licenseActive;

        } else if ( false === isLicenseValid ) {

            $status.closest( '.mdp-dashboard__status__license' ).className += `--invalid`;
            $status.innerHTML += translation.licenseInactive;

        } else {

            $status.closest( '.mdp-dashboard__status__license' ).className += `--unknown`;
            $status.innerHTML += translation.licenseUnknown;

        }

    }

    /**
     * Render update status
     * @param pluginName
     * @param lastVersion
     */
    function renderUpdateStatus( pluginName, lastVersion ) {

        const $update = document.querySelector( `div[data-plugin="${ pluginName }"] .mdp-dashboard__status__update` );
        const currentVersion = $update.dataset.version;

        if ( lastVersion === '' ) {
            $update.remove();
            return;
        }
        $update.className += `-version`;

        if ( currentVersion === lastVersion ) {

            $update.querySelector( '.mdp-dashboard__status__update-needed' ).remove();
            $update.querySelector( '.mdp-dashboard__status__update-no-needed .mdp-dashboard__status__version' ).innerHTML = lastVersion;
            $update.querySelector( '.mdp-dashboard__status__update-no-needed' ).removeAttribute( 'style' );

        } else {

            $update.querySelector( '.mdp-dashboard__status__update-no-needed' ).remove();
            $update.querySelector( '.mdp-dashboard__status__update-needed .mdp-dashboard__status__version' ).innerHTML = lastVersion;
            $update.querySelector( '.mdp-dashboard__status__update-needed' ).removeAttribute( 'style' );

        }

    }

    /**
     * Get plugins data
     */
    function getPlugins() {

        document.querySelectorAll( '.mdp-dashboard__plugin' ).forEach( ( plugin ) => {

            const pluginName = plugin.dataset.plugin;

            getAsyncValue( pluginName, 'license' );
            getAsyncValue( pluginName, 'update' )

        } );

    }

    /**
     * Init dashboard widget
     */
    function initUnityDashboard() {

        getPlugins();

        document.querySelectorAll( '.button-activate' ).forEach( ( plugin ) => {

            plugin.addEventListener( 'click', ( e ) => {

                e.preventDefault( e );
                activatePlugin( e.target );

            } );

        } );

    }

} () );
