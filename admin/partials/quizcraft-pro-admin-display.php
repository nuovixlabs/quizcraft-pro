<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <p><?php _e('Welcome to QuizCraft Pro! Here you can manage your quizzes and recommendations.', 'quizcraft-pro'); ?></p>
    <h2><?php _e('Quick Links', 'quizcraft-pro'); ?></h2>
    <ul>
        <li><a href="<?php echo admin_url('admin.php?page=' . $this->plugin_name . '-quizzes'); ?>"><?php _e('Manage Quizzes', 'quizcraft-pro'); ?></a></li>
        <li><a href="<?php echo admin_url('admin.php?page=' . $this->plugin_name . '-recommendations'); ?>"><?php _e('Manage Recommendations', 'quizcraft-pro'); ?></a></li>
    </ul>
</div>