/* jshint ignore: start */

/**
 *
 *
 * @author Leanza Francesco <leanzafrancesco@gmail.com>
 */

const fs    = require( 'fs' );
const glob  = require( 'glob' );
const chalk = require( 'chalk' );

const DELETED = chalk.reset.inverse.bold.green( ' DELETED ' );
const ERROR   = chalk.reset.inverse.bold.red( ' ERROR ' );

console.log( chalk.green( '\nCleaning language files...' ) );
glob( "languages/*.po~", function ( er, files ) {

    if ( files.length ) {
        console.log( `Processing ${files.length} files:` );

        files.forEach( ( file ) => {
            fs.unlink( file, ( err ) => {
                if ( err ) {
                    console.log( chalk.bold( ` - ${file} ` ) + ERROR );
                    console.error( err );
                    return;
                }
                console.log( chalk.bold( ` - ${file} ` ) + DELETED );
            } );
        } );
    } else {
        console.log( `No file to clean.\n` );
    }

} );