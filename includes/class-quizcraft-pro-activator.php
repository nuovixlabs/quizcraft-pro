<?php

class QuizCraft_Pro_Activator {

    public static function activate() {
        self::create_tables();
    }

    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = array();

        $sql[] = "CREATE TABLE {$wpdb->prefix}quizcraft_quizzes (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$wpdb->prefix}quizcraft_questions (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            quiz_id mediumint(9) NOT NULL,
            question text NOT NULL,
            order_num int NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY quiz_id (quiz_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$wpdb->prefix}quizcraft_answers (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            question_id mediumint(9) NOT NULL,
            answer text NOT NULL,
            order_num int NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY question_id (question_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$wpdb->prefix}quizcraft_recommendations (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            quiz_id mediumint(9) NOT NULL,
            title varchar(255) NOT NULL,
            description text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY quiz_id (quiz_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$wpdb->prefix}quizcraft_recommendation_rules (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            recommendation_id mediumint(9) NOT NULL,
            question_id mediumint(9) NOT NULL,
            answer_id mediumint(9) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY recommendation_id (recommendation_id),
            KEY question_id (question_id),
            KEY answer_id (answer_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}