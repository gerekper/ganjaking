<style>
@import url(https://fonts.googleapis.com/css?family=Roboto:400,300,600,400italic);
/*
* {
margin: 0;
padding: 0;
box-sizing: border-box;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
-webkit-font-smoothing: antialiased;
-moz-font-smoothing: antialiased;
-o-font-smoothing: antialiased;
font-smoothing: antialiased;
text-rendering: optimizeLegibility;
}

body {
font-family: "Roboto", Helvetica, Arial, sans-serif;
font-weight: 100;
font-size: 12px;
line-height: 30px;
color: #777;
background: #4CAF50;
}*/

.up-service-container {
max-width: 400px;
width: 100%;
margin: 0 auto;
position: relative;
}

#up-service-contact input[type="text"],
#up-service-contact input[type="email"],
#up-service-contact input[type="tel"],
#up-service-contact input[type="url"],
#up-service-contact textarea,
#up-service-contact button[type="submit"] {
font: 400 12px/16px "Roboto", Helvetica, Arial, sans-serif;
}

#up-service-contact {
background: #F9F9F9;
padding: 25px;
margin: 35px 0;
box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
}

#up-service-contact h3 {
display: block;
font-size: 30px;
font-weight: 300;
margin-bottom: 10px;
}

#up-service-contact h4 {
margin: 5px 0 15px;
display: block;
font-size: 13px;
font-weight: 400;
}

#up-service-contact fieldset {
border: medium none !important;
margin: 0 0 10px;
min-width: 100%;
padding: 0;
width: 100%;
}

#up-service-contact input[type="text"],
#up-service-contact input[type="email"],
#up-service-contact input[type="tel"],
#up-service-contact input[type="url"],
#up-service-contact select,
#up-service-contact textarea {
width: 100%;
border: 1px solid #ccc;
background: #FFF;
margin: 0 0 5px;
padding: 10px;
}

#up-service-contact input[type="text"]:hover,
#up-service-contact input[type="email"]:hover,
#up-service-contact input[type="tel"]:hover,
#up-service-contact input[type="url"]:hover,
#up-service-contact select,
#up-service-contact textarea:hover {
-webkit-transition: border-color 0.3s ease-in-out;
-moz-transition: border-color 0.3s ease-in-out;
transition: border-color 0.3s ease-in-out;
border: 1px solid #aaa;
}

#up-service-contact textarea {
height: 100px;
max-width: 100%;
resize: none;
}

#up-service-contact button[type="submit"] {
cursor: pointer;
width: 100%;
border: none;
background: #4CAF50;
color: #FFF;
margin: 0 0 5px;
padding: 10px;
font-size: 15px;
}

#up-service-contact button[type="submit"]:hover {
background: #43A047;
-webkit-transition: background 0.3s ease-in-out;
-moz-transition: background 0.3s ease-in-out;
transition: background-color 0.3s ease-in-out;
}

#up-service-contact button[type="submit"]:active {
box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.5);
}

.copyright {
text-align: center;
}

#up-service-contact input:focus,
#up-service-contact textarea:focus {
outline: 0;
border: 1px solid #aaa;
}

::-webkit-input-placeholder {
color: #888;
}

:-moz-placeholder {
color: #888;
}

::-moz-placeholder {
color: #888;
}

:-ms-input-placeholder {
color: #888;
}
</style>
<?php
  global $userpro;
?>
<div class="up-service-container">

  <form id="up-service-contact" action="" method="post">
    <?php
/*
      if( isset( $_POST['up-service-submit'])){
        if( !isset( $_POST['email'] ) ){

          $output = 'Please enter email address';
        }else{
          $to = 'services@userproplugin.com';
          $f_email = $_POST['email'];
          $type = isset( $_POST['type'] )?$_POST['type']:'';
          $module = isset( $_POST['module'] )?$_POST['module']:'';
          $description = isset( $_POST['detailed-description'] )?$_POST['detailed-description']:'';

          $headers = 'From: ' . userpro_get_option( 'mail_from_name' ) . ' <' . $f_email . '>' . "\r\n";
          $subject = "Service Request : {$module} {$type}";
          $body = 'FROM: '.$f_email.'\r\n';
          $body .= "Subject: {$module} {$type}" . "\r\n\r\n";
          $body .= "Message Body:\r\n";
          $body .= $description;
          //wp_mail( $to, $subject, $body, $headers );

          $output = 'Thank you for contacting us, our Services Team will get back to you at earliest.';
        }
*/
    ?>
    <div class="up-service-message">
        <?php
          //echo $output;
        ?>
    </div>
    <?php
    //}
    ?>
    <h3>Service Request Form</h3>
    <h4>Contact us for custom quote</h4>
    <fieldset>
      <input name="email" id="email" placeholder="Your Email Address" type="email" tabindex="1" required>
    </fieldset>
    <fieldset>
      <select name='type' id="type" tabindex="2" required>
        <option value="Installation and Configuration">Installation and Configuration</option>
        <option value="Customization">Customization</option>
      </select>
    </fieldset>
    <fieldset>
      <select name="module" id="module" tabindex="3" required>
        <option value="UserPro">UserPro</option>
        <option value="Private Messaging Add-on">Private Messaging Add-on</option>
        <option value="Social Wall Add-on">Social Wall Add-on</option>
        <option value="User Rating Add-on">User Rating Add-on</option>
        <option value="Media Manager Add-on">Media Manager Add-on</option>
        <option value="Bookmarks Add-on">Bookmarks Add-on</option>
        <option value="Payment Add-on">Payment Add-on</option>
        <option value="VK Add-on">VK Add-on</option>
        <option value="WPLMS Add-on">WPLMS Add-on</option>
        <option value="User Dashboard Add-on">User Dashboard Add-on</option>
        <option value="UserPro Woocommerce Add-on">UserPro Woocommerce Add-on</option>
        <option value="User Tag Add-on">User Tag Add-on</option>
        <option value="Profile Completeness Add-on">Profile Completeness Add-on</option>
        <option value="UserPro Livechat">UserPro Livechat</option>
        <option value="Memberlist Layouts">Memberlist Layouts</option>
      </select>
    </fieldset>
    <fieldset>
      <textarea name="detailed-description" id="detailed_description" placeholder="Description about required customization" tabindex="4" required></textarea>
    </fieldset>
    <fieldset>
      <button name="up-service-submit" type="submit" id="up-service-submit">Submit</button>
    </fieldset>
    <div class="up-service-loading">
    <img src="<?php echo $userpro->skin_url(); ?>loading.gif" alt="" />
  </div>
  </form>
</div>
