(function ($) {
  "use strict";

  $(document).ready(function () {
    var $quizForm = $("#quizcraft-quiz-form");
    var $questionsContainer = $("#questions-container");

    function addQuestion(question = null) {
      var questionIndex = $questionsContainer.children().length;
      var $question = $('<div class="question">').appendTo($questionsContainer);
      $question.append("<h3>Question " + (questionIndex + 1) + "</h3>");
      $question.append(
        '<input type="text" name="questions[' +
          questionIndex +
          '][text]" placeholder="Enter question" required>'
      );
      var $answersContainer = $('<div class="answers-container">').appendTo(
        $question
      );
      $question.append(
        '<button type="button" class="add-answer button">Add Answer</button>'
      );
      $question.append(
        '<button type="button" class="remove-question button">Remove Question</button>'
      );

      if (question) {
        $question
          .find('input[name^="questions"][name$="[text]"]')
          .val(question.question);
        if (question.answers) {
          question.answers.forEach(function (answer) {
            addAnswer($answersContainer, answer.answer);
          });
        }
      } else {
        addAnswer($answersContainer);
        addAnswer($answersContainer);
      }
    }

    function addAnswer($container, answerText = "") {
      var answerIndex = $container.children().length;
      var $answer = $('<div class="answer">').appendTo($container);
      $answer.append(
        '<input type="text" name="questions[' +
          $container.parent().index() +
          "][answers][" +
          answerIndex +
          '][text]" placeholder="Enter answer" required>'
      );
      $answer.append(
        '<button type="button" class="remove-answer button">Remove</button>'
      );
      $answer.find("input").val(answerText);
    }

    $("#add-question").on("click", function () {
      addQuestion();
    });

    $questionsContainer.on("click", ".add-answer", function () {
      addAnswer($(this).siblings(".answers-container"));
    });

    $questionsContainer.on("click", ".remove-question", function () {
      $(this).parent().remove();
      updateQuestionNumbers();
    });

    $questionsContainer.on("click", ".remove-answer", function () {
      $(this).parent().remove();
    });

    function updateQuestionNumbers() {
      $questionsContainer.find(".question").each(function (index) {
        $(this)
          .find("h3")
          .text("Question " + (index + 1));
      });
    }

    $quizForm.on("submit", function (e) {
      e.preventDefault();
      var quizData = {
        id: $("#quiz-id").val(),
        title: $("#quiz-title").val(),
        description: $("#quiz-description").val(),
        questions: [],
      };

      $(".question").each(function () {
        var question = {
          text: $(this).find('input[name^="questions"][name$="[text]"]').val(),
          answers: [],
        };

        $(this)
          .find(".answer input")
          .each(function () {
            question.answers.push({
              text: $(this).val(),
            });
          });

        quizData.questions.push(question);
      });

      $.ajax({
        url: quizcraft_pro_ajax.ajax_url,
        type: "POST",
        data: {
          action: "quizcraft_save_quiz",
          nonce: quizcraft_pro_ajax.nonce,
          quiz_data: JSON.stringify(quizData),
        },
        success: function (response) {
          if (response.success) {
            alert("Quiz saved successfully!");
            location.reload();
          } else {
            alert("Error saving quiz: " + response.data);
          }
        },
        error: function () {
          alert("An error occurred while saving the quiz.");
        },
      });
    });

    $(".edit-quiz").on("click", function () {
      var quizId = $(this).data("id");
      $.ajax({
        url: quizcraft_pro_ajax.ajax_url,
        type: "POST",
        data: {
          action: "quizcraft_get_quiz",
          nonce: quizcraft_pro_ajax.nonce,
          quiz_id: quizId,
        },
        success: function (response) {
          if (response.success) {
            var quiz = response.data;
            $("#quiz-id").val(quiz.id);
            $("#quiz-title").val(quiz.title);
            $("#quiz-description").val(quiz.description);
            $questionsContainer.empty();
            quiz.questions.forEach(function (question) {
              addQuestion(question);
            });
          } else {
            alert("Error loading quiz: " + response.data);
          }
        },
        error: function () {
          alert("An error occurred while loading the quiz.");
        },
      });
    });

    $(".delete-quiz").on("click", function () {
      if (confirm("Are you sure you want to delete this quiz?")) {
        var quizId = $(this).data("id");
        $.ajax({
          url: quizcraft_pro_ajax.ajax_url,
          type: "POST",
          data: {
            action: "quizcraft_delete_quiz",
            nonce: quizcraft_pro_ajax.nonce,
            quiz_id: quizId,
          },
          success: function (response) {
            if (response.success) {
              alert("Quiz deleted successfully!");
              location.reload();
            } else {
              alert("Error deleting quiz: " + response.data);
            }
          },
          error: function () {
            alert("An error occurred while deleting the quiz.");
          },
        });
      }
    });
  });
})(jQuery);
