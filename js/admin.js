/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global d4plib_admin_data, coreactivity_data, coreactivity_live*/

;(function($, window, document, undefined) {
    window.wp = window.wp || {};
    window.wp.coreactivity = window.wp.coreactivity || {};

    window.wp.coreactivity.admin = {
        toggles: {
            on: "d4p-ui-toggle-on",
            off: "d4p-ui-toggle-off"
        },
        init: function() {
            const dialog_view = $.extend({}, wp.dev4press.dialogs.default_dialog(), {
                width: 600,
                maxWidth: 720,
                maxHeight: 640,
                minWidth: 360,
                minHeight: 240,
                resizable: true,
                buttons: [
                    $.extend({}, wp.dev4press.dialogs.default_button('ok', true), {
                        click: function() {
                            $("#coreactivity-log-dialog").wpdialog("close");
                        }
                    })
                ]
            });

            $("#coreactivity-log-dialog").wpdialog(dialog_view);

            $(document).on("click", ".coreactivity-toggle-notification", function(e) {
                e.preventDefault();

                const button = $(this),
                    id = button.data("id"),
                    key = button.data("key"),
                    nonce = button.data("nonce");

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data: {
                        event: id,
                        notification: key
                    },
                    url: ajaxurl + "?action=coreactivity_toggle_notification&_ajax_nonce=" + nonce,
                    success: function(json) {
                        wp.coreactivity.admin.helpers.toggle(button, json);
                    }
                });
            });

            $(document).on("click", ".coreactivity-toggle-event", function(e) {
                e.preventDefault();

                const button = $(this),
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
                        wp.coreactivity.admin.helpers.toggle(button, json);
                    }
                });
            });

            $(document).on("click", ".coreactivity-show-view-popup", function(e) {
                e.preventDefault();

                const id = $(this).data("log");

                $("#coreactivity-popup-tabs-info").html($("#coreactivity-popup-data-info-" + id).clone());
                $("#coreactivity-popup-tabs-meta").html($("#coreactivity-popup-data-meta-" + id).clone());
                $("#coreactivity-popup-tabs-location").html($("#coreactivity-popup-data-location-" + id).clone());
                $("#coreactivity-popup-tabs-device").html($("#coreactivity-popup-data-device-" + id).clone());

                $("#coreactivity-log-dialog").wpdialog("open");
            });
        },
        columns: function() {
            $(document).on("change", ".hide-column-tog", function() {
                const visible = $(".wp-list-table thead tr > *:not(.hidden)").length;

                $(".wp-list-table tbody tr.coreactivity-hidden-row td").attr("colspan", visible);
            });

            $(document).on("click", "thead th.column-meta, tfoot th.column-meta", function(e) {
                e.preventDefault();

                const i = $(this).find("i"),
                    both = $("thead th.column-meta i, tfoot th.column-meta i"),
                    table = $(this).closest("table"),
                    rows = table.find("tbody tr.coreactivity-hidden-row"),
                    buttons = table.find("tbody td.column-meta button i"),
                    open = i.hasClass("d4p-ui-chevron-square-up");

                if (open) {
                    rows.addClass("__hidden");
                    buttons.removeClass("d4p-ui-chevron-square-up d4p-ui-chevron-square-down").addClass("d4p-ui-chevron-square-down");
                    both.removeClass("d4p-ui-chevron-square-up").addClass("d4p-ui-chevron-square-down");
                } else {
                    rows.removeClass("__hidden");
                    buttons.removeClass("d4p-ui-chevron-square-up d4p-ui-chevron-square-down").addClass("d4p-ui-chevron-square-up");
                    both.removeClass("d4p-ui-chevron-square-down").addClass("d4p-ui-chevron-square-up");
                }
            });

            $(document).on("click", "td.column-meta button", function(e) {
                e.preventDefault();

                const i = $(this).find("i"),
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
        },
        helpers: {
            toggle: function(button, json) {
                if (json.hasOwnProperty('toggle') && json.toggle !== '') {
                    const t = wp.coreactivity.admin.toggles;

                    button.find("i").removeClass(t.on).removeClass(t.off).addClass(t[json.toggle]);
                    button.find("span").html(button.data(json.toggle));
                }
            }
        },
        live: {
            init: function() {
                setTimeout(wp.coreactivity.admin.live.ajax, 15000);
            },
            ajax: function() {
                let hidden = [];
                const columns = $(".coreactivity-grid-logs #the-list tr.coreactivity-hidden-row td").first().attr("colspan"),
                    ref = $(".coreactivity-grid-logs #the-list tr:not(.coreactivity-old-live-row) > td.hidden");

                $(".coreactivity-live-row").addClass("coreactivity-old-live-row").removeClass("coreactivity-standout").removeClass("coreactivity-live-row");

                ref.each(function() {
                    const cls = $(this).attr("class").split(" ")[0];

                    if (hidden.indexOf(cls) < 0) {
                        hidden.push(cls);
                    }
                });

                $.ajax({
                    url: ajaxurl + "?action=coreactivity_live_logs",
                    type: "post",
                    dataType: "html",
                    data: {
                        args: JSON.stringify(coreactivity_live)
                    },
                    success: function(html) {
                        if (html.length > 0) {
                            const dot = html.indexOf("."),
                                id = html.substring(0, dot),
                                tr = html.substring(dot + 1);

                            coreactivity_live.id = parseInt(id);

                            $(".coreactivity-grid-logs #the-list").prepend(tr);
                            $(".coreactivity-live-row").addClass("coreactivity-standout");

                            $(".coreactivity-hidden-row.coreactivity-live-row > td").each(function() {
                                $(this).attr("colspan", columns);
                            });

                            $(".coreactivity-live-row > td").each(function() {
                                $(this).removeClass("hidden");

                                let cls = $(this).attr("class");

                                if (cls) {
                                    cls = cls.split(" ")[0];

                                    if (hidden.indexOf(cls) > -1) {
                                        $(this).addClass("hidden");
                                    }
                                }
                            });
                        }

                        setTimeout(wp.coreactivity.admin.live.ajax, 15000);
                    }
                });
            }
        }
    };

    $(document).ready(function() {
        window.wp.coreactivity.admin.init();

        if ($(".coreactivity-grid-logs").length > 0) {
            wp.coreactivity.admin.columns();

            if (coreactivity_data.live_updates === 'Y') {
                wp.coreactivity.admin.live.init();
            }
        }
    });
})(jQuery, window, document);
