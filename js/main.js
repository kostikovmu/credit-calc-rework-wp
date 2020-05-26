(function ($) {

  'use strict';
	const { __ } = wp.i18n;

  var declension = function(month) {
    var txt,
      count = month % 10;

    if( (month > 5 && month < 21) || count === 0 || (count >= 5 && count <= 9)) {
      txt = __('months', 'credit_calc');//месяцев
    }
    else if(count === 1) {
      txt = __('month', 'credit_calc');//месяц
    }
    else {
      txt = __('months', 'credit_calc');//месяца
    }

    return txt;
  };
  var declensionFrom = function(month) {
    var txt,
      count = month % 10;

    if(count === 1 && month !== 11) {
      txt = __('months', 'credit_calc');//месяца
    }
    else {
      txt = __('months', 'credit_calc');//месяцев
    }

    return txt;
  };



  var inputs = function () {

	  $('.cc__input_phone').each(function () {
		  $(this).inputmask({"mask": creditCalc.phoneMask
		  });
	  });


    $('.cc__input')
      .focus(function () {
        if($(this).val() === '') {
          $(this).next().addClass('cc__label_active');
        }
      })
      .blur(function () {
        if($(this).val() === '') {
          $(this).next().removeClass('cc__label_active');
        }
      })

      .each(function () {
        if($(this).val() !== '') {
          $(this).next().addClass('cc__label_active');
        }
      });
  }

  var ajaxSend = function () {

    $('.cc__form').submit(function(e){
      e.preventDefault();
      var data = '&payment=' + $('.cc__result-value').text() + '&action=credit_calc'
      $.ajax({
        url: creditCalc.ajaxUrl,
        type: 'post',
        data: $(this).serialize() + data,
        dataType: 'json',
        beforeSend: function(){
          $('.cc__status').html('');
          $('.cc__btn').prop('disabled', true).addClass('cc__btn_disabled');
          $('.cc__btn-icon').removeClass('ld-arrow ld-complete').addClass('ld-spin')
        },
        success: function(response){
          setTimeout( function () {
            if (response === 3){
              $('.cc__status').html(__('Sending error!', 'credit_calc')).css({'color':'#ff1a29'}); //Ошибка отправки!
            }
            else if (response === 1){
              $('.cc__status').html(__('Your request has been sent!', 'credit_calc')).css({'color':'#1d2029'});//Ваша заявка отправлена!
              $('.cc__input[name="name"], .cc__input[name="phone"]').val('');
              $('.cc__label-clear').removeClass('cc__label_active')
            }
            else if( response === 2) {
              $('.cc__status').html(__('Data validation error!', 'credit_calc')).css({'color':'#ff1a29'});//Ошибка проверки данных!
            }
            else {
              $('.cc__status').html(__('Uncaught error!', 'credit_calc')).css({'color':'#ff1a29'});//Не распознанная ошибка
              console.log(response);
            }
          }, 1000)
        },
        error: function(){
          $('.cc__status').html(__('Error, try again!', 'credit_calc')).css({'color':'#ff1a29'});//Ошибка, попробуйте еще раз!
        },
        complete: function (response) {
          console.log(response);
          setTimeout( function () {
            $('.cc__btn-icon').removeClass('ld-spin').addClass('ld-complete')
            $('.cc__btn').prop('disabled', false).removeClass('cc__btn_disabled');
          }, 1000)

        }
      });
    });
  };

  var calculator = function () {

    var termStart = +creditCalc.term_start;
    var amountStart = +creditCalc.amount_start;
    var termStartVal = termStart + ' ' +  declension(termStart);
    var rateStart = +creditCalc.rate / 100;
    var resStart = Math.round(( amountStart + (amountStart * rateStart * termStart)) / termStart);

    var termMin = __('From', 'credit_calc') + '&nbsp;' + creditCalc.term_min + ' ' + declensionFrom(+creditCalc.term_min)
    var termMax = __('to', 'credit_calc') + '&nbsp;' + creditCalc.term_max + ' ' + declensionFrom(+creditCalc.term_max)
    var currency = creditCalc.currency

    $('.cc__input-amount').val(amountStart.toLocaleString('ru-RU') + ' ' + currency);
    $('.cc__input-term').val(termStartVal);
    $('.cc__result-value').html(resStart.toLocaleString('ru-RU') + ' ' + currency);
    $('.cc__hint-part_min').html(termMin)
    $('.cc__hint-part_max').html(termMax)


    $('.cc__range-amount').slider({
      range: 'min',
      step: +creditCalc.step,
      min: +creditCalc.amount_min,
      max: +creditCalc.amount_max,
      value: +creditCalc.amount_start,
      slide: function (e, ui) {
        var term = $('.cc__range-term').slider('value'),
          amount = ui.value,
          rate = +creditCalc.rate / 100,
          res = Math.round(( amount + (amount * rate * term)) / term);

        $('.cc__input-amount').val(amount.toLocaleString('ru-RU') + ' ' + currency);
        $('.cc__result-value').html(res.toLocaleString('ru-RU') + ' ' + currency);
      }
    });
    $('.cc__range-term').slider({
      range: 'min',
      max: +creditCalc.term_max,
      min: +creditCalc.term_min,
      value: +creditCalc.term_start,
      slide: function (e, ui) {
        var amount = $('.cc__range-amount').slider('value'),
          term = ui.value,
          rate = +creditCalc.rate / 100,
          res = Math.round(( amount + (amount * rate * term)) / term);

        $('.cc__input-term').val(term + ' ' + declension(term));
        $('.cc__result-value').html(res.toLocaleString('ru-RU') + ' ' + currency);
      }
    });
  }

  var modalClose = function() {

    $('.popup__close, .overlay').click(function (e) {
      e.preventDefault();
      $('.popup').fadeOut();
      $('body').removeClass('modal-open');
      $('.overlay').fadeOut();
    })

  };

  $(document).ready(function(){
    calculator();
    inputs();
    ajaxSend();
    modalClose();
  });

})(jQuery);

