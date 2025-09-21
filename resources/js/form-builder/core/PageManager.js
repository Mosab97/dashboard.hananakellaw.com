class PageManager {
    constructor(formBuilder) {
        this.formBuilder = formBuilder;
        this.pages = [];
        this.currentPage = null;
        this.initEventListeners();
    }
    refreshCurrentPage(pageData) {
        if (!pageData) return;

        // Update the page in the pages array
        const pageIndex = this.pages.findIndex((p) => p.id === pageData.id);
        if (pageIndex !== -1) {
            this.pages[pageIndex] = pageData;
        }

        // Update the stepper UI for the current page
        this.renderPageNumbers(this.pages);

        // Set this page as current in the stepper
        $(`.stepper-item[data-page-id="${pageData.id}"]`).addClass("current");

        // Render the updated page content
        this.renderPageContent(pageData);
    }

    initEventListeners() {
        $(document).on("click", "[data-kt-stepper-element='nav']", (e) => {
            const $item = $(e.currentTarget);
            const pageId = $item.data("page-id");
            this.switchToPage(pageId);
        });

        // Add edit and delete buttons event listeners
        $(document).on("click", ".edit-page", (e) => {
            e.preventDefault();
            const pageId = $(e.currentTarget).data("page-id");
            this.showEditModal(pageId);
        });

        $(document).on("click", ".delete-page", (e) => {
            e.preventDefault();
            const pageId = $(e.currentTarget).data("page-id");
            this.confirmDelete(pageId);
        });
    }

    renderPageNumbers(pages) {
        this.pages = pages; // Pages are already sorted by order from backend
        const $pageNumbers = $("#pageNumbers");
        $pageNumbers.empty();

        const pageItems = pages
            .map(
                (page, index) => `
                <div class="stepper-item ${
                    page.id === this.currentPage?.id ? "current" : ""
                }"
                     data-kt-stepper-element="nav"
                     data-page-id="${page.id}"
                     data-page-order="${page.order}">
                    <div class="stepper-wrapper">
                        <div class="stepper-icon">
                            ${page.order}
                        </div>
                        <div class="stepper-label">
                            <div class="stepper-title">${
                                page.title?.en || "Untitled"
                            }</div>
                            <div class="stepper-desc">${
                                page.title?.ar || ""
                            }</div>
                            <div class="actions mt-2">
                                <button class="btn btn-sm btn-icon btn-light-primary edit-page" data-page-id="${
                                    page.id
                                }">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-light-danger delete-page ms-2" data-page-id="${
                                    page.id
                                }">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `
            )
            .join("");

        $pageNumbers.html(pageItems);

        // Load the first page content if available
        if (pages.length > 0 && !this.currentPage) {
            this.loadPageContent(pages[0].id);
        }
    }
    initializeScrollButtons() {
        const $wrapper = $(".stepper-nav-wrapper");
        const $scrollContainer = $(".stepper-nav-scroll");
        const $prevBtn = $(".stepper-nav-prev");
        const $nextBtn = $(".stepper-nav-next");

        // Check if scrolling is needed
        const checkScroll = () => {
            const isScrollable =
                $scrollContainer[0].scrollWidth >
                $scrollContainer[0].clientWidth;
            $prevBtn.toggleClass(
                "d-none",
                !isScrollable || $scrollContainer.scrollLeft() <= 0
            );
            $nextBtn.toggleClass(
                "d-none",
                !isScrollable ||
                    $scrollContainer.scrollLeft() + $scrollContainer.width() >=
                        $scrollContainer[0].scrollWidth
            );
        };

        // Initial check
        checkScroll();

        // Handle scroll buttons
        $prevBtn.on("click", () => {
            $scrollContainer.animate(
                {
                    scrollLeft: "-=200",
                },
                300,
                checkScroll
            );
        });

        $nextBtn.on("click", () => {
            $scrollContainer.animate(
                {
                    scrollLeft: "+=200",
                },
                300,
                checkScroll
            );
        });

        // Check on scroll
        $scrollContainer.on("scroll", checkScroll);

        // Check on window resize
        $(window).on("resize", checkScroll);
    }

    showEditModal(pageId) {
        const page = this.pages.find((p) => p.id === pageId);
        if (!page) return;

        const modalId = "editPageModal";
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="editPageForm" novalidate>
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Page</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="${
                                    page.id
                                }">
                                <div class="mb-5">
                                    <label class="form-label required">Page Title (English)</label>
                                    <input type="text" class="form-control" name="title[en]" value="${
                                        page.title?.en || ""
                                    }" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label required">Page Title (Arabic)</label>
                                    <input type="text" class="form-control" name="title[ar]" value="${
                                        page.title?.ar || ""
                                    }" dir="rtl" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label">Page Order</label>
                                    <input type="number" class="form-control" name="order" value="${
                                        page.order || ""
                                    }" min="1">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">Save Changes</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>`;

        $(`#${modalId}`).remove();
        $("body").append(modalHtml);

        const $form = $("#editPageForm");
        const modal = new bootstrap.Modal(document.getElementById(modalId));

        $form.on("submit", async (e) => {
            e.preventDefault();
            await this.handleFormSubmit($form, modal);
        });

        modal.show();
    }

    async handleFormSubmit($form, modal) {
        const $submitBtn = $form.find('[type="submit"]');
        $submitBtn.attr("data-kt-indicator", "on");
        $submitBtn.prop("disabled", true);

        // Reset validation state
        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find(".invalid-feedback").empty();

        try {
            const formData = new FormData($form[0]);
            const data = {
                id: formData.get("id"),
                title: {
                    en: formData.get("title[en]"),
                    ar: formData.get("title[ar]"),
                },
                order: formData.get("order") || null,
                program_id: this.formBuilder.programId,
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
                toastr.success(response.message);
                modal.hide();
                this.formBuilder.loadPages();
            }
        } catch (error) {
            if (error.status === 422) {
                // Validation errors
                const errors = error.responseJSON.errors;
                console.log("errors", errors);

                Object.keys(errors).forEach((key) => {
                    const field = key.replace(".", "[") + "]";
                    const $input = $form.find(`[name="${field}"]`);
                    $input.addClass("is-invalid");
                    $input.siblings(".invalid-feedback").text(errors[key][0]);
                });
            } else {
                toastr.error("Error saving page");
            }
        } finally {
            $submitBtn.attr("data-kt-indicator", "off");
            $submitBtn.prop("disabled", false);
        }
    }

    confirmDelete(pageId) {
        Swal.fire({
            text: "Are you sure you want to delete this page?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-light",
            },
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await $.ajax({
                        url: this.formBuilder.routesManager.getUrl(
                            "deletePage",
                            { pageId }
                        ),
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    });

                    if (response.success) {
                        toastr.success(response.message);
                        this.formBuilder.loadPages();
                    }
                } catch (error) {
                    toastr.error("Error deleting page");
                }
            }
        });
    }

    // In PageManager.js
    async loadPageContent(pageId) {
        if (!pageId) {
            console.error("Invalid page ID:", pageId);
            return;
        }

        try {
            const response = await $.ajax({
                url: this.formBuilder.routesManager.getUrl("pageContent", {
                    pageId,
                }),
                method: "GET",
            });

            if (response.success) {
                this.renderPageContent(response.data);
                // Make sure to set the current page
                this.formBuilder.setCurrentPage(response.data);
            }
        } catch (error) {
            toastr.error("Error loading page content");
            console.error("Load page content error:", error);
        }
    }

    renderPageContent(pageData) {
        const $pageContent = $("#pageContent");
        $pageContent.empty();

        // Questions container with better styling
        const questionsSection = `
            <div class="questions-section">
                <div id="questionsContainer" class="accordion mb-5">
                    <!-- Questions will be rendered here -->
                </div>
                <button type="button" class="btn btn-light-primary" id="addQuestion">
                    <i class="fas fa-plus"></i> Add Question
                </button>
            </div>
        `;

        $pageContent.append(questionsSection);

        // Transform questions data to match frontend expectations
        if (pageData.questions && pageData.questions.length > 0) {
            const transformedQuestions = pageData.questions.map((question) => {
                // Transform options format
                let transformedOptions = {
                    en: [],
                    ar: [],
                };
                let scores = []; // Array to hold scores

                // If options exist, transform them and extract scores
                if (question.options && question.options.length > 0) {
                    question.options.forEach((option) => {
                        transformedOptions.en.push(option.title.en);
                        transformedOptions.ar.push(option.title.ar);
                        scores.push(option.score || 0); // Add score from each option
                    });
                }

                return {
                    ...question,
                    options: transformedOptions,
                    scores: scores, // Use the extracted scores
                    file_config: question.file_config || null,
                };
            });

            // Render transformed questions
            transformedQuestions.forEach((question) => {
                this.formBuilder.questionManager.renderQuestion(question);
            });
        }
    }

    switchToPage(pageId) {
        const page = this.pages.find((p) => p.id === pageId);
        if (!page) {
            console.error("Invalid page ID:", pageId);
            return;
        }

        $(".stepper-item").removeClass("current");
        $(`[data-page-id="${pageId}"]`).addClass("current");

        this.currentPage = page;
        this.loadPageContent(pageId);
    }

    getSortedPages() {
        return [...this.pages].sort((a, b) => {
            const orderA = a.order || Number.MAX_SAFE_INTEGER;
            const orderB = b.order || Number.MAX_SAFE_INTEGER;
            return orderA - orderB;
        });
    }
}

export default PageManager;
