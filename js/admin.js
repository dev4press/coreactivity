/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global d4plib_admin_data, coreactivity_data, coreactivity_live*/

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
        },
        live: {
            init: function() {
                setTimeout(wp.coreactivity.admin.live.ajax, 15000);
            },
            ajax: function() {
                $(".coreactivity-live-row").addClass("coreactivity-old-live-row").removeClass("coreactivity-standout").removeClass("coreactivity-live-row");

                $.ajax({
                    url: ajaxurl + "?action=coreactivity_live_logs",
                    type: "post",
                    dataType: "html",
                    data: {
                        args: JSON.stringify(coreactivity_live)
                    },
                    success: function(html) {
                        if (html.length > 0) {
                            var dot = html.indexOf("."),
                                id = html.substring(0, dot),
                                tr = html.substring(dot + 1);

                            coreactivity_live.id = parseInt(id);

                            $(".coreactivity-grid-logs #the-list").prepend(tr);
                            $(".coreactivity-live-row").addClass("coreactivity-standout");
                        }

                        setTimeout(wp.coreactivity.admin.live.ajax, 15000);
                    }
                });
            }
        }
    };

    $(document).ready(function() {
        window.wp.coreactivity.admin.init();

        if (d4plib_admin_data.plugin.name === 'coreactivity' && d4plib_admin_data.page.panel === 'logs') {
            if (coreactivity_data.live_updates === 'Y') {
                wp.coreactivity.admin.live.init();
            }
        }
    });
})(jQuery, window, document);
