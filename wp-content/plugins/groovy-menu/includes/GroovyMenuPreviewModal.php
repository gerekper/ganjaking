<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * @return string
 */
function GroovyMenuPreviewModal() {
	$lang            = [ ];
	$lang['Preview'] = esc_html__( 'Preview', 'groovy-menu' );
	$lang['Default'] = esc_html__( 'Default', 'groovy-menu' );
	$lang['Sticky']  = esc_html__( 'Sticky', 'groovy-menu' );

	$html = <<<HTML
	
<!-- Global settings modal -->
<div class="gm-modal gm-fade modal-fullscreen" id="preview-modal" tabindex="-1">
	<div class="gm-modal-dialog modal-lg">
		<div class="gm-modal-content">
			<div class="gm-modal-body iframe--size-desktop">
				<div class="preview-size-change">
					<div class="modal-info">
						<span class="modal-title">{$lang['Preview']}</span>
						<span class="modal-preview-name"></span>
					</div>

					<div class="preview-size-change__tabs">
						<a href="#" data-size="desktop" class="active">
							<svg version="1.1" class="svg-preview-desktop" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="27px" height="23px"
		 viewBox="0 0 27 23" style="enable-background:new 0 0 27 23;" xml:space="preserve">
								<g>
									<path d="M26.1,0.6c-0.4-0.4-1-0.6-1.6-0.6H2.4C1.8,0,1.3,0.2,0.9,0.6c-0.4,0.4-0.6,1-0.6,1.6v15c0,0.6,0.2,1.1,0.6,1.6
										c0.4,0.4,1,0.6,1.6,0.6H10c0,0.4-0.1,0.7-0.2,1.1c-0.1,0.4-0.3,0.7-0.4,1c-0.1,0.3-0.2,0.5-0.2,0.6c0,0.2,0.1,0.4,0.3,0.6
										C9.5,22.9,9.7,23,10,23H17c0.2,0,0.4-0.1,0.6-0.3c0.2-0.2,0.3-0.4,0.3-0.6c0-0.1-0.1-0.3-0.2-0.6c-0.1-0.3-0.3-0.6-0.4-1
										c-0.1-0.4-0.2-0.7-0.2-1.1h7.5c0.6,0,1.1-0.2,1.6-0.6c0.4-0.4,0.6-1,0.6-1.6v-15C26.8,1.6,26.6,1.1,26.1,0.6z M25,13.7
										c0,0.1,0,0.2-0.1,0.3c-0.1,0.1-0.2,0.1-0.3,0.1H2.4c-0.1,0-0.2,0-0.3-0.1C2,13.9,2,13.8,2,13.7V2.2C2,2.1,2,2,2.1,1.9
										c0.1-0.1,0.2-0.1,0.3-0.1h22.1c0.1,0,0.2,0,0.3,0.1C25,2,25,2.1,25,2.2V13.7L25,13.7z"/>
								</g>
							</svg>
						</a>
						<a href="#" data-size="tablet">
							<svg version="1.1" class="svg-preview-mobile" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="27px"
							 viewBox="0 0 16 27" style="enable-background:new 0 0 16 27;" xml:space="preserve">
						<path d="M13.2,0.2H2.8C1.2,0.2,0,1.4,0,2.9v0.3v18.8v2.1c0,1.5,1.2,2.8,2.8,2.8h10.5c1.5,0,2.8-1.2,2.8-2.8v-2.1V3.2V2.9
							C16,1.4,14.8,0.2,13.2,0.2z M8,25.4c-0.7,0-1.3-0.6-1.3-1.3c0-0.7,0.6-1.3,1.3-1.3c0.7,0,1.3,0.6,1.3,1.3C9.3,24.8,8.7,25.4,8,25.4z
							 M14.7,21.3H1.3V3.9h13.3V21.3z"/>
							</svg>
						</a>
					</div>

					<div class="preview-color-change__tabs">
						<a href="#" data-color="transparent" class="active">
							<span class="preview-color-placeholder"></span>
						</a>
						<a href="#" data-color="black">
							<span class="preview-color-placeholder"></span>
						</a>
						<a href="#" data-color="gray">
							<span class="preview-color-placeholder"></span>
						</a>
						<a href="#" data-color="white">
							<span class="preview-color-placeholder"></span>
						</a>
					</div>


					<div class="preview-sticky-change__tabs">
						<a href="#" data-sticky="false" class="active">{$lang['Default']}</a>
						<a href="#" data-sticky="true">{$lang['Sticky']}</a>
					</div>
					<span class="close" data-dismiss="gm-modal"></span>
				</div>
				<div class="modal-body-iframe"></div>
			</div>
		</div>
	</div>
</div>
HTML;

	return $html;

}
