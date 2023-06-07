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

            if ($("#coreactivity-form-logs").length === 1) {
                $(document).on("change", ".hide-column-tog", function() {
                    var visible = $(".wp-list-table thead tr > *:not(.hidden)").length;

                    $(".wp-list-table tbody tr.coreactivity-hidden-row td").attr("colspan", visible);
                });

                $(document).on("click", "thead th.column-meta", function(e) {
                    e.preventDefault();

                    var i = $(this).find("i"),
                        table = $(this).closest("table"),
                        rows = table.find("tbody tr.coreactivity-hidden-row"),
                        buttons = table.find("tbody td.column-meta button i"),
                        open = i.hasClass("d4p-ui-chevron-square-up");

                    if (open) {
                        rows.addClass("__hidden");
                        buttons.removeClass("d4p-ui-chevron-square-up d4p-ui-chevron-square-down").addClass("d4p-ui-chevron-square-down");
                        i.removeClass("d4p-ui-chevron-square-up").addClass("d4p-ui-chevron-square-down");
                    } else {
                        rows.removeClass("__hidden");
                        buttons.removeClass("d4p-ui-chevron-square-up d4p-ui-chevron-square-down").addClass("d4p-ui-chevron-square-up");
                        i.removeClass("d4p-ui-chevron-square-down").addClass("d4p-ui-chevron-square-up");
                    }
                });

                $(document).on("click", "td.column-meta button", function(e) {
                    e.preventDefault();

                    var i = $(this).find("i"),
                        row = $(this).parent().parent().next(),
                        open = i.hasClass("d4p-ui-chevron-square-up");

                    if (open) {
                        row.addClass("__hidden");
                        i.removeClass("d4p-ui-chevron-square-up").addClass("d4p-ui-chevron-square-down");
                    } else {
                        row.removeClass("__hidden");
                        i.removeClass("d4p-ui-chevron-square-down").addClass("d4p-ui-chevron-square-up");
                    }
                });
            }
        }
    };

    $(document).ready(function() {
        window.wp.coreactivity.admin.init();
    });
})(jQuery, window, document);
