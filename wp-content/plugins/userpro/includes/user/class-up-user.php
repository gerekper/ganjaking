<?php

if (!defined('ABSPATH')) {
    exit;
}

class UP_User extends UP_Data
{
    /**
     * @since 4.9.31
     * @var string
     */
    protected $option_name = null;
    /**
     * @since 4.9.31
     * @var UP_UserSocial|null
     */
    public $user_social = null;
    /**
     * @since 4.9.31
     * @var UP_UserPosts|null
     */
    public $user_posts = null;

    public function __construct($id = null)
    {
        if (is_numeric($id) && $id > 0) {

            $this->setUserId($id);

            $this->user_social = new UP_UserSocial($this);
            $this->user_posts = new UP_UserPosts($this);
        }
    }

    /**
     * Get all User data.
     *
     * @return mixed
     * @since 4.9.31
     */
    public function getUserData()
    {
        return get_userdata($this->user_id);
    }

    /**
     * Get user fields data to user profile
     *
     * @param $template
     * @return array
     * @since 4.9.31
     */
    public function getUserProfileFields($template)
    {
        global $userpro;
        $checkup;
        $checkup2;
        // Get all default fields with all values
        $template_fields = $this->getOption('userpro_fields_groups');

        $view = array();

        foreach ($template_fields[$template]['default'] as $key => $visible_field) {
            // If fields is hidden we don't need it . If field type is picture we dont need it in visible fields array
            if (isset($visible_field['hidden']) && $visible_field['hidden'] == 1
                || empty($visible_field['label'])
                || $visible_field['type'] === 'picture') {
                continue;
            }
            // If user have field meta we will add it to array.
            if ($this->getUserMeta($key, true) != '') {
                foreach($userpro->fields_to_hide_from_view() as $result){
                    if($userpro->field_label($result) === $visible_field['label']){
                        $checkup = false;
                        break;
                    }
                    $checkup = true;
                }
                if($checkup == true){
                    $checkup2 = false;
                    foreach(userpro_retrieve_metakeys() as $result){
                        if(get_user_meta($this->user_id, 'hide_'.$result, true) == 0 && $visible_field['label'] === $userpro->field_label($result)){
                            $checkup2 = true;
                            break;
                        }
                    }
                    if($checkup2 == true){
                        $view[] = $this->addToFieldsArray($key, $visible_field);
                    }
                }
            }
        }
        
        return $view;
    }

    /**
     * Get user avatar url.
     *
     * @return bool|string
     * @since 4.9.31
     */
    public function getProfileAvatar()
    {
        return get_avatar($this->user_id, 190);
    }

    /**
     * Get User Display Name To profile
     *
     * @return mixed
     * @since 4.9.34
     */
    public function getUserDisplayName(){

        if(userpro_get_option('user_display_name') === 'name'){
            global $userpro;

            return $userpro->get_full_name($this->user_id);

        }

        return $this->getUserMeta('nickname', true);
    }
    /**
     * Get user profile cover image
     *
     * @return string
     * @since 4.9.31
     */
    public function getProfileCover()
    {
        // Get User uploaded cover image or default from backend.
        $cover = $this->getUserMeta('custom_profile_bg', true)
            ? $this->getUserMeta('custom_profile_bg', true)
            : $this->getOption('default_background_img', UP_PREFIX);

        if (empty($cover)) {
            $cover = userpro_url . '/skins/elegant/img/pattern.png';
        }

        return $cover;
    }

    /**
     * Get User Badges
     * @return string|null
     * @since 4.9.31
     */
    public function getUserBadges()
    {
        return userpro_show_badges($this->user_id);
    }

    /**
     * Get Profile Edit Url.
     * @return string
     * @since 4.9.31
     */
    public function getEditUrl()
    {
        global $userpro;

        return $userpro->permalink($this->user_id, 'edit');
    }

    /**
     * Get User Profile Data
     *
     * @param $user_id integer
     * @return string
     * @since 4.9.31
     */
    public function getProfileData($user_id)
    {

        $body = '';

        $this->setUserId($user_id);

        $profileDetails = $this->getUserProfileFields('view');

        foreach ($profileDetails as $field) {
            $body .= ' <div class="up-profile-information__field">';
            $body .= '<div class="up-label">' . $field['label'] . '</div>';
            if (is_array($field['value'])) {
                $body .= '<div class="up-value">';
                foreach ($field['value'] as $val) {
                    $body .= $val . ' ';
                }
                $body .= '</div>';
            } else {
                if($field['type'] == 'datepicker' && userpro_get_option('date_to_age') == 1){
                    $body .= '<div class="up-value">' . $this->date_to_age($field['value']) . '</div>';
                }
                else{
                    $body .= '<div class="up-value">' . $field['value'] . '</div>';
                }
            }
            $body .= '</div>';
        }

        return $body;
    }
    function date_to_age($dt) {
        $format = '';
        $date_format = explode('-',userpro_get_option('date_format'));
        foreach($date_format as $f){
            if($f == 'yy'){
                $format .= substr(strtoupper($f), 1) . '-';
            }
            else{
                $format .= substr($f, 1) . '-';
            }
        }
        $format = rtrim($format, '-');
        try{
            $start = DateTime::createFromFormat($format, date($format));
            $end   = DateTime::createFromFormat($format, $dt);
            if(!isset($start->diff( $end )->y) ){
                $diff .= $dt;
            }
            else{
                $diff  .= $start->diff( $end )->y;
            }
        }
        catch(Exception $ex){
            $diff = $dt;
        }

        return $diff;
    }
    /**
     * Add to visible fields array
     *
     * @param $key
     * @param $visible_field
     * @return array|null
     */
    protected function addToFieldsArray($key, $visible_field)
    {
        $value = $this->getUserMeta($key, true);
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $value = '<a target="_blank" href="' . $value . '">' . $value . '</a>';
        }

        $field = [
            'label' => $visible_field['label'],
            'value' => $value,
            'type' => $visible_field['type'],
        ];

        return $field;
    }
}
