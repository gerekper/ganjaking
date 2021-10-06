const { NODE_ENV } = process.env;

module.exports = require( `./src/webpack/${ NODE_ENV }` );
