<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div id="risk-main">
    <h3 style="text-align: center">Part 2</h3>
    <p style="margin: 15px">
        Consider you're at a casino. You have two choices before you: either you choose a fixed-dollar reward, or you try your luck on the spinner. <b>If you choose the fixed reward, the game ends</b>. However, if you spin the spinner, you have the choice to either accept or reject its outcome, and spin it again (or select the fixed reward). You can spin it a maximum of <span id="risk-count"></span> times. 
    </p>
    <p style="text-align: center; font-size: 20px">
        <b>This is a trial run, not for real monetary reward</b>.
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
            <button id="risk-result-no" style="display: none; border-color: #F44336;background-color: #F44336">No</button>
            <button id="risk-result-done">Done</button>
            <br>
        </div>
    </div>
</div>
