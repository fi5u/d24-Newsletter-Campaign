(function ( $ ) {
    "use strict";

    $(function () {

        /*
         * REPEATER FIELD
         *
         * Manages the dragging and dropping of repeater field elements
         */


        /* SCOPED VARIABLES */

            // List the input types used in the repeater
        var inputTypes = [
            'input[type="text"]',
            'textarea'
            ],

            // Store offsets for dragging
            dragAreaOffset = $('.d24nc-metabox-repeater').offset(),
            dropMarginTop = parseInt($('.d24nc-metabox-repeater__droparea').css('marginTop')),
            dropMarginLeft = parseInt($('.d24nc-metabox-repeater__droparea').css('marginLeft')),

            draggableAttr = {
                handle: '.d24nc-metabox-repeater__item-handle',
                start: function(event, ui) {
                    $(this).data('originalOffset', ui.offset);
                },
                revert: 'invalid'
            },

            droppableAttr = {
                activeClass: 'ui-state-default',
                hoverClass: 'ui-state-hover',
                accept: function() {
                    // If the drop area already has a repeater item, do not drop
                    var $this = $(this);
                    if ($this.has('.d24nc-metabox-repeater__item').length) {
                        return false;
                    } else {
                        return true;
                    }
                },
                drop: function( event, ui ) {
                    repeaterDrop($(this), event, ui);
                    removeExcessDropAreas();
                }
            };

        /* FUNCTIONS */

        /*
         * Detect if any repeater block has any empty fields
         * Return   TRUE if empty
         *          FALSE if not empty
         */

        function repeaterIsEmpty() {
/*.not('.CodeMirror textarea')*/
            var isEmpty = 0;
            $('.d24nc-metabox-repeater__item').find(inputTypes.join()).each(function() {
                if ($(this).val() === '') {
                    isEmpty = 1;
                }
            });
            if (isEmpty === 0) {
                return false;
            } else {
                return true;
            }

        }


        /*
         * Toggle the add row button from disabled to normal
         * Param    TRUE if the button should be disabled
         *          FALSE if the button should be normal
         */

        function repeaterToggleAddRow(makeDisabled) {

            if (makeDisabled) {
                $('#d24nc_metabox_repeater_btn_add').attr('disabled','true');
            } else {
                $('#d24nc_metabox_repeater_btn_add').removeAttr('disabled');
            }

        }


        /*
         * Set the new position of the dropped item after dropping
         * Param    jQuery object of the dropped item
         *          event
         *          ui
         */

        function repeaterDrop($this, event, ui) {

            ui.draggable.detach().appendTo($this);
            var originalOffset = ui.draggable.data('originalOffset');

            var repeaterItem = $this.children('.d24nc-metabox-repeater__item');
            var boxPosition = repeaterItem.position();

            var container = $this;
            var containerPosition = container.position();

            var newTop = originalOffset.top + boxPosition.top - containerPosition.top - dragAreaOffset.top - dropMarginTop;
            var newLeft = originalOffset.left + boxPosition.left - containerPosition.left - dragAreaOffset.left - dropMarginLeft;

            repeaterItem.css({top:newTop,left:newLeft}).animate({top:0,left:0});

        }


        /*
         * Create a clone of a drop area and place it in the desired position
         * Param    jQuery object of the repeater item to clone from (not the droparea)
         *          where the clone should be placed: 'before' or 'after'
         */

        function cloneDropArea($cloneFrom, place) {

            var repeaterClone = $cloneFrom.closest('.d24nc-metabox-repeater__droparea').clone().empty();
            if (place === 'after') {
                $cloneFrom.closest('.d24nc-metabox-repeater__droparea').after(repeaterClone);
            } else {
                $cloneFrom.closest('.d24nc-metabox-repeater__droparea').before(repeaterClone);
            }

            // Bind it to droppable
            repeaterClone.droppable(droppableAttr);

            // Bind it to draggable
            repeaterClone.find('.d24nc-metabox-repeater__item').draggable(draggableAttr);

        }


        /*
         * Ensure that there is an empty drop area between each repeater item
         */

        function addDropAreas() {

            var repeaterQty = $('.d24nc-metabox-repeater__item').length;

            // Iterate through each repeater item
            $('.d24nc-metabox-repeater__item').each(function(i) {

                // If the first element has not got an empty drop area above, add one
                if (i === 0 && !$(this).closest('.d24nc-metabox-repeater__droparea').prev('.d24nc-metabox-repeater__droparea').length) {
                    cloneDropArea($(this), 'before');
                }

                // If there are two full drop areas next to each other, put an empty one between
                if ($(this).closest('.d24nc-metabox-repeater__droparea').next('.d24nc-metabox-repeater__droparea').has('.d24nc-metabox-repeater__item').length) {
                    cloneDropArea($(this), 'after');
                }

                // If the last repeater does not have an empty drop area after it, add one
                if (i === repeaterQty-1 && !$(this).closest('.d24nc-metabox-repeater__droparea').next('.d24nc-metabox-repeater__droparea').length) {
                    cloneDropArea($(this), 'after');
                }
            });

        }


        /*
         * If there are two or more empty drop areas together, remove the excess
         */

        function removeExcessDropAreas() {

            $('.d24nc-metabox-repeater__droparea').each(function() {

                // If there are two empty dropareas together, remove the second one
                if ($(this).is(':empty') && $(this).next('.d24nc-metabox-repeater__droparea').is(':empty')) {
                    $(this).remove();
                }
            });

            // Proceed to add in extra drop areas
            addDropAreas();

            // Check repeater quantity to disable delete button if needed
            checkRepeaterQty();
        }


        /*
         * Add a new repeater item and bind its drag and drop functions
         */

        function addRepeaterBlock() {

            var repeaterClone = $('.d24nc-metabox-repeater__item').first().closest('.d24nc-metabox-repeater__droparea').clone();

            // Empty all values
            repeaterClone.find(inputTypes.join()).val('');

            repeaterClone.appendTo('.d24nc-metabox-repeater');

            // Fill the hidden input with a random hash for id
            repeaterClone.find('.d24nc-metabox-repeater__hidden-id').val(Math.random().toString(36).replace(/[^a-zA-Z0-9]+/g, '').substr(0,8));

            // Get an empty drop area to put after the new repeater
            $('.d24nc-metabox-repeater__droparea').first().clone().empty().appendTo('.d24nc-metabox-repeater');
            repeaterToggleAddRow(true);

            // Bind it to draggable
            repeaterClone.find('.d24nc-metabox-repeater__item').draggable(draggableAttr);

            // Check repeater quantity to undisable delete button if needed
            checkRepeaterQty();

        }


        function checkRepeaterQty() {
            var repeaterQty = $('.d24nc-metabox-repeater__item').length;

            if (repeaterQty <= 1) {
                $('.d24nc-metabox-repeater__droparea-delete').attr('disabled','true');
            }
            if (repeaterQty > 1) {
                $('.d24nc-metabox-repeater__droparea-delete').removeAttr('disabled');
            }

            if (repeaterIsEmpty() === false) {

                repeaterToggleAddRow(false);

            }
        }


        /*
         * Delete repeater item
         * Param    jquery object
         */

        function deleteRepeater($repeaterBtn, cb) {

            $repeaterBtn.closest('.d24nc-metabox-repeater__droparea').remove();
            if (cb) cb();

        }


        /* ON LOAD FUNCTION CALLS */

        /*
         * Bind all repeater items to jquery ui draggable
         */

        $('.d24nc-metabox-repeater__item').draggable(draggableAttr);


        /*
         * Bind all empty repeater drop areas to jquery ui droppable
         */

        $('.d24nc-metabox-repeater__droparea:not(:has(.d24nc-metabox-repeater__item))').droppable(droppableAttr);


        /*
         * Check to see if any field in a repeater block is empty
         */

        if (repeaterIsEmpty() === false) {

            repeaterToggleAddRow(false);

        }


        /*
         * Check set repeater delete button to disabled
         */
        checkRepeaterQty();


        /* EVENTS */

        /*
         * Whilst typing in a repeater field, check to see if any fields are still empty
         */

        $('.postbox-container').on('keyup', inputTypes.join(), function() {

            if (repeaterIsEmpty() === false) {
                repeaterToggleAddRow(false);
            } else {
                repeaterToggleAddRow(true);
            }

        });


        /*
         * When add repeater button is clicked generate a new repeater block
         */

        $('#d24nc_metabox_repeater_btn_add').click(function(e) {

            addRepeaterBlock();
            e.preventDefault();

        });


        /*
         * Delete drop area when delete button pressed
         */

        $('body').on('click', '.d24nc-metabox-repeater__droparea-delete', function() {

            deleteRepeater($(this), removeExcessDropAreas);

        });
    });

}(jQuery));