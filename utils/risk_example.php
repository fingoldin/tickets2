<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div id="risk-main">
    <h2 style="text-align: center">3.Task: Trial run</h2>
    <p style="margin: 15px">
        Now, consider you're at the casino again and you want to try your luck again.<br>
        Here is a spinner, as in the 2. Task, however this time, you can spin up to <span id="risk-count"></span> times in each trial.
        Each time you spin it, you can either accept the result as a reward, or spin the spinner again in hopes of a higher reward.<br>
        This time, the values on the spinner stay the same throughout the whole task.<br>
        If you choose the outcome at any point, the current trial will end and you will proceed to the next trial.

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
    <!--
    <div class="risk-low">
        <h2 class="risk-option"><b>Option 1</b></h2>
        <button id="risk-low-button"></button>
    </div>
    -->
    <div class="risk-canvas-wrap">
        <h2 class="risk-option"><b> </b></h2>
        <canvas id="risk-canvas">
            Canvas isn't supported by your browser. Click anywhere in this box to spin the spinner.
        </canvas>
    </div>
    <div id="risk-result">
        <div class="risk-result-inner">
            <span id="risk-result-money"></span>
            <br>
            <button id="risk-result-no" style="display: none; border-color: #F44336;background-color: #F44336">Spin again</button>
            <button id="risk-result-done">Done</button>
            <br>
        </div>
    </div>
</div>
