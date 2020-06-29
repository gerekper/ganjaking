<?php
/*
 * Plugin Name: WP Media Folders
 * Description: WP Media Folders is powerful WordPress plugin for organizing thousands of files into handy folders hierarchy. It turns Media Library into high quality WordPress File Manager that works similar to your PC's file explorer.
 * Author: WP Hi-Tech
 * Version: 1.2.3
 * Text Domain: wp-media-folders
 * Domain Path: /languages
*/


define('FEML_VERSION', '1.2.3');
define('FEML_FILE', __FILE__);

require_once('controllers/folders/create.inc');
require_once('controllers/folders/createPaths.inc');
require_once('controllers/folders/deleteList.inc');
require_once('controllers/folders/getCounts.inc');
require_once('controllers/folders/getList.inc');
require_once('controllers/folders/rename.inc');
require_once('controllers/settings/getData.inc');
require_once('controllers/settings/save.inc');
require_once('controllers/fileTypes/addCustomType.inc');
require_once('controllers/fileTypes/deleteType.inc');
require_once('controllers/fileTypes/getData.inc');
require_once('controllers/fileTypes/saveAllowed.inc');
require_once('controllers/files/delete.inc');
require_once('controllers/files/getNestedList.inc');
require_once('controllers/files/regenerate.inc');
require_once('controllers/items/moveList.inc');
require_once('services/fileTypes/getAll.inc');
require_once('services/fileTypes/getAllowed.inc');
require_once('services/fileTypes/getCustom.inc');
require_once('services/fileTypes/getSystem.inc');
require_once('services/fileTypes/getUpload.inc');
require_once('services/users/isAdmin.inc');
require_once('models/posts/getMetas.inc');
require_once('models/posts/getPost.inc');
require_once('models/posts/insertMetas.inc');
require_once('models/posts/insertPost.inc');
require_once('models/folders/getCount.inc');
require_once('models/folders/getCounts.inc');
require_once('models/folders/getList.inc');
require_once('models/folders/getOne.inc');
require_once('models/folders/getRootCount.inc');
require_once('models/files/getFiles.inc');
require_once('models/files/getFilesAll.inc');
require_once('models/files/getFilesByFolders.inc');
require_once('includes/activation.inc');
require_once('includes/bootstrap.inc');
require_once('includes/queryAttachments.inc');
require_once('includes/taxonomy.inc');
require_once('includes/uploadMimes.inc');
