(function ( $ ) {
    "use strict";

    $(function () {

        /*
         * NEWSLETTER BUILDER
         *
         * Manages the dragging and dropping of elements within builder
         */

        /* SCOPED VARIABLES */

        // Store offsets for dragging
        var dragAreaOffset = $('.d24nc-metabox-builder__posts').offset(),
            dropMarginTop = parseInt($('.d24nc-metabox-builder__output').css('marginTop')),
            dropMarginLeft = parseInt($('.d24nc-metabox-builder__output').css('marginLeft')),

            draggableAttr = {
                start: function(event, ui) {
                    $(this).data('originalOffset', ui.offset);
                },
                revert: 'invalid'
            },

            droppableAttr = {
                activeClass: 'ui-state-default',
                hoverClass: 'ui-state-hover',
                accept: '.d24nc-metabox-builder__post',
                drop: function( event, ui ) {
                    drop($(this), event, ui);
                }
            };

        /* FUNCTIONS */

        /*
         * Set the new position of the dropped item after dropping
         * Param    jQuery object of the dropped item
         *          event
         *          ui
         */

        function drop($this, event, ui) {

            ui.draggable.detach().appendTo($this);
            var originalOffset = ui.draggable.data('originalOffset');

            var dropItem = $this.children('.d24nc-metabox-builder__post');
            var boxPosition = dropItem.position();

            var container = $this;
            var containerPosition = container.position();

            var newTop = originalOffset.top + boxPosition.top - containerPosition.top - dragAreaOffset.top - dropMarginTop;
            var newLeft = originalOffset.left + boxPosition.left - containerPosition.left - dragAreaOffset.left - dropMarginLeft;

            dropItem.css({top:newTop,left:newLeft}).animate({top:0,left:0});

            // Set the name of the post from the drop area data-name value
            if (dropItem.closest('.d24nc-metabox-builder__output').length) {
                dropItem.find('.d24nc-metabox-builder__post-id').attr('name', 'd24nc_metabox_builder_' + dropItem.closest('.d24nc-metabox-builder__output').attr('data-name') + '[]');
            } else {
                dropItem.find('.d24nc-metabox-builder__post-id').removeAttr('name');
            }

        }


        /* ON LOAD FUNCTION CALLS */

        /*
         * Bind all repeater items to jquery ui draggable
         */

        $('.d24nc-metabox-builder__post').draggable(draggableAttr);


        /*
         * Bind all empty repeater drop areas to jquery ui droppable
         */

        $('.d24nc-metabox-builder__output').droppable(droppableAttr);
        $('.d24nc-metabox-builder__posts').droppable(droppableAttr);


        /* EVENTS */

    });

}(jQuery));