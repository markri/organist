$(function () {

    $(document).ready(function() {
        $("#appselect").select2();

        $('.filter-list').liveFilter('.filter-box', 'li', {
            filterChildSelector: 'a'
        });
    });

});