<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WPDeveloper\BetterDocs\Core\Settings;
use WPDeveloper\BetterDocs\Utils\Database;
use WPDeveloper\BetterDocs\Core\Roles as FreeRoles;

class Roles extends FreeRoles {
    private $settings;
    public function __construct( Database $database, Settings $settings ) {
        parent::__construct( $database );

        $this->settings = $settings;

        add_action( 'betterdocs::settings::saved', [$this, 'saved_settings'], 11, 4 );
    }

    public function get_selected_roles( $settings ) {
        return [
            'edit_docs'           => [
                'roles' => isset( $settings['article_roles'] ) ? $settings['article_roles'] : $this->settings->get( 'article_roles' )
            ],
            'edit_docs_settings'  => [
                'roles' => isset( $settings['settings_roles'] ) ? $settings['settings_roles'] : $this->settings->get( 'settings_roles' )
            ],
            'read_docs_analytics' => [
                'roles' => isset( $settings['analytics_roles'] ) ? $settings['analytics_roles'] : $this->settings->get( 'analytics_roles' )
            ]
        ];
    }

    protected function array_diff_assoc_recursive( $array1, $array2 ) {
        $difference = [];

        foreach ( $array1 as $key => $value ) {
            if ( is_array( $value ) ) {
                if ( ! isset( $array2[$key] ) || ! is_array( $array2[$key] ) ) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = $this->array_diff_assoc_recursive( $value, $array2[$key] );
                    if ( ! empty( $new_diff ) ) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif ( ! array_key_exists( $key, $array2 ) || $array2[$key] !== $value ) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }

    public function reset_settings( $key, $settings, $dhatSettings ) {
        if ( empty( $settings[$key] ) ) {
            $dhatSettings->save( $key, ['administrator'] );
        } elseif ( ! in_array( 'administrator', $settings[$key], true ) ) {
            $dhatSettings->save( $key, array_merge( $settings[$key], ['administrator'] ) );
        }
    }

    protected function take_action( $role, $_role, $action = 'add_cap' ){
        $_default_roles = $this->defaults_capabilities();
        $caps           = isset( $_default_roles[$_role] ) ? $_default_roles[$_role] : $_default_roles['other'];
        foreach ( $caps as $_cap ) {
            call_user_func([ $role, $action ], $_cap );
        }
    }

    public function saved_settings( $is_saved, $_normalized_settings, $old_settings, $settings ) {
        if ( $is_saved ) {
            $this->reset_settings( 'article_roles', $_normalized_settings, $settings );
            $this->reset_settings( 'settings_roles', $_normalized_settings, $settings );
            $this->reset_settings( 'analytics_roles', $_normalized_settings, $settings );

            $_mapped_roles     = $this->get_selected_roles( $_normalized_settings );
            $_old_mapped_roles = $this->get_selected_roles( $old_settings );
            $_removed_roles    = $this->array_diff_assoc_recursive( $_old_mapped_roles, $_mapped_roles );

            if ( ! empty( $_removed_roles ) ) {
                foreach ( $_removed_roles as $cap => $_roles ) {
                    if ( ! empty( $_roles['roles'] ) ) {
                        foreach ( $_roles['roles'] as $_role ) {
                            if ( $_role === 'administrator' ) {
                                continue;
                            }
                            $role = get_role( $_role );
                            if ( ! is_null( $role ) && $role instanceof \WP_Role ) {
                                if ( $cap === 'edit_docs' ) {
                                    $this->take_action( $role, $_role, 'remove_cap' );
                                    // $_default_roles = $this->defaults_capabilities();
                                    // $caps           = isset( $_default_roles[$_role] ) ? $_default_roles[$_role] : $_default_roles['other'];
                                    // foreach ( $caps as $_cap ) {
                                    //     $role->remove_cap( $_cap );
                                    // }
                                } else {
                                    $role->remove_cap( $cap );
                                }
                            }
                        }
                    }
                }
            }

            foreach ( $_mapped_roles as $cap => $_roles ) {
                if ( ! empty( $_roles['roles'] ) ) {
                    foreach ( $_roles['roles'] as $_role ) {
                        if ( $_role === 'administrator' ) {
                            continue;
                        }
                        $role = get_role( $_role );
                        if ( ! is_null( $role ) && $role instanceof \WP_Role ) {
                            if ( $cap === 'edit_docs' ) {
                                $this->take_action( $role, $_role, 'add_cap' );
                                // $_default_roles = $this->defaults_capabilities();
                                // $caps           = isset( $_default_roles[$_role] ) ? $_default_roles[$_role] : $_default_roles['other'];
                                // foreach ( $caps as $_cap ) {
                                //     $role->add_cap( $_cap );
                                // }
                            } else {
                                $role->add_cap( $cap );
                            }
                        }
                    }
                }
            }
        }
    }
}
