<?php
/**
 * Jilt for WooCommerce Promotions
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Jilt_Promotions\Notices;

defined( 'ABSPATH' ) or exit;

/**
 * The notice object.
 *
 * @since 1.1.0
 */
class Notice {


	/** @var string "button" notice action type */
	const ACTION_TYPE_BUTTON = 'button';

	/** @var string "link" notice action type */
	const ACTION_TYPE_LINK = 'link';


	/** @var string the notice message identifier */
	private $message_id = '';

	/** @var string the notice title */
	private $title = '';

	/** @var string the notice content */
	private $content = '';

	/** @var array the notice actions */
	private $actions = [];


	/**
	 * Sets the notice message ID.
	 *
	 * @since 1.1.0
	 *
	 * @param string $message_id message identifier
	 */
	public function set_message_id( $message_id ) {

		$this->message_id = $message_id;
	}


	/**
	 * Gets the notice message ID.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_message_id() {

		return $this->message_id;
	}


	/**
	 * Sets the notice title.
	 *
	 * @since 1.1.0
	 *
	 * @param string $title the notice title
	 */
	public function set_title( $title ) {

		$this->title = $title;
	}


	/**
	 * Gets the notice title.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_title() {

		return $this->title;
	}


	/**
	 * Sets the notice content.
	 *
	 * @since 1.1.0
	 *
	 * @param string $content the notice content
	 */
	public function set_content( $content ) {

		$this->content = $content;
	}


	/**
	 * Gets the notice content.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_content() {

		return $this->content;
	}


	/**
	 * Parses a set of actions.
	 *
	 * @since 1.1.0
	 *
	 * @param array $actions actions to parse
	 * @return array
	 */
	private function parse_actions( array $actions ) {

		return array_map( function( $action ) {

			return wp_parse_args( $action, [
				'label'   => '',
				'name'    => '',
				'url'     => '',
				'primary' => false,
				'type'    => self::ACTION_TYPE_LINK,
			] );
		}, $actions );
	}


	/**
	 * Sets the notice actions.
	 *
	 * @since 1.1.0
	 *
	 * @param array $actions the notice actions
	 */
	public function set_actions( array $actions ) {

		$this->actions = $this->parse_actions( $actions );
	}


	/**
	 * Gets the notice actions.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_actions() {

		return $this->parse_actions( $this->actions );
	}


	/**
	 * Outputs the notice.
	 *
	 * @since 1.1.0
	 */
	public function render() {

		?>
		<div class="sv-wc-jilt-promotional-notice notice notice-success is-dismissible" data-message-id="<?php echo esc_attr( $this->get_message_id() ); ?>">
			<p><?php $this->render_content(); ?></p>
			<?php $this->render_actions(); ?>
		</div>
		<?php
	}


	/**
	 * Outputs the notice content.
	 *
	 * @since 1.1.0
	 */
	private function render_content() {

		echo sprintf(
			/** translators: Placeholders: %1$s - <strong> opening HTML tag, %2$s - the title for the notice, %3$s - <strong> closing HTML tag, %4$s - the content of the notice */
			esc_html__( '%1$s%2$s%3$s %4$s', 'sv-wc-jilt-promotions' ),
			'<strong>',
			$this->get_title(),
			'</strong>',
			$this->get_content()
		);
	}


	/**
	 * Outputs the notice actions.
	 *
	 * @since 1.1.0
	 */
	private function render_actions() {

		?>
		<div class="sv-wc-jilt-prompt-actions">
			<?php foreach ( $this->get_actions() as $action ) : ?>

				<?php $classes = $action['primary'] ? 'sv-wc-jilt-prompt-action sv-wc-jilt-prompt-primary-action' : 'sv-wc-jilt-prompt-action'; ?>

				<?php if ( self::ACTION_TYPE_LINK === $action['type'] ) : ?>
					<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( $action['url'] ); ?>" target="_blank" data-action="<?php echo esc_attr( $action['name'] ); ?>"><?php echo esc_html( $action['label'] ); ?></a>
				<?php else : ?>
					<button class="<?php echo esc_attr( $classes ); ?> button<?php echo $action['primary'] ? ' button-primary' : ''; ?>" data-action="<?php echo esc_attr( $action['name'] ); ?>"><?php echo esc_html( $action['label'] ); ?></button>
				<?php endif; ?>

			<?php endforeach; ?>
		</div>
		<?php
	}


}
