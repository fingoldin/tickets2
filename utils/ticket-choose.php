<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<script type="text/javascript">
function resize()
{
    var s_top = $("#jspsych-main").scrollTop();
    var tcs = $("#ticket-choose-seq");
    var h = tcs.height();
    var o = Math.max(0, 1 - s_top / h);
    tcs.css("opacity", o);
}

$(window).resize(resize);
$("#jspsych-main").scroll(resize);
</script>
<div id="ticket-choose-main">
    <div id="ticket-name"></div>
	<div id="ticket-wrap"></div>
    <div id="ticket-choose-progress-wrap">
        <link rel="stylesheet" href="<?= $site_prefix ?>/utils/bootstrap.min.css">
        <script src="<?= $site_prefix ?>/utils/popper.min.js"></script>
        <script src="<?= $site_prefix ?>/utils/bootstrap.min.js"></script>
        <div class="progress">
            <div class="progress-bar" id="ticket-choose-progress"></div>
        </div>
    </div>
	<div>
		<div id="number-animation-above"></div>
		<div id="number-animation">
			<span></span>
			<p></p>
		</div>
		<div id="number-animation-below"></div>
	</div>
	<div class="ticket-choose-footer">
		<button id="ticket-choose-select">Choose this</button>
		<button id="ticket-choose-next" style="background-color: #ddd; color: black">Next price <span style="font-size: 20px">&#187;</span></button>
	</div>
</div>
