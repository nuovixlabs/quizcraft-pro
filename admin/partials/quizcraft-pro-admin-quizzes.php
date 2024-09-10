<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div id="quizcraft-quiz-list">
        <h2><?php _e('Existing Quizzes', 'quizcraft-pro'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'quizcraft-pro'); ?></th>
                    <th><?php _e('Title', 'quizcraft-pro'); ?></th>
                    <th><?php _e('Actions', 'quizcraft-pro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $quizzes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}quizcraft_quizzes ORDER BY id DESC");
                foreach ($quizzes as $quiz) :
                ?>
                <tr>
                    <td><?php echo esc_html($quiz->id); ?></td>
                    <td><?php echo esc_html($quiz->title); ?></td>
                    <td>
                        <button class="button edit-quiz" data-id="<?php echo esc_attr($quiz->id); ?>"><?php _e('Edit', 'quizcraft-pro'); ?></button>
                        <button class="button delete-quiz" data-id="<?php echo esc_attr($quiz->id); ?>"><?php _e('Delete', 'quizcraft-pro'); ?></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div id="quizcraft-quiz-editor">
        <h2><?php _e('Quiz Editor', 'quizcraft-pro'); ?></h2>
        <form id="quizcraft-quiz-form">
            <input type="hidden" id="quiz-id" name="quiz_id" value="">
            <p>
                <label for="quiz-title"><?php _e('Quiz Title', 'quizcraft-pro'); ?></label>
                <input type="text" id="quiz-title" name="quiz_title" required>
            </p>
            <p>
                <label for="quiz-description"><?php _e('Quiz Description', 'quizcraft-pro'); ?></label>
                <textarea id="quiz-description" name="quiz_description"></textarea>
            </p>
            <div id="questions-container"></div>
            <button type="button" id="add-question" class="button"><?php _e('Add Question', 'quizcraft-pro'); ?></button>
            <p>
                <input type="submit" class="button button-primary" value="<?php _e('Save Quiz', 'quizcraft-pro'); ?>">
            </p>
        </form>
    </div>
</div>