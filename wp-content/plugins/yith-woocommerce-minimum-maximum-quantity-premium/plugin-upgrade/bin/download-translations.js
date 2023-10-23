/* jshint ignore: start */

/**
 * This script download translations from https://translate.yithemes.com/
 *
 * @version 1.1.0
 * @author Leanza Francesco <leanzafrancesco@gmail.com>
 */

const fs      = require( 'fs' );
const path    = require( 'path' );
const axios   = require( 'axios' );
const chalk   = require( 'chalk' );
const options = require( './download-translations-config' );

const SPACE = '\t';
const DONE  = chalk.reset.inverse.bold.green( ' DONE ' );
const ERROR = chalk.reset.inverse.bold.red( ' ERROR ' );

async function download( url, dest ) {
	const destPath = path.resolve( dest );
	const writer   = fs.createWriteStream( destPath );

	const response = await axios( {
									  url,
									  method      : 'GET',
									  responseType: 'stream'
								  } );

	response.data.pipe( writer );

	return new Promise( ( resolve, reject ) => {
		writer.on( 'finish', resolve );
		writer.on( 'error', reject );
	} )
}

const downloadLanguage = function ( language ) {
	const languageName = language.name || language.id;
	const source       = options.projectPath + language.id + "/default/export-translations/";
	const fileName     = options.textDomain + '-' + language.slug + '.po';
	const dest         = options.destFolder + fileName;
	const message      = ' - ' + chalk.bold( languageName ) + SPACE;

	download( source, dest ).then( () => {
		console.log( message + DONE );
	} ).catch( ( err ) => {
		console.log( message + ERROR );
		throw err;
	} );
};

console.log( chalk.green( '\nDownloading Transations from translate.yithemes.com...' ) );

options.languages.forEach( ( language ) => {
	downloadLanguage( language );
} );