<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div id="risk-main">
    <h2 style="text-align: center">2.Task: Trial run</h2>
    <p style="margin: 15px">
        Here is the gamble.<br>
        You have two choices in each game: <br> Either you choose a fixed reward (Option 1) or you spin the wheel (Option 2). <br><br>
        If you spin the wheel of fortune your win depends on where the spinner stops. <br>
        The numbers on the wheel tell you how much each region of the wheel is worth.
        You can start the spinner by clicking on it (or the wheel).<br>
        Let's do <span id="risk-count"></span> trial gambles before we start with the real gambles.
    </p>
    <p style="text-align: center; font-size: 20px">
        <b>These are trial runs, not for real monetary reward</b>.
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
            <button id="risk-result-no" style="display: none; border-color: #F44336;background-color: #F44336">No</button>
            <button id="risk-result-done">Done</button>
            <br>
        </div>
    </div>
</div>
