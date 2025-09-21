class ValidationManager {
    constructor() {
        this.clearErrors();
        this.fieldTypes = window.appData.fieldTypes;
    }
    clearErrors() {
        // Remove all error classes and messages
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").empty();
    }
    shouldShowOptions(fieldTypeId) {
        const fieldType = this.fieldTypes.find(
            (type) => type.id === parseInt(fieldTypeId)
        );
        return fieldType?.has_options || false;
    }

    handleValidationErrors(error) {
        this.clearErrors();

        if (!error.responseJSON?.errors) {
            toastr.error(error.responseJSON?.message || "An error occurred");
            return;
        }

        const errors = error.responseJSON.errors;

        Object.entries(errors).forEach(([field, messages]) => {
            // Parse the field name to identify the element
            const parts = field.split(".");
            let $element;

            if (parts[0] === "questions") {
                const questionIndex = parts[1];
                const fieldType = parts[3]; // 'en' or 'ar'

                // Find the specific question input based on index
                $element = $(
                    `.question-card:eq(${questionIndex}) .question-${fieldType}`
                );
            } else {
                // Handle other field types if needed
                $element = $(`[name="${field}"]`);
            }

            if ($element?.length) {
                // Add error class and message
                $element.addClass("is-invalid");

                // Create or update feedback element
                let $feedback = $element.siblings(".invalid-feedback");
                if (!$feedback.length) {
                    $feedback = $('<div class="invalid-feedback"></div>');
                    $element.after($feedback);
                }
                $feedback.text(messages[0]);

                // Scroll to the first error
                if (!this.hasScrolled) {
                    $element[0].scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                    this.hasScrolled = true;
                }
            }
        });
    }

    validateQuestionForm() {
        let isValid = true;
        this.clearErrors();

        $(".question-card").each((index, card) => {
            const $card = $(card);
            const $questionEn = $card.find(".question-en");
            const $questionAr = $card.find(".question-ar");
            const fieldTypeId = parseInt($card.find(".field-type").val());

            // Validate required fields
            if (!$questionEn.val().trim()) {
                this.addError(
                    $questionEn,
                    "The English question text is required."
                );
                isValid = false;
            }

            if (!$questionAr.val().trim()) {
                this.addError(
                    $questionAr,
                    "The Arabic question text is required."
                );
                isValid = false;
            }

            // Validate options for questions that support them
            if (this.shouldShowOptions(fieldTypeId)) {
                const $options = $card.find(".option-item");

                if ($options.length === 0) {
                    this.addError(
                        $card.find(".options-section"),
                        "At least one option is required."
                    );
                    isValid = false;
                } else {
                    // Check each option
                    $options.each((_, optionItem) => {
                        const $item = $(optionItem);
                        const $optionEn = $item.find(".option-text-en");
                        const $optionAr = $item.find(".option-text-ar");

                        if (!$optionEn.val().trim()) {
                            this.addError(
                                $optionEn,
                                "Option text in English is required."
                            );
                            isValid = false;
                        }

                        if (!$optionAr.val().trim()) {
                            this.addError(
                                $optionAr,
                                "Option text in Arabic is required."
                            );
                            isValid = false;
                        }

                        // Validate score if needed
                        const $score = $item.find(".option-score");
                        if (
                            $score.length &&
                            $score.val() &&
                            isNaN(parseInt($score.val()))
                        ) {
                            this.addError(
                                $score,
                                "Score must be a valid number."
                            );
                            isValid = false;
                        }
                    });
                }
            }
        });

        return isValid;
    }
    addError($element, message) {
        $element.addClass("is-invalid");
        let $feedback = $element.siblings(".invalid-feedback");
        if (!$feedback.length) {
            $feedback = $('<div class="invalid-feedback"></div>');
            $element.after($feedback);
        }
        $feedback.text(message);
    }
}

export default ValidationManager;
