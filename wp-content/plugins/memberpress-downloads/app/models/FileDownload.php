<?php
namespace memberpress\downloads\models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base,
    memberpress\downloads\lib as lib,
    memberpress\downloads\models as models;

class FileDownload extends lib\BaseModel {
  public function __construct($obj = null) {
    $this->initialize(
      array(
        'id'             => array('default' => 0, 'type' => 'integer'),
        'file_id'        => array('default' => 0, 'type' => 'integer'),
        'download_count' => array('default' => 0, 'type' => 'integer'),
        'created_at'     => array('default' => lib\Utils::ts_to_mysql_date(time()), 'type' => 'datetime'),
        'updated_at'     => array('default' => lib\Utils::ts_to_mysql_date(time()), 'type' => 'datetime'),
      ),
      $obj
    );
  }

  /**
  * Used to validate the file download object
  * @return true|null ValidationException raised on failure
  */
  public function validate() {
    // lib\Validate::not_empty($this->file_id, 'file_id');
    // lib\Validate::is_numeric($this->download_count, 'download_count');
    // lib\Validate::not_empty($this->created_at, 'created_at');

    // return true;
  }

  /**
  * Used to create the file download record
  * @param FileDownload $file_download
  * @return integer id
  */
  public static function create($file_download) {
    // $db = new lib\Db;
    // $attrs = $file_download->get_values();

    // return $db->create_record($db->file_downloads, $attrs, false);
  }

  /**
  * Used to update the file download record
  * @return integer id
  */
  private function update() {
    // $db = new lib\Db;
    // $attrs = $this->get_values();
    // $attrs = \array_merge($attrs, array('updated_at' => lib\Utils::ts_to_mysql_date(time())));

    // return $db->update_record($db->file_downloads, $this->id, $attrs);
  }

  /**
  * Destroy the file download
  * @return integer|false Returns number of rows affected or false
  */
  public function destroy() {
    // $db = new lib\Db;

    // return $db->delete_records($db->file_downloads, array('id' => $this->id));
  }

    /**
  * Used to create or update the file download record
  * @param boolean $validate default true
  * @return integer id
  */
  public function store($validate = true) {
    // if($validate) {
    //   $this->validate();
    // }

    // if(isset($this->id) && (int) $this->id > 0) {
    //   $this->update();
    // }
    // else {
    //   $this->id = self::create($this);
    // }

    // return $this->id;
  }

  /**
  * Find or create the record and increment the download count
  * @param int $file_id File ID
  * @return FileDownload
  */
  public static function create_or_increment($file_id) {
    // $file_download = self::get_one(array('file_id' => $file_id));
    // if(!isset($file_download)) {
    //   $file_download = new self(array('file_id' => $file_id));
    // }

    // $file_download->increment_download_count();

    // return $file_download;
  }

  /**
  * Increment download count
  * @return void
  */
  private function increment_download_count() {
    // $this->download_count += 1;
    // $this->store();
  }
}
