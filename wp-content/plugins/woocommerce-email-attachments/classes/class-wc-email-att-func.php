<?php
/**
 * Contains helper functions
 *
 * @author Guenter Schoenmann
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class WC_Email_Att_Func 
{
	/**
	 * Text to put in htaccess of attachment folder
	 * 
	 * @var array 
	 */
	public static $htaccess;
	
	/**
	 * @return string Complete path excluding / at end
	 */
	static public function get_full_upload_path( $folder )
	{
		$folder = trim( $folder );
		$folder = '/' . ltrim( $folder, '/' );
		$folder = untrailingslashit( $folder );
		
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . $folder;
	}

	/**
	 * Creates the folder with index.php and .htaccess inside
	 * 
	 * @param string $upload_folder
	 * @param bool $addfiles	true, if index.php should be added on a new created folder
	 * 
	 * @return bool		true, if folder could be created or exists
	 */
	static public function create_folder( $upload_folder, $addfiles = true )
	{
		$folder = self::get_full_upload_path( $upload_folder );
		
		if( is_dir( $folder ) )
		{
			return true;
		}
		
//		$oldmask = @umask(0);
		
		$created = wp_mkdir_p( trailingslashit( $folder ) );
		if( $created )
			@chmod( $folder, 0777 );
		
//		$newmask = @umask($oldmask);
		
		if( ! $addfiles )
		{
			return $created;
		}
		
		$index_file = trailingslashit( $folder ) . 'index.php';
		if ( file_exists( $index_file ) )
		{
			return $created;
		}

		$handle = @fopen( $index_file, 'w' );
		if( $handle ) 
		{
			fwrite( $handle, "<?php\r\necho 'Sorry, browsing of directory is not allowed !!!!!';\r\n?>" );
			fclose( $handle );
		}
		
		$index_file = trailingslashit( $folder ) . '.htaccess';
		if ( file_exists( $index_file ) )
		{
			return $created;
		}
		
		$handle = @fopen( $index_file, 'w' );
		if ( $handle ) 
		{
			if ( ! isset( self::$htaccess ) )
			{
				self::$htaccess = array ( 'deny from all' );
			}
			
			$out = implode( "\r\n", self::$htaccess) . "\r\n";
			fwrite( $handle, $out );
			fclose( $handle );
		}
		
		return $created;
	}

	/**
	 * Removes a folder, if empty or only files to skip are inside. These files are deleted and the folder is removed.
	 * 
	 * @param string $folder   relativ from WP Upload path
	 * @param array $skip_files
	 */
	static public function remove_empty_folder( $folder, array $skip_files = array() )
	{
		$path = self::get_full_upload_path( $folder );
		
		$filenames = self::get_all_files( $path, $skip_files, true );
		
		if( count( $filenames ) > 0 )
		{
			return;
		}
		
		self::remove_folder( $path );
	}
	
	/**
	 * Returns all existing files in the upload directory lowercase sorted by name
	 * 
	 * This function checks for lowercase and specioal characters to recognise manual upload.
	 * Filenames are changed if necessary.
	 * 
	 * @param bool $skipdefaults	if true, it skips the default files in the list
	 * @return array				files in the upload directory
	 */
	static public function &get_all_files( $path, array $skip_files = array(), $skipdefaults = true )
	{
		$retfiles = array();
		
		if( empty( $skip_files ) ) 
		{
			$skipdefaults = false;
		}
		
		$path = trailingslashit( $path );
		if( empty( $path ) )
		{
			return $retfiles;
		}
		
		if( ! is_dir( $path ) )
		{
			return $retfiles;
		}
		
		$files = scandir( $path );
		if( ( $files === false ) || empty( $files ) )
		{
			return $retfiles;
		}
		
		foreach ( $files as $name )
		{
			if( ( $name == '.' ) || ( $name == '..' ) )  {  continue;  }
			
				//	skip any files you do not want to display
			if( $skipdefaults && in_array( $name, $skip_files ) )  {  continue;  }
				
				//	skip folders
			if( is_dir( $path.$name ) )  {  continue;  }
			
			if( ! is_file( $path.$name ) )  {  continue;  }
			
			$retfiles[] = $name;
		}
		
		return $retfiles;
	}
	
	/**
	 * Deletes all files in the given folder
	 * 
	 * @param string $folder
	 */
	static public function remove_folder( $folder )
	{
		$filenames = WC_Email_Att_Func::get_all_files( $folder );
				
		foreach ( $filenames as $filename ) 
		{
			unlink( trailingslashit( $folder ) . $filename );
		}
		
		rmdir( $folder );
	}
	
}
