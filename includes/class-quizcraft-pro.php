<?php

class QuizCraft_Pro {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = QUIZCRAFT_PRO_VERSION;
        $this->plugin_name = 'quizcraft-pro';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once QUIZCRAFT_PRO_PLUGIN_DIR . 'includes/class-quizcraft-pro-loader.php';
        require_once QUIZCRAFT_PRO_PLUGIN_DIR . 'includes/class-quizcraft-pro-i18n.php';
        require_once QUIZCRAFT_PRO_PLUGIN_DIR . 'admin/class-quizcraft-pro-admin.php';
        require_once QUIZCRAFT_PRO_PLUGIN_DIR . 'public/class-quizcraft-pro-public.php';

        $this->loader = new QuizCraft_Pro_Loader();
    }

    private function set_locale() {
        $plugin_i18n = new QuizCraft_Pro_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks() {
        $plugin_admin = new QuizCraft_Pro_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_action('wp_ajax_quizcraft_save_quiz', $plugin_admin, 'ajax_save_quiz');
        $this->loader->add_action('wp_ajax_quizcraft_get_quiz', $plugin_admin, 'ajax_get_quiz');
        $this->loader->add_action('wp_ajax_quizcraft_delete_quiz', $plugin_admin, 'ajax_delete_quiz');
    }

    private function define_public_hooks() {
        $plugin_public = new QuizCraft_Pro_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_shortcode('quizcraft_quiz', $plugin_public, 'display_quiz');
        $this->loader->add_action('wp_ajax_quizcraft_submit_quiz', $plugin_public, 'ajax_submit_quiz');
        $this->loader->add_action('wp_ajax_nopriv_quizcraft_submit_quiz', $plugin_public, 'ajax_submit_quiz');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }
}