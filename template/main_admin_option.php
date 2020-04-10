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
$step           = esc_attr( sanitize_text_field( $options[ 'step' ] ) );
//todo fill form val
?>



<div class="row">
  <div class="col s12">
    <h4>Настройки</h4>
  </div>
  <div class="col m4 s12">
    <form class="card-panel" name='credit_calc_option' method='post' action='<?= $_SERVER['PHP_SELF']; ?>?page=credit_calc_option&amp;update=true'>
      <?
      if(function_exists('wp_nonce_field')) {
        wp_nonce_field('credit_calc_option_form');
      }
      ?>
      <div class="input-field">
        <label for="credit_calc_rate">Ежемесячная ставка (%)</label>
        <input type='number' min='1' step="0.1" name='credit_calc_rate' id="credit_calc_rate" required value='<?= $rate ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_email">Почта для получения заявок</label>
        <input type='email' name='credit_calc_email' id="credit_calc_email" required value='<?= $email ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_amount_start">Сумма кредита по умолчанию</label>
        <input type='number' step="1" name='credit_calc_amount_start' id="credit_calc_amount_start" required value='<?= $amount_start ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_step">Шаг для суммы кредита</label>
        <input type='number' step="1" name='credit_calc_step' id="credit_calc_step" required value='<?= $step ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_term_start">Срок кредита по умолчанию (мес.)</label>
        <input type='number' step="1" name='credit_calc_term_start' id="credit_calc_term_start" required value='<?= $term_start ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_term_min">Минимальный срок кредита (мес.)</label>
        <input type='number' step="1" name='credit_calc_term_min' id="credit_calc_term_min" required value='<?= $term_min ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_term_max">Максимальный срок кредита (мес.)</label>
        <input type='number' step="1" name='credit_calc_term_max' id="credit_calc_term_max" required value='<?= $term_max ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_amount_min">Минимальная сумма кредита</label>
        <input type='number' step="1" name='credit_calc_amount_min' id="credit_calc_amount_min" required value='<?= $amount_min ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_amount_max">Максимальная сумма кредита</label>
        <input type='number' step="1" name='credit_calc_amount_max' id="credit_calc_amount_max" required value='<?= $amount_max ?>'>
      </div>
      <div class="input-field">
        <label for="credit_calc_amount_max">Валюта</label>
        <input type='text' name='credit_calc_currency' id="credit_calc_currency" required value='<?= $currency ?>'>
      </div>
      <div class="input-field">
        <button name='credit_calc_option_btn' class="btn">Сохранить</button>
      </div>
    </form>
  </div>
  <div class="col s12">
    <p>
      Шорткод для вывода калькулятора [credit_calc]
    </p>
  </div>
  <div class="col s12">
    <p>
      Если вы хотите заказать доработку или есть другие вопросы, напишите мне на почту <a href="mailto:kostikovmu@ya.ru">kostikovmu@ya.ru</a>
    </p>
  </div>
</div>


<style>
  #wpfooter {
    display: none;
  }
</style>


