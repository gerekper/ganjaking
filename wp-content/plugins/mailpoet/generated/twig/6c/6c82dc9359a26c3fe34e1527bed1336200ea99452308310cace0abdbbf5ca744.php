<?php

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
use MailPoetVendor\Twig\Extension\SandboxExtension;
use MailPoetVendor\Twig\Markup;
use MailPoetVendor\Twig\Sandbox\SecurityError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedTagError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFilterError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFunctionError;
use MailPoetVendor\Twig\Source;
use MailPoetVendor\Twig\Template;

/* newsletter/templates/blocks/coupon/settings.hbs */
class __TwigTemplate_242c7d80a2f2d73a801daf666a04cd25b175c9bb43d4ac8139c307f90cb229e5 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<h3 data-automation-id=\"coupon_settings_heading\">";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Coupon");
        echo "</h3>
<div class=\"mailpoet_form_field\">
  <div class=\"mailpoet_form_field_radio_option\">
    <label>
      <input type=\"radio\" name=\"source\" class=\"mailpoet_field_coupon_source\" value=\"createNew\" {{#ifCond model.source '!==' 'useExisting'}}CHECKED{{/ifCond}}/>
      ";
        // line 6
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Create new");
        echo "
    </label>
  </div>
  <div class=\"mailpoet_form_field_radio_option\">
    <label>
      <input type=\"radio\" name=\"source\" class=\"mailpoet_field_coupon_source\" value=\"useExisting\" {{#ifCond model.source '===' 'useExisting'}}CHECKED{{/ifCond}}/>
      ";
        // line 12
        echo $this->extensions['MailPoet\Twig\I18n']->translateWithContext("Use existing");
        echo "
    </label>
  </div>
</div>

<div class=\"mailpoet_field_coupon_source_create_new {{#ifCond model.source '===' 'useExisting'}}mailpoet_hidden{{/ifCond}}\">
  <details open>
    <summary>
      <strong> ";
        // line 20
        echo $this->extensions['MailPoet\Twig\I18n']->translate("General");
        echo " </strong>
    </summary>
    <div className=\"content\">
      <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_type\">";
        // line 24
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Discount type");
        echo "</div>
        <div class=\"mailpoet_form_field_input_option mailpoet_form_field_input_nowrap\">
          <select id=\"mailpoet_field_coupon_discount_type\" name=\"discount-type\" class=\"mailpoet_select mailpoet_select_full mailpoet_field_coupon_discount_type\">
            {{#each availableDiscountTypes}}
              <option value=\"{{@key}}\" {{#ifCond @key '==' ../model.discountType}}SELECTED{{/ifCond}}>{{ this }}</option>
            {{/each}}
          </select>
        </div>
      </div>

      <div class=\"mailpoet_form_field\">
        <label>
          <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 36
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Coupon amount");
        echo "</div>
          <div class=\"coupon_amount_wrapper\">
            {{#ifCond model.amountMax '===' 100}}
              <span class=\"amount_percentage_sign\">%</span>
            {{/ifCond}}
            <input type=\"text\" name=\"amount\"  data-parsley-validate data-parsley-validation-threshold=\"0\" data-parsley-required data-parsley-trigger=\"input\"  data-parsley-type=\"digits\" {{#ifCond model.amountMax '!==' null }} data-parsley-maxlength=\"{{ model.amountMax }}\" max=\"{{ model.amountMax }}\" {{/ifCond}}   class=\"mailpoet_input mailpoet_field_coupon_amount mailpoet_input_medium\" value=\"{{ model.amount }}\" min=\"0\" />
          </div>
        </label>
      </div>

      <div class=\"mailpoet_form_field\">
        <label>
          <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 48
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Coupon expiry days");
        echo "</div>
          <div class=\"mailpoet_form_field_input_option\">
            <input type=\"text\" data-parsley-validate data-parsley-required data-parsley-validation-threshold=\"0\"  name=\"expiry-day\" data-parsley-trigger=\"input\"  data-parsley-type=\"digits\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_coupon_expiry_day\" value=\"{{ model.expiryDay }}\" />
            <p class='description'>";
        // line 51
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Number of days the coupon is valid after the email is sent");
        echo "</p>
          </div>
        </label>
      </div>

      <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_checkbox_option\">
          <label>
            <input type=\"checkbox\" name=\"free-shipping\" class=\"mailpoet_field_coupon_free_shipping\"  {{#ifCond model.freeShipping '===' true }}CHECKED{{/ifCond}}/>
            ";
        // line 60
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Allow free shipping");
        echo "
          </label>
        </div>
      </div>
    </div>
  </details>

  <details>
    <summary>
      <strong> ";
        // line 69
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Usage restriction");
        echo " </strong>
    </summary>
    <div className=\"content\">

      <div class=\"mailpoet_form_field\">
        <label>
          <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 75
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Minimum spend");
        echo "</div>
          <div class=\"mailpoet_form_field_input_option\">
            <input type=\"text\" name=\"minimum-amount\" class=\"mailpoet_input mailpoet_field_coupon_minimum_amount\" value=\"{{ model.minimumAmount }}\" placeholder=\"";
        // line 77
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("No minimum"), "html_attr");
        echo "\" />
            <span class=\"mailpoet_error mailpoet_hidden\"> {{ minAndMaxAmountFieldsErrorMessage }} </span>
          </div>
        </label>
      </div>

      <div class=\"mailpoet_form_field\">
        <label>
          <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 85
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Maximum spend");
        echo "</div>
          <div class=\"mailpoet_form_field_input_option\">
            <input type=\"text\" name=\"maximum-amount\" class=\"mailpoet_input mailpoet_field_coupon_maximum_amount\" value=\"{{ model.maximumAmount }}\" placeholder=\"";
        // line 87
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("No Maximum"), "html_attr");
        echo "\" />
            <span class=\"mailpoet_error mailpoet_hidden\"> {{ minAndMaxAmountFieldsErrorMessage }} </span>
          </div>
        </label>
      </div>

      <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_checkbox_option\">
          <label>
            <input type=\"checkbox\" name=\"individual-use\" class=\"mailpoet_field_coupon_individual_use\"  {{#ifCond model.individualUse '===' true }}CHECKED{{/ifCond}}/>
            ";
        // line 97
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Individual use only");
        echo "
          </label>
        </div>
      </div>

      <div class=\"mailpoet_form_field\">
        <div class=\"mailpoet_form_field_checkbox_option\">
          <label>
            <input type=\"checkbox\" name=\"exclude-sale-items\" class=\"mailpoet_field_coupon_exclude_sale_items\"  {{#ifCond model.excludeSaleItems '===' true }}CHECKED{{/ifCond}}/>
            ";
        // line 106
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Exclude sale items");
        echo "
          </label>
        </div>
      </div>

      <hr />
      <div class=\"mailpoet_product_selection_filter_row\">
        <label for=\"mailpoet_field_coupon_product_ids\">
          ";
        // line 114
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Products");
        echo "
        </label>
        <select id=\"mailpoet_field_coupon_product_ids\" class=\"mailpoet_select\" data-placeholder=\"";
        // line 116
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Search for a product…"), "html_attr");
        echo "\" multiple=\"multiple\">
          {{#each model.productIds}}
            <option value=\"{{ id }}\" selected=\"selected\">{{ text }}</option>
          {{/each}}
        </select>
      </div>

      <div class=\"mailpoet_product_selection_filter_row\">
        <label for=\"mailpoet_field_coupon_excluded_product_ids\">
          ";
        // line 125
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Exclude products");
        echo "
        </label>
        <select id=\"mailpoet_field_coupon_excluded_product_ids\" class=\"mailpoet_select\" data-placeholder=\"";
        // line 127
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Search for a product…"), "html_attr");
        echo "\" multiple=\"multiple\">
          {{#each model.excludedProductIds}}
            <option value=\"{{ id }}\" selected=\"selected\">{{ text }}</option>
          {{/each}}
        </select>
      </div>

      <hr />
      <div class=\"mailpoet_product_selection_filter_row\">
        <label for=\"mailpoet_field_coupon_product_categories\">
          ";
        // line 137
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Product categories");
        echo "
        </label>
        <select id=\"mailpoet_field_coupon_product_categories\" class=\"mailpoet_select\" data-placeholder=\"";
        // line 139
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Any category"), "html_attr");
        echo "\" multiple=\"multiple\">
          {{#each model.productCategories}}
            <option value=\"{{ id }}\" selected=\"selected\">{{ text }}</option>
          {{/each}}
        </select>
      </div>

      <div class=\"mailpoet_product_selection_filter_row\">
        <label for=\"mailpoet_field_coupon_excluded_product_categories\">
          ";
        // line 148
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Exclude categories");
        echo "
        </label>
        <select id=\"mailpoet_field_coupon_excluded_product_categories\" class=\"mailpoet_select\" data-placeholder=\"";
        // line 150
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("No categories"), "html_attr");
        echo "\" multiple=\"multiple\">
          {{#each model.excludedProductCategories}}
            <option value=\"{{ id }}\" selected=\"selected\">{{ text }}</option>
          {{/each}}
        </select>
      </div>

      <hr />
      <div class=\"mailpoet_form_field\">
        <label>
          <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 160
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Allowed emails");
        echo "</div>
          <div class=\"mailpoet_form_field_input_option\">
            <input type=\"email\" name=\"email-restrictions\" class=\"mailpoet_input mailpoet_field_coupon_email_restrictions\" value=\"{{ model.emailRestrictions }}\" placeholder=\"";
        // line 162
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("No restrictions"), "html_attr");
        echo "\" multiple=\"multiple\" />
            <span class=\"mailpoet_error mailpoet_hidden\"> </span>
          </div>
        </label>
      </div>

    </div>
  </details>

  <details>
    <summary>
      <strong> ";
        // line 173
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Usage limits");
        echo " </strong>
    </summary>
    <div className=\"content\">

      <div class=\"mailpoet_form_field\">
        <label>
          <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 179
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Usage limit per coupon");
        echo "</div>
          <div class=\"mailpoet_form_field_input_option\">
            <input type=\"number\" name=\"usage-limit\" class=\"mailpoet_input mailpoet_field_coupon_usage_limit\" value=\"{{ model.usageLimit }}\" placeholder=\"";
        // line 181
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Unlimited usage"), "html_attr");
        echo "\" step=\"1\" min=\"0\" />
          </div>
        </label>
      </div>

      <div class=\"mailpoet_form_field\">
        <label>
          <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 188
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Usage limit per user");
        echo "</div>
          <div class=\"mailpoet_form_field_input_option\">
            <input type=\"number\" name=\"usage-limit-per-user\" class=\"mailpoet_input mailpoet_field_coupon_usage_limit_per_user\" value=\"{{ model.usageLimitPerUser }}\" placeholder=\"";
        // line 190
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Unlimited usage"), "html_attr");
        echo "\" step=\"1\" min=\"0\" />
          </div>
        </label>
      </div>

    </div>
  </details>
</div>

<div class=\"mailpoet_form_field mailpoet_field_coupon_source_use_existing {{#ifCond model.source '!==' 'useExisting'}}mailpoet_hidden{{/ifCond}}\">
  <div class=\"mailpoet_form_field_type\">";
        // line 200
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Existing coupon");
        echo "</div>
  <div class=\"mailpoet_form_field_select_option mailpoet_form_field_input_nowrap\">
    <select name=\"existing_coupon\" class=\"mailpoet_select mailpoet_select_large mailpoet_field_coupon_existing_coupon\">
      {{#each availableCoupons}}
        <option value=\"{{ id }}\" {{#ifCond ../model.couponId '==' id}}selected=\"selected\"{{/ifCond}}>{{ text }}</option>
      {{/each}}
    </select>
  </div>
</div>

<hr />
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_title\">";
        // line 212
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Alignment");
        echo "</div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
        <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_coupon_alignment\" value=\"left\" {{#ifCond model.styles.block.textAlign '===' 'left'}}CHECKED{{/ifCond}}/>
        ";
        // line 216
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Left");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_coupon_alignment\" value=\"center\" {{#ifCond model.styles.block.textAlign '===' 'center'}}CHECKED{{/ifCond}}/>
            ";
        // line 222
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Center");
        echo "
        </label>
    </div>
    <div class=\"mailpoet_form_field_radio_option\">
        <label>
            <input type=\"radio\" name=\"alignment\" class=\"mailpoet_field_coupon_alignment\" value=\"right\" {{#ifCond model.styles.block.textAlign '===' 'right'}}CHECKED{{/ifCond}}/>
            ";
        // line 228
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Right");
        echo "
        </label>
    </div>
</div>
<div class=\"mailpoet_form_field mailpoet_form_narrow_select2\">
    <div class=\"mailpoet_form_field_title\">";
        // line 233
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Text");
        echo "</div>
    <div class=\"mailpoet_form_field_input_option mailpoet_form_field_input_nowrap\">
        <input type=\"text\" name=\"font-color\" id=\"mailpoet_field_coupon_font_color\" class=\"mailpoet_field_coupon_font_color mailpoet_color\" value=\"{{ model.styles.block.fontColor }}\" />
        <select id=\"mailpoet_field_coupon_font_family\" name=\"font-family\" class=\"mailpoet_select mailpoet_select_medium mailpoet_field_coupon_font_family mailpoet_font_family\">
        <optgroup label=\"";
        // line 237
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Standard fonts");
        echo "\">
        {{#each availableStyles.fonts.standard}}
            <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.block.fontFamily}}SELECTED{{/ifCond}}>{{ this }}</option>
        {{/each}}
        </optgroup>
        <optgroup label=\"";
        // line 242
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Custom fonts");
        echo "\">
        {{#each availableStyles.fonts.custom}}
            <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.block.fontFamily}}SELECTED{{/ifCond}}>{{ this }}</option>
        {{/each}}
        </optgroup>
        </select>
        <select id=\"mailpoet_field_coupon_font_size\" name=\"font-size\" class=\"mailpoet_select mailpoet_select_small mailpoet_field_coupon_font_size mailpoet_font_size\">
        {{#each availableStyles.headingSizes}}
            <option value=\"{{ this }}\" {{#ifCond this '==' ../model.styles.block.fontSize}}SELECTED{{/ifCond}}>{{ this }}</option>
        {{/each}}
        </select>
    </div>
</div>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_checkbox_option\">
        <label>
            <input type=\"checkbox\" name=\"fontWeight\" class=\"mailpoet_field_coupon_font_weight\" value=\"bold\" {{#ifCond model.styles.block.fontWeight '===' 'bold'}}CHECKED{{/ifCond}}/>
            ";
        // line 259
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Bold");
        echo "
        </label>
    </div>
</div>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"text\" name=\"background-color\" class=\"mailpoet_field_coupon_background_color mailpoet_color\" value=\"{{ model.styles.block.backgroundColor }}\" />
    </div>
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 267
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Background");
        echo "</div>
</div>
<div class=\"mailpoet_form_field\">
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"text\" name=\"border-color\" class=\"mailpoet_field_coupon_border_color mailpoet_color\" value=\"{{ model.styles.block.borderColor }}\" />
    </div>
    <div class=\"mailpoet_form_field_title mailpoet_form_field_title_inline\">";
        // line 273
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Border");
        echo "</div>
    <div class=\"mailpoet_form_field_input_option\">
        <input type=\"number\" name=\"border-width-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_coupon_border_width_input\" value=\"{{getNumber model.styles.block.borderWidth}}\" min=\"0\" max=\"10\" step=\"1\" /> px
        <input type=\"range\" min=\"0\" max=\"10\" step=\"1\" name=\"border-width\" class=\"mailpoet_range mailpoet_range_small mailpoet_field_coupon_border_width\" value=\"{{getNumber model.styles.block.borderWidth}}\" />
    </div>
</div>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title\">";
        // line 281
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Rounded corners");
        echo "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"number\" name=\"border-radius-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_coupon_border_radius_input\" value=\"{{getNumber model.styles.block.borderRadius}}\" min=\"0\" max=\"40\" step=\"1\" /> px
            <input type=\"range\" min=\"0\" max=\"40\" step=\"1\" name=\"border-radius\" class=\"mailpoet_range mailpoet_range_medium mailpoet_field_coupon_border_radius\" value=\"{{getNumber model.styles.block.borderRadius }}\" />
        </div>
    </label>
</div>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title\">";
        // line 290
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Width");
        echo "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"number\" name=\"width-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_coupon_width_input\" value=\"{{getNumber model.styles.block.width}}\" min=\"50\" max=\"288\" step=\"1\" /> px
            <input type=\"range\" min=\"50\" max=\"288\" step=\"1\" name=\"width\" class=\"mailpoet_range mailpoet_range_medium mailpoet_field_coupon_width\" value=\"{{getNumber model.styles.block.width }}\" />
        </div>
    </label>
</div>
<div class=\"mailpoet_form_field\">
    <label>
        <div class=\"mailpoet_form_field_title\">";
        // line 299
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Height");
        echo "</div>
        <div class=\"mailpoet_form_field_input_option\">
            <input type=\"number\" name=\"line-height-input\" class=\"mailpoet_input mailpoet_input_small mailpoet_field_coupon_line_height_input\" value=\"{{getNumber model.styles.block.lineHeight}}\" min=\"20\" max=\"50\" step=\"1\" /> px
            <input type=\"range\" min=\"20\" max=\"50\" step=\"1\" name=\"line-height\" class=\"mailpoet_range mailpoet_range_medium mailpoet_field_coupon_line_height\" value=\"{{getNumber model.styles.block.lineHeight }}\" />
        </div>
    </label>
</div>
{{#ifCond renderOptions.hideApplyToAll '!==' true}}
<div class=\"mailpoet_form_field\">
    <input type=\"button\" name=\"replace-all-coupon-styles\" class=\"button button-secondary mailpoet_coupon_full mailpoet_field_coupon_replace_all_styles\" value=\"";
        // line 308
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Apply styles to all coupons"), "html_attr");
        echo "\" />
</div>
{{/ifCond}}

<div class=\"mailpoet_form_field\">
    <input type=\"button\" class=\"button button-primary mailpoet_done_editing\" data-automation-id=\"coupon_done_button\" value=\"";
        // line 313
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Done"), "html_attr");
        echo "\" />
</div>

<script type=\"text/javascript\">
    fontsSelect('#mailpoet_field_coupon_font_family');
</script>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/coupon/settings.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  488 => 313,  480 => 308,  468 => 299,  456 => 290,  444 => 281,  433 => 273,  424 => 267,  413 => 259,  393 => 242,  385 => 237,  378 => 233,  370 => 228,  361 => 222,  352 => 216,  345 => 212,  330 => 200,  317 => 190,  312 => 188,  302 => 181,  297 => 179,  288 => 173,  274 => 162,  269 => 160,  256 => 150,  251 => 148,  239 => 139,  234 => 137,  221 => 127,  216 => 125,  204 => 116,  199 => 114,  188 => 106,  176 => 97,  163 => 87,  158 => 85,  147 => 77,  142 => 75,  133 => 69,  121 => 60,  109 => 51,  103 => 48,  88 => 36,  73 => 24,  66 => 20,  55 => 12,  46 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/coupon/settings.hbs", "/home/circleci/mailpoet/mailpoet/views/newsletter/templates/blocks/coupon/settings.hbs");
    }
}
