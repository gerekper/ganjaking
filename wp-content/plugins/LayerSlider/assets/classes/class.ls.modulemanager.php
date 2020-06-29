<?php


class LS_ModuleManager {

	public $repoURL;

	public $uploadsDir;
	public $uploadsURL;

	public $modulesDir;
	public $modulesURL;

	public $moduleHandle;
	public $moduleDir;
	public $moduleURL;

	public $errMessage = '';
	public $errCode = '';


	public function __construct( $moduleHandle, $moduleProperties = array() ) {

		if( empty( $moduleHandle ) ) {
			return false;
		}

		$moduleProperties = array_merge(array(
			'autoCheck' => true,
			'autoDownload' => true,
		), $moduleProperties );

		$this->moduleHandle = $moduleHandle;
		$this->repoURL = LS_REPO_BASE_URL.'modules/';

		if( ! $this->checkDirectories() ) {
			return false;
		}

		if( $moduleProperties['autoCheck'] ) {
			if( ! $this->checkModule() && $moduleProperties['autoDownload'] ) {
				if( ! $this->downloadModule() ) {
					return false;
				}
			}
		}

		return true;
	}


	public function checkDirectories( ) {

		$uploadsDir = wp_upload_dir();
		$uploadsBaseDir = $uploadsDir['basedir'];
		$uploadsBaseURL = $uploadsDir['baseurl'];

		$this->uploadsDir = $uploadsBaseDir . DIRECTORY_SEPARATOR . 'layerslider';
		$this->uploadsURL = $uploadsBaseURL.'/layerslider';

		$this->modulesDir = $this->uploadsDir . DIRECTORY_SEPARATOR . 'modules';
		$this->modulesURL = $this->uploadsURL.'/modules';

		$this->moduleDir  = $this->modulesDir . DIRECTORY_SEPARATOR . $this->moduleHandle;
		$this->moduleURL  = $this->modulesURL.'/'.$this->moduleHandle;

		if( ! file_exists( $this->uploadsDir ) ) {
			if( ! mkdir( $this->uploadsDir, 0755 ) ) {
				$this->logError('ERR_UPLOADS_DIR_NOT_WRITABLE', sprintf( __('LayerSlider was unable to create the directory for the Image Editor module. Please verify that your uploads folder is writable. See the %sCodex%s for more information.', 'LayerSlider'), '<a href="https://wordpress.org/support/article/changing-file-permissions/" target="_blank">', '</a>' ));
				return false;
			}
		}

		if( ! file_exists( $this->modulesDir ) ) {
			if( ! mkdir( $this->modulesDir, 0755 ) ) {
				$this->logError('ERR_MODULES_DIR_NOT_WRITABLE', sprintf( __('LayerSlider was unable to create the directory for the Image Editor module. Please verify that your uploads folder is writable. See the %sCodex%s for more information.', 'LayerSlider'), '<a href="https://wordpress.org/support/article/changing-file-permissions/" target="_blank">', '</a>' ));
				return false;
			}
		}

		return true;
	}



	public function checkModule( ) {

		$targetDir  = $this->moduleDir;
		$filesCount = count( glob( "$targetDir/*" ) );

		if( ! file_exists( $targetDir ) || $filesCount === 0) {
			return false;
		}

		return true;
	}




	public function downloadModule( ) {

		// Attempt to remove module dir if it already exists.
		// This helps avoid issues with partial or corrupt downloads.
		if( file_exists( $this->moduleDir ) ) {
			@rmdir( $this->moduleDir );
		}

		$targetURL 	= $this->repoURL . '?module=' . $this->moduleHandle;
		$zipContent = $GLOBALS['LS_AutoUpdate']->sendApiRequest( $targetURL );

		if( ! $zipContent || is_wp_error( $zipContent ) ) {
			$this->logError('ERR_DOWNLOAD', sprintf( __('LayerSlider was unable to download the Image Editor module. Please check %sLayerSlider → Options → System Status%s for potential issues. The WP Remote functions may be unavailable or your web hosting provider has to allow external connections to our domain.', 'LayerSlider'), '<a href="'.admin_url( 'admin.php?page=layerslider-options&section=system-status' ).'" target="_blank">', '</a>' ) );
			return false;
		}

		$dlFilePath = tempnam( sys_get_temp_dir(), 'ZIP_' );
		file_put_contents( $dlFilePath, $zipContent );

		$zip = new ZipArchive();

		if( $zip->open( $dlFilePath ) === true ) {
			if( $zip->extractTo( $this->moduleDir ) ) {
				$zip->close();
				unlink( $dlFilePath );

				return true;
			}
		}

		$this->logError('ERR_ZIP_EXTRACTION', sprintf( __('LayerSlider was unable to uncompress the Image Editor module. Please check %sLayerSlider → Options → System Status%s for potential issues. The WP Remote functions may be unavailable or your web hosting provider has to allow external connections to our domain.', 'LayerSlider'), '<a href="'.admin_url( 'admin.php?page=layerslider-options&section=system-status' ).'" target="_blank">', '</a>' ) );
		unlink( $dlFilePath );
		return false;
	}



	protected function logError( $code = '', $message = '' ) {

		$this->errCode = $code;
		$this->errMessage = $message;
	}
}