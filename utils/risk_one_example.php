<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div id="risk-main">
    <h3 style="text-align: center">Part 2</h3>
    <p style="margin: 15px">
        Consider you're at a casino. You are spinning the below roulette wheel, which could return a reward from a range of values, each with a probability equal to its slice of the wheel it takes up. After each spin, you have the choice to either accept or reject the spinner's outcome. <b>If you accept the result, the trial ends</b>. Otherwise, you can spin it again, up to a maximum of <span id="risk-count"></span> times. 
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
