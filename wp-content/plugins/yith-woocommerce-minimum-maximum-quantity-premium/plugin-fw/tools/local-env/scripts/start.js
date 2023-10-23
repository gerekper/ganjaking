const dotenv = require( 'dotenv' );
const { execSync } = require( 'child_process' );

dotenv.config();

// Start the local-env containers.
execSync( 'docker-compose up -d', { stdio: 'inherit' } );
