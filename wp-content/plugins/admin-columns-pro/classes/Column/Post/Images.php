<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export;
use ACP\Sorting;

/**
 * @since 4.0.8
 */
class Images extends AC\Column
	implements Sorting\Sortable, AC\Column\AjaxValue, Export\Exportable {

	public function __construct() {
		$this->set_type( 'column-images' );
		$this->set_label( __( 'Images', 'codepress-admin-columns' ) );
	}

	public function sorting() {
		return new Sorting\Model\Post\ImageFileSizes();
	}

	public function export() {
		return new Export\Model\Post\ImageFileSizes( $this );
	}

	/**
	 * Returns the file size and dimensions of the image with a link to the edit media page.
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_ajax_value( $id ) {
		$items = [];

		foreach ( $this->get_image_urls( $id ) as $url ) {
			$size = ac_helper()->image->get_local_image_size( $url );

			if ( ! $size ) {
				continue;
			}

			$dimensions = false;
			$extension = false;
			$link = false;
			$size_int = false;
			$size_unit = false;

			if ( $info = ac_helper()->image->get_local_image_info( $url ) ) {
				$dimensions = $info[0] . ' x ' . $info[1];
				$extension = image_type_to_extension( $info[2], false );
			}

			$attachment_id = ac_helper()->media->get_attachment_id_by_url( $url, true );

			if ( $attachment_id ) {
				$link = get_edit_post_link( $attachment_id );
			}

			if ( $file_size = ac_helper()->file->get_readable_filesize_as_array( $size ) ) {
				$size_int = $file_size[0];
				$size_unit = $file_size[1];
			}

			$items[] = (object) [
				'file_size'       => $size_int,
				'file_size_label' => $size_unit,
				'dimensions'      => $dimensions,
				'extension'       => $extension,
				'link'            => $link,
				'file_name'       => basename( $url ),
			];

		}

		if ( ! $items ) {
			return false;
		}

		ob_start();
		?>
		<div class="ac-image-details">
		<?php foreach ( $items as $item ) : ?>

			<?php if ( $item->link ) : ?>
				<a href="<?php echo esc_url( $item->link ); ?>" class="ac-image-info">
				<?php echo ac_helper()->html->tooltip( ac_helper()->icon->dashicon( [ 'icon' => 'format-image' ] ), $item->file_name ); ?>
			<?php else : ?>
				<div class="ac-image-info">
			<?php endif; ?>

			<?php if ( $item->extension ) : ?>
				<span class="image-extension"><?php echo $item->extension; ?></span>
			<?php endif; ?>
			<?php if ( $item->file_size ) : ?>
				<span class="image-file-size"><?php echo $item->file_size; ?><span class="suffix"><?php echo $item->file_size_label; ?></span></span>
			<?php endif; ?>
			<?php if ( $item->dimensions ) : ?>
				<span class="image-dimensions"><?php echo $item->dimensions; ?><span class="suffix">px</span></span>
			<?php endif; ?>

			<?php if ( $item->link ) : ?>
				</a>
			<?php else : ?>
				</div>
			<?php endif; ?>

		<?php endforeach; ?>
		</div>
		<?php

		return ob_get_clean();
	}

	public function get_value( $id ) {
		$sizes = $this->get_raw_value( $id );

		if ( ! $sizes ) {
			return $this->get_empty_char();
		}

		$count = count( $sizes );

		$label = ac_helper()->html->rounded( $count );

		// File size as label with count
		$label .= ac_helper()->file->get_readable_filesize( array_sum( $sizes ) );

		// Total images
		$tooltip = '<strong>' . sprintf( _n( '%s image', '%s images', $count, 'codepress-admin-columns' ), $count ) . '</strong>';
		$value = ac_helper()->html->tooltip( $label, $tooltip );

		return ac_helper()->html->get_ajax_toggle_box_link( $id, $value, $this->get_name() );
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public function get_raw_value( $id ) {
		$sizes = [];

		foreach ( $this->get_image_urls( $id ) as $url ) {
			if ( $size = ac_helper()->image->get_local_image_size( $url ) ) {
				$sizes[] = $size;
			}
		}

		return $sizes;
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	private function get_image_urls( $id ) {
		$string = ac_helper()->post->get_raw_field( 'post_content', $id );

		/**
		 * Parsed content for images.
		 *
		 * @param string $string
		 * @param int    $id
		 * @param int    $this
		 *
		 * @return string
		 */
		$string = apply_filters( 'ac/column/images/content', $string, $id, $this );

		return array_unique( ac_helper()->image->get_image_urls_from_string( $string ) );
	}

}