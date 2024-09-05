$("input[name='new_term_name").on("input", function() {
    if ($(this).val() == "") {
        $("#old_terms").prop("disabled", false);
    } else {
        $("#old_terms").prop("disabled", true);
    }
})

$("#old_terms").on("change", function() {
    if (current_term_id == $(this).val()) {
        $("input[name='new_term_name").prop("disabled", false);
        $("input[name='tourn_term").prop("disabled", false);
    } else {
        $("input[name='new_term_name").prop("disabled", true);
        $("input[name='tourn_term").prop("disabled", true);
    }
})
