<?php

namespace MailOptin\Core\EmailCampaigns;


interface TemplateInterface
{
    /**
     * Default customizer values of a template.
     *
     * @return array
     */
    public function default_customizer_values();

    /**
     * HTML body structure of the template
     *
     * @return string
     */
    public function get_body();

    /**
     * CSS stylesheet for the template
     *
     * @return string
     */
    public function get_styles();

    /**
     * Customizer JavaScript for the template.
     *
     * E.g return MAILOPTIN_TEMPLATES_URL . 'Lucid/Lucid.js
     *
     * @return string
     */
    public function get_script();
}