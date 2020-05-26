<?
defined('ABSPATH') || exit;

$options = get_option('credit_calc');
$amount_start = number_format($options['amount_start'], 0, '.', ' ');
$amount_min = number_format($options['amount_min'], 0, '.', ' ');
$amount_max = number_format($options['amount_max'], 0, '.', ' ');

$currency = $options['currency']
?>

<div class="cc">
  <form class="cc__form">
    <div class="cc__inner">
      <? wp_nonce_field('credit-calc__form_action', '_wpnonce'); ?>
      <div class="cc__header"><?= __('Calculate your monthly payment', 'credit_calc'); ?></div>
      <div class="cc__body">
        <div class="cc__fieldset">
          <div class="cc__group">
            <div class="cc__field cc__field_border">
              <input class="cc__input cc__input-amount" required="" name="amount" id="cc__amount" placeholder="<?= __('Loan amount', 'credit_calc'); ?>" readonly>
              <label class="cc__label" for="cc__amount"><?= __('Loan amount', 'credit_calc'); ?></label>
            </div>
            <div class="cc__range cc__range-amount">
            </div>
            <div class="cc__hint">
              <div class="cc__hint-part"><?= __('From', 'credit_calc'); ?>&nbsp;<?= $amount_min . ' ' .  $currency?></div>
              <div class="cc__hint-part"><?= __('to', 'credit_calc'); ?>&nbsp;<?= $amount_max. ' ' . $currency ?></div>
            </div>
          </div>
          <div class="cc__group">
            <div class="cc__field cc__field_border">
              <input class="cc__input cc__input-term" required="" name="term" id="cc__term" placeholder="<?= __('Term loan', 'credit_calc'); ?>" readonly>
              <label class="cc__label" for="cc__term"><?= __('Term loan', 'credit_calc'); ?></label>
            </div>
            <div class="cc__range cc__range-term">
            </div>
            <div class="cc__hint">
              <div class="cc__hint-part cc__hint-part_min"><?= __('From', 'credit_calc'); ?></div>
              <div class="cc__hint-part cc__hint-part_max"><?= __('to', 'credit_calc'); ?></div>
            </div>
          </div>
        </div>
        <div class="cc__result">
          <div class="cc__result-key"><?= __('Monthly payment', 'credit_calc'); ?> -</div>
          <div class="cc__result-value">5300 ₽</div>
        </div>
        <div class="cc__fieldset">
          <div class="cc__group">
            <div class="cc__field">
              <input class="cc__input" required="" name="name" id="cc__name" placeholder="<?= __('First name', 'credit_calc'); ?>">
              <label class="cc__label cc__label-clear" for="cc__name"><?= __('First name', 'credit_calc'); ?></label>
            </div>
          </div>
          <div class="cc__group">
            <div class="cc__field">
              <input class="cc__input cc__input_phone" required="" type="tel" name="phone" id="cc__phone" placeholder="<?= __('Phone', 'credit_calc'); ?>">
              <label class="cc__label cc__label-clear" for="cc__phone"><?= __('Phone', 'credit_calc'); ?></label>
            </div>
          </div>
        </div>
      </div>
      <div class="cc__footer">
        <label class="checkbox-label cc__checkbox"><?= __('By clicking the button I accept the privacy policy', 'credit_calc'); ?>
          <input class="checkbox" type="checkbox" required="" checked=""><span class="checkmark"></span>
        </label>
        <button class="cc__btn"><?= __('Get approval', 'credit_calc'); ?><i class="ld ld-arrow cc__btn-icon"></i></button>
      </div>
      <div class="cc__status"></div>
    </div>
  </form>
</div>
