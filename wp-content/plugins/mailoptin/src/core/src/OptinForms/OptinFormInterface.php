<?php

namespace MailOptin\Core\OptinForms;


interface OptinFormInterface
{
    /**
     * Optin theme class must declare support for special features they support.
     *
     * @return mixed
     */
    public function features_support();

    /**
     * HTML body structure of the optin form
     *
     * @return string
     */
    public function optin_form();

    /**
     * CSS stylesheet for the optin form
     *
     * @return string
     */
    public function optin_form_css();

    /**
     * Customizer JavaScript for the template
     *
     * @return string
     */
    public function optin_script();
}