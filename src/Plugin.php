<?php
if (! defined( 'ABSPATH' )) {
    exit;
} // Exit if accessed directly

require_once('Config.php');

final class Clearvisio_Booker_Plugin
{
    private $config;

    public function __construct()
    {
        $this->config = new Clearvisio_Booker_Config();

        add_action('init', [ $this, 'registerScripts' ]);
        add_filter('script_loader_tag', [ $this, 'importBookerJsAsModule' ] , 10, 3);

        add_action('wp_enqueue_scripts', function() {
            wp_enqueue_script('clearvisio_booker_config');
            wp_enqueue_style('clearvisio_booker_css');
        });
    }

    public function registerScripts()
    {
        wp_register_style('clearvisio_booker_css', plugins_url('/vendor/style.css', __FILE__));
        wp_register_script('clearvisio_booker_config', plugins_url("/booker{$this->config->getVersion()}.js", __FILE__));
    }

    public function importBookerJsAsModule($tag, $handle, $src)
    {
        if ( 'clearvisio_booker_config' !== $handle ) {
            return $tag;
        }

        return '<script type="module" src="' . esc_url( $src ) . '"></script>';
    }
}
