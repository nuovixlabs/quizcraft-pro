<div class="quizcraft-pro-quiz" data-quiz-id="<?php echo esc_attr($quiz['id']); ?>">
    <h2><?php echo esc_html($quiz['title']); ?></h2>
    <p><?php echo esc_html($quiz['description']); ?></p>
    <form id="quizcraft-pro-form">
        <?php foreach ($quiz['questions'] as $index => $question): ?>
            <div class="quizcraft-question">
                <h3><?php echo esc_html($question['question']); ?></h3>
                <?php foreach ($question['answers'] as $answer): ?>
                    <label>
                        <input type="radio" name="question_<?php echo esc_attr($question['id']); ?>" value="<?php echo esc_attr($answer['id']); ?>" required>
                        <?php echo esc_html($answer['answer']); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit"><?php _e('Submit', 'quizcraft-pro'); ?></button>
    </form>
    <div id="quizcraft-result" style="display: none;"></div>
</div>