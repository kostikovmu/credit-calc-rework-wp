<?php
/*
Plugin Name: Калькулятор кредитования
Description: Расчет ежемесячной суммы платежа и отправка заявки на почту.
Version: 1.0.15
Author: kostikovmu
Author URI: https://kostikovmu.ru/
*/
defined( 'ABSPATH' ) || exit;

require_once 'inc/helpers.php';
require_once 'inc/functions.php';
require_once ABSPATH . 'wp-includes/class-phpmailer.php';


class Credit_Calc_Plugin {

  public function __construct() {
    register_activation_hook(__FILE__, [ $this, 'activate' ] );
    register_deactivation_hook(__FILE__, [ $this, 'deactivate' ] );
    add_action( 'wp_enqueue_scripts', [ $this, 'assets' ] );
    add_shortcode('credit_calc', [ $this, 'short_code' ] );
    add_action( 'admin_menu',  [ $this , 'add_admin_pages' ] );
    add_action( 'wp_ajax_credit_calc', [ $this, 'ajax_callback' ] );
    add_action( 'wp_ajax_nopriv_credit_calc', [ $this, 'ajax_callback' ] );

    add_action('activated_plugin', [ $this, 'activated_plugin']);

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), [ $this, 'plugin_action_links']);
  }

  public function activate() {
    $this->add_data();
  }
  public function deactivate() {
    $this->remove_data();
  }
  public function assets()
  {
    wp_register_script('credit_calc_libs_script', plugins_url('credit_calc/js/libs.min.js'), array('jquery', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-slider'), filemtime(__DIR__ . '/js/libs.min.js'));
    wp_register_script('credit_calc_main_script', plugins_url('credit_calc/js/main.min.js'), array('credit_calc_libs_script'), filemtime( __DIR__ .'/js/main.min.js'));

    wp_register_style('roboto_font', 'https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap&subset=cyrillic');
    wp_register_style('credit_calc_libs_style', plugins_url('credit_calc/css/libs.min.css'), array(), filemtime( __DIR__ . '/css/libs.min.css'));
    wp_register_style('credit_calc_main_style', plugins_url('credit_calc/css/main.min.css'), array('credit_calc_libs_style'), filemtime( __DIR__ . '/css/main.min.css'));

    $options = get_option('credit_calc');

    wp_localize_script('credit_calc_main_script','creditCalc', $options );
  }

  public function short_code()
  {
    wp_enqueue_style('roboto_font');
    wp_enqueue_style('credit_calc_libs_style');
    wp_enqueue_style('credit_calc_main_style');
    wp_enqueue_script('credit_calc_libs_script');
    wp_enqueue_script('credit_calc_main_script');

    return get_template_html('calc');
  }
  public function add_admin_pages()
  {

    $submenu = add_submenu_page('options-general.php', 'Кальлкулятор кредита - настройки', 'Кальлкулятор кредита', 'manage_options', 'credit_calc_option', [ $this , 'change_option']);

    add_action('load-' . $submenu, [ $this, 'load_admin_scripts' ]);
  }
  public function load_admin_scripts()
  {
    add_action('admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
  }
  public function enqueue_admin_scripts()
  {
  wp_enqueue_script('materialize_js', plugin_dir_url(__FILE__) . 'js/materialize.min.js', [], filemtime(__DIR__ . '/js/materialize.min.js'), true);
  wp_enqueue_style('materialize_css', plugin_dir_url(__FILE__) . 'css/materialize.min.css', [], filemtime(__DIR__ . '/css/materialize.min.css'));
  }
  public function change_option() {

//  Была ли отправлена форма с помощью нашей кнопки
    if (isset($_POST['credit_calc_option_btn'])) {
      //    проверка прав пользователя на запись
      if (function_exists('current_user_can')
        && !current_user_can('manage_options'))
        die(_e('Hacker', 'credit_form'));

//    проверка одноразового поля
      if (function_exists('check_admin_referer')) {
        check_admin_referer('credit_calc_option_form');
      }

      $rate           = $_POST['credit_calc_rate'];
      $email          = $_POST['credit_calc_email'];
      $amount_start   = $_POST['credit_calc_amount_start'];
      $term_start     = $_POST['credit_calc_term_start'];
      $term_min       = $_POST['credit_calc_term_min'];
      $term_max       = $_POST['credit_calc_term_max'];
      $amount_min     = $_POST['credit_calc_amount_min'];
      $amount_max     = $_POST['credit_calc_amount_max'];
      $currency       = $_POST['credit_calc_currency'];
      $step           = $_POST['credit_calc_step'];

      $options = [
        'rate'          => $rate,
        'email'         => $email,
        'amount_start'  => $amount_start,
        'term_start'    => $term_start,
        'term_min'      => $term_min,
        'term_max'      => $term_max,
        'amount_min'    => $amount_min,
        'amount_max'    => $amount_max,
        'currency'      => $currency,
        'step'          => $step,
        'ajaxUrl'       => admin_url('admin-ajax.php')
      ];

      update_option('credit_calc', $options);
    }

    $html = get_template_html('main_admin_option');

    echo $html;

  }
  public function ajax_callback() {
    if(!wp_verify_nonce($_POST['_wpnonce'], 'credit-calc__form_action')) {
      echo 2;
      wp_die();
    }

    $sendto   = get_option('credit_calc')['email'];
    $username = $_POST['name'];
    $userphone = $_POST['phone'];
    $amount = $_POST['amount'];
    $term = $_POST['term'];
    $rate = get_option('credit_calc_rate');
    $payment = $_POST['payment'];
    $url = $_SERVER['HTTP_HOST'];

// Формирование заголовка письма
    $subject  = "Сообщение с сайта";
    $headers = "From: '{$url}' <{$sendto}>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html;charset=utf-8 \r\n";

// Формирование тела письма
    $msg  = "<html><body style='font-family:Arial,sans-serif;'>";
    $msg .= "<h2 style='font-weight:bold;border-bottom:1px dotted #ccc;'>Сообщение с сайта $url</h2>\r\n";
    $msg .= "<p><strong>Имя: </strong> " .$username."</p>\r\n";
    $msg .= "<p><strong>Телефон: </strong> " .$userphone."</p>\r\n";
    $msg .= "<p><strong>Сумма: </strong> " .$amount."</p>\r\n";
    $msg .= "<p><strong>Срок: </strong> " .$term."</p>\r\n";
    $msg .= "<p><strong>Ставка: </strong> " .$rate." %</p>\r\n";
    $msg .= "<p><strong>Платеж: </strong> " .$payment."</p>\r\n";

    $msg .= "</body></html>";

// отправка сообщения
    if(@mail($sendto, $subject, $msg, $headers)) {
      echo 1;
    } else {
      echo 3;
    }
    wp_die();
  }

  private function add_data() {
    $options = [
      'rate' => 2,
      'email' => 'name@site.ru',
      'amount_start' => 50000,
      'term_start' => 36,
      'term_min' => 3,
      'term_max' => 60,
      'step' => 10000,
      'amount_min' => 10000,
      'amount_max' => 1000000,
      'currency' => '₽',
      'ajaxUrl' => admin_url('admin-ajax.php')
    ];
    add_option('credit_calc', $options);
  }
  private function remove_data() {
    delete_option('credit_calc');
  }

  public function activated_plugin( $plugin ) {
    if( $plugin === plugin_basename(__FILE__ ) ) {
      exit ( wp_redirect( admin_url( 'options-general.php?page=credit_calc_option' ) ) );
    }
  }
  public function plugin_action_links( $links ) {
    $url = admin_url( 'options-general.php?page=credit_calc_option' );
    $links[] = "<a href='$url'>Настройки</a>";
    return $links;
  }
}

new Credit_Calc_Plugin();





