$(".sortable").sortable({
    update: function () {

        var neworder = new Array();
        var update_url = <?php echo json_encode($url) ?>;
        var order = "";
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");

        $('.sortable li').each(function () {

            //get the id
            var id = $(this).attr("id");

            if (order == ""){
                order = id;
            } else {
                order = order + "|" + id;
            }
        });

        $.post(update_url, {'neworder': order, '<?php echo $this->security->get_csrf_token_name(); ?>': cct}, function (data) {
        }, "json");
    }
});