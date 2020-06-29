<?php

namespace MailOptin\Core\EmailCampaigns;


interface TemplatifyInterface
{
    public function replace_footer_placeholder_tags($content);

    public function forge();
}