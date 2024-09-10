<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Drop custom tables
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}quizcraft_quizzes");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}quizcraft_questions");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}quizcraft_answers");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}quizcraft_recommendations");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}quizcraft_recommendation_rules");

// Delete any options or user meta if needed
// delete_option('quizcraft_pro_option');
// delete_metadata('user', 0, 'quizcraft_pro_user_meta', '', true);