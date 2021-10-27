<?php
/**
* Plugin Name: Clearvis.io Appointment Booker
* Plugin URI: https://clearvis.io/
* Description: This plugin adds an appointment booker widget to your wordpress backed by clearvis.io calendar.
* Version: 1.0
* Author: Clearvis.io Team
* Author URI: https://clearvis.io/
**/

if (! defined( 'ABSPATH' )) {
        exit;
} // Exit if accessed directly

require_once('Plugin.php');
require_once('Admin.php');

new Clearvisio_Booker_Plugin();
new Clearvisio_Booker_Admin();
