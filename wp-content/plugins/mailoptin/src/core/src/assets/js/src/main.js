requirejs.config({
    baseUrl: mailoptin_globals.public_js
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