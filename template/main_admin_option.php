<?
defined( 'ABSPATH' ) || exit;

$options = get_option('credit_calc');

$rate           = esc_attr( sanitize_text_field( $options[ 'rate' ] ) );
$email          = esc_attr( sanitize_email( $options[ 'email' ] ) );
$amount_start   = esc_attr( sanitize_text_field( $options[ 'amount_start' ] ) );
$term_start     = esc_attr( sanitize_text_field( $options[ 'term_start' ] ) );
$term_min       = esc_attr( sanitize_text_field( $options[ 'term_min' ] ) );
$term_max       = esc_attr( sanitize_text_field( $options[ 'term_max' ] ) );
$amount_min     = esc_attr( sanitize_text_field( $options[ 'amount_min' ] ) );
$amount_max     = esc_attr( sanitize_text_field( $options[ 'amount_max' ] ) );
$currency       = esc_attr( sanitize_text_field( $options[ 'currency' ] ) );
$phone_mask     = esc_attr( sanitize_text_field( $options[ 'phoneMask' ] ) );
$step           = esc_attr( sanitize_text_field( $options[ 'step' ] ) );
?>



<div class="row">
  <div class="col s12">
    <h4><?= __('Settings', 'credit_calc'); ?></h4>
  </div>
  <div class="col m4 s12">
    <form class="card-panel" name='credit_calc_option' method='post' action='<?= $_SERVER['PHP_SELF']; ?>?page=credit_calc_option&amp;update=true'>
      <?
      if(function_exists('wp_nonce_field')) {
        wp_nonce_field('credit_calc_option_form');
      }
      ?>
      <div class="input-field">
        <label for="credit_calc_rate"><?= __('Monthly rate (%)', 'credit_calc'); ?></label>
        <input type='number' min='1' step="0.1" name='credit_calc_rate' id="credit_calc_rate" required value='<?= $rate ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_email"><?= __('Your email to getting requests', 'credit_calc'); ?></label>
        <input type='email' name='credit_calc_email' id="credit_calc_email" required value='<?= $email ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_amount_start"><?= __('Default credit amount', 'credit_calc'); ?></label>
        <input type='number' step="1" name='credit_calc_amount_start' id="credit_calc_amount_start" required value='<?= $amount_start ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_step"><?= __('Step loan amount', 'credit_calc'); ?></label>
        <input type='number' step="1" name='credit_calc_step' id="credit_calc_step" required value='<?= $step ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_term_start"><?= __('Default loan term (mo.)', 'credit_calc'); ?></label>
        <input type='number' step="1" name='credit_calc_term_start' id="credit_calc_term_start" required value='<?= $term_start ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_term_min"><?= __('Minimum loan term (mo.)', 'credit_calc'); ?></label>
        <input type='number' step="1" name='credit_calc_term_min' id="credit_calc_term_min" required value='<?= $term_min ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_term_max"><?= __('Maximum loan term (mo.)', 'credit_calc'); ?></label>
        <input type='number' step="1" name='credit_calc_term_max' id="credit_calc_term_max" required value='<?= $term_max ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_amount_min"><?= __('Minimum loan amount', 'credit_calc'); ?></label>
        <input type='number' step="1" name='credit_calc_amount_min' id="credit_calc_amount_min" required value='<?= $amount_min ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_amount_max"><?= __('Maximum loan amount', 'credit_calc'); ?></label>
        <input type='number' step="1" name='credit_calc_amount_max' id="credit_calc_amount_max" required value='<?= $amount_max ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_amount_max"><?= __('Currency', 'credit_calc'); ?></label>
        <input type='text' name='credit_calc_currency' id="credit_calc_currency" required value='<?= $currency ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_amount_max"><?= __('Phone mask', 'credit_calc'); ?></label>
        <input type='text'  name='credit_calc_phone_mask' id="credit_calc_phone_mask" required value='<?= $phone_mask ?>'>
      </div>
      <div class="input-field">
        <button name='credit_calc_option_btn' class="btn"><?= __('Save', 'credit_calc'); ?></button>
      </div>
    </form>
  </div>
  <div class="col s12">
    <p>
      <?= __('Shortcode for output credit form', 'credit_calc'); ?>&nbsp;[credit_calc]
    </p>
  </div>
  <div class="col s12">
    <p>
      <?= __('Have a suggestion or found a bug? Let me know.', 'credit_calc'); ?>&nbsp;<a href="mailto:kostikovmu@ya.ru">kostikovmu@ya.ru</a>
    </p>
  </div>
</div>


<style>
  #wpfooter {
    display: none;
  }
</style>


