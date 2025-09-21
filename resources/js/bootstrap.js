import jQuery from "jquery";
window.$ = window.jQuery = jQuery;

// Import jQuery UI JS only
import "jquery-ui-dist/jquery-ui.js";

// Import toastr
import toastr from "toastr";
window.toastr = toastr;
import "toastr/build/toastr.min.css";

// Axios setup
import axios from "axios";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Setup CSRF token
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});
