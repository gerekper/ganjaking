<?php
/**
 * UAEL Table Module Template.
 *
 * @package UAEL
 */

use Elementor\Control_Media;
// Wrapper.
$this->add_render_attribute( 'uael_table_wrapper', 'class', 'uael-table-wrapper' );
$this->add_render_attribute( 'uael_table_wrapper', 'itemtype', 'http://schema.org/Table' );

$this->add_render_attribute( 'uael_table_id', 'id', 'uael-table-id-' . $node_id );

$this->add_render_attribute( 'uael_table_id', 'class', 'uael-text-break' );

$this->add_render_attribute( 'uael_table_id', 'class', 'uael-column-rules' );
$this->add_render_attribute( 'uael_table_id', 'class', 'uael-table' );
// Tr (Row).
$this->add_render_attribute( 'uael_table_row', 'class', 'uael-table-row' );
// Text span.
$this->add_render_attribute( 'uael_table__text', 'class', 'uael-table__text' );
// Sortable.
if ( 'yes' === $settings['sortable'] ) {
	$this->add_render_attribute( 'uael_table_id', 'data-sort-table', $settings['sortable'] );
} else {
	$this->add_render_attribute( 'uael_table_id', 'data-sort-table', 'no' );
}
// Show entries.
if ( 'yes' === $settings['show_entries'] ) {
	$this->add_render_attribute( 'uael_table_id', 'data-show-entry', $settings['show_entries'] );
} else {
	$this->add_render_attribute( 'uael_table_id', 'data-show-entry', 'no' );
}

if ( 'yes' === $settings['searchable'] ) {
	$this->add_render_attribute( 'uael_table_id', 'data-searchable', $settings['searchable'] );
	$this->add_render_attribute( 'uael_table_id', 'data-search_text', $settings['search_text'] );
} else {
	$this->add_render_attribute( 'uael_table_id', 'data-searchable', 'no' );
}
if ( 'yes' === $settings['table_responsive'] ) {
	$this->add_render_attribute( 'uael_table_id', 'data-responsive', $settings['table_responsive'] );
} else {
	$this->add_render_attribute( 'uael_table_id', 'data-responsive', 'no' );
}
$csv = $this->parse_csv();

?>
<div itemscope <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table_wrapper' ) ); ?>>
	<?php
	if ( 'file' === $settings['source'] ) {
		echo wp_kses_post( $csv['html'] );
	} else {
		?>
	<table <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table_id' ) ); ?>>
		<?php
		$first_row_h    = true;
		$counter_h      = 1;
		$cell_counter_h = 0;
		$inline_count   = 0;
		$row_count_h    = count( $settings['table_headings'] );
		$header_text    = array();
		$data_entry     = 0;

		if ( $row_count_h > 1 ) {
			?>
		<thead>
			<?php
			if ( $settings['table_headings'] ) {
				foreach ( $settings['table_headings'] as $index => $head ) {
					// Header text prepview editing.
					$repeater_heading_text = $this->get_repeater_setting_key( 'heading_text', 'table_headings', $inline_count );
					$this->add_render_attribute( $repeater_heading_text, 'class', 'uael-table__text-inner' );
					// TH.
					if ( true === $first_row_h ) {
						$this->add_render_attribute( 'current_' . $head['_id'], 'data-sort', $cell_counter_h );
					}
					$this->add_render_attribute( 'current_' . $head['_id'], 'class', 'sort-this' );
					$this->add_render_attribute( 'current_' . $head['_id'], 'class', 'elementor-repeater-item-' . $head['_id'] );

					$this->add_render_attribute( 'current_' . $head['_id'], 'class', 'uael-table-col' );

					if ( 'yes' === $head['show_head_id_class'] ) {
						$this->add_render_attribute( 'current_' . $head['_id'], 'id', $head['table_head_cell_id'] );
						$this->add_render_attribute( 'current_' . $head['_id'], 'class', $head['table_head_cell_class'] );
					}

					$this->add_render_attribute( 'current_' . $head['_id'], 'class', 'uael-table-head-cell-text' );

					if ( 1 < $head['heading_col_span'] ) {
						$this->add_render_attribute( 'current_' . $head['_id'], 'colspan', $head['heading_col_span'] );
					}
					if ( 1 < $head['heading_row_span'] ) {
						$this->add_render_attribute( 'current_' . $head['_id'], 'rowspan', $head['heading_row_span'] );
					}
					// Sort Icon.
					if ( 'yes' === $settings['sortable'] && true === $first_row_h ) {
						$this->add_render_attribute( 'icon_sort_' . $head['_id'], 'class', 'uael-sort-icon' );
					}
					if ( ! empty( $head['head_image']['url'] ) ) {
						$this->add_render_attribute( 'uael_head_col_img' . $head['_id'], 'src', $head['head_image']['url'] );
						$this->add_render_attribute( 'uael_head_col_img' . $head['_id'], 'class', 'uael-col-img--' . $settings['all_image_align'] );
						$this->add_render_attribute( 'uael_head_col_img' . $head['_id'], 'title', get_the_title( $head['head_image']['id'] ) );
						$this->add_render_attribute( 'uael_head_col_img' . $head['_id'], 'alt', Control_Media::get_image_alt( $head['head_image'] ) );
					}
					// ICON.
					$this->add_render_attribute( 'uael_heading_icon_align' . $head['_id'], 'class', 'uael-align-icon--' . $settings['all_icon_align'] );

					if ( 'cell' === $head['header_content_type'] ) {
						?>
						<th <?php echo wp_kses_post( $this->get_render_attribute_string( 'current_' . esc_attr( $head['_id'] ) ) ); ?> scope="col">
							<span class="sort-style">
							<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table__text' ) ); ?>>
								<?php if ( 'icon' === $head['header_content_icon_image'] && $head['new_heading_icon']['value'] ) { ?>
									<?php if ( 'left' === $settings['all_icon_align'] ) { ?>
										<?php $this->render_heading_icon( $head ); ?>
									<?php } ?>
								<?php } else { ?>
										<?php if ( ! empty( $head['head_image']['url'] ) ) { ?>
											<?php if ( 'left' === $settings['all_image_align'] ) { ?>
											<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_head_col_img' . esc_attr( $head['_id'] ) ) ); ?>>
										<?php } ?>
										<?php } ?>
								<?php } ?>
								<span <?php echo wp_kses_post( $this->get_render_attribute_string( $repeater_heading_text ) ); ?>><?php echo wp_kses_post( $head['heading_text'] ); ?></span>

								<?php if ( 'icon' === $head['header_content_icon_image'] && $head['new_heading_icon']['value'] ) { ?>

									<?php if ( 'right' === $settings['all_icon_align'] ) { ?>
										<?php $this->render_heading_icon( $head ); ?>
									<?php } ?>

								<?php } else { ?>
										<?php if ( ! empty( $head['head_image']['url'] ) ) { ?>
											<?php if ( 'right' === $settings['all_image_align'] ) { ?>
											<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_head_col_img' . esc_attr( $head['_id'] ) ) ); ?>>
										<?php } ?>
										<?php } ?>
								<?php } ?>
							</span>
							<?php if ( 'yes' === $settings['sortable'] && true === $first_row_h ) { ?>
								<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon_sort_' . esc_attr( $head['_id'] ) ) ); ?>></span>
							<?php } ?>
							</span>
						</th>
						<?php
						$header_text[ $cell_counter_h ] = $head['heading_text'];
						$cell_counter_h++;
					} else {
						if ( $counter_h > 1 && $counter_h < $row_count_h ) {
							// Break into new row.
							?>
							</tr><tr <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table_row' ) ); ?>>
							<?php
							$first_row_h = false;
						} elseif ( 1 === $counter_h && false === $this->is_invalid_first_row() ) {
							?>
							<tr <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table_row' ) ); ?>>
							<?php
						}
						$cell_counter_h = 0;
					}
					$counter_h++;
					$inline_count++;
				}
			}
			?>
		</thead>
		<?php } ?>
		<tbody>
			<!-- ROWS -->
			<?php
			$counter           = 1;
			$cell_counter      = 0;
			$cell_inline_count = 0;
			$row_count         = count( $settings['table_content'] );
			if ( $settings['table_content'] ) {
				foreach ( $settings['table_content'] as $index => $row ) {
					// Cell text inline classes.
					$repeater_cell_text = $this->get_repeater_setting_key( 'cell_text', 'table_content', $cell_inline_count );
					$this->add_render_attribute( $repeater_cell_text, 'class', 'uael-table__text-inner' );
					$this->add_render_attribute( 'uael_cell_icon_align' . $row['_id'], 'class', 'uael-align-icon--' . $settings['all_icon_align'] );
					$this->add_render_attribute( 'uael_table_col' . $row['_id'], 'class', 'uael-table-col' );

					if ( 'yes' === $row['show_content_id_class'] ) {
						$this->add_render_attribute( 'uael_table_col' . $row['_id'], 'id', $row['table_content_cell_id'] );
						$this->add_render_attribute( 'uael_table_col' . $row['_id'], 'class', $row['table_content_cell_class'] );
					}

					$this->add_render_attribute( 'uael_table_col' . $row['_id'], 'class', 'uael-table-body-cell-text' );

					$this->add_render_attribute( 'uael_table_col' . $row['_id'], 'class', 'elementor-repeater-item-' . $row['_id'] );
					if ( 1 < $row['cell_span'] ) {
						$this->add_render_attribute( 'uael_table_col' . $row['_id'], 'colspan', $row['cell_span'] );
					}
					if ( 1 < $row['cell_row_span'] ) {
						$this->add_render_attribute( 'uael_table_col' . $row['_id'], 'rowspan', $row['cell_row_span'] );
					}
					if ( ! empty( $row['image']['url'] ) ) {
						$this->add_render_attribute( 'uael_col_img' . $row['_id'], 'src', $row['image']['url'] );
						$this->add_render_attribute( 'uael_col_img' . $row['_id'], 'class', 'uael-col-img--' . $settings['all_image_align'] );
						$this->add_render_attribute( 'uael_col_img' . $row['_id'], 'title', get_the_title( $row['image']['id'] ) );
						$this->add_render_attribute( 'uael_col_img' . $row['_id'], 'alt', Control_Media::get_image_alt( $row['image'] ) );
					}
					if ( ! empty( $row['link']['url'] ) ) {
						$this->add_link_attributes( 'col-link-' . $row['_id'], $row['link'] );
					}

					if ( 'cell' === $row['content_type'] ) {
						// Fetch corresponding header cell text.
						if ( isset( $header_text[ $cell_counter ] ) && $header_text[ $cell_counter ] ) {
							$this->add_render_attribute( 'uael_table_col' . $row['_id'], 'data-title', $header_text[ $cell_counter ] );
						}

						?>
						<<?php echo esc_attr( $row['table_th_td'] ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table_col' . esc_attr( $row['_id'] ) ) ); ?>>
							<?php if ( ! empty( $row['link']['url'] ) ) { ?>
							<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'col-link-' . esc_attr( $row['_id'] ) ) ); ?>>
							<?php } ?>
								<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table__text' ) ); ?>>
									<?php if ( 'icon' === $row['cell_content_icon_image'] && $row['new_cell_icon']['value'] ) { ?>
										<?php if ( 'left' === $settings['all_icon_align'] ) { ?>
											<?php $this->render_row_icon( $row ); ?>
										<?php } ?>
									<?php } else { ?>
										<?php if ( ! empty( $row['image']['url'] ) ) { ?>
											<?php if ( 'left' === $settings['all_image_align'] ) { ?>
											<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_col_img' . esc_attr( $row['_id'] ) ) ); ?>>
										<?php } ?>
										<?php } ?>
									<?php } ?>
									<span <?php echo wp_kses_post( $this->get_render_attribute_string( $repeater_cell_text ) ); ?>><?php echo $row['cell_text'];// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									<?php if ( 'icon' === $row['cell_content_icon_image'] && $row['new_cell_icon']['value'] ) { ?>
										<?php if ( 'right' === $settings['all_icon_align'] ) { ?>
											<?php $this->render_row_icon( $row ); ?>
										<?php } ?>
									<?php } else { ?>
										<?php if ( ! empty( $row['image']['url'] ) ) { ?>
											<?php if ( 'right' === $settings['all_image_align'] ) { ?>
											<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_col_img' . esc_attr( $row['_id'] ) ) ); ?>>
										<?php } ?>
										<?php } ?>
									<?php } ?>
								</span>
							<?php if ( ! empty( $row['link']['url'] ) ) { ?>
							</a>
							<?php } ?>
						</td>
							<?php
							// Increment to next cell.
							$cell_counter++;
					} else {
						if ( $counter > 1 && $counter < $row_count ) {
							// Break into new row.
							++$data_entry;
							?>
							</tr><tr data-entry="<?php echo esc_attr( $data_entry ); ?>" <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table_row' ) ); ?>>
												<?php
						} elseif ( 1 === $counter && false === $this->is_invalid_first_row() ) {
							$data_entry = 1;
							?>
							<tr data-entry="<?php echo esc_attr( $data_entry ); ?>" <?php echo wp_kses_post( $this->get_render_attribute_string( 'uael_table_row' ) ); ?>>
											<?php
						}
						$cell_counter = 0;
					}
					$counter++;
					$cell_inline_count++;
				}
			}
			?>
		</tbody>
	</table>
		<?php
	}
	?>
</div>
<?php
