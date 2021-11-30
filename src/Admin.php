<?php
if (! defined( 'ABSPATH' )) {
    exit;
} // Exit if accessed directly

require_once('Config.php');

class Clearvisio_Booker_Admin
{
    private $config;

    public function __construct()
    {
        $this->config = new Clearvisio_Booker_Config();

        add_filter('plugin_action_links', function($links, $file) {
            if ($file == 'clearvisio-booker/clearvisio-booker.php') {
                array_unshift($links, '<a href="' . esc_url(add_query_arg(['page' => 'clearvisio-booker-config'], admin_url('options-general.php'))) . '">'.esc_html__( 'Settings' , 'clearvisio-booker').'</a>');
            }

            return $links;
        }, 10, 2);

        $page_callback = [ $this, 'displaySettingsPage' ];
        add_action('admin_menu', function() use ($page_callback) {
            add_options_page( __('Clearvis.io Appointment Booker', 'clearvisio-booker'), __('Appointments', 'clearvisio-booker'), 'manage_options', 'clearvisio-booker-config', $page_callback );
        }, 5);

        add_action('admin_init', [ $this, 'initializeSettings' ], 5 );
    }

    public function displaySettingsPage()
    {
        if (! current_user_can( 'manage_options')) {
            return;
        }

        if (isset( $_GET['settings-updated'])) {
            $this->generateJs();
        }

        ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <form action="options.php" method="post">
                    <?php
                    settings_fields('clearvisio_booker_options');
                    do_settings_sections('clearvisio_booker_options');
                    submit_button('Save Settings');
                    ?>
                </form>
            </div>
        <?php
    }

    private function generateJs()
    {
        $path = dirname(__FILE__) . "/booker{$this->config->getVersion()}.js";
        $url = get_site_url();
        $extraSettings = @json_decode($this->config->get('extra_settings', '{}'));
        $extraSettingsString = ($extraSettings === null) ? "{}" : $this->config->get('extra_settings', '{}');
        file_put_contents($path, <<<EOS
import ClearvisioAppointmentBooker from './vendor/index.js';

document.addEventListener('click', (event) => {
  var node = getElementOrSomeParentWithClass(event.target, 'clearvisio-booker');
  if (node !== null) {
    startBooking(node);
    return false;
  }
});

function startBooking(node) {
  var storeCode = '{$this->config->get('store_code')}';
  if (node.dataset && node.dataset.storeCode) {
      storeCode = node.dataset.storeCode;
  }

  if (!storeCode) {
      alert("Store code is missing, please configure it in admin UI or provide in data-store-code property of button with clearvisio-booker class");
      return;
  }

  new ClearvisioAppointmentBooker(Object.assign({
    storeCode: storeCode,
    apiPath: '{$url}/wp-content/plugins/clearvisio-booker/api.php'
  }, {$extraSettingsString}));
}

function getElementOrSomeParentWithClass(element, classname) {
  if (element.classList && element.classList.contains(classname)) {
    return element;
  } else {
    return element.parentNode ? getElementOrSomeParentWithClass(element.parentNode, classname) : null;
  }
}
EOS
        );
    }

    public function initializeSettings()
    {
        register_setting('clearvisio_booker_options', 'clearvisio_booker_options');

        add_settings_section(
            'api_settings',
            '', function() {
                echo '<p>' .
                    esc_html__('Clearvis.io is a cloud based Practice Management Software (PMS) for optical retailers. It provides complete solution for the daily front- and backoffice tasks in an optical retail store. Clearvis.io includes EHR, POS and CRM features and it is suitable for private practices and retail chains as well.', 'clearvisio_booker') . '</p><p>' .
                    esc_html__('This plugin is a frontend for the Appointment Booking API of clearvis.io. The API itself is only available for subscribers, this is a simple, configurable, open source frontend for it, that is easy to add to wordpress. It allows the registration of a customer, accepting the store\'s privacy policy and booking of an available appointment matching the customer\'s desired expectations. (Including selecting the type of examination, the optometrist or opthalmologist and of course the date and time of the examination.)', 'clearvisio_booker') . '</p><h2>' .
                    esc_html__('Plugin Settings', 'clearvisio_booker') . '</h2>';
            },
            'clearvisio_booker_options'
        );

        $this->addTextSettingsField('api_url', 'API URL', 'Enter the API URL as displayed in clearvis.io store settings');
        $this->addTextSettingsField('api_key', 'API Key', 'Enter the API key of the technical user created in clearvis.io');
        $this->addTextSettingsField('store_code', 'Store Code', 'Enter store code if clearvis.io subscription has more than one store (optional)');
        $this->addTextareaSettingsField('extra_settings', 'Extra JSON settings', 'See https://github.com/clearvis-io/clearvisio-appointment-booker for options');
    }

    private function addTextAreaSettingsField($name, $title, $description)
    {
        add_settings_field(
            'clearvisio_booker_field_' . $name,
            __($title, 'clearvisio_booker' ),
            function() use ($name, $description){
                $options = get_option('clearvisio_booker_options');
                echo '<textarea id="clearvisio_booker_field_' . $name .'" name="clearvisio_booker_options[' . $name . ']" rows="6" cols="80">' . esc_attr($options[$name]) . '</textarea>' .
                    '<p class="description">' . esc_html__($description, 'clearvisio_booker') . '</p>';
            },
            'clearvisio_booker_options',
            'api_settings'
        );
    }

    private function addTextSettingsField($name, $title, $description)
    {
        add_settings_field(
            'clearvisio_booker_field_' . $name,
            __($title, 'clearvisio_booker' ),
            function() use ($name, $description){
                $options = get_option('clearvisio_booker_options');
                echo '<input id="clearvisio_booker_field_' . $name .'" name="clearvisio_booker_options[' . $name . ']" type="text" value="' . esc_attr($options[$name]) . '" />' .
                    '<p class="description">' . esc_html__($description, 'clearvisio_booker') . '</p>';
            },
            'clearvisio_booker_options',
            'api_settings'
        );
    }
}
