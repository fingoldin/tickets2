/*
 * Example plugin template
 */

jsPsych.plugins["store_order"] = (function() {

  var plugin = {};

  plugin.trial = function(display_element, trial) {

    trial.phase = trial.phase || 0;
    trial.order = trial.order || [];

//console.log(trial.order);

    var trial_data = {
      phase: trial.phase,
      order: trial.order
    };

console.log(trial_data);

    jsPsych.finishTrial(trial_data);
  };

  return plugin;
})();
