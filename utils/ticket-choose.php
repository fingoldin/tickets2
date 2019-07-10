<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div class="ticket-choose-main">
    <div id="ticket-choose-progress-wrap">
        <link rel="stylesheet" href="<?= $site_prefix ?>/utils/bootstrap.min.css">
        <script src="<?= $site_prefix ?>/utils/popper.min.js"></script>
        <script src="<?= $site_prefix ?>/utils/bootstrap.min.js"></script>
        <div class="progress">
            <div class="progress-bar" id="ticket-choose-progress"></div>
        </div>
    </div>
	<div id="ticket-wrap"></div>
	<div>
		<div class="number-animation-above">Ticket <span>1</span> of <span>10</span></div>
		<div class="number-animation">
			<span></span>
			<p></p>
		</div>
		<div class="number-animation-below"></div>
	</div>
	<div class="ticket-choose-footer">
		<button id="ticket-choose-select">Choose this</button>
		<button id="ticket-choose-next" style="background-color: #ddd; color: black">Next price &#187;</button>
	</div>
</div>
