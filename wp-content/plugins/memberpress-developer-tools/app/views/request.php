<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

$method = esc_html($method);
$current_user = MeprUtils::get_currentuserinfo();
$url = esc_html($url);
$delim = preg_match('/\?/',$url) ? '&' : '?';

if(!is_array($params)) { $params = array($params); }

/*
foreach($params as $pkey => $pval) {
  $params[$pkey] = esc_html($params[$pkey]);
}
*/

if($method=='get'):

  $argstr = '';

  if(!empty($params)):

    $argstr = $delim . http_build_query($params);

  endif;

?>
$ curl "<?php echo $url.$argstr; ?>" \
       -u <?php echo $current_user->user_login; ?>:yourpassword
<?php

elseif($method=='post'):

?>
$ curl -X POST "<?php echo $url; ?>" \
       -u <?php echo $current_user->user_login; ?>:yourpassword \
<?php

  $curl_params = array();
  foreach($params as $param => $pval):

    if((!is_bool($pval) && !empty($pval)) && (is_numeric($pval) || is_string($pval) || is_bool($pval))):
      if(strlen($pval) > 255) { continue; } // Not gonna deal with text blobs yet

      if(preg_match('/[^a-z0-9_]/i', $pval) ||
         (preg_match('/[a-z]/i', $pval) && preg_match('/[0-9]/', $pval))) {
        //if(preg_match('/\"/',$pval)) {
        //  $pval = addslashes($pval);
        //}

        $pval = '"'.addslashes($pval).'"';
      }

      $curl_params[] = "       -d {$param}={$pval} ";
    endif;

  endforeach;

  echo implode("\\\n", $curl_params);

elseif($method=='delete'):

?>
$ curl -X DELETE "<?php echo $url; ?>" \
       -u <?php echo $current_user->user_login; ?>:yourpassword
<?php

endif;

