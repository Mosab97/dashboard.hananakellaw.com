class Question {
    constructor(data = {}) {
        this.id = data.id || null;
        this.field_type_id = data.field_type_id || null;
        this.question = {
            en: data.question?.en || "",
            ar: data.question?.ar || "",
        };
        this.options = {
            en: data.options?.en || [],
            ar: data.options?.ar || [],
        };
        this.required = data.required || false;
        this.order = data.order || 0;
    }

    toJSON() {
        return {
            id: this.id,
            field_type_id: this.field_type_id,
            question: this.question,
            options: this.options,
            required: this.required,
            order: this.order,
        };
    }

    static fromElement($element) {
        return new Question({
            id: $element.data("question-id"),
            field_type_id: parseInt($element.find(".field-type").val()),
            question: {
                en: $element.find(".question-en").val().trim(),
                ar: $element.find(".question-ar").val().trim(),
            },
            required: $element.find(".question-required").prop("checked"),
            order: $element.index(),
        });
    }
}

export default Question;
