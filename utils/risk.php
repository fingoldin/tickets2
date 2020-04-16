<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div id="risk-main">
    <h3 style="text-align: center">Part 2</h3>
    <p style="margin: 15px">
        You have up to <span id="risk-count"></span> trials of the following choice: either option 1, which will give you a fixed reward, or option 2, which will give you one of a set of rewards, with probabilities as shown in the pie chart. The progress bar shows which of these trials you are currently on. If you choose the outcome from option 2 at any point, this trial will end and you will proceed to the next trial.
    </p>
    <br>
    <div id="risk-progress-wrap">
        <link rel="stylesheet" href="<?= $site_prefix ?>/utils/bootstrap.min.css">
        <script src="<?= $site_prefix ?>/utils/popper.min.js"></script>
        <script src="<?= $site_prefix ?>/utils/bootstrap.min.js"></script>
        <div class="progress">
            <div class="progress-bar" id="risk-progress"></div>
        </div>
    </div>
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
            <button id="risk-result-no" style="display: none; border-color: #F44336;background-color: #F44336">No</button>
            <button id="risk-result-done">Done</button>
            <br>
        </div>
    </div>
</div>
