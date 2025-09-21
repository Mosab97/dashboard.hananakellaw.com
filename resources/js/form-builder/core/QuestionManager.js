class QuestionManager {
    constructor(optionHandler) {
        this.optionHandler = optionHandler;
        this.fieldTypes = window.appData.fieldTypes;
        this.translations = window.appData.translations;

        if (!$.fn.sortable) {
            console.warn(
                "jQuery UI sortable is not loaded. Drag and drop functionality will be disabled."
            );
        }
        this.initEventListeners();
    }

    // Add a translate helper method
    t(key) {
        return this.translations[key] || key;
    }

    generateFieldTypeOptions(selectedTypeId) {
        // console.log("Starting generateFieldTypeOptions:", {
        //     selectedTypeId: selectedTypeId,
        //     fieldTypes: this.fieldTypes,
        // });

        if (!this.fieldTypes) {
            console.error(
                "Field types not loaded - fieldTypes is:",
                this.fieldTypes
            );
            return "";
        }

        const currentLocale = document.documentElement.lang;
        // console.log("Current locale:", currentLocale);

        const options = this.fieldTypes.map((type) => {
            // console.log("Processing field type:", {
            //     id: type.id,
            //     rawName: type.name,
            //     selectedTypeId: selectedTypeId,
            //     isSelected: type.id === selectedTypeId,
            // });

            // Log the name resolution process
            const localizedName = type.name?.[currentLocale];
            const englishName = type.name?.en;
            const finalName = localizedName || englishName || "";

            // console.log("Name resolution for type", type.id, ":", {
            //     localizedName,
            //     englishName,
            //     finalName,
            // });

            const option = `<option value="${type.id}" ${
                type.id === selectedTypeId ? "selected" : ""
            }>${finalName}</option>`;

            // console.log("Generated option HTML:", option);
            return option;
        });

        const finalHTML = options.join("");
        // console.log("Final generated HTML:", finalHTML);

        return finalHTML;
    }
    initEventListeners() {
        $(document).on("click", "#addQuestion", () => this.addQuestion());
        $(document).on("click", ".delete-question", function () {
            $(this).closest(".question-card").remove();
        });

        // Add Option button event listener with logging
        $(document).on("click", ".add-option", (e) => {
            // Capture the translations from the window object or pass them in
            const translations = window.appData.translations;

            const $optionsList = $(e.currentTarget)
                .closest(".options-section")
                .find(".options-list");

            const optionTemplate = `
                <div class="option-item mb-3 bg-light-primary rounded p-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0">
                            <i class="fas fa-grip-vertical text-gray-500"></i>
                        </span>
                        <input type="text"
                               class="form-control option-text-en me-2"
                               placeholder="${
                                   translations["Option"] || "Option"
                               } (${translations["English"] || "English"})">
                        <input type="text"
                               class="form-control option-text-ar me-2"
                               placeholder="${
                                   translations["Option"] || "Option"
                               } (${translations["Arabic"] || "Arabic"})"
                               dir="rtl">
                        <input type="number"
                               class="form-control option-score"
                               placeholder="${translations["Score"] || "Score"}"
                               style="max-width: 100px;">
                        <button class="btn btn-icon btn-sm btn-light-danger delete-option ms-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            $optionsList.append(optionTemplate);
        });

        $(document).on("click", ".delete-option", function () {
            $(this).closest(".option-item").remove();
        });

        // Field type change event
        $(document).on("change", ".field-type", (e) =>
            this.handleFieldTypeChange(e)
        );

        // File config change listeners
        $(document).on("change", ".file-max-size, .file-types", (e) => {
            const $card = $(e.target).closest(".question-card");
            const $fileConfig = $card.find(".file-config-section");
            const fileConfig = {
                max_size:
                    parseInt($fileConfig.find(".file-max-size").val()) || 5,
                allowed_types: $fileConfig.find(".file-types").val() || [],
            };

            // Store the updated config in the data attribute
            $card.attr("data-file-config", JSON.stringify(fileConfig));
            console.log(
                "Updated file config for question:",
                $card.data("question-id"),
                fileConfig
            );
        });
    }

    shouldShowOptions(fieldTypeId) {
        const fieldType = this.fieldTypes.find(
            (type) => type.id === parseInt(fieldTypeId)
        );
        return fieldType?.has_options || false;
    }

    hasFileConfig(fieldTypeId) {
        const fieldType = this.fieldTypes.find(
            (type) => type.id === parseInt(fieldTypeId)
        );
        return fieldType?.has_file_config || false;
    }

    renderQuestion(question) {
        const accordionId = `accordion_${question.id || "new_" + Date.now()}`;
        // Properly escape the JSON string for data attribute
        const fileConfigAttr = question.file_config
            ? ` data-file-config='${JSON.stringify(
                  question.file_config
              ).replace(/'/g, "&apos;")}'`
            : "";
        const questionTemplate = `
            <div class="accordion-item question-card mb-5 border-0"              data-question-id="${
                question.id || ""
            }"${fileConfigAttr}>

                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#${accordionId}">
                        <div class="d-flex align-items-center gap-4 w-100">
                            <div class="question-preview">
                                ${question.question?.en || "New Question"}
                            </div>
                            <div class="ms-auto d-flex align-items-center">
                                <span class="badge bg-light-primary me-2">
                                    ${
                                        this.fieldTypes.find(
                                            (t) =>
                                                t.id === question.field_type_id
                                        )?.name || "Unknown Type"
                                    }
                                </span>
                                ${
                                    question.required
                                        ? '<span class="badge bg-light-warning">Required</span>'
                                        : ""
                                }
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="${accordionId}" class="accordion-collapse collapse show">
                    <div class="accordion-body p-8">
                        <div class="card-header py-4 bg-light d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-4 w-100">
                                <div class="position-relative w-25">
                                    <label class="form-label fw-bold mb-2">${this.t(
                                        "Field Type"
                                    )}</label>
                                    <select class="form-select field-type">
                                        ${this.fieldTypes
                                            .map(
                                                (type) => `
                                            <option value="${type.id}" ${
                                                    type.id ===
                                                    question.field_type_id
                                                        ? "selected"
                                                        : ""
                                                }>
                                                ${
                                                    type.name?.en ||
                                                    type.name ||
                                                    ""
                                                }
                                            </option>
                                        `
                                            )
                                            .join("")}
                                    </select>
                                </div>

                                <div class="form-check ms-auto">
                                    <input type="checkbox" class="form-check-input question-required"
                                        ${question.required ? "checked" : ""}>
                                    <label class="form-check-label text-gray-600">
                                        ${this.t("Required Field")}
                                    </label>
                                </div>

                                <button class="btn btn-icon btn-sm btn-light-danger delete-question ms-2">
                                    <i class="fas fa-trash fs-5"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-8">
                            <div class="row g-5 mb-8">
                                <div class="col-md-6">
                                    <label class="form-label required fw-semibold">
                                        ${this.t("Question")} (${this.t(
            "English"
        )})
                                    </label>
                                    <input type="text" class="form-control question-en"
                                        value="${question.question?.en || ""}"
                                        placeholder="${this.t(
                                            "Enter question in English"
                                        )}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fw-semibold">
                                        ${this.t("Question")} (${this.t(
            "Arabic"
        )})
                                    </label>
                                    <input type="text" class="form-control question-ar"
                                        value="${question.question?.ar || ""}"
                                        placeholder="${this.t(
                                            "Enter question in Arabic"
                                        )}"
                                        dir="rtl">
                                </div>
                            </div>
 ${
     this.shouldShowOptions(question.field_type_id)
         ? this.renderOptionsSection(question)
         : this.hasFileConfig(question.field_type_id)
         ? this.renderFileConfigSection(question)
         : ""
 }
                        </div>
                    </div>
                </div>
            </div>
        `;

        $("#questionsContainer").append(questionTemplate);

        // Initialize question controls
        const $newQuestion = $(`[data-question-id="${question.id}"]`);
        this.initializeQuestionControls(question.id);

        // Add event listener to update preview when question changes
        $newQuestion.find(".question-en").on("input", function () {
            const previewText = $(this).val() || "New Question";
            $(this)
                .closest(".question-card")
                .find(".question-preview")
                .text(previewText);
        });

        // Add event listener to update required badge
        $newQuestion.find(".question-required").on("change", function () {
            const $badge = $(this)
                .closest(".question-card")
                .find(".badge.bg-light-warning");
            if (this.checked) {
                if ($badge.length === 0) {
                    $(this)
                        .closest(".question-card")
                        .find(".ms-auto")
                        .append(
                            '<span class="badge bg-light-warning ms-2">Required</span>'
                        );
                }
            } else {
                $badge.remove();
            }
        });
    }
    renderOptionsSection(question) {
        console.log("renderOptionsSection", question);

        let optionsHtml = "";
        if (
            question.options &&
            (Array.isArray(question.options.en) ||
                Array.isArray(question.options.ar))
        ) {
            optionsHtml = (question.options.en || [])
                .map(
                    (option, index) => `
                <div class="option-item mb-3 bg-light-primary rounded p-3">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0">
                            <i class="fas fa-grip-vertical text-gray-500"></i>
                        </span>
                        <input type="text"
                               class="form-control option-text-en me-2"
                               value="${option || ""}"
                               placeholder="${this.t("Option")} (${this.t(
                        "English"
                    )})">
                        <input type="text"
                               class="form-control option-text-ar me-2"
                               value="${question.options.ar[index] || ""}"
                               placeholder="${this.t("Option")} (${this.t(
                        "Arabic"
                    )})"
                               dir="rtl">
                        <input type="number"
                               class="form-control option-score"
                               value="${
                                   Array.isArray(question.scores)
                                       ? question.scores[index] || 0
                                       : 0
                               }"
                               placeholder="${this.t("Score")}"
                               style="max-width: 100px;">
                        <button class="btn btn-icon btn-sm btn-light-danger delete-option ms-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `
                )
                .join("");
        }

        return `
            <div class="options-section bg-light rounded p-6 mt-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="form-label fw-bold m-0">${this.t(
                        "Options"
                    )}</label>
                    <button type="button" class="btn btn-sm btn-light-primary add-option">
                        <i class="fas fa-plus"></i> ${this.t("Add Option")}
                    </button>
                </div>
                <div class="options-list">
                    ${optionsHtml}
                </div>
            </div>
        `;
    }
    initializeQuestionControls(questionId) {
        try {
            const $card = $(`.question-card[data-question-id="${questionId}"]`);
            const $select = $card.find(".field-type");

            // console.log("Initializing controls for question:", {
            //     questionId,
            //     select: $select[0],
            //     currentValue: $select.val(),
            // });

            // Force set the value if needed
            if (questionId) {
                const selectedType = $select.attr("data-selected");
                if (selectedType) {
                    $select.val(selectedType);
                    // console.log("Forced select value:", {
                    //     selectedType,
                    //     newValue: $select.val(),
                    // });
                }
            }
            const $optionsList = $card.find(".options-list");

            if ($optionsList.length && typeof $.fn.sortable === "function") {
                $optionsList.sortable({
                    handle: ".fa-grip-vertical",
                    axis: "y",
                    update: function (event, ui) {
                        // Handle reordering if needed
                    },
                });
            }
        } catch (error) {
            console.warn("Error initializing question controls:", error);
        }
    }

    renderFileConfigSection(question = {}) {
        console.log("Rendering file config with:", question.file_config); // Debug log

        const fileConfig = question.file_config || {
            max_size: 5,
            allowed_types: ["image/jpeg", "image/png", "application/pdf"],
        };

        return `
            <div class="file-config-section bg-light rounded p-6 mt-8">
                <div class="row g-5">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">${this.t(
                            "Maximum File Size (MB)"
                        )}</label>
                        <input type="number"
                               class="form-control file-max-size"
                               placeholder="${this.t(
                                   "Enter maximum file size in MB"
                               )}"
                               value="${fileConfig.max_size}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">${this.t(
                            "Allowed File Types"
                        )}</label>
                        <select class="form-select file-types" multiple>
                            <option value="image/jpeg" ${
                                fileConfig.allowed_types.includes("image/jpeg")
                                    ? "selected"
                                    : ""
                            }>JPEG/JPG</option>
                            <option value="image/png" ${
                                fileConfig.allowed_types.includes("image/png")
                                    ? "selected"
                                    : ""
                            }>PNG</option>
                            <option value="application/pdf" ${
                                fileConfig.allowed_types.includes(
                                    "application/pdf"
                                )
                                    ? "selected"
                                    : ""
                            }>PDF</option>
                            <option value="application/msword" ${
                                fileConfig.allowed_types.includes(
                                    "application/msword"
                                )
                                    ? "selected"
                                    : ""
                            }>DOC</option>
                            <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                    ${
                                        fileConfig.allowed_types.includes(
                                            "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                        )
                                            ? "selected"
                                            : ""
                                    }>DOCX</option>
                            <option value="application/vnd.ms-excel" ${
                                fileConfig.allowed_types.includes(
                                    "application/vnd.ms-excel"
                                )
                                    ? "selected"
                                    : ""
                            }>XLS</option>
                            <option value="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                    ${
                                        fileConfig.allowed_types.includes(
                                            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                        )
                                            ? "selected"
                                            : ""
                                    }>XLSX</option>
                        </select>
                        <div class="form-text">${this.t(
                            "Hold Ctrl/Cmd to select multiple types"
                        )}</div>
                    </div>
                </div>
            </div>`;
    }

    handleFieldTypeChange(e) {
        const $select = $(e.target);
        const $card = $select.closest(".question-card");
        const fieldTypeId = parseInt($select.val());

        // Update the field type select value
        $card.find(".field-type").val(fieldTypeId);

        // Update the badge in the accordion header
        const selectedFieldTypeName = $select.find("option:selected").text();
        $card.find(".badge.bg-light-primary").text(selectedFieldTypeName);

        // Get existing question data
        const questionId = $card.data("question-id");
        const existingQuestion = {
            id: questionId,
            field_type_id: fieldTypeId,
            file_config: $card.data("file-config"),
        };

        // Handle options section based on field type properties
        if (this.shouldShowOptions(fieldTypeId)) {
            if ($card.find(".options-section").length === 0) {
                $card.find(".card-body").append(
                    this.renderOptionsSection({
                        options: { en: [], ar: [] },
                        scores: [],
                    })
                );
            }
            $card.find(".options-section").show();
            $card.find(".file-config-section").hide();
        } else if (this.hasFileConfig(fieldTypeId)) {
            $card.find(".options-section").hide();
            if ($card.find(".file-config-section").length === 0) {
                $card
                    .find(".card-body")
                    .append(this.renderFileConfigSection(existingQuestion));
            }
            $card.find(".file-config-section").show();
        } else {
            $card.find(".options-section").hide();
            $card.find(".file-config-section").hide();
        }
    }

    collectQuestions() {
        const questions = [];

        $(".question-card").each((index, card) => {
            const $card = $(card);
            const fieldTypeId = parseInt($card.find(".field-type").val());

            const questionData = {
                id: $card.data("question-id"),
                field_type_id: fieldTypeId,
                question: {
                    en: $card.find(".question-en").val().trim(),
                    ar: $card.find(".question-ar").val().trim(),
                },
                required: $card.find(".question-required").prop("checked"),
                order: index,
                options: {
                    en: [],
                    ar: [],
                },
                scores: [],
            };

            // Add file configuration if it's a file type question
            if (this.hasFileConfig(fieldTypeId)) {
                const $fileConfig = $card.find(".file-config-section");
                const maxSize =
                    parseInt($fileConfig.find(".file-max-size").val()) || 5;
                const allowedTypes =
                    $fileConfig.find(".file-types").val() || [];

                questionData.file_config = {
                    max_size: maxSize,
                    allowed_types: allowedTypes,
                };

                $card.attr(
                    "data-file-config",
                    JSON.stringify(questionData.file_config)
                );
            }

            // Collect options for field types that support them
            if (this.shouldShowOptions(fieldTypeId)) {
                $card.find(".option-item").each((_, optionItem) => {
                    const $item = $(optionItem);
                    const enText = $item.find(".option-text-en").val().trim();
                    const arText = $item.find(".option-text-ar").val().trim();
                    const score =
                        parseInt($item.find(".option-score").val()) || 0;

                    if (enText || arText) {
                        questionData.options.en.push(enText);
                        questionData.options.ar.push(arText);
                        questionData.scores.push(score);
                    }
                });
            }

            questions.push(questionData);
        });

        return questions;
    }
    addQuestion() {
        // Create a new empty question object with default values
        const newQuestion = {
            id: "new_" + Date.now(), // Temporary ID for new questions
            field_type_id: this.fieldTypes[0]?.id, // Default to first field type
            question: {
                en: "",
                ar: "",
            },
            required: false,
            order: $(".accordion-item.question-card").length, // Order based on current questions count
            options: {
                en: [],
                ar: [],
            },
            scores: [],
        };

        // Use the same renderQuestion method to maintain consistency
        this.renderQuestion(newQuestion);

        // Find the newly added question
        const $newQuestion = $(
            `.accordion-item[data-question-id="${newQuestion.id}"]`
        );

        // Auto show the accordion
        const $accordionCollapse = $newQuestion.find(".accordion-collapse");
        $accordionCollapse.addClass("show");

        // Focus on the English question input
        $newQuestion.find(".question-en").focus();
    }

    handleQuestionOptions(question, $card) {
        const $optionsContainer = $card.find(".options-container");
        const $optionsList = $card.find(".options-list");

        if (
            question.options &&
            (Array.isArray(question.options.en) ||
                Array.isArray(question.options.ar))
        ) {
            $optionsContainer.removeClass("d-none");
            this.renderOptions(question.options, $optionsList);
        }
    }

    renderOptions(options, $optionsList) {
        const maxOptions = Math.max(
            Array.isArray(options.en) ? options.en.length : 0,
            Array.isArray(options.ar) ? options.ar.length : 0
        );

        for (let i = 0; i < maxOptions; i++) {
            const optionHtml = this.optionHandler.getOptionTemplate();
            const $option = $(optionHtml);

            $option.find(".option-text-en").val(options.en?.[i] || "");
            $option.find(".option-text-ar").val(options.ar?.[i] || "");

            $optionsList.append($option);
        }
    }
}

export default QuestionManager;
