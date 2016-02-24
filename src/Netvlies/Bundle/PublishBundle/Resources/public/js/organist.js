$(function () {

    $(document).ready(function() {

        /** Bitbucket status **/
        $("#bitbucketstatus").ready(
            function(){
                var sp = new StatusPage.page({ page : 'bqlf8qjztdtr' });
                sp.status({
                    success : function(data) {
                        var status = data.status.indicator;
                        var label = '';
                        var cssclass = '';

                        if (status == 'none') {
                            label = ' Bitbucket: ok!';
                            cssclass = 'btn-success';
                        } else {
                            label = ' Bitbucket: oops!';
                            cssclass = 'btn-danger';
                        }

                        $("#bitbucketstatus").after(label);
                        $("#bitbucketlink").addClass(cssclass);
                    }
                });
            }
        );

        /** Github status **/
        $("#githubstatus").ready(
            function(){
                $.ajax({
                    url: window.organist.settings.githubstatus,
                    success: function (data, status, jqxhr) {
                        var response = $.parseJSON(JSON.stringify(data));
                        var ghstatus = response.status;
                        var label = '';
                        var cssclass = '';

                        if (ghstatus == 'good') {
                            label = ' Github: ok!';
                            cssclass = 'btn-success';
                        } else {
                            label = ' Github: oops!'+ghstatus;
                            cssclass = 'btn-danger';
                        }

                        $("#githubstatus").after(label);
                        $("#githublink").addClass(cssclass);
                    }
                });
            }
        );

        // ****************** Application filter
        $("#appselect").select2(
            {
                placeholder: 'Applications'
            }
        );

        $("#appselect").on("change",
            function(e) {
                document.location.href= $(this).val();
            });

        // ******************* Branch/Tag filter
        $(".bigrevisionselect").select2(
            {
                placeholder: 'Revisions'
            }
        );

        // ****************** Load changeset
        //$("#netvlies_publishbundle_applicationdeploy_target").change(
        //    function(){
        //        loadChangeList();
        //    }
        //);
        //
        //$("#netvlies_publishbundle_applicationdeploy_revision").change(
        //    function(){
        //        loadChangeList();
        //    }
        //);
        //
        //function loadChangeList(){
        //    var target = $("#netvlies_publishbundle_applicationdeploy_target").val();
        //    var revision = $("#netvlies_publishbundle_applicationdeploy_revision").val();
        //
        //    if(!target || !revision){
        //        return;
        //    }
        //
        //    var app_dev = '';
        //    if($('#loadchangeset').data('env') == 'dev'){
        //        app_dev = '/app_dev.php'
        //    }
        //    $('#loadchangeset').html('<div class="col-lg-2"></div><div class="col-lg-10"><img src="/bundles/netvliespublish/img/ajax-loader.gif"> Loading changeset</div>')
        //
        //    $.ajax({
        //        url: app_dev+"/command/loadchangeset/"+target+"/"+revision
        //    }).done(function(changeset) {
        //        $('#loadchangeset').html(changeset);
        //    });
        //}



        // Hook up ACE editor to all textareas with data-editor attribute
        $('textarea[data-editor]').each(function () {
            var textarea = $(this);
            var mode = textarea.data('editor');
            var editDiv = $('<div>', {
                position: 'absolute',
                width: textarea.width(),
                height: textarea.height(),
                'class': textarea.attr('class')
            }).insertBefore(textarea);
            textarea.css('visibility', 'hidden');
            textarea.attr('rows', 1);

            var editor = ace.edit(editDiv[0]);
            editor.renderer.setShowGutter(false);
            editor.getSession().setValue(textarea.val());
            editor.getSession().setMode("ace/mode/" + mode);
            editor.setTheme("ace/theme/twilight");
            // editor.setTheme("ace/theme/idle_fingers");

            // copy back to textarea on form submit...
            textarea.closest('form').submit(function () {
                textarea.val(editor.getSession().getValue());
            })
        });



        // Manage one to many relations (e.g. shared assets in settings for an application)
        $('div.onetomanycontainer').each(function(index)
        {
            var collectionHolder = $('div.onetomanycontainer');
            var $addLink = $('<div class="col-lg-10"><a id="addbutton" href="#">Add another item</a></div>');
            var $newLink = $('<div><div class="col-lg-2"></div></div>').append($addLink);

            // add the "add a tag" anchor and li to the tags ul
            collectionHolder.append($newLink);

            // count the current form inputs we have (e.g. 2), use that as the new
            // index when inserting a new item (e.g. 2)
            collectionHolder.data('index', collectionHolder.find(':input').length);

            collectionHolder.find('div.form-group').each(function() {
                addDeleteLink($(this));
            });


            $addLink.on('click', function(e) {
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
                addDeleteLink($newFormDiv);

                $newLink.before($newFormDiv);
            });

            function addDeleteLink($formDiv) {
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
});
