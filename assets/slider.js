class NVSlider extends HTMLElement
{
	constructor() {
		super();
	}

	connectedCallback()
	{
		setTimeout( () =>
		{
			const slider = this;
			const items = this.q("article, a");

			const slider_items = createNode().addClass("slider-items");
			slider_items.insert( items );


			if (typeof this.attr("controls") == "string")
			{
				const controls_wrapper = createNode().addClass("slider-controls");

				let prev = createNode("a")
				.addClass("slider-prev").addClass("nvicon").addClass("nvicon-arrow-left")
				.on( "click", () =>
				{
					for ( let item of items )
					{
						if ( slider_items.scrollLeft > item.offsetLeft ) 
						{
							slider_items.scrollTo( {
								left: item.offsetLeft,
								top: 0,
								behavior: "smooth"
							} );
						}
					}
				} );

				let next = createNode("a")
				.addClass("slider-next").addClass("nvicon").addClass("nvicon-arrow-right")
				.on( "click", () =>
				{
					for ( let i = 0; i < items.length; i++ )
					{
						console.log("next!");
						if ( slider_items.scrollLeft < items[i].offsetLeft ) 
						{
							console.log("next!");
							slider_items.scrollTo( {
								left: items[i+1].offsetLeft,
								top: 0,
								behavior: "smooth"
							} );
							break;
						}
					}
				} );

				controls_wrapper.insert( next );
				controls_wrapper.insert( prev );

				slider.insert( controls_wrapper ); 
			}

			slider.insert( slider_items ); 
		}, 20 );
	}

  // there can be other element methods and properties
}
customElements.define( "nv-slider", NVSlider );