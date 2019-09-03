<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div id="risk-main">
    <h3 style="text-align: center">Part 2</h3>
    <p style="margin: 15px">
        <b>Now let's begin the real trials</b>. Again, consider you're at a casino. You are spinning the below roulette wheel, which could return a reward from a range of values, each with a probability equal to its slice of the wheel it takes up. After each spin, you have the choice to either accept or reject the spinner's outcome. <b>If you accept the result, the trial ends</b>. Otherwise, you can spin it again, up to a maximum of <span id="risk-count"></span> times. 
    </p>
    <br>
    <div class="risk-canvas-wrap">
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
