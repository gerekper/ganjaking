<?php

namespace MailOptin\Core\Admin\Customizer;

class UpsellCustomizerSection extends \WP_Customize_Section {

    public $type = 'mo-upsell-section';
    public $pro_url = '';
    public $pro_text = '';
    public $id = '';

    public function json() {
        $json = parent::json();
        $json['pro_text'] = $this->pro_text;
        $json['pro_url']  = esc_url( $this->pro_url );
        $json['id'] = $this->id;
        return $json;
    }

    protected function render_template() {
        ?>
        <li id="accordion-section-{{ data.id }}" class="mailoptin-upsell-accordion-section control-section-{{ data.type }} cannot-expand accordion-section">
            <h3><a href="{{{ data.pro_url }}}" target="_blank">{{ data.pro_text }}</a></h3>
        </li>
        <?php
    }
}