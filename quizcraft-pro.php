<?php
/**
 * Plugin Name: QuizCraft Pro: Logic-Driven Recommendation Engine
 * Description: Create engaging quizzes with a customizable, rule-based recommendation system to guide users to relevant products or services.
 * Version: 1.0.0
 * Author: Rakesh Mandal
 * Author URI: https://rakeshmandal.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: quizcraft-pro
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('QUIZCRAFT_PRO_VERSION', '1.0.0');
define('QUIZCRAFT_PRO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('QUIZCRAFT_PRO_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_quizcraft_pro() {
    require_once QUIZCRAFT_PRO_PLUGIN_DIR . 'includes/class-quizcraft-pro-activator.php';
    QuizCraft_Pro_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_quizcraft_pro() {
    require_once QUIZCRAFT_PRO_PLUGIN_DIR . 'includes/class-quizcraft-pro-deactivator.php';
    QuizCraft_Pro_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_quizcraft_pro');
register_deactivation_hook(__FILE__, 'deactivate_quizcraft_pro');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require QUIZCRAFT_PRO_PLUGIN_DIR . 'includes/class-quizcraft-pro.php';

/**
 * Begins execution of the plugin.
 */
function run_quizcraft_pro() {
    $plugin = new QuizCraft_Pro();
    $plugin->run();
}
run_quizcraft_pro();