<?php
namespace Composer\Script;
if (!defined('ABSPATH')) exit;
class ScriptEvents
{
 const PRE_INSTALL_CMD = 'pre-install-cmd';
 const POST_INSTALL_CMD = 'post-install-cmd';
 const PRE_UPDATE_CMD = 'pre-update-cmd';
 const POST_UPDATE_CMD = 'post-update-cmd';
 const PRE_STATUS_CMD = 'pre-status-cmd';
 const POST_STATUS_CMD = 'post-status-cmd';
 const PRE_AUTOLOAD_DUMP = 'pre-autoload-dump';
 const POST_AUTOLOAD_DUMP = 'post-autoload-dump';
 const POST_ROOT_PACKAGE_INSTALL = 'post-root-package-install';
 const POST_CREATE_PROJECT_CMD = 'post-create-project-cmd';
 const PRE_ARCHIVE_CMD = 'pre-archive-cmd';
 const POST_ARCHIVE_CMD = 'post-archive-cmd';
}
