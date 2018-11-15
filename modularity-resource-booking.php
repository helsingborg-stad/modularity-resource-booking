<?php

/**
 * Plugin Name:       Modularity Resource Booking
 * Plugin URI:        https://github.com/helsingborg-stad/modularity-resource-booking
 * Description:       Book a physical resource for a predefined period of time
 * Version:           1.0.0
 * Author:            Sebastian Thulin
 * Author URI:        https://github.com/sebastianthulin
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       modularity-resource-booking
 * Domain Path:       /languages
 */

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

define('MODULARITYRESOURCEBOOKING_PATH', plugin_dir_path(__FILE__));
define('MODULARITYRESOURCEBOOKING_URL', plugins_url('', __FILE__));
define('MODULARITYRESOURCEBOOKING_TEMPLATE_PATH', MODULARITYRESOURCEBOOKING_PATH . 'templates/');

load_plugin_textdomain('modularity-resource-booking', false, plugin_basename(dirname(__FILE__)) . '/languages');

require_once MODULARITYRESOURCEBOOKING_PATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once MODULARITYRESOURCEBOOKING_PATH . 'Public.php';

// Instantiate and register the autoloader
$loader = new ModularityResourceBooking\Vendor\Psr4ClassLoader();
$loader->addPrefix('ModularityResourceBooking', MODULARITYRESOURCEBOOKING_PATH);
$loader->addPrefix('ModularityResourceBooking', MODULARITYRESOURCEBOOKING_PATH . 'source/php/');
$loader->register();

//Load fields
// Acf auto import and export
add_action('plugins_loaded', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('modularity-resource-booking');
    $acfExportManager->setExportFolder(MODULARITYRESOURCEBOOKING_PATH . 'source/acf-fields/');
    $acfExportManager->autoExport(array(
        'mod-product-details' => 'group_5beacf4f7895b',
        'mod-package-details' => 'group_5bead7869a8ed',
        'mod-order-details' => 'group_5bed425d9abc2'
    ));
    $acfExportManager->import();
});

// Start application
new ModularityResourceBooking\App();
