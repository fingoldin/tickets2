<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div id="risk-main">
    <p style="margin: 15px">
        Now, consider you're at the casino. You will do <span id="risk-count"></span> trials of the following choice: either option 1, which will give you a fixed reward, or option 2, which will give you one of two rewards with equal chance. At the end of these 36 trials, one trial will be chosen randomly and its value will be converted to real money by dividing by 200 (so, if you selected option 1 for the chosen trial and were rewarded $180, you would gain $0.90 of real money), and that will be added to your bonus. Each option will have different rewards.
    </p>
    <br>
    <p id="risk-first"><b>The first trial will be an example for you to understand the interface; it will not count for money</b></p>
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
            Canvas isn't supported by your browser. Press the below button to spin the spinner and
            win either <span id="risk-low"></span> or <span id="risk-high"></span>.
            <button id="risk-button">Spin!</button>
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
