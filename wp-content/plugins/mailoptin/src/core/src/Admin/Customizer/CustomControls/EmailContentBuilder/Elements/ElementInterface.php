<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements;


interface ElementInterface
{
    public function id();

    public function title();

    public function description();

    public function icon();

    public function tabs();

    public function settings();

    public function is_premium_element();
}