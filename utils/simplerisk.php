<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div id="risk-main">
    <p style="margin: 15px">
        Now, consider you're at the casino. You will do 30 trials of the following choice: either option 1, which will give you $180, or option 2, which will give you $220 or $140 with equal chance. At the end of these 30 trials, one will be chosen and its value will be converted to real money by dividing by 1000 (so, $180 in the gamble would correspond to 0.18 dollars of real money), and that will be added to your bonus.
    </p>
    <br>
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
        <button id="risk-low-button">$180</button>
    </div>
    <div class="risk-canvas-wrap">
        <h2 class="risk-option"><b>Option 2</b></h2>
        <div id="risk-spinner-result">
            <div class="risk-spinner-result-inner">
                <span id="risk-spinner-result-text">Your ticket will cost $180.</span>
                <br>
                <button id="risk-spinner-result-no">No</button>
                <button id="risk-spinner-result-yes">Yes</button>
            </div>
        </div>
        <canvas id="risk-canvas">
            Canvas isn't supported by your browser. 
            <button id="risk-button"></button>
        </canvas>
    </div>
    <div id="risk-result">
        <div class="risk-result-inner">
            <span id="risk-result-money"></span>
            <br>
            <button id="risk-result-done">Done</button>
            <br>
        </div>
    </div>
</div>