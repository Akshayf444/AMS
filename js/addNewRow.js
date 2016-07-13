    function addRow() {
        $("#add").show();
        var search_term1 = "";
        $.post('getNewRow.php', {search_term1: search_term1}, function (data) {

            var lastRowid = $("#items  tbody tr:last .multiselect").attr("name");
            if (lastRowid == null) {
                var newId = 1;
            } else {
                var final = lastRowid.split("[]")
                var newId = parseInt(final[0]) + 1;
            }
            //alert(newId);
            $("#items  tbody").append(data);

            $(".multiselect").multiselect('destroy');
            $(".multiselect").multiselect({
                numberDisplayed: 1,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 300
            });

            //Assign name and Id to newly added row.....
            $("#items tbody tr:last ").attr("id", newId + "11");
            $("#items tbody tr:last .multiselect").attr("name", newId + "[]");
            $("#items tbody tr:last .multiselect").attr("id", newId + "1");


            hide();
        });


    }

    function hide() {
        //alert();
        $("#add").hide();
    }


    jQuery("#items ").delegate('.delete ', 'click', function () {

        var currentRow = $(this).closest('tr');
        var rowid = currentRow.attr('id');
        //alert(rowid);
        $("#" + rowid).remove();

        var count = 1;
        /********** Assign new Id to each and every element ************/
        jQuery("#items .targetfields").each(function () {
            jQuery(this).find(".multiselect").attr("name", count + "[]");
            jQuery(this).find(".multiselect").attr("id", count + "1");
            jQuery(this).attr("id", count + "11");
            count++;
            //alert(count);
        });
    });