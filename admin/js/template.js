(function ( $ ) {
    var ncCodemirror = [];

    function init() {
        setCodemirrorTextareas();
        addButtonBar();
    }


    /**
     * Instantiate the Codemirror editor
     * @param  {obj} id The textarea to apply the editor to
     */
    function codemirrorEditor(id, order) {
        ncCodemirror[order] = CodeMirror.fromTextArea(id, codemirrorArgs);
    }


    /**
     * Get all textareas to pass to Codemirror
     */
    function setCodemirrorTextareas() {
        $('textarea').each(function(i) {
            codemirrorEditor(this, i);
        });
    }


    /**
     * Generate the buttons to go into each button bar
     * @param  {str} textareaId The id of the textarea with which the button bar is located
     * @return {str}            The complete HTML for the buttons
     */
    function generateButtons(textareaId) {
        var buttonBar = '';

        for (var button in buttons) {
            if (buttons.hasOwnProperty(button)) {

                // If arg instance_include is present make sure to output only for that instance
                if ((buttons[button].instance_include && buttons[button].instance_include !== textareaId) ||
                    (buttons[button].instance_exclude && buttons[button].instance_exclude === textareaId)) {
                    continue;
                }
                buttonBar += '<li class="' + buttons[button].class + '"><a href="#"';
                buttonBar += buttons[button].id ? ' id="' + buttons[button].id + '"' : '';
                buttonBar += '>' + buttons[button].title + '</a>';

                if (buttons[button].children) {
                    buttonBar += '<ul>';

                    for (var buttonChild in buttons[button].children) {
                        if (buttons[button].children.hasOwnProperty(buttonChild)) {
                            var selfButtonChild = buttons[button].children[buttonChild];

                            // If this button should not be in this button bar, skip this iteration
                            if ((selfButtonChild.instance_include && selfButtonChild.instance_include !== textareaId) ||
                                (selfButtonChild.instance_exclude && selfButtonChild.instance_exclude === textareaId)) {
                                continue;
                            }

                            buttonBar += '<li class="' + selfButtonChild.class + '"><a href="#"';
                            buttonBar += selfButtonChild.id ? ' id="' + selfButtonChild.id + '"' : '';
                            buttonBar += ' title="' + selfButtonChild.title + '"';
                            buttonBar += selfButtonChild.shortcode ? ' data-shortcode="' + selfButtonChild.shortcode + '"' : '';
                            buttonBar += '>' + selfButtonChild.title + '</a>';
                            if (selfButtonChild.children) {
                                buttonBar += '<ul>';
                                for (var buttonGrandchild in selfButtonChild.children) {
                                    if (selfButtonChild.children.hasOwnProperty(buttonGrandchild)) {
                                        var selfButtonGrandchild = selfButtonChild.children[buttonGrandchild];

                                        // If this button should not be in this button bar, skip this iteration
                                        if ((selfButtonGrandchild.instance_include && selfButtonGrandchild.instance_include !== textareaId) ||
                                            (selfButtonGrandchild.instance_exclude && selfButtonGrandchild.instance_exclude === textareaId)) {
                                            continue;
                                        }
                                        buttonBar += '<li class="' + selfButtonGrandchild.class + '"><a href="#"';
                                        buttonBar += selfButtonGrandchild.id ? ' id="' + selfButtonGrandchild.id + '"' : '';
                                        buttonBar += ' title="' + selfButtonGrandchild.title + '"';
                                        buttonBar += selfButtonGrandchild.shortcode ? ' data-shortcode="' + selfButtonGrandchild.shortcode + '"' : '';
                                        buttonBar += '>' + selfButtonGrandchild.title + '</a>';
                                        if (selfButtonGrandchild.children) {
                                            buttonBar += '<ul>';
                                            for (var buttonGreatgrandchild in selfButtonGrandchild.children) {
                                                if (selfButtonGrandchild.children.hasOwnProperty(buttonGreatgrandchild)) {
                                                    var selfButtonGreatgrandchild = selfButtonGrandchild.children[buttonGreatgrandchild];

                                                    // If this button should not be in this button bar, skip this iteration
                                                    if ((selfButtonGreatgrandchild.instance_include && selfButtonGreatgrandchild.instance_include !== textareaId) ||
                                                        (selfButtonGreatgrandchild.instance_exclude && selfButtonGreatgrandchild.instance_exclude === textareaId)) {
                                                        continue;
                                                    }

                                                    buttonBar += '<li class="' + selfButtonGreatgrandchild.class + '"><a href="#" id="' + selfButtonGreatgrandchild.id + '" title="' + selfButtonGreatgrandchild.title + '" data-shortcode="' + selfButtonGreatgrandchild.shortcode + '">' + selfButtonGreatgrandchild.title + '</a>';
                                                }
                                            }
                                            buttonBar += '</ul>';
                                        }
                                        buttonBar += '</li>';
                                    }
                                }
                                buttonBar += '</ul>';
                            }
                            buttonBar += '</li>';
                        }
                    }

                    buttonBar += '</ul>';
                }

                buttonBar += '</li>';
            }
        }

        return buttonBar;
    }


    /**
     * Insert the button bar and buttons into the DOM before every Codemirror
     */
    function addButtonBar() {
        $('.CodeMirror').before('<div class="d24nc-button-bar"><ul></ul></div>');

        $('.CodeMirror').each(function() {
            var selfBtns = generateButtons($(this).prevAll('textarea').attr('id'));
            $(this).prev('.d24nc-button-bar').find('ul').append(selfBtns);
        });
    }


    /**
     * Insert the args bar into the DOM
     * @param {obj} args      Properties for arguments
     * @param {str} shortcode The shortcode used by WordPress
     * @param {int} iteration The nth instance
     * @param {str} shortcodeTitle Title of the shortcode
     */
    function addArgsBar(args, shortcode, iteration, shortcodeTitle) {
        var argsBar = '<div class="d24nc-button-bar--args" id="d24nc-button-bar-args-' + shortcode + '">';

        // First remove any currenly active args bars
        $('.d24nc-button-bar--args').remove();
        argsBar += '<h5 class="d24nc-button-bar__title">' + shortcodeTitle + '</h5>';

        for (var arg = 0; arg < args.length; arg++) {
            if (typeof args[arg].title === 'undefined') {
                continue;
            }
            argsBar += '<div class="d24nc-button-bar__arg">';
            argsBar += '<label class="d24nc-button-bar__arg-label" for="' + args[arg].name + '">' + args[arg].title + '</label>';

            if (args[arg].type === 'select' && args[arg].values) {
                argsBar += '<select name="' + args[arg].name + '" id="' + args[arg].name + '" data-arg="' + args[arg].arg + '">';
                // Insert a value-less option if a default hasn't been passed
                if (typeof args[arg].default === 'undefined') {
                    argsBar += '<option value="">' + translation.selectAnOption + '</option>';
                }
                for (var i = 0; i < args[arg].values.length; i++) {
                    var value = args[arg].values[i];
                    // If we've got a specific key and value to use, use them
                    if (args[arg].key && args[arg].value) {
                        argsBar += '<option value="' + value[args[arg].value] + '"';
                        if (typeof args[arg].default !== 'undefined' && value[args[arg].key] === args[arg].default) {
                            argsBar += ' selected';
                        }
                        argsBar += '>' + value[args[arg].key] + '</option>';
                    } else {
                        // A simple array has been passed
                        argsBar += '<option value="' + value + '"';
                        if (typeof args[arg].default !== 'undefined' && value === args[arg].default) {
                            argsBar += ' selected';
                        }
                        argsBar += '>' + value + '</option>';
                    }
                };
                argsBar += '</select>';
            } else if (args[arg].type === 'bool') {
                argsBar += '<input type="checkbox" name="' + args[arg].name + '" id="' + args[arg].name + '" data-arg="' + args[arg].arg + '" value="' + args[arg].arg + '">';
            } else {
                argsBar += '<input type="text" name="' + args[arg].name + '" id="' + args[arg].name + '" placeholder="' + translation.optional + '" data-arg="' + args[arg].arg + '">';
            }
            argsBar += '</div>';
        };

        argsBar += '<button type="button" class="button" id="d24nc-shortcode-arg-btn-' + shortcode + '">' + translation.insert + '</button>'; /* TODO: gettext this val */
        argsBar += '<button type="button" class="button" id="d24nc-shortcode-cancel-btn-' + shortcode + '">' + translation.cancel + '</button>'; /* TODO: gettext this val */
        argsBar += '</div>';

        // Insert into the DOM
        $('.CodeMirror').eq(iteration).prevAll('.d24nc-button-bar').after(argsBar);
    }


    /**
     * Fetch all the args for this shortcode
     * @param  {str} shortcode The shortcode WordPress uses
     * @param  {int} iteration The nth instance
     */
    function populateWithArgs(shortcode, iteration) {
        var args = '',
            shortcodeComplete,
            nesting;
        $('#d24nc-button-bar-args-' + shortcode + '').find('.d24nc-button-bar__arg').each(function(i) {
            var inputType = $(this).find('input').length ? 'input' : 'select';

            if ($(this).find(inputType).data('arg') === 'nesting') {
                if ($(this).find(inputType).val() > 0) {
                    nesting = $(this).find(inputType).val();
                }
                return true;
            }

            if ($(this).find('input').attr('type') === 'checkbox') {
                if (!$(this).find('input').prop('checked')) {
                    return true;
                }
            }

            var argName = $(this).find(inputType).data('arg'),
                argVal = $(this).find(inputType).val();

            if (argVal) {
                args += ' ' + argName + '="' + argVal + '"';
            }
        });

        shortcodeComplete = '[' + shortcode;
        shortcodeComplete += nesting > 0 ? '_' + nesting : '';
        shortcodeComplete += args + ']';

        // Insert the shortcode with args (if supplied)
        ncCodemirror[iteration].doc.replaceSelection(shortcodeComplete);


        for (var buttonCount = 0; buttonCount < buttons.length; buttonCount++) {
            var button = buttons[buttonCount];
            if (button.shortcode && button.shortcode === shortcode) { // Do we need the double check?
                // Insert the enclosing text
                insertEnclosingText(shortcode, iteration, nesting, buttonCount);
                break;
            } else {

                // Keep lookin
                if (button.children) {
                    for (var buttonChildCount = 0; buttonChildCount < button.children.length; buttonChildCount++) {
                        var buttonChild = button.children[buttonChildCount];
                        if (buttonChild.shortcode && buttonChild.shortcode === shortcode) {
                            // Insert the enclosing text
                            insertEnclosingText(shortcode, iteration, nesting, buttonCount, buttonChildCount);
                            break;
                        } else {

                            // Keep lookin
                            if (buttonChild.children) {
                                for (var buttonGrandchildCount = 0; buttonGrandchildCount < buttonChild.children.length; buttonGrandchildCount++) {
                                    var buttonGrandchild = buttonChild.children[buttonGrandchildCount];
                                    if (buttonGrandchild.shortcode && buttonGrandchild.shortcode === shortcode) {
                                        // Insert the enclosing shortcode
                                        insertEnclosingText(shortcode, iteration, nesting,buttonCount, buttonChildCount, buttonGrandchildCount);
                                        break;
                                    } else {

                                        // Keep lookin
                                        if (buttonGrandchild.children) {
                                            for (var buttonGreatgrandchildCount = 0; buttonGreatgrandchildCount < buttonGrandchild.children.length; buttonGreatgrandchildCount++) {
                                                var buttonGreatgrandchild = buttonGrandchild.children[buttonGreatgrandchildCount];
                                                if (buttonGreatgrandchild.shortcode && buttonGreatgrandchild.shortcode === shortcode) {
                                                    // Insert the enclosing shortcode
                                                    insertEnclosingText(shortcode, iteration, nesting, buttonCount, buttonChildCount, buttonGrandchildCount, buttonGreatgrandchildCount);
                                                    break;
                                                }

                                            }
                                        }

                                    }
                                }

                            }

                        }
                    }
                }

            }

        }
    }


    function insertEnclosingText(shortcode, iteration, nesting, button, child, grandchild, greatgrandchild) {
        var targetChild,
            closingTag,
            cursorPos;

        if (greatgrandchild || greatgrandchild === 0) {
            targetChild = buttons[button].children[child].children[grandchild].children[greatgrandchild];
        } else if (grandchild || grandchild === 0) {
            targetChild = buttons[button].children[child].children[grandchild];
        } else {
            targetChild = buttons[button].children[child];
        }

        if (targetChild.enclosing) {
            closingTag = '[/' + shortcode;
            closingTag += nesting > 0 ? '_' + nesting + ']': ']';
            ncCodemirror[iteration].doc.replaceSelection(closingTag);

            // Set the cursor to the middle of the tags
            var cursor = ncCodemirror[iteration].doc.getCursor();

            // Calculate the middle of the tags, 3 is total bracket chars
            // 5 is if the nesting characters are present
            cursorPos = cursor.ch - shortcode.length;
            cursor.ch = nesting > 0 ? cursorPos - 5 : cursorPos - 3;

            ncCodemirror[iteration].doc.setCursor(cursor);

            // Add the enclosing text
            ncCodemirror[iteration].doc.replaceSelection(targetChild.enclosing_text);

            // Select the added enclosing text
            ncCodemirror[iteration].doc.setSelection(cursor, {ch: cursor.ch + targetChild.enclosing_text.length, line: cursor.line});

            // Set the focus to the codemirror instance
            ncCodemirror[iteration].doc.cm.focus();
        }
    }


    /**
     * Insert shortcode at the cursor, or if has optional parameters, fetch them
     * @param  {str} shortcode The shortcode WordPress uses
     * @param  {int} iteration The nth instance
     */
    function populateWithShortcode(shortcode, iteration) {
        // Find out if this shortcode takes args and/or enclosing text
        for (var buttonCount = 0; buttonCount < buttons.length; buttonCount++) {
            var button = buttons[buttonCount];
            if (button.shortcode && button.shortcode === shortcode) { // Do we need the double check?

                // Found the shortcode
                if (button.args) {
                    // Add the args bar
                    addArgsBar(button.args, shortcode, iteration, button.title);
                } else {
                    // Insert the shortcode without args
                    ncCodemirror[iteration].doc.replaceSelection('[' + shortcode + ']');
                    // Insert the enclosing shortcode
                    insertEnclosingText(shortcode, iteration, null, buttonCount);
                }
                break;
            } else {

                // Keep lookin
                if (button.children) {

                    for (var buttonChildCount = 0; buttonChildCount < button.children.length; buttonChildCount++) {
                        var buttonChild = button.children[buttonChildCount];
                        if (buttonChild.shortcode && buttonChild.shortcode === shortcode) {

                            // Found the shortcode
                            if (buttonChild.args) {
                                // Add the args bar
                                addArgsBar(buttonChild.args, shortcode, iteration, buttonChild.title);
                            } else {
                                // Insert the shortcode without args
                                ncCodemirror[iteration].doc.replaceSelection('[' + shortcode + ']');
                                // Insert the enclosing shortcode
                                insertEnclosingText(shortcode, iteration, null, buttonCount, buttonChildCount);
                            }
                            break;
                        } else {

                            // Keep lookin
                            if (buttonChild.children) {
                                for (var buttonGrandchildCount = 0; buttonGrandchildCount < buttonChild.children.length; buttonGrandchildCount++) {
                                    var buttonGrandchild = buttonChild.children[buttonGrandchildCount];
                                    if (buttonGrandchild.shortcode && buttonGrandchild.shortcode === shortcode) {

                                        // Found the shortcode
                                        if (buttonGrandchild.args) {
                                            // Add the args bar
                                            addArgsBar(buttonGrandchild.args, shortcode, iteration, buttonGrandchild.title);
                                        } else {
                                            // Insert the shortcode without args
                                            ncCodemirror[iteration].doc.replaceSelection('[' + shortcode + ']');
                                            // Insert the enclosing shortcode
                                            insertEnclosingText(shortcode, iteration, null, buttonCount, buttonChildCount, buttonGrandchildCount);
                                        }
                                        break;
                                    } else {

                                        // Keep lookin
                                        if (buttonGrandchild.children) {
                                            for (var buttonGreatgrandchildCount = 0; buttonGreatgrandchildCount < buttonGrandchild.children.length; buttonGreatgrandchildCount++) {
                                                var buttonGreatgrandchild = buttonGrandchild.children[buttonGreatgrandchildCount];
                                                if (buttonGreatgrandchild.shortcode && buttonGreatgrandchild.shortcode === shortcode) {

                                                    // Found the shortcode
                                                    if (buttonGreatgrandchild.args) {
                                                        // Add the args bar
                                                        addArgsBar(buttonGreatgrandchild.args, shortcode, iteration, buttonGreatgrandchild.title);
                                                    } else {
                                                        // Insert the shortcode without args
                                                        ncCodemirror[iteration].doc.replaceSelection('[' + shortcode + ']');
                                                        // Insert the enclosing shortcode
                                                        insertEnclosingText(shortcode, iteration, null, buttonCount, buttonChildCount, buttonGrandchildCount, buttonGreatgrandchildCount);
                                                    }
                                                    break;
                                                }

                                            }
                                        }

                                    }
                                }

                            }

                        }
                    }
                }

            }

        }
    }


    /**
     * Remove the current argument bar
     * @param  {obj} $self The pressed cancel button
     */
    function cancelArgInput($self) {
        if ($self.closest('.d24nc-button-bar').next('.d24nc-button-bar--args').length) {
            $self.closest('.d24nc-button-bar').next('.d24nc-button-bar--args').remove();
        } else {
            $self.closest('.d24nc-button-bar--args').remove();
        }
    }


    /**
     * On load function calls
     */
    init();


    /*
     * EVENTS
     */

    // Click a shortcode button
    $('body').on('click', '.d24nc-button-bar__button a', function(e) {
        // Remove any current args bar
        cancelArgInput($(this));
        // Get the instance iteration
        var iteration = $(this).closest('.d24nc-button-bar').nextAll('.CodeMirror').index('.CodeMirror');
        populateWithShortcode($(this).data('shortcode'), iteration);
        e.preventDefault();
    });

    // Click the shortcode insert with args button
    $('body').on('click', '[id^=d24nc-shortcode-arg-btn-]', function(e) {
        var idSplit = $(this).attr('id').split('-'),
            shortcode = idSplit[idSplit.length - 1],
            iteration = $(this).closest('.d24nc-button-bar--args').nextAll('.CodeMirror').index('.CodeMirror');
        populateWithArgs(shortcode, iteration);
        e.preventDefault();
    });

    // Click the shortcode args cancel button
    $('body').on('click', '[id^=d24nc-shortcode-cancel-btn-]', function(e) {
        cancelArgInput($(this));
    });

}(jQuery));