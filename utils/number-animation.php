<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<div id="wrong-top" style="display:none;" class="top-alert grey">Oops, your estimate was not precise enough - please take a look at some more potential tickets</div>
<div class="ticket-choose-main">
  <img class="logo-img small" src="<?= $site_prefix ?>/utils/logo.png"></img>
	<div>
		<div class="number-animation">
			<span></span>
			<p></p>
		</div>
	</div>
	<div class="ticket-choose-footer">
		<button class="big-btn" id="ticket-choose-next">Next</button>
	</div>
</div>
