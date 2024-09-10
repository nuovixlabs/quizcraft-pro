<?php

class QuizCraft_Pro_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, QUIZCRAFT_PRO_PLUGIN_URL . 'admin/css/quizcraft-pro-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, QUIZCRAFT_PRO_PLUGIN_URL . 'admin/js/quizcraft-pro-admin.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'quizcraft_pro_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('quizcraft_pro_nonce')
        ));
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            'QuizCraft Pro', 
            'QuizCraft Pro', 
            'manage_options', 
            $this->plugin_name, 
            array($this, 'display_plugin_setup_page'),
            'dashicons-clipboard',
            6
        );

        add_submenu_page(
            $this->plugin_name,
            'Quizzes',
            'Quizzes',
            'manage_options',
            $this->plugin_name . '-quizzes',
            array($this, 'display_quizzes_page')
        );

        add_submenu_page(
            $this->plugin_name,
            'Recommendations',
            'Recommendations',
            'manage_options',
            $this->plugin_name . '-recommendations',
            array($this, 'display_recommendations_page')
        );
    }

    public function display_plugin_setup_page() {
        include_once 'partials/quizcraft-pro-admin-display.php';
    }

    public function display_quizzes_page() {
        include_once 'partials/quizcraft-pro-admin-quizzes.php';
    }

    public function display_recommendations_page() {
        include_once 'partials/quizcraft-pro-admin-recommendations.php';
    }

    public function ajax_save_quiz() {
        check_ajax_referer('quizcraft_pro_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $quiz_data = json_decode(stripslashes($_POST['quiz_data']), true);

        global $wpdb;

        $wpdb->query('START TRANSACTION');

        try {
            // Save or update quiz
            $quiz_table = $wpdb->prefix . 'quizcraft_quizzes';
            $quiz = array(
                'title' => sanitize_text_field($quiz_data['title']),
                'description' => sanitize_textarea_field($quiz_data['description'])
            );

            if (isset($quiz_data['id']) && $quiz_data['id'] > 0) {
                $wpdb->update($quiz_table, $quiz, array('id' => $quiz_data['id']));
                $quiz_id = $quiz_data['id'];
            } else {
                $wpdb->insert($quiz_table, $quiz);
                $quiz_id = $wpdb->insert_id;
            }

            // Delete existing questions and answers
            $question_table = $wpdb->prefix . 'quizcraft_questions';
            $answer_table = $wpdb->prefix . 'quizcraft_answers';
            $wpdb->delete($question_table, array('quiz_id' => $quiz_id));

            // Save new questions and answers
            foreach ($quiz_data['questions'] as $q_index => $question) {
                $wpdb->insert($question_table, array(
                    'quiz_id' => $quiz_id,
                    'question' => sanitize_text_field($question['text']),
                    'order_num' => $q_index
                ));
                $question_id = $wpdb->insert_id;

                foreach ($question['answers'] as $a_index => $answer) {
                    $wpdb->insert($answer_table, array(
                        'question_id' => $question_id,
                        'answer' => sanitize_text_field($answer['text']),
                        'order_num' => $a_index
                    ));
                }
            }

            $wpdb->query('COMMIT');
            wp_send_json_success(array('quiz_id' => $quiz_id));
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            wp_send_json_error('Error saving quiz: ' . $e->getMessage());
        }
    }

    public function ajax_get_quiz() {
        check_ajax_referer('quizcraft_pro_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $quiz_id = intval($_POST['quiz_id']);

        global $wpdb;
        $quiz_table = $wpdb->prefix . 'quizcraft_quizzes';
        $question_table = $wpdb->prefix . 'quizcraft_questions';
        $answer_table = $wpdb->prefix . 'quizcraft_answers';

        $quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM $quiz_table WHERE id = %d", $quiz_id), ARRAY_A);

        if (!$quiz) {
            wp_send_json_error('Quiz not found');
        }

        $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM $question_table WHERE quiz_id = %d ORDER BY order_num", $quiz_id), ARRAY_A);

        foreach ($questions as &$question) {
            $question['answers'] = $wpdb->get_results($wpdb->prepare("SELECT * FROM $answer_table WHERE question_id = %d ORDER BY order_num", $question['id']), ARRAY_A);
        }

        $quiz['questions'] = $questions;

        wp_send_json_success($quiz);
    }

    public function ajax_delete_quiz() {
        check_ajax_referer('quizcraft_pro_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $quiz_id = intval($_POST['quiz_id']);

        global $wpdb;
        $quiz_table = $wpdb->prefix . 'quizcraft_quizzes';
        $question_table = $wpdb->prefix . 'quizcraft_questions';
        $answer_table = $wpdb->prefix . 'quizcraft_answers';

        $wpdb->query('START TRANSACTION');

        try {
            // Delete answers
            $wpdb->query($wpdb->prepare("DELETE a FROM $answer_table a INNER JOIN $question_table q ON a.question_id = q.id WHERE q.quiz_id = %d", $quiz_id));

            // Delete questions
            $wpdb->delete($question_table, array('quiz_id' => $quiz_id));

            // Delete quiz
            $wpdb->delete($quiz_table, array('id' => $quiz_id));

            $wpdb->query('COMMIT');
            wp_send_json_success();
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            wp_send_json_error('Error deleting quiz: ' . $e->getMessage());
        }
    }
}