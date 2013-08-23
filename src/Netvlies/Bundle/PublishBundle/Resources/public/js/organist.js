$(function () {

    $(document).ready(function() {
        $("#appselect").select2(
            {
                placeholder: 'Type in here to find your application'
            }
        );

        $("#appselect").on("change",
            function(e) {
                document.location.href= $(this).val();
            });


        $('.filter-list').liveFilter('.filter-box', 'li', {
            filterChildSelector: 'a'
        });
    });

});