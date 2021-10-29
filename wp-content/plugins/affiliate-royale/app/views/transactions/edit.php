<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="wrap">
  <?php
  WafpAppHelper::plugin_title(__('Edit Transaction','affiliate-royale', 'easy-affiliate'));
  require(WAFP_VIEWS_PATH . "/shared/errors.php");
  ?>

  <div class="form-wrap">
    <form action="" method="post">
      <?php if(isset($txn) and $txn->id > 0): ?>
        <input type="hidden" name="id" value="<?php echo $txn->id; ?>" />
      <?php endif; ?>
      <input type="hidden" name="action" value="update" />
      <table class="form-table">
        <tbody>
          <tr valign="top"><th scope="row"><label><?php _e('Transaction ID:', 'affiliate-royale', 'easy-affiliate'); ?></label></th><td><?php echo $txn->id; ?></td></tr>
          <tr valign="top"><th scope="row"><label><?php _e('Created:', 'affiliate-royale', 'easy-affiliate'); ?></label></th><td><?php echo WafpAppHelper::format_date($txn->created_at); ?></td></tr>
          <?php require(WAFP_VIEWS_PATH . "/transactions/_form.php"); ?>
        </tbody>
      </table>
      <div class="wafp-commissions">
        <h3><?php _e('Edit Commission Payouts', 'affiliate-royale', 'easy-affiliate'); ?></h3>
        <?php
          foreach($commissions as $commish):
            $aff = new WafpUser($commish->affiliate_id);
            ?>
            <div id="wafp-commissions-<?php echo $commish->id; ?>" class="wafp-commissions-table postbox">
              <?php
                if($commish->payment_id > 0):
                  $payment = WafpPayment::get_one($commish->payment_id)
                ?>
                  <div class="wafp-commissions-paid"><?php printf(__('Payed on %s','affiliate-royale', 'easy-affiliate'), $payment->created_at); ?></div>
              <?php
                else:
              ?>
                  <div class="wafp-commissions-delete"><a href="" data-id="<?php echo $commish->id; ?>"><i class="ar-icon-cancel-circled ar-16"> </i></a></div>
              <?php
                endif;
              ?>
              <table>
                <tr>
                  <td width="150px"><label for="commissions[<?php echo $commish->id; ?>][commission_level]"><?php _e('Commission Level', 'affiliate-royale', 'easy-affiliate'); ?></label></td>
                  <?php if( $commish->payment_id <= 0 ): ?>
                    <td><input type="text" name="commissions[<?php echo $commish->id; ?>][commission_level]" class="regular-text" value="<?php echo $commish->commission_level+1; ?>" style="width: 30px;"/></td>
                  <?php else: ?>
                    <td><input type="hidden" name="commissions[<?php echo $commish->id; ?>][commission_level]" value="<?php echo $commish->commission_level+1; ?>"/><?php echo $commish->commission_level+1; ?></td>
                  <?php endif; ?>
                </tr>
                <tr>
                  <td><label for="commissions[<?php echo $commish->id; ?>][referrer]"><?php _e('Affiliate', 'affiliate-royale', 'easy-affiliate'); ?></label></td>
                  <?php if( $commish->commission_level < 1 or $commish->payment_id > 0 ): ?>
                    <td><input type="hidden" name="commissions[<?php echo $commish->id; ?>][referrer]" value="<?php echo $aff->get_field('user_login'); ?>" /><?php echo $aff->get_field('user_login'); ?></td>
                  <?php else: ?>
                    <td><input type="text" name="commissions[<?php echo $commish->id; ?>][referrer]" class="regular-text wafp-affiliate-referrer" value="<?php echo $aff->get_field('user_login'); ?>" /></td>
                  <?php endif; ?>
                </tr>
                <tr>
                  <td><label for="commissions[<?php echo $commish->id; ?>][commission_type]"><?php _e('Commission Type', 'affiliate-royale', 'easy-affiliate'); ?></label></td>
                  <?php if( $commish->payment_id <= 0 ): ?>
                    <td>
                      <select name="commissions[<?php echo $commish->id; ?>][commission_type]" data-id="commissions-<?php echo $commish->id; ?>" class="wafp_multi_commission_type">
                        <option value="percentage"<?php selected('percentage',$commish->commission_type); ?>><?php _e("Percentages", 'affiliate-royale', 'easy-affiliate'); ?></option>
                        <option value="fixed"<?php selected('fixed',$commish->commission_type); ?>><?php _e("Fixed Amounts", 'affiliate-royale', 'easy-affiliate'); ?></option>
                      </select>
                    </td>
                  <?php else: ?>
                    <td><input type="hidden" name="commissions[<?php echo $commish->id; ?>][commission_type]" value="<?php echo $commish->commission_type; ?>" class="wafp_multi_commission_type" /><?php echo $commish->commission_type; ?></td>
                  <?php endif; ?>
                </tr>
                <tr>
                  <td><label for="commissions[<?php echo $commish->id; ?>][commission_percentage]"><?php _e('Commissions', 'affiliate-royale', 'easy-affiliate'); ?></label></td>
                  <?php if( $commish->payment_id <= 0 ): ?>
                    <td><span id="commissions-<?php echo $commish->id; ?>-currency-symbol"><?php echo $wafp_options->currency_symbol; ?></span><input type="text" name="commissions[<?php echo $commish->id; ?>][commission_percentage]" value="<?php echo $commish->commission_percentage; ?>" style="width: 60px;" /><span id="commissions-<?php echo $commish->id; ?>-percent-symbol">%</span></td>
                  <?php else: ?>
                    <td><span id="commissions-<?php echo $commish->id; ?>-currency-symbol"><?php echo $wafp_options->currency_symbol; ?></span><input type="hidden" name="commissions[<?php echo $commish->id; ?>][commission_percentage]" value="<?php echo $commish->commission_percentage; ?>" /><?php echo $commish->commission_percentage; ?><span id="commissions-<?php echo $commish->id; ?>-percent-symbol">%</span></td>
                  <?php endif; ?>
                </tr>
                <tr>
                  <td><?php _e('Correction Amount', 'affiliate-royale', 'easy-affiliate'); ?></td>
                  <td><?php echo WafpAppHelper::format_currency($commish->correction_amount); ?></td>
                </tr>
                <tr>
                  <td><?php _e('Commission Amount', 'affiliate-royale', 'easy-affiliate'); ?></td>
                  <td><?php echo WafpAppHelper::format_currency($commish->commission_amount); ?></td>
                </tr>
              </table>
            </div>
            <?php
          endforeach;
          ?>
          </tbody>
        </table>
      </div>
      <div class="wafp-commissions-add"><a href="" data-id="<?php echo $commish->id; ?>"><i class="ar-icon-plus-circled ar-24"> </i></a></div>
      <p class="submit">
        <input type="submit" id="submit" class="button button-primary" value="<?php _e('Update', 'affiliate-royale', 'easy-affiliate'); ?>" />
      </p>
    </form>
  </div>
</div>

