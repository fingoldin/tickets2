Number.prototype.clamp = function(min, max) {
  return Math.min(Math.max(this, min), max);
};

var NUM_TICKETS = 30;
var TICKET_IMAGES = [];
TICKET_IMAGES[0] = [];
TICKET_IMAGES[1] = [];

function showTicket(p, e)
{
	var n = Math.floor(Math.random() * (NUM_TICKETS - 1));

	$(e).empty();

	if(TICKET_IMAGES[p][n].complete)
	{
		TICKET_IMAGES[p][n].classList += " ticket-img";
		$(e).append(TICKET_IMAGES[p][n]);
	}
	else if(!p)
		$(e).append("<img src='/tickets/utils/tickets/" + window.ticketprefix + "/ticket" + (n+1) + ".jpg' class='ticket-img'></img>");
	else
		$(e).append("<img src='/tickets/utils/tickets/" + window.ticketprefix + "/2ticket" + (n+1) + ".jpg' class='ticket-img'></img>");

/*	window.setTimeout(function() {
		window.viewportUnitsBuggyfill.refresh();
	}, 50);
*/
}

function showPoints(e, p)
{
	var e2 = document.createElement("DIV");

	$(e2).load("/tickets/utils/points.html", function()
	{
		$(e2).find("#points-p").html(p.p);

		e.append(e2)
	});
}

function gaussian(mean, stdev)
{
    var y2;
    var use_last = false;
    return function() {
        var y1;
        if(use_last) {
           y1 = y2;
           use_last = false;
        }
        else {
            var x1, x2, w;
            do {
                 x1 = 2.0 * Math.random() - 1.0;
                 x2 = 2.0 * Math.random() - 1.0;
                 w  = x1 * x1 + x2 * x2;
            } while( w >= 1.0);
            w = Math.sqrt((-2.0 * Math.log(w))/w);
            y1 = x1 * w;
            y2 = x2 * w;
            use_last = true;
       }

       var retval = mean + stdev * y1;
       if(retval > 0) 
           return retval;
       return -retval;
   }
}

function GAnimData(mean, variance, min, max, num)
{
	this.mean = mean;
	this.variance = variance;
	this.min = min;
	this.max = max;
	this.num = num;

	this.g = gaussian(this.mean, Math.sqrt(this.variance));
	this.data = [];

	var i = 0;
	while(i < this.num)
	{
		var x = parseInt(this.g());
		if(x >= this.min && x <= this.max)
		{
			this.data.push(x);
			i++;
		}
	}
}


function GAnimData2(min, max, num, clamp) {
        this.min = min;
        this.max = max;
        this.num = num;
        this.clamp = clamp;
        this.data = [];

        this.mean = Math.random() * (this.max - this.min) * (1 - this.clamp * 2) + (this.min + (this.max - this.min) * this.clamp);
        this.stdev = Math.min(this.mean - this.min, this.max - this.mean) / 2;
        this.g = gaussian(this.mean, this.stdev);

        console.log("Generated data with mean: " + this.mean + " and standard deviation: " + this.stdev);

        var i = 0;
        while(i < this.num) {
                var x = parseInt(this.g());
                if(x >= this.min && x <= this.max) {
                        this.data.push(x);
                        i++;
                }
        }
}

