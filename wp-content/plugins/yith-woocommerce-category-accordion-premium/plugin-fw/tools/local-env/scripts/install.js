const dotenv = require( 'dotenv' );
const { execSync } = require( 'child_process' );

dotenv.config();

phpunitExec('/var/www/html/install-wp-tests.sh yith_plugin_fw_tests root password mysql latest true');

/**
 * Runs commands in the Docker PHPUnit environment.
 *
 * @param {string} cmd The command to run.
 */
function phpunitExec( cmd ) {
	execSync( `docker-compose run --rm phpunit ${cmd}`, { stdio: 'inherit' } );
}