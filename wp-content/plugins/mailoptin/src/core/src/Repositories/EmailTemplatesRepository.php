<?php

namespace MailOptin\Core\Repositories;


class EmailTemplatesRepository extends AbstractRepository
{
    private static $email_templates;

    public static function defaultTemplate()
    {
        if (is_null(self::$email_templates)) {
            self::$email_templates = apply_filters('mailoptin_registered_email_templates', array(
                array(
                    'name'           => 'Lucid',
                    'template_class' => 'Lucid',
                    'campaign_type'  => EmailCampaignRepository::NEW_PUBLISH_POST,
                    'screenshot'     => MAILOPTIN_ASSETS_URL . 'images/email-templates/lucid.png'
                ),
                array(
                    'name'           => 'Lucid',
                    'template_class' => 'Lucid',
                    'campaign_type'  => EmailCampaignRepository::POSTS_EMAIL_DIGEST,
                    'screenshot'     => MAILOPTIN_ASSETS_URL . 'images/email-templates/lucid.png'
                ),
                array(
                    'name'           => 'Lucid',
                    'template_class' => 'Lucid',
                    'campaign_type'  => EmailCampaignRepository::NEWSLETTER,
                    'screenshot'     => MAILOPTIN_ASSETS_URL . 'images/email-templates/lucid.png'
                )
            ));
        }
    }

    /**
     * All email templates available.
     *
     * @return mixed
     */
    public static function get_all()
    {
        self::defaultTemplate();

        return self::$email_templates;
    }

    /**
     * Get email_template of a given type.
     *
     * @param string $campaign_type
     *
     * @return mixed
     */
    public static function get_by_type($campaign_type)
    {
        $all = self::get_all();

        return array_reduce($all, function ($carry, $item) use ($campaign_type) {

            // remove leading & trailing whitespace.
            $campaign_type_array = array_map('trim', explode(',', $item['campaign_type']));

            if (in_array($campaign_type, $campaign_type_array)) {
                $carry[] = $item;
            }

            return $carry;
        });
    }

    /**
     * Get email_template by name.
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function get_by_name($name)
    {
        $all = self::get_all();

        return array_reduce($all, function ($carry, $item) use ($name) {

            if ($item['name'] == $name) {
                $carry = $item;
            }

            return $carry;
        });
    }

    /**
     * Add email template to template repository.
     *
     * @param mixed $data
     *
     * @return void
     */
    public static function add($data)
    {
        self::defaultTemplate();
        self::$email_templates[] = $data;
    }

    /**
     * Delete email template from stack.
     *
     * @param mixed $template_name
     *
     * @return void
     */
    public static function delete_by_name($template_name)
    {
        self::defaultTemplate();

        foreach (self::$email_templates as $index => $email_template) {
            if ($email_template['name'] == $template_name) {
                unset(self::$email_templates[$index]);
            }
        }
    }
}