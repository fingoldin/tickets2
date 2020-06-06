<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div id="ticket-choose-seq">
    Sequence <span id="ticket-choose-seq-num"></span> of <span id="ticket-choose-seq-total"></span>
    <img class="logo-img small" src="<?= $site_prefix ?>/utils/crashed.jpg"></img>
</div>
<div class="ticket-choose-main">
    <div id="ticket-choose-progress-wrap">
        <link rel="stylesheet" href="<?= $site_prefix ?>/utils/bootstrap.min.css">
        <script src="<?= $site_prefix ?>/utils/popper.min.js"></script>
        <script src="<?= $site_prefix ?>/utils/bootstrap.min.js"></script>
        <div class="progress">
            <div class="progress-bar" id="ticket-choose-progress"></div>
        </div>
    </div>
	<div style="width: 500px">
		<div class="number-animation-above">Selling <span>1</span> of <span>10</span></div>
		<div class="number-animation">
			<span></span>
			<p></p>
		</div>
		<div class="number-animation-below"></div>
	</div>
	<div class="ticket-choose-footer">
		<button id="ticket-choose-select" style="border-color: #F44336;background-color: #F44336">SELL</button>
		<button id="ticket-choose-next">CONTINUE</button>
	</div>
</div>
