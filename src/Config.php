<?php
if (! defined( 'ABSPATH' )) {
    exit;
} // Exit if accessed directly

class Clearvisio_Booker_Config
{
    public function get($name, $default = null)
    {
        $options = get_option('clearvisio_booker_options');
        if (!is_array($options) || !isset($options[$name])) {
            return $default;
        }

        return $options[$name];
    }

    public function getVersion()
    {
        $options = get_option('clearvisio_booker_options');
        if (!is_array($options) || !isset($options['api_key']) || !strlen($options['api_key'])) {
            return null;
        }

        return substr(md5(serialize($options)), 0, 8);
    }
}
