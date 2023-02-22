//to get rid of jQuery, there is left:

var cal = {}; 

q( () => {

<?php
if ($iss) :

	echo "var iss = true;";
	$disabledDays = $nvbk->get_disabled_days( $meta_fields["calendar_id"][0] );

	if ( is_wp_error($disabledDays) ) {
		echo "cal.disabledDays = [];";
		echo "var systemerror = " . json_encode($disabledDays) . ";";
	} else {
		echo "cal.disabledDays = ". json_encode( $disabledDays ) . ";"; 
	}
	echo "cal.apartmentId = " . (int)$meta_fields["calendar_id"][0] . ";";
	echo "cal.apartmentName = '" . $title . "';";
	echo "cal.peopleLimit = " . (int)$meta_fields["capacity"][0] . ";";

else:

	echo "var iss = false;";
	echo "cal.disabledDays = [];";
	echo "cal.peopleLimit = 1;";

endif;
?>

cal.peopleNumber = 1;

cal.formNode = q("form.availability-form");
cal.spinnerNode = cal.formNode.q(".spinner-wrapper");
cal.peopleNode = cal.formNode.q("#field-people");
cal.priceNode = cal.formNode.q("#field-price");

cal.begin = {
	fieldNode : q("#field-begin"),
	calendarNode : q("#calendar-begin"),
	datepickerNode : q("#datepicker-begin"),
	input: q("#field-begin input"),
	selected : [],

	args : {
		selector: '#calendar-begin',
	    format: "YYYY-MM-DD",
	    todayHighlight: true,
	    disableDates: cal.disabledDays,
	    weekStart: 1,
	    onNavigation: () => {
	    	cal.redraw("begin");
	    	//cal.calculateRates("begin");
	    },
	    onSelect: () => cal.select("begin")
	},
};

cal.end = {
	fieldNode : q("#field-end"),
	calendarNode : q("#calendar-end"),
	datepickerNode : q("#datepicker-end"),
	input : q("#field-end input"),
	selected : [],

	args : {
		selector: '#calendar-end',
	    format: "YYYY-MM-DD",
	    todayHighlight: true,
	    disableDates: cal.disabledDays,
	    weekStart: 1,
	    onNavigation: () => {
	    	cal.redraw("end");
	    	//cal.calculateRates("end");
	    },
	    onSelect: () => cal.select("end")
	},
};






var URLParams = new URLSearchParams(location.search);

if (URLParams.get("begin")) {
	var day = new Date(URLParams.get("begin"));
	cal.begin.fieldNode.q(".field-value").content(day.getDate() + ". " + (day.getMonth() + 1) + ". " + day.getFullYear() );
	cal.begin.args.daysSelected = [URLParams.get("begin")];
	cal.begin.selected = [URLParams.get("begin")];
}
if (URLParams.get("end")) {
	var day = new Date(URLParams.get("end"));
	cal.end.fieldNode.q(".field-value").content(day.getDate() + ". " + (day.getMonth() + 1) + ". " + day.getFullYear() );
	cal.end.args.daysSelected = [URLParams.get("end")];
	cal.end.selected["end"] = [URLParams.get("end")];
}


cal.begin.hw = new HelloWeek( cal.begin.args );
cal.end.hw = new HelloWeek( cal.end.args );




//USER INTERACTION FUNCTIONS - show, set, reset and navigate

cal.redraw = function (selector)
{
	var theOther = cal.otherSelector(selector);
	
	if (selector == "begin") {
		if (cal[theOther].selected.length != 0) {
			cal[selector].hw.setMaxDate(new Date(cal[theOther].selected[0]).shift(-2).getTime());
		} else {
			cal[selector].hw.setMaxDate(new Date().shift(365 * 2));
		}
		cal["begin"].hw.setMinDate(new Date().shift(1).getTime());
	}
	if (selector == "end") {
		if (cal[theOther].selected.length != 0) {
			cal[selector].hw.setMinDate(new Date(cal[theOther].selected[0]).shift(+3).getTime());
		}
		else {
			cal[selector].hw.setMinDate(new Date().shift(1).getTime());
		}
		cal[selector].hw.setMaxDate(new Date().shift(365 * 2).getTime());
	}

	cal[selector].hw.update();
	cal[theOther].hw.update();

	if (cal[theOther].selected.length != 0) {
		cal[selector].calendarNode.q(".month .day").removeClass("is-highlight");
		if(cal[selector].hw.getMonth() == cal[theOther].hw.getMonth())
			cal[selector].hw.days[ parseInt( cal[theOther].selected[0].slice(-2) ) ].element.addClass("is-highlight");
		//cal.registerMouseOver(selector);
		cal[selector].calendarNode.q(".month").on("mouseout", (e) => cal.handleMouseOver(selector,e,true));
	}
}

cal.show = function (selector)
{
	otherSelector = cal.otherSelector(selector);

	cal[selector].datepickerNode.addClass("show");

	if (cal[selector].selected.length != 0) {
		cal[selector].hw.goToDate(cal[selector].selected[0]);
	} else {
		if (cal[otherSelector].selected.length != 0)
			cal[selector].hw.goToDate(cal[otherSelector].selected[0]);
	}
	cal.redraw(selector);
	//cal.calculateRates(selector);
}

cal.set = function (selector)
{
	var otherSelector = cal.otherSelector(selector);
	cal[selector].selected = cal[selector].hw.daysSelected;

	if ( cal[selector].selected.length != 0)
	{
		var day = new Date(cal[selector].selected[0]);
		cal[selector].fieldNode.q(".field-value").content(day.getDate() + ". " + (day.getMonth() + 1) + ". " + day.getFullYear() );
 
		cal[selector].input[0].value = cal.dateToString(day);
		
		if (cal[otherSelector].selected.length != 0 && iss) {
			cal.spinnerNode.show();
			cal.preCheckout("yes", (data) => {
				var data = JSON.parse(data);
				//console.log(data);
				cal.priceNode.show().q(".field-value").content(data["price"] + " Kč");
				cal.spinnerNode.hide();
			});	
		}
	}
	//validace OK
	cal.redraw(selector);
	jQuery("#datepicker-" + selector).removeClass("show");
}

cal.reset = function (selector)
{
	cal[selector].selected = [];

	cal[selector].datepickerNode.removeClass("show");
	cal[selector].fieldNode.q(".field-value").content("Vybrat");

	cal.redraw(selector);
	cal[selector].input.attr("value", "");
}


cal.handleMouseOver = function (selector, event, mouseout = false)
{
	theOther = cal.otherSelector(selector);
	if (cal[theOther].selected.length == 0) return;

	var otherSelector = cal.otherSelector(selector);
	var days = cal[selector].calendarNode.q(".month .day");
	var daySelected = cal[selector].calendarNode.q(".day.is-selected");
	var dayHighlight;
	var highlighting = false;

	days.removeClass("range");

	if (cal[selector].calendarNode.q(".month .day.is-highlight").length == 0)
	{
		if (selector == "end") dayHighlight = days[0];
		else dayHighlight = days[days.length - 1];
	} else
	{
		var dayHighlight = cal[selector].calendarNode.q(".day.is-highlight")[0];
	}
	console.log(selector);
	if ( selector = "begin" ) {
		for ( let i = 0; i < days.length; i++ )
		{
			if ((days[i] == event.currentTarget) ||
				(mouseout && daySelected[0] == days[i]))
				highlighting = true;

			if (highlighting) days[i].addClass("range");
			
			if ((days[i] == dayHighlight) ||
				(mouseout && days[i] == dayHighlight))
				break;
		}
	}
	else if ( selector == "end" ) {
		console.log(dayHighlight);
		for ( let i = 0; i < days.length; i++ )
		{
			
			//if (mouseout && (daySelected.length != 0)) break;
			if (days[i] == dayHighlight) highlighting = true;

			if (highlighting) days[i].addClass("range");
			
			if ((mouseout && (days[i] == daySelected[0])) ||
			    (days[i] == event.currentTarget))
			    break;
		}
	}
}
cal.registerMouseOver = function (s) {
	cal[s].calendarNode.q(".month .day").onmouseover = (e) => cal.handleMouseOver(s,e);
}


cal.setPeople = function (num) {
	var newValue = cal.peopleNumber + num;

	if (newValue < 1) newValue = 1;
	if (newValue > cal.peopleLimit) newValue = newValue - num;
	
	cal.peopleNumber = newValue;
	q("#field-people .field-value").content(newValue);
}

cal.dateToString = function (date) { return [date.getFullYear(), ("0" + (date.getMonth() + 1)).slice(-2), ("0" + date.getDate()).slice(-2)].join("-"); }
Date.prototype.shift = function (by) { this.setDate(this.getDate() + by); return this; };

cal.select = function (selector) { cal[selector].selected = cal[selector].hw.daysSelected; };
cal.otherSelector = function (s) { return (s == "begin") ? "end" : "begin" };




//AJAX FUNCTIONS

cal.calculateRates = function (selector, dateSelected = new Date() )
{
	if (!iss) return;
	if ( cal[selector].selected.length != 0 )
	{
		var dateSelected = new Date( cal[selector].selected[0] );
	}

	dateSelected.setDate(1);
	var begin = cal.dateToString( dateSelected );
	var end = cal.dateToString( new Date( dateSelected.getFullYear(), dateSelected.getMonth() + 1, 0 ) );

	cal[selector].calendarNode.q(".month .day").attr("data-price", "");
	
	jax.post( "/wp-admin/admin-ajax.php",
		{
			"action": "nvbk_show_rates",
			"from": begin,
			"to": end,
			"apartmentId" : cal.apartmentId
		},
		(data) =>
		{
			var rates = JSON.parse(data)["data"][cal.apartmentId];
			
			var days = q("#calendar-" + selector + " .month .day");
			for( let i = 0; i < days.length; i++ )
			{
				var rateDay = "0" + (i + 1);
				var index = begin.slice(0,8) + rateDay.slice(-2);

				days[i].attr("data-price", rates[ index ]["price"]);
			};
		},
		(error) =>
		{
			alert("chyba lavky!");
			console.log(error);
		}
	);
}

cal.sendToCheckout = function ()
{
	if (cal["begin"].selected.length == 0 || cal["end"].selected.length == 0)
		return alert("Vyberte prosím datum příjezdu i odjezdu");

	cal.spinnerNode.removeClass("hidden");
	cal.preCheckout( "no", (data) => {
		var data = JSON.parse(data);
		if(!data.success) {
			alert("Chybišta se vloudila");
			console.log(data);
		} else {
			location.replace("/checkout");
		}
	});
}

cal.preCheckout = function (isPrecheckout = "yes", successCb = ()=>{}, errorCb = ()=>{})
{
	jax.post( "/wp-admin/admin-ajax.php",
		{
			"action": "nvbk_to_checkout",
			"preCheckout" : isPrecheckout,
			"begin": cal.begin.selected[0],
			"end": cal.end.selected[0],
			"people" : cal.peopleNumber,
			"apartmentId" : cal.apartmentId,
			"apartmentName" : cal.apartmentName
		},
		(data) => successCb(data),
		(error) => errorCb(error)
	);
}

cal.search = function ()
{
	if (cal["begin"].selected.length == 0 || cal["end"].selected.length == 0)
		return alert("Vyberte prosím datum příjezdu i odjezdu");

	location.replace("/ubytovani?begin=" + cal["begin"].selected[0] + "&end=" + cal["end"].selected[0]);
}



document.body.on("click", (e) => {
	if ( e.path.indexOf(cal.formNode[0]) == -1) {
		console.log();
	}
		//q(".datepicker").removeClass("show");
});
console.log("loaded");
cal.spinnerNode.hide();

}); //DOMCONTENTLOADED 