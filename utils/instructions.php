<?php

session_start();

$site_prefix = $_SESSION["site_prefix"];

?>

<script type="text/javascript">

$(window).resize(resize);

function resize()
{
        var height = window.innerHeight;

        $(".consent-main").css("height", height*0.95 + "px");
}

resize();

</script>
	<div id="wrong-top" class="top-alert">That was the incorrect answer. Please read the instructions again.</div>
	<div id="id-check" class="check-main" style="display:none;">
        	<div class="check-head">What do you have to do in the test phase?</div>
        	<div class="check-bot">
                <button id="c-wrong" class="big-btn">A: Choose the fastest car</button>
                	<button id="c-right" class="big-btn">B: Choose the highest price offer</button>
        	</div>
	</div>
  <div id="id-followup" style="display:none;">
    <div class="consent-main">
      <h3>Followup title</h3>
      <div class="cscroller">
        <p>Followup text</p>
      </div>
      <div class="consent-footer">
        <button id="continue2">Continue</button>
      </div>
    </div>
  </div>
	<div id="id-int">
        <div class="consent-main">
                <h3>Experiment Instructions</h3>
		<div class="cscroller">
                       	<p>
        This experiment is divided into 3 tasks.
        <br><br>
        <b>1. Task</b><br>
        In the first task, you are looking to sell a crashed car to a scrapyard for the highest amount you can get.
        <br>
        <b>2. Task</b><br>
        In the second task, you are at the casino and take spins on a wheel of fortune.
        <br>
        <b>3. Task</b><br>
        This task is similar to the second task, but you have the possibility to spin the wheel repeatetly to improve your gain.
        	<br><br>

        Let's start with the first task: <br>

        <b> 1. Task</b>	<br>

        Suppose one day you are driving along, and manage to crash your car, rendering it not usable. Having done this, you now want to sell your car to a scrapyard, obviously for the largest price you can find. But it turns out that there are many scrapyards you could sell it to, and you go out looking for the one that will give you the highest price. Here is your car:
        <img class="logo-img" src="<?= $site_prefix ?>/utils/crashed.jpg"></img>
        You task is to learn about the distribution of prices that these scrapyards will offer you. However, <b>if you decline an offer from a particular scrapyard you cannot go back and sell it there anymore</b>. Given this, you will have to try to choose the highest price from a sequence of prices.
<!--	        <b>3. Gamble phase 1</b>
=======
          You will encounter 60 potential prices and we will then check how
          well you have learned these prices. If you do well enough you can continue to the test phase,
          otherwise you will encounter 60 more prices.
                       		<br><br>
				<b>2. Test phase</b>
				<br>
				You will choose a price out of a sequence of 10 pices that are presented one after the other.
        The goal is to find the highest price. You will be shown 50 of these sequences.
        <br><br>
        <b>3. Gamble phase 1</b>
>>>>>>> 0ebbebe6f385895263656444e15d5f006dd73b53
        <br>
          Here, you will be presented with a spinner. You will be able to spin the spinner up to a 10 times, and each time it returns a number you can either accept that number as the price of the you will sell, or spin again. But, once you spin again you won't be able to go back to previous results. You will be presented with 50 such trials.
        <br><br>
        <b>3. Gamble phase 2</b>
        <br>
          This will be similar to Gamble phase 1, but you will not have the option to choose a fixed reward with every spinner trial, and the spinners themselves may be changing (the probabilities of getting different results from the spinners will be changing).
        <br><br>
        <br>
        -->
<br>
                Please press continue to begin.
<!--				<b>Training phase 2 </b>
				<br>
          This time you plan to fly to Mexico City and you encounter slightly different ticket prices.
          In this section you will see again 50 ticket prices for your new trip.
				<br><br>
        <b>Test phase 2 </b>
        <br>
			  Find again the cheapest ticket out of 10 tickets.
				<br><br>
				This study lasts at most 30 minutes and you will get $2.00 for participating. Further,
				you will get a bonus depending on your performance up to a maximum of $4.00.
				<br><br>
				You will get detailed instructions and a trial run during the study.
				<br><br>
				Please press continue to begin.
			</p>
-->
		</div>
                <div class="consent-footer">
                        <button id="continue1">Continue</button>
                </div>
        </div>
	</div>
