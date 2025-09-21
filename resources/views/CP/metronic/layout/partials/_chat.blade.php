<div id="kt_drawer_chat" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
    data-kt-drawer-close="#kt_drawer_chat_close" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'100%', 'md': '50%'}">
    <div class="card w-100 rounded-0 chat">
        <div class="card-header pe-5">
            <div class="card-title d-flex align-items-center w-100 gap-2">
                <!-- Back Button -->
                <button class="btn btn-sm btn-icon btn-active-light-primary back-to-sessions d-none" id="backButton">
                    <span class="svg-icon svg-icon-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="11" width="13" height="2" rx="1"
                                fill="currentColor"></rect>
                            <path
                                d="M8.56569 11.4343L12.75 7.25C13.1642 6.83579 13.1642 6.16421 12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75L5.70711 11.2929C5.31658 11.6834 5.31658 12.3166 5.70711 12.7071L11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25C13.1642 17.8358 13.1642 17.1642 12.75 16.75L8.56569 12.5657C8.25327 12.2533 8.25327 11.7467 8.56569 11.4343Z"
                                fill="currentColor"></path>
                        </svg>
                    </span>
                </button>

                <!-- Title -->
                <h4 class="font-weight-bold m-0 me-auto">Messages <span id="senderN"></span></h4>

                <!-- Action Buttons -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Edit Lead Button -->
                    <a href="#" class="btn btn-sm btn-icon btn-active-light-primary edit-lead-btn d-none"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit Lead">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3"
                                    d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                    fill="currentColor"></path>
                                <path
                                    d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                    fill="currentColor"></path>
                            </svg>
                        </span>
                    </a>

                    <!-- Close Button -->
                    <button class="btn btn-sm btn-icon btn-active-light-primary" id="kt_drawer_chat_close"
                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="Close">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3"
                                    d="M6 19.7C5.7 19.7 5.5 19.6 5.3 19.4C4.9 19 4.9 18.4 5.3 18L18 5.3C18.4 4.9 19 4.9 19.4 5.3C19.8 5.7 19.8 6.29999 19.4 6.69999L6.7 19.4C6.5 19.6 6.3 19.7 6 19.7Z"
                                    fill="currentColor" />
                                <path
                                    d="M18.8 19.7C18.5 19.7 18.3 19.6 18.1 19.4L5.40001 6.69999C5.00001 6.29999 5.00001 5.7 5.40001 5.3C5.80001 4.9 6.40001 4.9 6.80001 5.3L19.5 18C19.9 18.4 19.9 19 19.5 19.4C19.3 19.6 19 19.7 18.8 19.7Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body hover-scroll-overlay-y">
            <!-- Search form - only visible in sessions view -->
            <div class="sessions-view-elements">
                <form data-kt-search-element="form" class="w-100 position-relative mb-5" autocomplete="off">
                    <i
                        class="ki-duotone searchWhatClick ki-magnifier fs-2 fs-lg-1 text-gray-500 position-absolute top-50 ms-5 translate-middle-y">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" class="form-control searchinputWhat form-control-lg form-control-solid px-15"
                        name="inputsearchWhat" placeholder="Search" data-kt-search-element="input">
                    <span
                        class="btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 me-5 searchWhatClose">
                        <i class="ki-duotone ki-cross fs-2 fs-lg-1 me-0">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                </form>

                <div class="chat-sessions-list">
                    <!-- Sessions will be rendered here -->
                </div>
            </div>

            <div class="chat-messages-container d-none">
                <div class="chat-messages">
                    <!-- Messages content here -->
                </div>

                <!-- Message Input Box -->
                <div class="card-footer pt-4" id="messageInputBox">
                    <form id="sendMessageForm">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-solid" placeholder="Type a message"
                                id="messageInput">
                            <button class="btn btn-primary" type="submit">
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z"
                                            fill="currentColor" />
                                        <path opacity="0.3"
                                            d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
