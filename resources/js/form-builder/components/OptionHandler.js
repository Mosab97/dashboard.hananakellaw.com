class OptionHandler {
    constructor(fieldTypes) {
        this.fieldTypes = fieldTypes;
        this.initEventListeners();
    }
    shouldShowOptions(fieldTypeId) {
        const fieldType = this.fieldTypes.find(
            (type) => type.id === parseInt(fieldTypeId)
        );
        return fieldType?.has_options || false;
    }
    initEventListeners() {
        $(document).on("click", ".add-option", (e) => this.handleAddOption(e));
        $(document).on("click", ".delete-option", (e) =>
            this.handleDeleteOption(e)
        );
        $(document).on("change", ".field-type", (e) =>
            this.handleFieldTypeChange(e)
        );
    }

    handleAddOption(e) {
        const $button = $(e.currentTarget);
        const $optionsList = $button.siblings(".options-list");
        this.addNewOption($optionsList);
    }

    handleDeleteOption(e) {
        const $button = $(e.currentTarget);
        const $optionRow = $button.closest(".option-row");
        const $optionsList = $optionRow.closest(".options-list");

        if ($optionsList.children().length > 1) {
            $optionRow.remove();
        } else {
            toastr.warning(
                "At least one option is required for this field type."
            );
        }
    }

    handleFieldTypeChange(e) {
        const $select = $(e.currentTarget);
        const $card = $select.closest(".field-card");
        const $optionsContainer = $card.find(".options-container");
        const fieldTypeId = parseInt($select.val());

        this.toggleOptionsVisibility($optionsContainer, fieldTypeId);
    }
    addNewOption($optionsList) {
        const optionTemplate = this.getOptionTemplate();
        $optionsList.append(optionTemplate);
    }

    toggleOptionsVisibility($container, fieldTypeId) {
        const $optionsList = $container.find(".options-list");

        if (this.shouldShowOptions(fieldTypeId)) {
            $container.removeClass("d-none");
            if ($optionsList.children().length === 0) {
                this.addNewOption($optionsList);
            }
        } else {
            $container.addClass("d-none");
            $optionsList.empty();
        }
    }

    getOptionTemplate() {
        return `
            <div class="input-group mb-3 option-row">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-grip-vertical handle"></i>
                    </span>
                </div>
                <input type="text"
                       class="form-control option-text-en"
                       placeholder="Option in English"
                       required>
                <input type="text"
                       class="form-control option-text-ar"
                       placeholder="الخيار بالعربية"
                       dir="rtl"
                       required>
                <button type="button"
                        class="btn btn-icon btn-light-danger delete-option"
                        title="Remove Option">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }

    collectOptions($questionCard) {
        const options = {
            en: [],
            ar: [],
        };

        $questionCard.find(".option-row").each(function () {
            const enOption = $(this).find(".option-text-en").val().trim();
            const arOption = $(this).find(".option-text-ar").val().trim();

            if (enOption || arOption) {
                options.en.push(enOption);
                options.ar.push(arOption);
            }
        });

        return options;
    }
}

export default OptionHandler;
