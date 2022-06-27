<?php

/**
* script-login.php is created for adding Social related JS code in login page footer.
* @since 1.0.7
*/

?>
<script>

function addLoginPressSocialButton(){

  var rmLoginPressChecked = false;
  if ( document.getElementById('rememberme').checked == true ) {
    rmLoginPressChecked = true;
  }

  var $slLoginPressContainer = document.querySelector('.social-networks');
  if ( document.querySelectorAll('.social-networks').length > 0 ) {

    $slLoginPressContainer.remove();
    let slLoginPressParentContainer = document.createElement( 'div' );
    slLoginPressParentContainer.classList.add( 'social-networks', 'block' );
    slLoginPressParentContainer.append($slLoginPressContainer);
    document.getElementById('loginform').append(slLoginPressParentContainer );
  }

  if( rmLoginPressChecked !== false ) {
    document.getElementById('rememberme').setAttribute( 'checked', rmLoginPressChecked );
  }
};
if( document.getElementById('rememberme') ){
	document.addEventListener( 'DOMContentLoaded', addLoginPressSocialButton, false );
}

</script>
