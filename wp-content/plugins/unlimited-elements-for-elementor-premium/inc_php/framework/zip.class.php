<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteZipUC{

	private $_methods = array(0x0 => 'None', 0x1 => 'Shrunk', 0x2 => 'Super Fast', 0x3 => 'Fast', 0x4 => 'Normal', 0x5 => 'Maximum', 0x6 => 'Imploded', 0x8 => 'Deflated');
	private $_ctrlDirHeader = "\x50\x4b\x01\x02";
	private $_ctrlDirEnd = "\x50\x4b\x05\x06\x00\x00\x00\x00";
	private $_fileHeader = "\x50\x4b\x03\x04";
	private $_data = null;
	private $_metadata = null;
	private $contents = array();
	private $ctrldir = array();
	private $isZipArchiveExists = false;
	private $zip;

	/**
	 * make zip archive
	 * if exists additional paths, add additional items to the zip
	 */
	public function makeZip($srcPath, $zipFilepath, $additionPaths = array()){

		if(!is_dir($srcPath))
			UniteFunctionsUC::throwError("The path: '$srcPath' don't exists, can't zip");

		$this->isZipArchiveExists = $this->isZipArchiveExists();

		if($this->isZipArchiveExists === true){
			$this->zip = new ZipArchive();
			$success = $this->zip->open($zipFilepath, ZipArchive::CREATE);
			if($success === false)
				UniteFunctionsUC::throwError("Can't create zip file: $zipFilepath");
		}else{
			$this->contents = array();
			$this->ctrldir = array();
		}

		$this->addItem($srcPath, $srcPath);

		if(gettype($additionPaths) != "array")
			UniteFunctionsUC::throwError("Wrong additional paths variable.");

		//add additional paths
		if(!empty($additionPaths))
			foreach($additionPaths as $path){
				if(!is_dir($path))
					UniteFunctionsUC::throwError("Path: $path not found, can't zip");
				$this->addItem($path, $path);
			}

		if($this->isZipArchiveExists == true){
			$this->zip->close();
		}else{
			$this->_createZIPFile($this->contents, $this->ctrldir, $zipFilepath);
		}
	}

	/**
	 * extract zip archive
	 */
	public function extract($src, $dest){

		if($this->isZipArchiveExists() === true){
			$success = $this->extract_zipArchive($src, $dest);

			if($success === true)
				return true;

			$filename = basename($src);

			UniteFunctionsUC::throwError("Can't extract zip: $filename");
		}

		$success = $this->extract_custom($src, $dest);

		return ($success);
	}

	/**
	 * check if the zip archive exists
	 */
	private function isZipArchiveExists(){

		$exists = class_exists("ZipArchive");

		return $exists;
	}

	/**
	 * add zip file
	 */
	private function addItem($basePath, $path){

		$rel_path = str_replace($basePath . "/", "", $path);

		if(is_dir($path)){    //directory

			//add dir to zip
			if($basePath != $path){
				if($this->isZipArchiveExists)
					$this->zip->addEmptyDir($rel_path);
			}

			$files = scandir($path);
			foreach($files as $file){
				if($file == "." || $file == ".." || $file == ".svn")
					continue;
				$filepath = $path . "/" . $file;

				$this->addItem($basePath, $filepath);
			}
		}else{  //file
			if(!file_exists($path))
				UniteFunctionsUC::throwError("filepath: '$path' don't exists, can't zip");

			if($this->isZipArchiveExists){
				$path = str_replace("//", "/", $path);

				$this->zip->addFile($path, $rel_path);
			}else
				$this->addFileToCustomZip($path, $rel_path);
		}
	}

	/**
	 * check if dir exists, if not, create it recursively
	 */
	private function checkCreateDir($filepath){

		$dir = dirname($filepath);

		if(is_dir($dir) == false)
			$success = $this->checkCreateDir($dir);
		else
			return (true);

		//this dir is not exists, and all parent dirs exists

		@mkdir($dir);
		if(is_dir($dir) == false)
			UniteFunctionsUC::throwError("Can't create directory: {$dir} maybe zip file is brocken");
	}

	/**
	 * write some file
	 */
	private function writeFile($str, $filepath){

		//create folder if not exists
		$this->checkCreateDir($filepath);

		$fp = fopen($filepath, "w+");
		fwrite($fp, $str);
		fclose($fp);

		if(file_exists($filepath) == false)
			UniteFunctionsUC::throwError("can't write file: $filepath");
	}

	/**
	 * extract using zip archive
	 */
	private function extract_zipArchive($src, $dest){

		$zip = new ZipArchive();

		$result = $zip->open($src);
		if($result !== true){
			switch($result){
				case ZipArchive::ER_NOZIP:
					UniteFunctionsUC::throwError('not a zip archive');
				case ZipArchive::ER_INCONS :
					UniteFunctionsUC::throwError('consistency check failed');
				case ZipArchive::ER_CRC :
					UniteFunctionsUC::throwError('checksum failed');
				default:
					UniteFunctionsUC::throwError('error ' . $result);
			}
		}

		$extracted = @$zip->extractTo($dest);
		$zip->close();

		if($extracted == false)
			return (false);

		return (true);
	}

	private function a_MAKEZIP_CUSTOM(){}

	/**
	 * add empty dir to custom zip
	 */
	/*
	private function addEmptyZipToCustomZip($path, $rel_path){

		if(is_dir($path) == false)
			UniteFunctionsUC::throwError("Can't add directory to zip: $path");

		$time = filemtime($path);

		$file = array();
		$file["data"] = "";
		$file["name"] = $rel_path;
		$file["time"] = $time;

		$this->_addToZIPFile($file, $this->contents, $this->ctrldir);
	}
	*/

	/**
	 * add some file to custom zip
	 */
	private function addFileToCustomZip($path, $rel_path){

		if(is_file($path) == false)
			UniteFunctionsUC::throwError("can't add to zip file: $path");

		$content = file_get_contents($path);
		$time = filemtime($path);

		$file = array();
		$file["data"] = $content;
		$file["name"] = $rel_path;
		$file["time"] = $time;

		$this->_addToZIPFile($file, $this->contents, $this->ctrldir);
	}

	/**
	 * Adds a "file" to the ZIP archive.
	 *
	 * @param array  &$file File data array to add
	 * @param array  &$contents An array of existing zipped files.
	 * @param array  &$ctrldir An array of central directory information.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 *
	 * @todo    Review and finish implementation
	 */
	private function _addToZIPFile(array &$file, array &$contents, array &$ctrldir){

		$data = &$file['data'];
		$name = str_replace('\\', '/', $file['name']);

		/* See if time/date information has been provided. */
		$ftime = null;

		if(isset($file['time'])){
			$ftime = $file['time'];
		}

		// Get the hex time.
		$dtime = dechex($this->_unix2DosTime($ftime));
		$hexdtime = chr(hexdec($dtime[6] . $dtime[7])) . chr(hexdec($dtime[4] . $dtime[5])) . chr(hexdec($dtime[2] . $dtime[3]))
			. chr(hexdec($dtime[0] . $dtime[1]));

		/* Begin creating the ZIP data. */
		$fr = $this->_fileHeader;
		/* Version needed to extract. */
		$fr .= "\x14\x00";
		/* General purpose bit flag. */
		$fr .= "\x00\x00";
		/* Compression method. */
		$fr .= "\x08\x00";
		/* Last modification time/date. */
		$fr .= $hexdtime;

		/* "Local file header" segment. */
		$unc_len = strlen($data);
		$crc = crc32($data);
		$zdata = gzcompress($data);
		$zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2);
		$c_len = strlen($zdata);

		/* CRC 32 information. */
		$fr .= pack('V', $crc);
		/* Compressed filesize. */
		$fr .= pack('V', $c_len);
		/* Uncompressed filesize. */
		$fr .= pack('V', $unc_len);
		/* Length of filename. */
		$fr .= pack('v', strlen($name));
		/* Extra field length. */
		$fr .= pack('v', 0);
		/* File name. */
		$fr .= $name;

		/* "File data" segment. */
		$fr .= $zdata;

		/* Add this entry to array. */
		$old_offset = strlen(implode('', $contents));
		$contents[] = &$fr;

		/* Add to central directory record. */
		$cdrec = $this->_ctrlDirHeader;
		/* Version made by. */
		$cdrec .= "\x00\x00";
		/* Version needed to extract */
		$cdrec .= "\x14\x00";
		/* General purpose bit flag */
		$cdrec .= "\x00\x00";
		/* Compression method */
		$cdrec .= "\x08\x00";
		/* Last mod time/date. */
		$cdrec .= $hexdtime;
		/* CRC 32 information. */
		$cdrec .= pack('V', $crc);
		/* Compressed filesize. */
		$cdrec .= pack('V', $c_len);
		/* Uncompressed filesize. */
		$cdrec .= pack('V', $unc_len);
		/* Length of filename. */
		$cdrec .= pack('v', strlen($name));
		/* Extra field length. */
		$cdrec .= pack('v', 0);
		/* File comment length. */
		$cdrec .= pack('v', 0);
		/* Disk number start. */
		$cdrec .= pack('v', 0);
		/* Internal file attributes. */
		$cdrec .= pack('v', 0);
		/* External file attributes -'archive' bit set. */
		$cdrec .= pack('V', 32);
		/* Relative offset of local header. */
		$cdrec .= pack('V', $old_offset);
		/* File name. */
		$cdrec .= $name;
		/* Optional extra field, file comment goes here. */

		/* Save to central directory array. */
		$ctrldir[] = &$cdrec;
	}

	/**
	 * Creates the ZIP file.
	 *
	 * Official ZIP file format: https://support.pkware.com/display/PKZIP/APPNOTE
	 *
	 * @param array   &$contents An array of existing zipped files.
	 * @param array   &$ctrlDir An array of central directory information.
	 * @param string $path The path to store the archive.
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   11.1
	 *
	 * @todo  Review and finish implementation
	 */
	private function _createZIPFile(array &$contents, array &$ctrlDir, $path){

		$data = implode('', $contents);
		$dir = implode('', $ctrlDir);

		$buffer = $data . $dir . $this->_ctrlDirEnd . /* Total # of entries "on this disk". */
			pack('v', count($ctrlDir)) . /* Total # of entries overall. */
			pack('v', count($ctrlDir)) . /* Size of central directory. */
			pack('V', strlen($dir)) . /* Offset to start of central dir. */
			pack('V', strlen($data)) . /* ZIP file comment length. */
			"\x00\x00";

		UniteFunctionsUC::writeFile($buffer, $path);

		return true;
	}

	/**
	 * Converts a UNIX timestamp to a 4-byte DOS date and time format
	 * (date in high 2-bytes, time in low 2-bytes allowing magnitude
	 * comparison).
	 *
	 * @param int $unixtime The current UNIX timestamp.
	 *
	 * @return  int  The current date in a 4-byte DOS format.
	 *
	 * @since   11.1
	 */
	private function _unix2DOSTime($unixtime = null){

		$timearray = (is_null($unixtime)) ? getdate() : getdate($unixtime);

		if($timearray['year'] < 1980){
			$timearray['year'] = 1980;
			$timearray['mon'] = 1;
			$timearray['mday'] = 1;
			$timearray['hours'] = 0;
			$timearray['minutes'] = 0;
			$timearray['seconds'] = 0;
		}

		return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) | ($timearray['hours'] << 11) |
			($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
	}

	private function a_EXTRACT_CUSTOM(){}

	/**
	 * extract zip customely
	 */
	private function extract_custom($src, $dest){

		$this->_data = null;
		$this->_metadata = null;

		if(!extension_loaded('zlib'))
			UniteFunctionsUC::throwError('Zlib not supported, please enable in php.ini');

		$this->_data = file_get_contents($src);
		if(!$this->_data)
			UniteFunctionsUC::throwError('Get ZIP Data failed');

		$success = $this->extract_custom_readZipInfo($this->_data);
		if(!$success)
			UniteFunctionsUC::throwError('Get ZIP Information failed');

		for($i = 0, $n = count($this->_metadata); $i < $n; $i++){
			$lastPathCharacter = substr($this->_metadata[$i]['name'], -1, 1);

			if($lastPathCharacter !== '/' && $lastPathCharacter !== '\\'){
				//write file

				$buffer = $this->extract_custom_getFileData($i);
				$destFilepath = UniteFunctionsUC::cleanPath($dest . '/' . $this->_metadata[$i]['name']);

				$this->writeFile($buffer, $destFilepath);
			}
		}

		return true;
	}

	/**
	 * read zip info
	 */
	private function extract_custom_readZipInfo(&$data){

		$entries = array();

		// Find the last central directory header entry
		$fhLast = strpos($data, $this->_ctrlDirEnd);

		do{
			$last = $fhLast;
		}while(($fhLast = strpos($data, $this->_ctrlDirEnd, $fhLast + 1)) !== false);

		// Find the central directory offset
		$offset = 0;

		if($last){
			$endOfCentralDirectory = unpack(
				'vNumberOfDisk/vNoOfDiskWithStartOfCentralDirectory/vNoOfCentralDirectoryEntriesOnDisk/' .
				'vTotalCentralDirectoryEntries/VSizeOfCentralDirectory/VCentralDirectoryOffset/vCommentLength',
				substr($data, $last + 4)
			);
			$offset = $endOfCentralDirectory['CentralDirectoryOffset'];
		}

		// Get details from central directory structure.
		$fhStart = strpos($data, $this->_ctrlDirHeader, $offset);
		$dataLength = strlen($data);

		do{
			if($dataLength < $fhStart + 31){
				UniteFunctionsUC::throwError('Invalid Zip Data');
			}

			$info = unpack('vMethod/VTime/VCRC32/VCompressed/VUncompressed/vLength', substr($data, $fhStart + 10, 20));
			$name = substr($data, $fhStart + 46, $info['Length']);

			$entries[$name] = array(
				'attr' => null,
				'crc' => sprintf("%08s", dechex($info['CRC32'])),
				'csize' => $info['Compressed'],
				'date' => null,
				'_dataStart' => null,
				'name' => $name,
				'method' => $this->_methods[$info['Method']],
				'_method' => $info['Method'],
				'size' => $info['Uncompressed'],
				'type' => null,
			);

			$entries[$name]['date'] = mktime(
				(($info['Time'] >> 11) & 0x1f),
				(($info['Time'] >> 5) & 0x3f),
				(($info['Time'] << 1) & 0x3e),
				(($info['Time'] >> 21) & 0x07),
				(($info['Time'] >> 16) & 0x1f),
				((($info['Time'] >> 25) & 0x7f) + 1980)
			);

			if($dataLength < $fhStart + 43){
				UniteFunctionsUC::throwError('Invalid Zip Data');
			}

			$info = unpack('vInternal/VExternal/VOffset', substr($data, $fhStart + 36, 10));

			$entries[$name]['type'] = ($info['Internal'] & 0x01) ? 'text' : 'binary';
			$entries[$name]['attr'] = (($info['External'] & 0x10) ? 'D' : '-') . (($info['External'] & 0x20) ? 'A' : '-')
				. (($info['External'] & 0x03) ? 'S' : '-') . (($info['External'] & 0x02) ? 'H' : '-') . (($info['External'] & 0x01) ? 'R' : '-');
			$entries[$name]['offset'] = $info['Offset'];

			// Get details from local file header since we have the offset
			$lfhStart = strpos($data, $this->_fileHeader, $entries[$name]['offset']);

			if($dataLength < $lfhStart + 34){
				UniteFunctionsUC::throwError('Invalid Zip Data');
			}

			$info = unpack('vMethod/VTime/VCRC32/VCompressed/VUncompressed/vLength/vExtraLength', substr($data, $lfhStart + 8, 25));
			$name = substr($data, $lfhStart + 30, $info['Length']);
			$entries[$name]['_dataStart'] = $lfhStart + 30 + $info['Length'] + $info['ExtraLength'];

			// Bump the max execution time because not using the built in php zip libs makes this process slow.

			$maxTime = ini_get('max_execution_time');

			if(!empty($maxTime) && is_numeric($maxTime))
				@set_time_limit($maxTime);
		}while((($fhStart = strpos($data, $this->_ctrlDirHeader, $fhStart + 46)) !== false));

		$this->_metadata = array_values($entries);

		return true;
	}

	/**
	 * get file data for extract
	 */
	private function extract_custom_getFileData($key){

		if($this->_metadata[$key]['_method'] == 0x8){
			return gzinflate(substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']));
		}elseif($this->_metadata[$key]['_method'] == 0x0){
			/* Files that aren't compressed. */
			return substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']);
		}elseif($this->_metadata[$key]['_method'] == 0x12){
			// If bz2 extension is loaded use it
			if(extension_loaded('bz2')){
				return bzdecompress(substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']));
			}
		}

		return '';
	}

}
