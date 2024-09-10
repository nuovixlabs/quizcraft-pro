<?php

class QuizCraft_Pro_Public {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, QUIZCRAFT_PRO_PLUGIN_URL . 'public/css/quizcraft-pro-public.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, QUIZCRAFT_PRO_PLUGIN_URL . 'public/js/quizcraft-pro-public.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'quizcraft_pro_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('quizcraft_pro_nonce')
        ));
    }

    public function display_quiz($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts, 'quizcraft_quiz');

        $quiz_id = intval($atts['id']);

        if ($quiz_id <= 0) {
            return '<p>' . __('Invalid quiz ID.', 'quizcraft-pro') . '</p>';
        }

        $quiz = $this->get_quiz($quiz_id);

        if (!$quiz) {
            return '<p>' . __('Quiz not found.', 'quizcraft-pro') . '</p>';
        }

        ob_start();
        include QUIZCRAFT_PRO_PLUGIN_DIR . 'public/partials/quizcraft-pro-public-display.php';
        return ob_get_clean();
    }

    private function get_quiz($quiz_id) {
        global $wpdb;
        $quiz_table = $wpdb->prefix . 'quizcraft_quizzes';
        $question_table = $wpdb->prefix . 'quizcraft_questions';
        $answer_table = $wpdb->prefix . 'quizcraft_answers';

        $quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM $quiz_table WHERE id = %d", $quiz_id), ARRAY_A);

        if (!$quiz) {
            return null;
        }

        $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM $question_table WHERE quiz_id = %d ORDER BY order_num", $quiz_id), ARRAY_A);

        foreach ($questions as &$question) {
            $question['answers'] = $wpdb->get_results($wpdb->prepare("SELECT * FROM $answer_table WHERE question_id = %d ORDER BY order_num", $question['id']), ARRAY_A);
        }

        $quiz['questions'] = $questions;

        return $quiz;
    }

    public function ajax_submit_quiz() {
        check_ajax_referer('quizcraft_pro_nonce', 'nonce');

        $quiz_id = intval($_POST['quiz_id']);
        $answers = isset($_POST['answers']) ? $_POST['answers'] : array();

        $quiz = $this->get_quiz($quiz_id);

        if (!$quiz) {
            wp_send_json_error('Quiz not found');
        }

        $recommendation = $this->get_recommendation($quiz, $answers);

        wp_send_json_success(array(
            'recommendation' => $recommendation
        ));
    }

    private function get_recommendation($quiz, $answers) {
        // For now, we'll just return a default recommendation
        // In a future implementation, this method would contain the logic to determine the appropriate recommendation based on the answers
        return array(
            'title' => 'Default Recommendation',
            'description' => 'This is a default recommendation. In a full implementation, this would be based on the quiz answers.'
        );
    }
}