<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div id="risk-main">
    <h2 style="text-align: center">2. Task</h2>
    <p style="margin: 15px">
        Remember, you have the following choices:<br><br>
        <b>Option 1</b>: you get the fixed amount of money<br>
        <b>Option 2</b>: You try your luck on the wheel <br><br>
        Be aware that the values on the wheel are changing between the gambles.
        <br><br>
        There is a total of  <span id="risk-count"></span>  gambles.
    </p>
    <br>
    <div class="risk-low">
        <h2 class="risk-option"><b>Option 1</b></h2>
        <button id="risk-low-button"></button>
    </div>
    <div class="risk-canvas-wrap">
        <h2 class="risk-option"><b>Option 2</b></h2>
        <canvas id="risk-canvas">
            Canvas isn't supported by your browser. Click anywhere in this box to spin the spinner.
        </canvas>
    </div>
    <div id="risk-result">
        <div class="risk-result-inner">
            <span id="risk-result-money"></span>
            <br>
            <button id="risk-result-done">Ok</button>
            <br>
        </div>
    </div>
</div>
<div id="midpage" style="display:none;">
  <h3 style="margin: 15px; text-align: center; font-weight: normal">
    Now the spinner values will change.
  </h3>
  <div style="margin: 20px">
    <button id="midpage-cont">Ok</button>
  </div>
</div>
