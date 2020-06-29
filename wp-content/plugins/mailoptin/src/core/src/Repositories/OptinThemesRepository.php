<?php

namespace MailOptin\Core\Repositories;


class OptinThemesRepository extends AbstractRepository
{
    private static $optin_themes;

    public static function defaultThemes()
    {
        if (is_null(self::$optin_themes)) {
            self::$optin_themes = apply_filters(
                'mailoptin_registered_optin_themes',
                array_merge(self::free_themes(), self::premium_themes())
            );
        }
    }

    public static function free_themes()
    {
        return [
            [
                'name'        => 'BareMetal',
                'optin_class' => 'BareMetal',
                'optin_type'  => 'lightbox', // accept comma delimited values eg lightbox,sidebar,inpost
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/baremetal-lightbox.png'
            ],
            [
                'name'        => 'Elegance',
                'optin_class' => 'Elegance',
                'optin_type'  => 'lightbox',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/elegance-lightbox.png'
            ],
            [
                'name'        => 'Lupin',
                'optin_class' => 'Lupin',
                'optin_type'  => 'sidebar', // accept comma delimited values eg lightbox,sidebar,inpost
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/lupin-sidebar.png'
            ],
            [
                'name'        => 'Gridgum',
                'optin_class' => 'Gridgum',
                'optin_type'  => 'sidebar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/gridgum-sidebar-slidein.png'
            ],
            [
                'name'        => 'Columbine',
                'optin_class' => 'Columbine',
                'optin_type'  => 'inpost', // accept comma delimited values eg lightbox,sidebar,inpost
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/columbine-inpost.png'
            ],
            [
                'name'        => 'BareMetal',
                'optin_class' => 'BareMetal',
                'optin_type'  => 'inpost',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/baremetal-inpost.png'
            ],
            [
                'name'        => 'Elegance',
                'optin_class' => 'Elegance',
                'optin_type'  => 'inpost',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/elegance-inpost.png'
            ],
        ];
    }

    public static function premium_themes()
    {
        return [
            [
                'name'        => 'Bannino',
                'optin_class' => 'Bannino',
                'flag'        => 'premium',
                'optin_type'  => 'lightbox', // accept comma delimited values eg lightbox,sidebar,inpost
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/bannino/bannino-lightbox.png'
            ],
            [
                'name'        => 'Bannino',
                'optin_class' => 'Bannino',
                'flag'        => 'premium',
                'optin_type'  => 'inpost', // accept comma delimited values eg lightbox,sidebar,inpost
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/bannino/bannino-inpost.png'
            ],
            [
                'name'        => 'Daisy',
                'optin_class' => 'Daisy',
                'flag'        => 'premium',
                'optin_type'  => 'lightbox',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/daisy-lightbox.png'
            ],
            [
                'name'        => 'Muscari',
                'optin_class' => 'Muscari',
                'flag'        => 'premium',
                'optin_type'  => 'bar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/muscari-bar.png'
            ],
            [
                'name'        => 'Dahlia',
                'optin_class' => 'Dahlia',
                'flag'        => 'premium',
                'optin_type'  => 'bar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/dahlia-bar.png'
            ],
            [
                'name'        => 'Dashdot',
                'optin_class' => 'Dashdot',
                'flag'        => 'premium',
                'optin_type'  => 'bar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/dashdot-bar.png'
            ],
            [
                'name'        => 'Mimosa',
                'optin_class' => 'Mimosa',
                'flag'        => 'premium',
                'optin_type'  => 'slidein',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/mimosa-slidein.png'
            ],
            [
                'name'        => 'Letter Box',
                'optin_class' => 'LetterBox',
                'flag'        => 'premium',
                'optin_type'  => 'slidein',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/letterbox-slidein.png'
            ],
            [
                'name'        => 'Letter Box',
                'optin_class' => 'LetterBox',
                'flag'        => 'premium',
                'optin_type'  => 'sidebar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/letterbox-sidebar.png'
            ],
            [
                'name'        => 'Letter Box',
                'optin_class' => 'LetterBox',
                'flag'        => 'premium',
                'optin_type'  => 'inpost',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/letterbox-inpost.png'
            ],
            [
                'name'        => 'Letter Box',
                'optin_class' => 'LetterBox',
                'flag'        => 'premium',
                'optin_type'  => 'lightbox',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/letterbox-lightbox.png'
            ],
            [
                'name'        => 'Primrose',
                'optin_class' => 'Primrose',
                'flag'        => 'premium',
                'optin_type'  => 'sidebar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/primrose-sidebar.png'
            ],
            [
                'name'        => 'Liatris',
                'optin_class' => 'Liatris',
                'flag'        => 'premium',
                'optin_type'  => 'lightbox',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/liatris/liatris-lightbox.png'
            ],
            [
                'name'        => 'Liatris',
                'optin_class' => 'Liatris',
                'flag'        => 'premium',
                'optin_type'  => 'inpost',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/liatris/liatris-inpost.png'
            ],
            [
                'name'        => 'Liatris',
                'optin_class' => 'Liatris',
                'flag'        => 'premium',
                'optin_type'  => 'sidebar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/liatris/liatris-sidebar.png'
            ],
            [
                'name'        => 'Liatris',
                'optin_class' => 'Liatris',
                'flag'        => 'premium',
                'optin_type'  => 'slidein',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/liatris/liatris-slidein.png'
            ],
            [
                'name'        => 'Gridgum',
                'optin_class' => 'Gridgum',
                'flag'        => 'premium',
                'optin_type'  => 'inpost',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/gridgum-inpost.png'
            ],
            [
                'name'        => 'Gridgum',
                'optin_class' => 'Gridgum',
                'flag'        => 'premium',
                'optin_type'  => 'lightbox',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/gridgum-lightbox.png'
            ],
            [
                'name'        => 'Gridgum',
                'optin_class' => 'Gridgum',
                'flag'        => 'premium',
                'optin_type'  => 'slidein',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/gridgum-sidebar-slidein.png'
            ],
            [
                'name'        => 'Boldy',
                'optin_class' => 'Boldy',
                'flag'        => 'premium',
                'optin_type'  => 'lightbox',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/boldy-lightbox.png'
            ],
            [
                'name'        => 'Boldy',
                'optin_class' => 'Boldy',
                'flag'        => 'premium',
                'optin_type'  => 'inpost',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/boldy-inpost.png'
            ],
            [
                'name'        => 'Boldy',
                'optin_class' => 'Boldy',
                'flag'        => 'premium',
                'optin_type'  => 'sidebar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/boldy-sidebar.png'
            ],
            [
                'name'        => 'Boldy',
                'optin_class' => 'Boldy',
                'flag'        => 'premium',
                'optin_type'  => 'slidein',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/boldy-slidein.png'
            ],
            [
                'name'        => 'Rescript',
                'optin_class' => 'Rescript',
                'flag'        => 'premium',
                'optin_type'  => 'lightbox',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/rescript-lightbox.png'
            ],
            [
                'name'        => 'Rescript',
                'optin_class' => 'Rescript',
                'flag'        => 'premium',
                'optin_type'  => 'inpost',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/rescript-inpost.png'
            ],
            [
                'name'        => 'Rescript',
                'optin_class' => 'Rescript',
                'flag'        => 'premium',
                'optin_type'  => 'sidebar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/rescript-sidebar.png'
            ],
            [
                'name'        => 'Rescript',
                'optin_class' => 'Rescript',
                'flag'        => 'premium',
                'optin_type'  => 'slidein',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/rescript-slidein.png'
            ],
            [
                'name'        => 'Alyssum',
                'optin_class' => 'Alyssum',
                'flag'        => 'premium',
                'optin_type'  => 'lightbox',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/alyssum/alyssum-lightbox.png'
            ],
            [
                'name'        => 'Alyssum',
                'optin_class' => 'Alyssum',
                'flag'        => 'premium',
                'optin_type'  => 'inpost',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/alyssum/alyssum-inpost-sidebar.png'
            ],
            [
                'name'        => 'Alyssum',
                'optin_class' => 'Alyssum',
                'flag'        => 'premium',
                'optin_type'  => 'sidebar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/alyssum/alyssum-inpost-sidebar.png'
            ],
            [
                'name'        => 'Alyssum',
                'optin_class' => 'Alyssum',
                'flag'        => 'premium',
                'optin_type'  => 'slidein',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/alyssum/alyssum-slidein.png'
            ],
            [
                'name'        => 'Scilla',
                'optin_class' => 'Scilla',
                'flag'        => 'premium',
                'optin_type'  => 'sidebar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/scilla/scilla-sidebar-slidein.png'
            ],
            [
                'name'        => 'Scilla',
                'optin_class' => 'Scilla',
                'flag'        => 'premium',
                'optin_type'  => 'slidein',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/scilla/scilla-sidebar-slidein.png'
            ],
            [
                'name'        => 'Solidago',
                'optin_class' => 'Solidago',
                'flag'        => 'premium',
                'optin_type'  => 'lightbox',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/solidago-optin.png'
            ],
            [
                'name'        => 'Solidago',
                'optin_class' => 'Solidago',
                'flag'        => 'premium',
                'optin_type'  => 'inpost',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/solidago-optin.png'
            ],
            [
                'name'        => 'Solidago',
                'optin_class' => 'Solidago',
                'flag'        => 'premium',
                'optin_type'  => 'sidebar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/solidago-optin.png'
            ],
            [
                'name'        => 'Solidago',
                'optin_class' => 'Solidago',
                'flag'        => 'premium',
                'optin_type'  => 'slidein',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/solidago-optin.png'
            ],
            [
                'name'        => 'Quince',
                'optin_class' => 'Quince',
                'flag'        => 'premium',
                'optin_type'  => 'lightbox',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/quince/quince-lightbox.png'
            ],
            [
                'name'        => 'Quince',
                'optin_class' => 'Quince',
                'flag'        => 'premium',
                'optin_type'  => 'inpost',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/quince/quince-inpost-sidebar-slidein.png'
            ],
            [
                'name'        => 'Quince',
                'optin_class' => 'Quince',
                'flag'        => 'premium',
                'optin_type'  => 'sidebar',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/quince/quince-inpost-sidebar-slidein.png'
            ],
            [
                'name'        => 'Quince',
                'optin_class' => 'Quince',
                'flag'        => 'premium',
                'optin_type'  => 'slidein',
                'screenshot'  => MAILOPTIN_ASSETS_URL . 'images/optin-themes/quince/quince-inpost-sidebar-slidein.png'
            ],
        ];
    }

    /**
     * All Optin themes available.
     *
     * @return mixed
     */
    public static function get_all()
    {
        self::defaultThemes();

        return self::$optin_themes;
    }

    /**
     * Get optin themes of a given type.
     *
     * @param string $optin_type
     *
     * @return mixed
     */
    public static function get_by_type($optin_type)
    {
        $all = self::get_all();

        return array_reduce($all, function ($carry, $item) use ($optin_type) {

            // remove leading & trailing whitespace.
            $optin_type_array = array_map('trim', explode(',', $item['optin_type']));

            if (in_array($optin_type, $optin_type_array)) {
                $carry[] = $item;
            }

            return $carry;
        });
    }

    /**
     * Get optin theme by name.
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

    public static function listing_display_template($optin_type = 'lightbox')
    {
        $optin_designs = OptinThemesRepository::get_by_type($optin_type);

        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM') && in_array($optin_type, ['bar', 'slidein'])) $optin_designs = [];

        if (empty($optin_designs)) {
            if ($optin_type == 'bar') $optin_type = 'notification bar';
            echo '<div class="mo-error-box" style="padding: 87px 10px;margin:0;">';
            printf(
                __('Upgrade to %s for %s support.', 'mailoptin'),
                '<a href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=optin_themes_not_found" target="_blank">MailOptin Premium</a>',
                $optin_type
            );
            echo '</div>';
        } else {

            foreach ($optin_designs as $optin_theme) {
                $upgrade_url = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=premium_optin_theme';

                $theme_name  = $optin_theme['name'];
                $theme_class = $optin_theme['optin_class'];
                $screenshot  = $optin_theme['screenshot'];

                $is_premium_theme_disallowed = ! defined('MAILOPTIN_DETACH_LIBSODIUM') && isset($optin_theme['flag']) && $optin_theme['flag'] == 'premium';

                $extra_class = $is_premium_theme_disallowed ? '' : ' mo-allow-activate';
                $url         = $is_premium_theme_disallowed ? $upgrade_url : '#';
                $url_target  = $is_premium_theme_disallowed ? ' target="_blank"' : '';
                ?>
                <div id="mailoptin-optin-theme-list" class="mailoptin-optin-theme<?= $extra_class; ?> mailoptin-optin-theme-<?php echo $theme_class; ?>" data-optin-theme="<?php echo $theme_class; ?>" data-optin-type="<?php echo $optin_type; ?>">
                    <a <?= $url_target; ?> href="<?= $url; ?>">
                        <div class="mailoptin-optin-theme-screenshot">
                            <img src="<?php echo $screenshot; ?>" alt="<?php echo $theme_name; ?>">
                        </div>
                        <?php if ($is_premium_theme_disallowed) : ?>
                            <div class="mo-premium-flag"></div>
                        <?php endif; ?>
                        <h3 class="mailoptin-optin-theme-name"><?php echo $theme_name; ?></h3>
                        <div class="mailoptin-optin-theme-actions">
                            <a <?= $url_target; ?> href="<?= $url; ?>" class="button button-primary mailoptin-theme-select" data-optin-theme="<?php echo $theme_class; ?>" data-optin-type="<?php echo $optin_type; ?>" title="<?php _e('Select this theme', 'mailoptin'); ?>">
                                <?php _e('Select Theme', 'mailoptin'); ?>
                            </a>
                        </div>
                    </a>
                </div>
                <?php
            }
        }
    }

    /**
     * Add optin theme to theme repository.
     *
     * @param mixed $data
     *
     * @return void
     */
    public static function add($data)
    {
        self::defaultThemes();
        self::$optin_themes[] = $data;
    }

    /**
     * Delete optin theme from stack.
     *
     * @param mixed $optin_theme_name
     *
     * @return void
     */
    public static function delete_by_name($optin_theme_name)
    {
        self::defaultThemes();

        foreach (self::$optin_themes as $index => $optin_theme) {
            if ($optin_theme['name'] == $optin_theme_name) {
                unset(self::$optin_themes[$index]);
            }
        }
    }
}