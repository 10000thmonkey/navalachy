class NV_Booking
{
	constructor ( args )
	{
		Date.prototype.shift = function (by) { this.setDate(this.getDate() + by); return this; };
		this.iss = args.iss;
		this.shown = false;
		this.adults = 1;
		this.kids = 0;
		this.peopleLimit = args.peopleLimit;
		this.begin = [];
		this.end = [];
		this.form = q(args.selector);
		this.apartmentId = args.apartmentId;
		this.apartmentName = args.apartmentName;
		this.disableDates = args.disabled;

		this.el =
		{
			calendar: this.form.q("#calendar"),
			spinner: this.form.q(".spinner-wrapper"),
			datepicker: this.form.q("#datepicker"),

			beginValue: this.form.q("#field-begin .field-value"),
			endValue: this.form.q("#field-end .field-value"),

			adultsValue: this.form.q("#field-adults .field-value"),
			kidsValue: this.form.q("#field-kids .field-value"),

			priceField: this.form.q("#field-price"),
			priceFieldSet: q("aside.reservation #fieldset-price")[0],

			messageBox: this.form.q(".messagebox")
		}

		this.hw = new HelloWeek ( {
			format: "YYYY-MM-DD",
			beforeCreateDay: (n) => {
				console.log(n);
			},
			selector: args.selector + " #calendar",
			todayHighlight: true,
			disableDates: args.disabled,
			weekStart: 1,
			range: true,
			disablePastDays: true,
			onSelect: () => {
				this.select();
			}
		} );
	}
	show (sel = false)
	{
		if (this.shown)
		{
			this.focusfield();

			this.el.datepicker.hide();
			this.shown = false;
		}
		else {
			if ( !!this.end ) {
				this.focusfield( "begin" );
			} else {
				this.focusfield( "end" );
			}
			if (this.iss && this.el.datepicker[0].getBoundingClientRect().top < 80)
				window.scrollTo(0, q(".main.columns")[0].offsetTop + q(".main.columns")[0].offsetHeight - window.innerHeight);

			this.el.datepicker.show();
			this.shown = true;
		}
	}
	focusfield (sel = false)
	{
		if ( sel == "begin" ) { 
			this.form.q("#field-end").removeClass("focused");
			this.form.q("#field-begin").addClass("focused");
		}
		else if ( sel == "end" ) {
			this.form.q("#field-begin").removeClass("focused");
			this.form.q("#field-end").addClass("focused");
		}
		else {
			this.form.q("#field-begin").removeClass("focused");
			this.form.q("#field-end").removeClass("focused");
		}
	}
	select ()
	{
		if ( typeof this.hw.intervalRange["end"] != "number" ) {

			let day = new Date( this.hw.intervalRange.begin );
		 	this.el.beginValue.content(day.getDate() + ". " + (day.getMonth() + 1) + ". " + day.getFullYear() );
		 	this.begin = this.dateToString(day);

		 	this.el.datepicker.q(".month .day.is-selected").addClass("is-begin-range");
			this.focusfield("end");
		
		} else {

			let errorcode = -1;

			//check for intersection with disabled dates
			let daysSelected = this.hw.daysSelected;
			let intersect = cal.hw.daysSelected.reduce(function(result, element) {
				if (cal.disableDates.indexOf(element) !== -1) {
					result.push(element);
				}
				return result;
			}, []);
			if ( intersect.length > 0 ) {
				errorcode = 0;
			}

			//check for minimum two days stay
			if ( daysSelected.length < 3 ) {
				errorcode = 1;
			}

			if ( errorcode != -1 )
			{
			 	//reset calendar
				let currentMonth = this.hw.getMonth();
				let d = new Date(  );
				d.setMonth( currentMonth - 1 );
				this.hw.intervalRange = {};
				this.hw.reset();
				this.hw.goToDate( this.dateToString( d ) );

				//display the error
				this.showError( errorcode );

				this.focusfield("begin");
			} else {

				//display form value
				let day = new Date( this.hw.intervalRange.end );
			 	this.el.endValue.content(day.getDate() + ". " + (day.getMonth() + 1) + ". " + day.getFullYear() );
			 	this.end = this.dateToString(day);

			 	//values correct, store for submission
				this.begin = this.dateToString( new Date( this.hw.intervalRange.begin ) );
				this.end = this.dateToString( new Date( this.hw.intervalRange.end ) );

				this.focusfield();
				this.set();
				this.shown = false;

				q('aside.reservation').removeClass('reallyaside').addClass('slided');
				document.body.css('overflow','hidden');
			}
		}
	}
	set ()
	{
		if ( this.isSelected() ) 
		{	
			if ( this.iss ) {
				this.el.spinner.show();
				this.preCheckout("yes", (data) => {
					var data = JSON.parse(data);
					
					this.el.priceField.removeClass("nodisplay").q(".field-value").content(data.price.price_final);

					this.el.priceFieldSet.removeClass("nodisplay").content("");

					if (data.price.costs.length != 0) {
						this.el.priceFieldSet
							.insert(createNode("div").addClass(["field", "field-price-costs"])
							    .insert(createNode("div").addClass("field-label").content("Pronájem"))
							    .insert(createNode("div").addClass("field-value").content(data.price.price_host)))
						for (let cost of data.price.costs) {
							this.el.priceFieldSet
								.insert(createNode("div").addClass(["field", "field-price-costs"])
							    	.insert(createNode("div").addClass("field-label").content(cost[0]))
							    	.insert(createNode("div").addClass("field-value").content(cost[1])));
						}
					}
					if (data.price.discounts.length != 0) {
						for (let discount of data.price.discounts) {
							this.el.priceFieldSet
								.insert(createNode("div").addClass(["field","field-price-discounts"])
							    	.insert(createNode("div").addClass("field-label").content(discount.label))
							    	.insert(createNode("div").addClass("field-value").content(discount.value)));
						}
					}
					this.el.priceFieldSet
						.insert(createNode("div").addClass(["field","field-price-final"]) //celkova cena
					        .insert(createNode("div").addClass("field-label").content("Celkem"))
					        .insert(createNode("div").addClass("field-value").content(data.price.price_final)));


					this.el.spinner.hide();
				});	
			}
		}
		this.focusfield();
		this.el.datepicker.hide();
	}

	setFromUrl (begin, end)
	{
		let beginDay = new Date( begin );
	 	this.el.beginValue.content( beginDay.getDate() + ". " + (beginDay.getMonth() + 1) + ". " + beginDay.getFullYear() );
	 	this.begin = begin;

		let endDay = new Date( end );
	 	this.el.endValue.content( endDay.getDate() + ". " + (endDay.getMonth() + 1) + ". " + endDay.getFullYear() );
	 	this.end = end;
	}

	reset ()
	{
		//unfocus both
		this.focusfield();
		//reset values in cal object
		this.hw.intervalRange = {};
		this.hw.reset();
		//reset values in form
		this.el.beginValue.content("-");
		this.el.endValue.content("-");

		this.el.datepicker.hide();
		this.shown = false;
	}

	setPeople (num, kind)
	{
		if (kind == "adults")
		{
			var newValue = parseInt(this.adults) + parseInt(num);

			if (newValue < 1) newValue = 1;
			if ((newValue + this.kids) > cal.peopleLimit) newValue = newValue - num;
		
			this.adults = newValue;
			this.el.adultsValue.content(newValue);
		}
		else
		{
			var newValue = parseInt(this.kids) + parseInt(num);

			if (newValue < 1) newValue = 1;
			if ((newValue + this.adults) > cal.peopleLimit) newValue = newValue - num;
		
			this.kids = newValue;
			this.el.kidsValue.content(newValue);
		}
	}
	dateToString ( date )
	{
		return [date.getFullYear(), ("0" + (date.getMonth() + 1)).slice(-2), ("0" + date.getDate()).slice(-2)].join("-");
	}


	preCheckout ( isPrecheckout = "yes", successCb = ()=>{}, errorCb = ()=>{})
	{
		jax.post( "/wp-admin/admin-ajax.php",
			{
				"action": "nvbk_to_checkout",
				"preCheckout" : isPrecheckout,
				"begin": this.begin,
				"end": this.end,
				"adults" : this.adults,
				"kids" : this.kids,
				"apartmentId" : this.apartmentId,
				"apartmentName" : this.apartmentName
			},
			(data) => successCb(data),
			(error) => errorCb(error)
		);
	}
	sendToCheckout ()
	{
		if ( !this.isSelected() )
			return alert("Vyberte prosím datum příjezdu i odjezdu");

		this.el.spinner.show();
		this.el.datepicker.hide();

		this.preCheckout( "no", (data) => {
			var data = JSON.parse(data);
			if(!data.success) {
				alert("Chybišta se vloudila");
				console.log(data);
			} else {
				location.href = "/checkout";
			}
		});
	}
	showError ( code )
	{
		let codes = [
			"Termíny jsou obsazené",
			"Vyberte prosím alespoň dvě noci"
		];
		this.el.messageBox.content( "<i class='nvicon nvicon-calendar-error'></i>" + codes[code] ).display();
	}
	search ()
	{
		if ( !this.isSelected() )
			return alert("Vyberte prosím datum příjezdu i odjezdu");

		location.replace( "/ubytovani?begin=" + this.begin + "&end=" + this.end );
	}


	isSelected () { return !! ( this.begin && this.end); }
}


var cal;

function loadDatePicker ( c )
{
	cal = new NV_Booking({
		peopleLimit: c.capacity,
		selector: "#booking-form",
		disabled: c.disabledDays,
		iss: c.iss,
		apartmentId: c.apartmentId,
		apartmentName: c.apartmentName
	});
	cal.el.spinner.hide();


	let URLParams = new URLSearchParams(location.search);

	if ( URLParams.get("begin") && URLParams.get("end") )
		cal.setFromUrl( URLParams.get("begin"), URLParams.get("end") );

	document.body.on( "click", (e) => {
		if ( e.path.indexOf( cal.form[0] ) == -1 ) {
			if (cal.shown) cal.show();
		}
	} );
}