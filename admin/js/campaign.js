(function ( $ ) {
    "use strict";

    /** Ensure that document is saved before sending any data **/
    var preventSend = true;


    /**
     * Prevent emails being sent without input data being saved
     * @param  obj  self    The button bound to the event
     * @param  obj  event   The event object
     * @param  bool trigger On successful sending, should the originating button be clicked again
     */
    function autosave(self, event, trigger) {
        var postData = $('#post').serialize();

        // If preventSend is false then go ahead and submit form as usual
        // else
        if (preventSend === true) {
            // Page has not yet been saved, don't continue until saved
            $.post( "post.php", postData)
                .done(function() {
                    // Deactivate preventSend so that we can go forward next time
                    preventSend = false;
                    if (trigger) {
                        $(self).trigger('click');
                    }
                }).fail(function() {
                    // Alert with the translated output: Please save before proceeding
                    alert( nc_ajax_object.please_save );
                });

            event.preventDefault();
        }
    }

    $(function () {

        $('button[name="newsletter_campaign_campaign_test-send-btn"]').click(function(event) {
            autosave(this, event, true);
        });

        $('#nc_campaign_send_campaign').click(function(event) {

            // Has already been clicked once, therefore got to save the post and send campaign
            if ($(this).hasClass('nc-campaign__send-ready')) {
                return true;
            }

            // Add a class to identify the second click
            $(this).addClass('nc-campaign__send-ready');

            // Show the confirmation
            $('.nc-campaign__confirmation').slideDown();

            // Auto save data before proceeding
            autosave(this, event);
        });


        $('#nc_campaign_send_campaign_cancel').click(function() {

            // Remove the class to ensure the campaign doesn't get sent
            $('#nc_campaign_send_campaign').removeClass('nc-campaign__send-ready');

            // Hide the confirmation
            $('.nc-campaign__confirmation').slideUp();
        })


    });

}(jQuery));