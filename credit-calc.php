<?php

/*
Plugin Name: credit_calc
Text Domain: credit_calc
Domain Path: /language
Description: The calculation the monthly payment amount and sending the request to email.
Version: 1.1.0
Author: kostikovmu
Author URI: https://kostikovmu.ru/
Requires at least: 5.0
Requires PHP: 5.6
License: GPL3+
License URI: http://www.gnu.org/licenses/gpl-3.0.txt
*/

__('credit_calc', 'credit_calc');
__('The calculation the monthly payment amount and sending the request to email.', 'credit_calc');

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

    add_action('plugins_loaded', [ $this, 'init']);
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
    wp_register_script('credit_calc_main_script', plugins_url('credit_calc/js/main.js'), array('credit_calc_libs_script', 'wp-i18n'), filemtime( __DIR__ .'/js/main.js'));

    wp_register_style('roboto_font', 'https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap&subset=cyrillic');
    wp_register_style('credit_calc_libs_style', plugins_url('credit_calc/css/libs.min.css'), array(), filemtime( __DIR__ . '/css/libs.min.css'));
    wp_register_style('credit_calc_main_style', plugins_url('credit_calc/css/main.min.css'), array('credit_calc_libs_style'), filemtime( __DIR__ . '/css/main.min.css'));

    $options = get_option('credit_calc');



    wp_localize_script(
      'credit_calc_main_script',
      'creditCalc',
      $options
    );
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

    $submenu = add_submenu_page(
      'options-general.php',
      __('Credit calc - settings','credit_calc'),
      __('Credit calc','credit_calc'),
      'manage_options',
      'credit_calc_option',
      [ $this , 'change_option']
    );

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

    if (isset($_POST['credit_calc_option_btn'])) {

      if (function_exists('current_user_can')
        && !current_user_can('manage_options'))
        die(_e('Hacker', 'credit_calc'));

      if (function_exists('check_admin_referer')) {
        check_admin_referer('credit_calc_option_form');
      }

      $rate           = sanitize_text_field( $_POST['credit_calc_rate'] );
      $email          = sanitize_email( $_POST['credit_calc_email'] );
      $amount_start   = sanitize_text_field( $_POST['credit_calc_amount_start'] );
      $term_start     = sanitize_text_field( $_POST['credit_calc_term_start'] );
      $term_min       = sanitize_text_field( $_POST['credit_calc_term_min'] );
      $term_max       = sanitize_text_field( $_POST['credit_calc_term_max'] );
      $amount_min     = sanitize_text_field( $_POST['credit_calc_amount_min'] );
      $amount_max     = sanitize_text_field( $_POST['credit_calc_amount_max'] );
      $currency       = sanitize_text_field( $_POST['credit_calc_currency'] );
      $phone_mask     = sanitize_text_field( $_POST['credit_calc_phone_mask'] ) ;
      $step           = sanitize_text_field( $_POST['credit_calc_step'] );

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
        'phoneMask'      => $phone_mask,
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
    $username = sanitize_text_field( $_POST['name'] );
    $userphone = sanitize_text_field( $_POST['phone'] );
    $amount = sanitize_text_field( $_POST['amount'] );
    $term = sanitize_text_field( $_POST['term'] );
    $rate = get_option('credit_calc_rate');
    $payment = sanitize_text_field( $_POST['payment'] );
    $url = $_SERVER['HTTP_HOST'];

    $subject  = __('Request from site','credit_calc');
    $headers = "From: '{$url}' <{$sendto}>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html;charset=utf-8 \r\n";

    $msg  = "<html><body style='font-family:Arial,sans-serif;'>";
    $msg .= "<h2 style='font-weight:bold;border-bottom:1px dotted #ccc;'>".__('Request from site','credit_calc') . '&nbsp;'. $url."</h2>\r\n";
    $msg .= "<p><strong>".__('First name','credit_calc').":&nbsp;</strong> " .$username."</p>\r\n";
    $msg .= "<p><strong>".__('Phone','credit_calc').":&nbsp;</strong> " .$userphone."</p>\r\n";
    $msg .= "<p><strong>".__('Amount','credit_calc').":&nbsp;</strong> " .$amount."</p>\r\n";
    $msg .= "<p><strong>".__('Term','credit_calc').":&nbsp;</strong> " .$term."</p>\r\n";
    $msg .= "<p><strong>".__('Rate','credit_calc').":&nbsp;</strong> " .$rate." %</p>\r\n";
    $msg .= "<p><strong>".__('Payment','credit_calc').":&nbsp;</strong> " .$payment."</p>\r\n";

    $msg .= "</body></html>";

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
      'amount_start' => 1000,
      'term_start' => 36,
      'term_min' => 3,
      'term_max' => 60,
      'step' => 100,
      'amount_min' => 100,
      'amount_max' => 10000,
      'currency' => '$',
      'phoneMask' => '+1 (999) 999-99-99',
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
  public function init() {
    load_plugin_textdomain(
      'credit_calc' ,
      false,
      dirname( plugin_basename( __FILE__ ) ) . '/language/'
    );
  }
  public function plugin_action_links( $links ) {
    $url = admin_url( 'options-general.php?page=credit_calc_option' );
    $links[] = "<a href='$url'>Настройки</a>";
    return $links;
  }
}

new Credit_Calc_Plugin();





