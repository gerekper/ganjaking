<?php
declare( strict_types=1 );

namespace ACP\Admin\ScriptFactory;

use AC\Asset;
use AC\Asset\Location;
use AC\Asset\Script;
use AC\Asset\Script\Localize\Translation;
use AC\Asset\ScriptFactory;
use AC\ListScreen;
use ACP\Settings\ListScreen\HideOnScreenCollection;
use ACP\Type\HideOnScreen\Group;

class SettingsFactory implements ScriptFactory {

	public const HANDLE = 'acp-settings';

	private $location;

	private $elements;

	private $list_screen;

	public function __construct( Location $location, HideOnScreenCollection $elements, ListScreen $list_screen ) {
		$this->location = $location;
		$this->elements = $elements;
		$this->list_screen = $list_screen;
	}

	public function create(): Script {
		$script = new Asset\Script(
			self::HANDLE,
			$this->location->with_suffix( 'assets/core/js/layouts.js' ),
			[ 'ac-admin-page-columns' ]
		);

		$translation = new Translation( [
			'roles' => __( 'Select roles', 'codepress-admin-columns' ),
			'users' => __( 'Select users', 'codepress-admin-columns' ),
		] );

		$script->localize( 'acp_settings_i18n', $translation );

		$inline_vars = [
			'_nonce' => wp_create_nonce( 'acp-layout' ),
		];

		$groups = [
			Group::FEATURE => __( 'Features', 'codepress-admin-columns' ),
			Group::ELEMENT => __( 'Default Elements', 'codepress-admin-columns' ),
		];

		foreach ( $groups as $group_name => $group_label ) {
			$group = [
				'group_name'  => $group_name,
				'group_label' => $group_label,
			];

			$elements = $this->elements->all( [
				'filter_by_group' => new Group( $group_name ),
			] );

			foreach ( $elements as $element ) {
				$group['elements'][] = [
					'name'         => $element->get_name(),
					'label'        => $element->get_label(),
					'active'       => ! $element->is_hidden( $this->list_screen ),
					'dependent_on' => $element->has_dependent_on() ? $element->get_dependent_on() : null,
				];
			}

			$inline_vars['table_elements'][] = $group;
			$inline_vars['read_only'] = $this->list_screen->is_read_only();
		}

		return $script->add_inline_variable( 'acp_settings', $inline_vars );
	}

}