<?
defined('ABSPATH') || exit;

$options = get_option('credit_calc');
$amount_start = number_format($options['amount_start'], 0, '.', ' ');
$amount_min = number_format($options['amount_min'], 0, '.', ' ');
$amount_max = number_format($options['amount_max'], 0, '.', ' ');

$currency = $options['currency']
//todo move to template to plugin
?>

<div class="cc">
  <form class="cc__form">
    <div class="cc__inner">
      <? wp_nonce_field('credit-calc__form_action', '_wpnonce'); ?>
      <div class="cc__header">Рассчитайте ежемесячный платеж и сумму к выплате</div>
      <div class="cc__body">
        <div class="cc__fieldset">
          <div class="cc__group">
            <div class="cc__field cc__field_border">
              <input class="cc__input cc__input-amount" required="" name="amount" id="cc__amount" placeholder="Сумма займа" readonly>
              <label class="cc__label" for="cc__amount">Сумма займа</label>
            </div>
            <div class="cc__range cc__range-amount">
            </div>
            <div class="cc__hint">
              <div class="cc__hint-part">От <?= $amount_min . ' ' .  $currency?></div>
              <div class="cc__hint-part">до <?= $amount_max. ' ' . $currency ?></div>
            </div>
          </div>
          <div class="cc__group">
            <div class="cc__field cc__field_border">
              <input class="cc__input cc__input-term" required="" name="term" id="cc__term" placeholder="Срок займа" readonly>
              <label class="cc__label" for="cc__term">Срок займа</label>
            </div>
            <div class="cc__range cc__range-term">
            </div>
            <div class="cc__hint">
              <div class="cc__hint-part cc__hint-part_min">От</div>
              <div class="cc__hint-part cc__hint-part_max">до</div>
            </div>
          </div>
        </div>
        <div class="cc__result">
          <div class="cc__result-key">Ежемесячный платеж -</div>
          <div class="cc__result-value">5300 ₽</div>
        </div>
        <div class="cc__fieldset">
          <div class="cc__group">
            <div class="cc__field">
              <input class="cc__input" required="" name="name" id="cc__name" placeholder="Иван">
              <label class="cc__label cc__label-clear" for="cc__name">Имя</label>
            </div>
          </div>
          <div class="cc__group">
            <div class="cc__field">
              <input class="cc__input cc__input_phone" required="" type="tel" name="phone" id="cc__phone" placeholder="Телефон">
              <label class="cc__label cc__label-clear" for="cc__phone">Телефон</label>
            </div>
          </div>
        </div>
      </div>
      <div class="cc__footer">
        <label class="checkbox-label cc__checkbox">Нажимая на кнопку я принимаю условия соглашения
          <input class="checkbox" type="checkbox" required="" checked=""><span class="checkmark"></span>
        </label>
        <button class="cc__btn">Получить одобрение<i class="ld ld-arrow cc__btn-icon"></i></button>
      </div>
      <div class="cc__status"></div>
    </div>
  </form>
</div>
