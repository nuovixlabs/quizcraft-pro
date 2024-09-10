<?php

class QuizCraft_Pro_i18n {

    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'quizcraft-pro',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}