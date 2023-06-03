/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */

;(function($, window, document, undefined) {
    window.wp = window.wp || {};
    window.wp.coreactivity = window.wp.coreactivity || {};

    window.wp.coreactivity.admin = {
        init: function() {
            $(document).on("click", ".coreactivity-event-toggle", function(e) {
                e.preventDefault();

                var button = $(this),
                    id = button.data("id"),
                    nonce = button.data("nonce");

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data: {
                        event: id
                    },
                    url: ajaxurl + "?action=coreactivity_toggle_event&_ajax_nonce=" + nonce,
                    success: function(json) {
                        if (json.hasOwnProperty('toggle') && json.toggle !== '') {
                            button.find("i").removeClass("d4p-ui-toggle-on").removeClass("d4p-ui-toggle-off").addClass(json.toggle);
                        }
                    }
                });
            });
        }
    };

    window.wp.coreactivity.admin.init();
})(jQuery, window, document);
