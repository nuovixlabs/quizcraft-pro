(function ($) {
  "use strict";

  $(document).ready(function () {
    $("#quizcraft-pro-form").on("submit", function (e) {
      e.preventDefault();

      var $form = $(this);
      var $quizContainer = $form.closest(".quizcraft-pro-quiz");
      var quizId = $quizContainer.data("quiz-id");
      var answers = {};

      $form.find('input[type="radio"]:checked').each(function () {
        var name = $(this).attr("name");
        var questionId = name.replace("question_", "");
        answers[questionId] = $(this).val();
      });

      $.ajax({
        url: quizcraft_pro_ajax.ajax_url,
        type: "POST",
        data: {
          action: "quizcraft_submit_quiz",
          nonce: quizcraft_pro_ajax.nonce,
          quiz_id: quizId,
          answers: answers,
        },
        success: function (response) {
          if (response.success) {
            var recommendation = response.data.recommendation;
            var resultHtml = "<h3>" + recommendation.title + "</h3>";
            resultHtml += "<p>" + recommendation.description + "</p>";
            $("#quizcraft-result").html(resultHtml).show();
            $form.hide();
          } else {
            alert("Error submitting quiz: " + response.data);
          }
        },
        error: function () {
          alert("An error occurred while submitting the quiz.");
        },
      });
    });
  });
})(jQuery);
