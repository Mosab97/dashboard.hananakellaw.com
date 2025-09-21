import RoutesManager from "../core/RoutesManager";

class PreviewManager {
    constructor(programId, questionManager) {
        this.programId = programId;
        this.questionManager = questionManager;
        this.routesManager = new RoutesManager(
            window.appData.routes,
            programId
        );
        this.fieldTypes = window.appData.fieldTypes;
        this.pages = [];
        this.currentPreviewPageIndex = 0;
    }

    loadFormPreview() {
        $.ajax({
            url: this.routesManager.getUrl("pages"),
            method: "GET",
            success: (pages) => {
                this.pages = pages; // Store pages data
                this.showFormPreviewModal();
            },
            error: (xhr) => {
                toastr.error("Error loading form preview");
            },
        });
    }

    showFormPreviewModal() {
        // Create modal content
        const modalContent = `
            <div class="modal fade" id="formPreviewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Form Preview</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="previewForm">
                                <div class="stepper stepper-pills">
                                    <div class="stepper-nav mb-5">
                                        ${this.pages
                                            .map(
                                                (page, index) => `
                                            <div class="stepper-item me-5 ${
                                                index === 0 ? "current" : ""
                                            }"
                                                 data-kt-stepper-element="nav"
                                                 data-preview-page="${index}">
                                                <div class="stepper-icon">${
                                                    index + 1
                                                }</div>
                                                <div class="stepper-label">${
                                                    page.title?.en || "Untitled"
                                                }</div>
                                            </div>
                                        `
                                            )
                                            .join("")}
                                    </div>

                                    <div class="stepper-content">
                                        ${this.pages
                                            .map(
                                                (page, index) => `
                                            <div class="preview-page" id="previewPage${index}"
                                                 ${
                                                     index === 0
                                                         ? ""
                                                         : 'style="display: none;"'
                                                 }>
                                                <h3 class="mb-5">${
                                                    page.title?.en || "Untitled"
                                                }</h3>
                                                <div class="questions-container" id="previewQuestions${index}">
                                                    Loading questions...
                                                </div>
                                            </div>
                                        `
                                            )
                                            .join("")}
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>`;

        // Remove existing modal if any
        $("#formPreviewModal").remove();

        // Add modal to body and show it
        $("body").append(modalContent);
        const modal = new bootstrap.Modal(
            document.getElementById("formPreviewModal")
        );
        modal.show();

        // Load questions for first page immediately
        if (this.pages.length > 0) {
            this.loadPreviewPageQuestions(this.pages[0], 0);
        }

        // Handle page navigation
        $(document).on("click", ".stepper-item[data-preview-page]", (e) => {
            const pageIndex = parseInt($(e.currentTarget).data("preview-page"));
            this.switchPreviewPage(pageIndex);
        });

        // Clean up when modal is hidden
        $("#formPreviewModal").on("hidden.bs.modal", () => {
            $(document).off("click", ".stepper-item[data-preview-page]");
            $("#formPreviewModal").remove();
        });
    }

    switchPreviewPage(pageIndex) {
        if (!this.pages[pageIndex]) return;

        // Update stepper UI
        $(".stepper-item[data-preview-page]").removeClass("current");
        $(`.stepper-item[data-preview-page="${pageIndex}"]`).addClass(
            "current"
        );

        // Show selected page content
        $(".preview-page").hide();
        $(`#previewPage${pageIndex}`).show();

        // Load questions if not already loaded
        this.loadPreviewPageQuestions(this.pages[pageIndex], pageIndex);
    }

    loadPreviewPageQuestions(page, pageIndex) {
        if (!page || !page.id) return;

        $.ajax({
            url: this.routesManager.getUrl("pageContent", { pageId: page.id }),
            method: "GET",
            success: (response) => {
                if (response.success) {
                    this.renderPreviewQuestions(
                        response.data.questions,
                        pageIndex
                    );
                }
            },
            error: (xhr) => {
                $(`#previewQuestions${pageIndex}`).html(
                    "Error loading questions"
                );
            },
        });
    }

    renderPreviewQuestions(questions, pageIndex) {
        const questionsHtml = questions
            .map((question) => {
                let inputHtml = "";
                const fieldType = this.fieldTypes.find(
                    (type) => type.id === question.field_type_id
                );

                if (!fieldType) {
                    console.error(
                        "Field type not found:",
                        question.field_type_id
                    );
                    return "";
                }

                switch (fieldType.value) {
                    case "dropdown":
                        inputHtml = `
                        <select class="form-select" ${
                            question.required ? "required" : ""
                        }>
                            <option value="">Select an option</option>
                            ${question.options
                                .map(
                                    (option) => `
                                <option value="${option.id}">${option.title.en}</option>
                            `
                                )
                                .join("")}
                        </select>`;
                        break;

                    case "checkbox":
                        inputHtml = question.options
                            .map(
                                (option) => `
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input"
                                   name="question_${question.id}[]"
                                   value="${option.id}">
                            <label class="form-check-label">${option.title.en}</label>
                        </div>
                    `
                            )
                            .join("");
                        break;

                    case "radio":
                    case "tags":
                        inputHtml = question.options
                            .map(
                                (option) => `
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input"
                                   name="question_${question.id}"
                                   value="${option.id}">
                            <label class="form-check-label">${option.title.en}</label>
                        </div>
                    `
                            )
                            .join("");
                        break;

                    case "file":
                        const fileConfig = question.file_config || {
                            max_size: 5,
                            allowed_types: [
                                "image/jpeg",
                                "image/png",
                                "application/pdf",
                            ],
                        };

                        const allowedExtensions = fileConfig.allowed_types
                            .map((type) => `.${type.split("/")[1]}`)
                            .join(",");

                        inputHtml = `
                        <div class="input-group">
                            <input type="file"
                                   class="form-control"
                                   name="question_${question.id}_file"
                                   id="question_${question.id}_file"
                                   ${question.required ? "required" : ""}
                                   accept="${allowedExtensions}">
                            <label class="input-group-text" for="question_${
                                question.id
                            }_file">
                                <i class="fas fa-upload"></i>
                            </label>
                        </div>
                        <div class="form-text">
                            Maximum file size: ${fileConfig.max_size}MB<br>
                            Accepted files: ${fileConfig.allowed_types
                                .map((type) => type.split("/")[1].toUpperCase())
                                .join(", ")}
                        </div>`;
                        break;

                    default:
                        inputHtml = `
                        <input type="text" class="form-control"
                               ${question.required ? "required" : ""}>`;
                }

                return `
                <div class="mb-5">
                    <label class="form-label">
                        ${question.question.en}
                        ${
                            question.required
                                ? '<span class="text-danger">*</span>'
                                : ""
                        }
                    </label>
                    ${inputHtml}
                </div>`;
            })
            .join("");

        $(`#previewQuestions${pageIndex}`).html(
            questionsHtml || "No questions available"
        );

        // Initialize file input event listeners
        questions.forEach((question) => {
            const fieldType = this.fieldTypes.find(
                (type) => type.id === question.field_type_id
            );
            if (fieldType?.value === "file") {
                $(`#question_${question.id}_file`).on("change", function () {
                    const fileName = this.files[0]?.name;
                    if (fileName) {
                        const fileSize = this.files[0].size;
                        const maxSize =
                            (question.file_config?.max_size || 5) * 1024 * 1024; // Convert MB to bytes

                        if (fileSize > maxSize) {
                            toastr.error(
                                `File size should not exceed ${
                                    question.file_config?.max_size || 5
                                }MB`
                            );
                            this.value = "";
                            return;
                        }

                        // Validate file type
                        const fileType = this.files[0].type;
                        const allowedTypes =
                            question.file_config?.allowed_types || [];
                        if (!allowedTypes.includes(fileType)) {
                            toastr.error("File type not allowed");
                            this.value = "";
                            return;
                        }
                    }
                });
            }
        });
    }
}

export default PreviewManager;
