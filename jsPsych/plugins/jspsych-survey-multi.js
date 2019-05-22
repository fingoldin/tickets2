jsPsych.plugins['survey-multi'] = (function() {

  var plugin = {};

  plugin.trial = function(display_element, trial) {

    var plugin_id_name = "jspsych-survey-multi-choice";
    var plugin_id_selector = '#' + plugin_id_name;
    var _join = function( /*args*/ ) {
      var arr = Array.prototype.slice.call(arguments, _join.length);
      return arr.join(separator = '-');
    }

    // trial defaults
    trial.preamble = typeof trial.preamble == 'undefined' ? "" : trial.preamble;
    trial.title = typeof trial.title == 'undefined' ? "" : trial.title;
    trial.required = typeof trial.required == 'undefined' ? null : trial.required;
    trial.horizontal = typeof trial.required == 'undefined' ? false : trial.horizontal;

    // if any trial variables are functions
    // this evaluates the function and replaces
    // it with the output of the function
    trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

    // inject CSS for trial
    var node = display_element.append('<style id="jspsych-survey-multi-choice-css">')
    var cssstr = ".jspsych-survey-multi-choice-question { width: 100%; display: inline-block; padding: 1em 5px 1em 15px; text-align: left; }"+
      ".jspsych-survey-multi-choice-question:nth-child(even) { background-color: #eee }"+
      ".jspsych-survey-multi-choice-text span.required {color: darkred;}"+
      ".jspsych-survey-multi-choice-horizontal .jspsych-survey-multi-choice-text { min-height: 70px; max-width: 50%; display: inline-block; }"+
      ".jspsych-survey-multi-choice-option { line-height: 2; }"+
      ".jspsych-survey-multi-choice-horizontal .jspsych-survey-multi-choice-option {  display: inline-block;  margin-left: 1em;  margin-right: 1em;  vertical-align: top;}"+
      "#jspsych-survey-multi-choice-form { min-width: 100px; top: 0; position: absolute; left: 0; padding: 20px;}"+
      "#jspsych-survey-multi-choice-next { margin: 15px; }"+
      ".jspsych-content { padding-top 20px; padding-bottom: 10px; }"+
      ".jspsych-survey-multi-choice-option-group { display: inline-block; float: right }"+
      ".jspsych-survey-multi-choice-preamble span { text-align: left; display: block }"+
      ".jspsych-survey-multi-choice-title { font-size: 30px; margin: 20px }"+
      "label.jspsych-survey-multi-choice-text input[type='radio'] {margin-right: 1em;}"

    $('#jspsych-survey-multi-choice-css').html(cssstr);

    // form element
    var trial_form_id = _join(plugin_id_name, "form");
    display_element.append($('<form>', {
      "id": trial_form_id
    }));
    var $trial_form = $("#" + trial_form_id);
    
    var title_class = _join(plugin_id_name, 'title');
    $trial_form.append($('<div>', {
      "class": title_class
    }));
    $('.' + title_class).html(trial.title);

    // show preamble text
    var preamble_id_name = _join(plugin_id_name, 'preamble');
    $trial_form.append($('<div>', {
      "id": preamble_id_name,
      "class": preamble_id_name
    }));
    $('#' + preamble_id_name).html(trial.preamble);

    // add multiple-choice questions
    for (var i = 0; i < trial.questions.length; i++) {
      // create question container
      var question_classes = [_join(plugin_id_name, 'question')];
      if (trial.horizontal) {
        question_classes.push(_join(plugin_id_name, 'horizontal'));
      }

      $trial_form.append($('<div>', {
        "id": _join(plugin_id_name, i),
        "class": question_classes.join(' ')
      }));

      var question_selector = _join(plugin_id_selector, i);

      // add question text
      $(question_selector).append(
        '<p class="' + plugin_id_name + '-text survey-multi-choice">' + trial.questions[i] + '</p>'
      );
      
      var group_selector = _join(plugin_id_name, "option-group", i);
      $(question_selector).append($('<div>', {
        "id": group_selector,
        "class": _join(plugin_id_name, "option-group")
      }));
      group_selector = "#" + group_selector;

      // create option radio buttons
      for (var j = 0; j < trial.options[i].length; j++) {
        var option_id_name = _join(plugin_id_name, "option", i, j),
          option_id_selector = '#' + option_id_name;

        // add radio button container
        $(group_selector).append($('<div>', {
          "id": option_id_name,
          "class": _join(plugin_id_name, 'option')
        }));

        // add label and question text
        var option_label = '<label class="' + plugin_id_name + '-text">' + trial.options[i][j] + '</label>';
        $(option_id_selector).append(option_label);

        // create radio button
        var input_id_name = _join(plugin_id_name, 'response', i);
        $(option_id_selector + " label").prepend('<input type="radio" name="' + input_id_name + '" value="' + trial.options[i][j] + '">');
      }

      if (trial.required && trial.required[i]) {
        // add "question required" asterisk
        $(question_selector + " p").append("<span class='required'>*</span>")

        // add required property
        $(question_selector + " input:radio").prop("required", true);
      }
    }

    // add submit button
    $trial_form.append($('<button>', {
      'id': plugin_id_name + '-next',
      'class': 'big-btn',
      'form': trial_form_id
    }));
    $("#" + plugin_id_name + "-next").html("Submit answers");

    $trial_form.submit(function(event) {

      event.preventDefault();

      // measure response time
      var endTime = (new Date()).getTime();
      var response_time = endTime - startTime;

      // create object to hold responses
      var question_data = {};
      $("div." + plugin_id_name + "-question").each(function(index) {
        var id = "Q" + index;
        var val = $(this).find("input:radio:checked").val();
        var obje = {};
        obje[id] = val;
        $.extend(question_data, obje);
      });

      // save data
      var trial_data = {
        "rt": response_time,
        "responses": JSON.stringify(question_data)
      };

      display_element.html('');

      // next trial
      jsPsych.finishTrial(trial_data);
    });

    var startTime = (new Date()).getTime();
  };

  return plugin;
})();
