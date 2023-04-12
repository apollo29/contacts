/* cntnd_contacts */
$(document).ready(function () {
    $(".new_contact").click(function () {
        $("#editor").addClass('active');
    });

    $(".add_contact").click(function () {
        let contact = $(this).data("contact");
        let action = $(this).data("action");
        let source = $(this).data("source");
        let index = $(this).data("index");
        let data = $("#" + contact).val();
        let json = JSON.parse(window.atob(data));
        $('form[name="editor_form"] .editor_form_remove').show();

        console.log(json);

        $('form[name="editor_form"] :input').each(function () {
            $(this).removeClass("highlight");
            let input = $(this).attr("name");
            if (input!==undefined && input.startsWith('data')) {
                let type = $(this).attr("type");

                let value = undefined;
                var matches = input.match(/\[(.*?)]/);
                if (matches) {
                    value = json[matches[1]];
                }
                if (value !== undefined) {
                    if (type === "checkbox" || type === "radio") {
                        $(this).prop("checked", value);
                    } else {
                        $(this).val(value);
                    }
                }
            }
        });

        if (json["_diff"] !== undefined) {
            $.each(json["_diff"], function (key) {
                $('form[name="editor_form"] input[name="data[' + key + ']"]').addClass("highlight");
            });
        }

        $('form[name="editor_form"] input[name="editor_form_action"]').val(action);
        $('form[name="editor_form"] input[name="editor_form_source"]').val(source);
        $('form[name="editor_form"] input[name="editor_form_index"]').val(index);
        $("#editor").addClass('active');
    });

    $(".editor_form_remove").click(function () {
        $('form[name="editor_form"] input[name="editor_form_action"]').val("delete");
        $('form[name="editor_form"]').submit();
    });

    $(".remove_contact").click(function () {
        let source = $(this).data("source");
        let index = $(this).data("index");
        $('form[name="delete_form"] input[name="editor_form_source"]').val(source);
        $('form[name="delete_form"] input[name="editor_form_index"]').val(index);

        $('form[name="delete_form"]').submit();
    });

    $(".popup .close").click(function () {
        $('form[name="editor_form"] .editor_form_remove').hide();
        $('form[name="editor_form"]').trigger("reset");
        $('form[name="editor_form"] :input').each(function () {
            $(this).removeClass("highlight");
        });

        let overlay = $(this).parent(".popup").parent(".overlay");
        closePopup(overlay);
    });

    $('form[name="editor_form"]').submit(function () {
        let action = $('input[name="editor_form_action"]').val();
        if (action === undefined) {
            $('input[name="editor_form_action"]').val("new");
        }
        // set action for later post
        // validation?
        let overlay = $(this).parent(".popup").parent(".overlay");
        closePopup(overlay);

        console.log("editor submit");
        return true;
    });

    function closePopup(overlay) {
        if (overlay.hasClass("active")) {
            overlay.toggleClass('active');
        }
    }
});
