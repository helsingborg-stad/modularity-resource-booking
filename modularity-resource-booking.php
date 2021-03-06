<?php

/**
 * Plugin Name:       Modularity Resource Booking
 * Plugin URI:        https://github.com/helsingborg-stad/modularity-resource-booking
 * Description:       Book a physical resource for a predefined period of time
 * Version:           1.0.0
 * Author:            Sebastian Thulin, Nikolas Ramstedt, Jonatan Hansson, Johan Silvergrund
 * Author URI:        https://github.com/helsingborg-stad
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

//Disable security mode
if (!defined('RESOURCE_BOOKING_DISABLE_SECURITY')) {
    define('RESOURCE_BOOKING_DISABLE_SECURITY', false);
}

load_plugin_textdomain('modularity-resource-booking', false, plugin_basename(dirname(__FILE__)) . '/languages');

// Require composer dependencies (autoloader)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
// Include vendor files
if (file_exists(dirname(ABSPATH) . '/vendor/autoload.php')) {
    require_once dirname(ABSPATH) . '/vendor/autoload.php';
}

require_once MODULARITYRESOURCEBOOKING_PATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once MODULARITYRESOURCEBOOKING_PATH . 'Public.php';

// Instantiate and register the autoloader
$loader = new ModularityResourceBooking\Vendor\Psr4ClassLoader();
$loader->addPrefix('ModularityResourceBooking', MODULARITYRESOURCEBOOKING_PATH);
$loader->addPrefix('ModularityResourceBooking', MODULARITYRESOURCEBOOKING_PATH . 'source/php/');
$loader->register();

// Acf auto import and export
add_action('plugins_loaded', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('modularity-resource-booking');
    $acfExportManager->setExportFolder(MODULARITYRESOURCEBOOKING_PATH . 'source/acf-fields/');
    $acfExportManager->autoExport(
        array(
            'mod-product-details' => 'group_5beacf4f7895b',
            'mod-product-media-requirement' => 'group_5bffb822b9213',
            'mod-package-details' => 'group_5bead7869a8ed',
            'mod-order-details' => 'group_5bed425d9abc2',
            'mod-order-notes' => 'group_5bed90f741b0e',
            'mod-order-media' => 'group_5bffbfe266f20',
            'mod-customer-group' => 'group_5bfe8d5fdeedd',
            'mod-customer-group-vat' => 'group_5c52a6c8c0db5',
            'mod-time-slots-management' => 'group_5bed4d621923e',
            'mod-default-order-status' => 'group_5bfd2dab2fd89',
            'mod-email-settings' => 'group_5c063df1cb24f',
            'mod-billing-info' => 'group_5c010376c78be',
            'mod-booking-form' => 'group_5c35f66a679df',
            'mod-order-status-actions' => 'group_5c360bf77a6cf',
            'mod-group-settings' => 'group_5c0a43038051e',
            'mod-sign-in-settings' => 'group_5c4ef6a792434',
            'mod-resource_booking_map' => 'group_5c4acdd9a5388',
            'mod-email-color-settings' => 'group_5c505a650df0a',
            'mod-messages-settings' => 'group_5c5963e405f7b',
            'mod-order-resend-mail' => 'group_5c5dc351c1bfc',
            'mod-customer-action-settings' => 'group_5c65d682e3a62',
            'mod-mail-template' => 'group_5c62cadbdf9b5',
            'mod-order-notice' => 'group_5c7931e1d5479',
            'mod-registration-form-recaptcha' => 'group_5c828e80a91d5'
        )
    );
    $acfExportManager->import();
});

register_activation_hook(plugin_basename(__FILE__), '\ModularityResourceBooking\ResourceAdmin::createUserRoles');
register_deactivation_hook(plugin_basename(__FILE__), '\ModularityResourceBooking\ResourceAdmin::removeCustomUserRoles');
register_activation_hook(plugin_basename(__FILE__), '\ModularityResourceBooking\Customer::createUserRoles');
register_deactivation_hook(plugin_basename(__FILE__), '\ModularityResourceBooking\Customer::removeUserRoles');



// Start application
new ModularityResourceBooking\App();
