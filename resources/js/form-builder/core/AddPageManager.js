class AddPageManager {
    constructor(formBuilder) {
        this.formBuilder = formBuilder;
        this.modalId = "addPageModal";
        this.initModal();
        this.initEventListeners();
    }

    initModal() {
        const modalHtml = `
            <div class="modal fade" id="${this.modalId}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="addPageForm">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Page</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-5">
                                    <label class="form-label required">Page Title (English)</label>
                                    <input type="text" class="form-control" name="title[en]" required>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label required">Page Title (Arabic)</label>
                                    <input type="text" class="form-control" name="title[ar]" dir="rtl" required>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label">Page Order</label>
                                    <input type="number" class="form-control" name="order" min="1">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">Save Page</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>`;

        // Remove existing modal if any
        $(`#${this.modalId}`).remove();
        $("body").append(modalHtml);
    }

    initEventListeners() {
        // Handle Add Page button click
        $(document).on("click", "#add_program_page_modal", (e) => {
            e.preventDefault();
            this.showModal();
        });

        // Handle form submission
        $(document).on("submit", "#addPageForm", (e) => {
            e.preventDefault();
            this.handleSubmit(e);
        });
    }

    showModal() {
        const modal = new bootstrap.Modal(
            document.getElementById(this.modalId)
        );
        modal.show();
    }

    async handleSubmit(e) {
        const $form = $(e.currentTarget);
        const $submitButton = $form.find('[type="submit"]');

        // Show loading state
        $submitButton.attr("data-kt-indicator", "on");
        $submitButton.prop("disabled", true);

        try {
            const formData = new FormData($form[0]);
            const data = {
                title: {
                    en: formData.get("title[en]"),
                    ar: formData.get("title[ar]"),
                },
                order: formData.get("order") || null,
                program_id: this.formBuilder.programId,
                id: null, // Add the ID to the data instead of the URL
            };

            const response = await $.ajax({
                url: this.formBuilder.routesManager.getUrl("storePage"),
                method: "POST",
                data: data,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            if (response.success) {
                toastr.success("Page added successfully");
                $(`#${this.modalId}`).modal("hide");
                this.formBuilder.loadPages();
                $form[0].reset();
            } else {
                toastr.error(response.message || "Error adding page");
            }
        } catch (error) {
            console.error("Error adding page:", error);
            toastr.error("Error adding page");
        } finally {
            // Reset loading state
            $submitButton.attr("data-kt-indicator", "off");
            $submitButton.prop("disabled", false);
        }
    }
}

export default AddPageManager;
