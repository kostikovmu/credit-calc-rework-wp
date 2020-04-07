<?
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'get_template_html' ) ) {
  function get_template_html( $template_name ) {
    ob_start();

    do_action('credit_calc_before_' . $template_name );

    require dirname(__DIR__) . '/template/' . $template_name . '.php';

    do_action('credit_calc_after_' . $template_name );

    $html = ob_get_clean();

    return $html;
  }

}