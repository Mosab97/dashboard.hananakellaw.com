import "../../../css/form-builder/style.css";
import RoutesManager from "./RoutesManager";
import PageManager from "./PageManager";
import ValidationManager from "../components/ValidationManager";
import AddPageManager from "./AddPageManager";

class FormBuilderCore {
    constructor(programId, questionManager, previewManager) {
        this.programId = programId;
        this.questionManager = questionManager;
        this.previewManager = previewManager;
        this.currentPage = null;
        this.routesManager = new RoutesManager(
            window.appData.routes,
            programId
        );
        this.pageManager = new PageManager(this);
        this.addPageManager = new AddPageManager(this);

        this.initEventListeners();
        this.loadPages();
        this.validationManager = new ValidationManager();
    }

    initEventListeners() {
        $(document).on("click", "#saveForm", () => {
            console.log("Save button clicked");
            this.saveForm();
        });

        $("#showFormPreview").click(() =>
            this.previewManager.loadFormPreview()
        );
    }

    setCurrentPage(page) {
        console.log("Setting current page:", page);
        this.currentPage = page;
        this.pageManager.currentPage = page;
    }

    async saveForm() {
        console.log("Starting saveForm with currentPage:", this.currentPage);

        if (!this.currentPage) {
            console.error("No current page set");
            toastr.error("No page selected to save");
            return;
        }

        // Validate form before submission
        if (!this.validationManager.validateQuestionForm()) {
            toastr.error("Please fix the validation errors before saving");
            return;
        }

        const $submitButton = $("#saveForm");
        $submitButton.prop("disabled", true);

        try {
            const questionsData = this.questionManager.collectQuestions();
            console.log("Collected questions:", questionsData);

            const data = {
                page: {
                    id: this.currentPage.id,
                    title: {
                        en: this.currentPage.title?.en || "",
                        ar: this.currentPage.title?.ar || "",
                    },
                },
                questions: questionsData,
            };

            const response = await $.ajax({
                url: this.routesManager.getUrl("batchSave"),
                method: "POST",
                data: JSON.stringify(data),
                contentType: "application/json",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            if (response.success) {
                this.validationManager.clearErrors(); // Clear any existing errors
                toastr.success("Form saved successfully");
                // this.loadPages();
                // Update the current page with the fresh data from response
                if (response.data && response.data.page) {
                    this.setCurrentPage(response.data.page);
                    // Refresh the current page content without switching pages
                    this.pageManager.refreshCurrentPage(response.data.page);
                }
            }
        } catch (error) {
            console.error("Save error:", error);
            if (error.status === 422) {
                // Handle validation errors
                this.validationManager.handleValidationErrors(error);
            } else {
                toastr.error(
                    error.responseJSON?.message || "Error saving form"
                );
            }
        } finally {
            $submitButton.prop("disabled", false);
        }
    }

    loadPages() {
        $.ajax({
            url: this.routesManager.getUrl("pages"),
            method: "GET",
            success: (response) => {
                this.pageManager.renderPageNumbers(response);
                if (response.length > 0) {
                    // Set the first page as current when loading
                    this.setCurrentPage(response[0]);
                    this.pageManager.loadPageContent(response[0].id);
                }
            },
            error: (xhr) => {
                toastr.error("Error loading pages");
                console.error("Load pages error:", xhr);
            },
        });
    }
}

export default FormBuilderCore;
