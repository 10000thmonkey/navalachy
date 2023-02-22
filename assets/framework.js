window.nv = {};

class NVElement extends HTMLElement {
	constructor() {
		super();
		// Check to see if observedAttributes are defined and has length
		// if (this.constructor.observedAttributes && this.constructor.observedAttributes.length) {
		// 	// Loop through the observed attributes
		// 	this.constructor.observedAttributes.forEach( attribute => {
		// 		// Dynamically define the property getter/setter
		// 		let prop = attribute.replaceAll("-", "_");
		// 		Object.defineProperty( this, prop, {
		// 			get () { return this.attr( attribute ); },
		// 			set ( attrValue ) { this.attr( attribute, attrValue ); }
		// 		});
		// 	});
		// }
	}

	prepareTemplate ( templateHTML, keys, values )
	{
		let newTemplateHTML = templateHTML;

		for ( let key of keys )
		{
			let value = values[key];

			switch ( typeof value ) {
				case "number":
					value = String( parseInt( value ) );
					break;
				case "object":
				case "array":
					value = JSON.stringify( value ).escapeAttr();
					break;
			}
			newTemplateHTML = newTemplateHTML.replaceAll( "{$" + key + "}", value.escapeAttr() );
			newTemplateHTML = newTemplateHTML.replaceAll( "{!" + key + "}", value );
		}
		return document.createRange().createContextualFragment( newTemplateHTML );
	}
}







class NVModal extends NVElement
{
	constructor()
	{
		super();
	}

	connectedCallback()
	{
		window.nv.modal[ this.id ] = this;

		this.on( "click", this.outerClickHandler );

		setTimeout( ()=>{
			this.q("[nv-modal-close]").on("click", () => this.closeModal() );
			document.q("[nv-modal-open="+this.id+"]").on("click", () => this.openModal() );
		}, 5);
	}
	disconnectedCallback()
	{
		delete window.nv.modal[ this.id ];

		this.on( "click", this.outerClickHandler, "remove" );
	}

	static get observedAttributes()
	{
		return ["open"];
	}

	attributeChangedCallback( name, vold, vnew )
	{
	}


	outerClickHandler (e)
	{
		if ( e.composedPath().indexOf( this.firstElementChild ) == -1 )
		{
			this.closeModal();
		}
	}
	openModal ()
	{
		this.attr("open", true);
		document.body.css( "overflow", "hidden" );
	}
	closeModal ()
	{
		this.attr("open", false);
		document.body.css( "overflow", "initial" );
	}

}
window.nv.modal = {};
customElements.define( "nv-modal", NVModal );







class NVForm extends HTMLFormElement
{
	constructor()
	{
		super();

		this.on( "submit", (e) =>
			{
				e.preventDefault();

				jax.send(
					"/wp-admin/admin-ajax.php",
					
				);

			}
		);
	}

	connectedCallback()
	{
		window.nv.forms[ this.id ] = this;

		setTimeout( () =>
		{

		}, 10 );
	}

	disconnectedCallback() {
		// browser calls this method when the element is removed from the document
		// (can be called many times if an element is repeatedly added/removed)
	}

	static get observedAttributes() {
		return [/* array of attribute names to monitor for changes */];
	}

	attributeChangedCallback(name, oldValue, newValue) {
		// called when one of attributes listed above is modified
	}

  // there can be other element methods and properties
}
window.nv.forms = {};
customElements.define( "nv-form", NVForm );







class NVRepeat extends NVElement
{
	constructor ()
	{
		super();

		this.nv_items = [];
		this.nv_fill = [];
	}

	//static get observedAttributes () { return ["nv-items"]; }

	connectedCallback ()
	{
		setTimeout( () =>
		{
			let template = this.q("template")[0];
			this.template = template.cloneNode(true);
			template.remove();

			this.feed = createNode("div", "nv-feed-items");
			this.insert( this.feed );

			if ( this.attr("nv-fill") ) this.nv_fill = this.attr("nv-fill").split(",");

			if ( this.attr("nv-inner-class") ) {
				this.feed.addClass( this.attr("nv-inner-class").split(" ") );
				this.attr("nv-inner-class", false);
			}

			if ( this.attr("nv-items") ) {
				this.addItems( JSON.parse( this.attr("nv-items") ) );
				this.attr("nv-items", false);
			}
		}, 5 );
	}

	attributeChangedCallback(name, old, newv) {}

	addItems ( items )
	{
		this.nv_items.push( ...items );

		let keys = this.nv_fill.length > 0 ?
			this.nv_fill :
			Object.keys( items[0] );

		for (let item of items) {
			this.feed.insert( this.prepareTemplate( this.template.innerHTML, keys, item ) );
		}

		return this;
	}

	cleanItems ()
	{
		this.nv_items = [];
		this.feed.innerHTML = "";

		return this;
	}
}
customElements.define( "nv-repeat", NVRepeat );



class NVFeed extends NVRepeat
{
	constructor () {
		super();
	}
	connectedCallback () {
		super.connectedCallback();
		setTimeout ( () =>
		{
			this.spinnerElement =
				createNode("div", ["spinner-wrapper", "hiding", "hidden"] )
					.insert( createNode("div", "spinner") );
			
			this.insert( this.spinnerElement );
		}, 5 );
	}
	spinnerShow()
	{
		this.spinnerElement.show();
		return this;
	}
	spinnerHide()
	{
		this.spinnerElement.hide();
		return this;
	}
}
customElements.define( "nv-feed", NVFeed );