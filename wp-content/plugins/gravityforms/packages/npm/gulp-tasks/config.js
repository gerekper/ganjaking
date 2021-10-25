const findConfig = require( 'find-config' );

const configModule = findConfig.require( 'gravityforms.config', { module: true } );
const config = configModule?.gulpConfig || {};

module.exports = config;
