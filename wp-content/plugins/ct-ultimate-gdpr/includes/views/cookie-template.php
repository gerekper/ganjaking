<?php

if($options['cookie_single_popup']) :
    ct_ultimate_gdpr_locate_template('cookie-single-popup', true, $options);
else:
    ct_ultimate_gdpr_locate_template('cookie-group-popup', true, $options);
endif;