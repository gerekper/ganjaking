<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCGPF_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_Google_Product_Feed_Save_Feed
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Google_Product_Feed_Save_Feed' ) ) {

    /**
     * Class YITH_WCGPF_Google_Product_Feed_Merchant
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Google_Product_Feed_Save_Feed
    {
        /**
         * Construct
         *
         * @since 1.0
         */
        public function __construct()
        {
        }

        /**
         * Create directory
         *
         * @param $path
         * @return bool
         */
        public function create_dir($path)
        {
            if (!file_exists($path)) {
                return wp_mkdir_p($path);
            }
            return true;
        }

        /**
         * Save XML and TXT feed file
         *
         * @param $path
         * @param $file
         * @param $content
         * @return bool
         */
        public function save_feed_file($path, $file, $feed,$mode ='wb')
        {
            if ($this->create_dir($path)) {
                $fp = fopen($file, $mode);
                fwrite($fp, $feed);
                fclose($fp);
                return time();
            } else {
                return false;
            }
        }
        /**
         * Save CSV feed file
         *
         * @param $path
         * @param $file
         * @param $content
         * @return bool
         */
        public function save_feed_txt_file($path, $file, $feed,$mode='wb') {

            if ($this->create_dir($path)) {
                $fp = fopen($file, $mode);
                foreach ($feed as $feedline) {
                    fputs($fp, $feedline);
                    fputs($fp, "\n");
                }
                fclose($fp);
                return time();
            } else {
                return false;
            }
        }
    }
}