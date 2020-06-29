requirejs.config({
    baseUrl: mailoptin_globals.public_js,
    packages: [{
        name: 'moment',
        // This location is relative to baseUrl. Choose bower_components
        // or node_modules, depending on how moment was installed.
        location: './',
        main: 'moment'
    }]
});

if (typeof jQuery === 'function') {
    define('jquery', function () {
        return jQuery;
    });
}

define('mailoptin_globals', function () {
    return mailoptin_globals;
});


// Start the main app logic.
requirejs(['mailoptin']);