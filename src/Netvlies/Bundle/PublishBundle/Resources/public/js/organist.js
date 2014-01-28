$(function () {

    $(document).ready(function() {

        // ****************** Application filter
        $("#appselect").select2(
            {
                placeholder: 'Type in here to find your application'
            }
        );

        $("#appselect").on("change",
            function(e) {
                document.location.href= $(this).val();
            });


//        $('.filter-list').liveFilter('.filter-box', 'li', {
//            filterChildSelector: 'a'
//        });


        // ******************* Branch/Tag filter
        $(".bigrevisionselect").select2(
            {
                placeholder: 'Type in here to find your revision'
            }
        );

        // ****************** Load changeset
        $("#netvlies_publishbundle_applicationdeploy_target").change(
            function(){
                loadChangeList();
            }
        );

        $("#netvlies_publishbundle_applicationdeploy_revision").change(
            function(){
                loadChangeList();
            }
        );

        function loadChangeList(){
            var target = $("#netvlies_publishbundle_applicationdeploy_target").val();
            var revision = $("#netvlies_publishbundle_applicationdeploy_revision").val();

            if(!target || !revision){
                return;
            }

            var app_dev = '';
            if($('#loadchangeset').data('env')){
                app_dev = '/app_dev.php'
            }
            $('#loadchangeset').html('<div class="col-lg-2"></div><div class="col-lg-10"><img src="/bundles/netvliespublish/img/ajax-loader.gif"> Loading changeset</div>')

            $.ajax({
                url: app_dev+"/command/loadchangeset/"+target+"/"+revision
            }).done(function(changeset) {
                $('#loadchangeset').html(changeset);
            });
        }



        // ******************** User files
        // Get the ul that holds the collection of tags
        var collectionHolder = $('div.userfiles');

        // setup an "add a tag" link
        var $addUserFileLink = $('<div class="col-lg-2"></div> <a id="adduserfilebutton" href="#">Add another shared directory/file</a>');
        var $newLink = $('<div></div>').append($addUserFileLink);


        // add the "add a tag" anchor and li to the tags ul
        collectionHolder.append($newLink);

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        collectionHolder.data('index', collectionHolder.find(':input').length);

        collectionHolder.find('div.form-group').each(function() {
            addTagFormDeleteLink($(this));
        });


        $addUserFileLink.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();

            // Get the data-prototype explained earlier
            var prototype = collectionHolder.data('prototype');

            // get the new index
            var index = collectionHolder.data('index');

            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have
            var newForm = prototype.replace(/__name__/g, index);

            // increase the index with one for the next item
            collectionHolder.data('index', index + 1);

            // Display the form in the page in an li, before the "Add a tag" link li
            var $newFormDiv = $('<div class="form-group"></div>').append(newForm);

            // add a delete link to the new form
            addTagFormDeleteLink($newFormDiv);

            $newLink.before($newFormDiv);
        });


        function addTagFormDeleteLink($formDiv) {
            var $removeFormA = $('<div class="col-lg-1"><a href="#" class="btn btn-danger">delete</a></div>');
            $formDiv.append($removeFormA);

            $removeFormA.on('click', function(e) {
                // prevent the link from creating a "#" on the URL
                e.preventDefault();

                // remove the li for the tag form
                $formDiv.remove();
            });
        }
    });

});