window.nv = {};

class NVElement extends HTMLElement {
	constructor() {
		super();
		//this.css("display", "block");

	}

	connectedCallback() {}

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




/*


					MODAL

*/


class NVModal extends NVElement
{
	constructor()
	{
		super();
	}

	connectedCallback()
	{
		window.nv.modal[ this.id ] = this;

		this.on( "click", this._outerClickHandler );

		setTimeout( ()=>{
			this.q("[nv-modal-close]").on("click", () => this.closeModal() );
			document.q("[nv-modal-open="+this.id+"]").on("click", () => this.openModal() );
		}, 5);
	}
	disconnectedCallback()
	{
		delete window.nv.modal[ this.id ];

		this.on( "click", this._outerClickHandler, "remove" );
	}

	static get observedAttributes()
	{
		return ["open"];
	}

	attributeChangedCallback( name, vold, vnew )
	{
	}


	_outerClickHandler (e)
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






/*

					Repeatable components


*/






class NVRepeat extends NVElement
{
	constructor ()
	{
		super();

		this._items = [];
		this._fill = [];
	}

	//static get observedAttributes () { return ["nv-items"]; }

	connectedCallback ()
	{
		setTimeout( () =>
		{
			let template = this.q("template")[0];
			this._template = template.cloneNode(true);
			template.remove();

			if ( this.q("nv-items").length > 0 ) {
				this._feed = this.q("nv-items")[0];
			} else {
				this._feed = createNode("nv-items");
				this.insert( this._feed );
			}

			if ( this.attr("nv-fill") ) this._fill = this.attr("nv-fill").split(",");

			if ( this.attr("nv-inner-class") ) {
				this._feed.addClass( this.attr("nv-inner-class").split(" ") );
				this.attr("nv-inner-class", false);
			}

			if ( this.attr("nv-items") ) {
				this.addItems( JSON.parse( this.attr("nv-items") ) );
				this.attr("nv-items", false);
			}
		}, 5 );
	}

	addItems ( items )
	{
		this._items.push( ...items );

		let keys = this._fill.length > 0 ?
			this._fill :
			Object.keys( items[0] );

		for (let item of items) {
			this._feed.insert( this.prepareTemplate( this._template.innerHTML, keys, item ) );
		}

		return this;
	}

	cleanItems ()
	{
		this._items = [];
		this._feed.innerHTML = "";

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
			this._spinner =
				createNode("div", ["spinner-wrapper", "hiding", "hidden"] )
					.insert( createNode("div", "spinner") );
			
			this.insert( this._spinner );
			this.feedFetch();
		}, 5 );
	}
	feedFetch()
	{
		if (this.attr("nv-ajax-get"))
		{
			this.spinnerShow();
	 
			let params = { action: this.attr("nv-ajax-get") };

			if ( this.attr("nv-ajax-params") ) {
				let urlparams = Object.fromEntries( new URLSearchParams( location.search ) );
				let attrparams = this.attr("nv-ajax-params").split(",");

				for ( let key of attrparams ) if ( urlparams[ key ] ) params[ key ] = urlparams[ key ];
			} else {
				params = Object.fromEntries( new URLSearchParams( location.search ) );
			}

			jax.post(
				"/wp-admin/admin-ajax.php",
				{ ...params },
				( response ) =>	{
					let data = JSON.parse( response );

					if ( parseInt( data.status ) === 0 )
					{
						this.cleanItems().addItems( data.items ).removeClass("feed-filtered");
					}

					if ( this.q("#button-loadmore").length > 0 )
					{
						if ( ! parseInt(data.more) ) loadMoreBtn.noDisplay();
						else loadMoreBtn.display();
					}
					this.spinnerHide();
				},
				error => console.log( error )
			);
		}
	}

	spinnerShow()
	{
		this._spinner.show();
		return this;
	}
	spinnerHide()
	{
		this._spinner.hide();
		return this;
	}
}
customElements.define( "nv-feed", NVFeed );




class NVGallery extends NVRepeat
{
	constructor () { super(); }

	connectedCallback ()
	{
		super.connectedCallback();
	}
}
customElements.define( "nv-gallery", NVGallery );



class NVGallerySlider extends NVRepeat
{
	constructor () {
		super();
	}
	connectedCallback ()
	{
		super.connectedCallback();
		
		setTimeout ( () =>
		{
			const slider_feed = this.feed;
			const items = this._feed.children;
			const controls_wrapper = createNode("div", "slider-controls");

			let prev = createNode( "a", ["slider-prev", "nvicon", "nvicon-arrow-left"] )
			.on( "click", this.galleryPrev );

			let next = createNode( "a", ["slider-next", "nvicon", "nvicon-arrow-right"])
				.on( "click", this.galleryNext );

			controls_wrapper.insert( next );
			controls_wrapper.insert( prev );

			this.insert( controls_wrapper ); 
		}, 5 );
	}
	galleryPrev ()
	{
		for ( let i = 0; i < items.length; i++ )
		{
			if ( slider_feed.scrollLeft > items[i].offsetLeft ) 
			{
				slider_feed.scrollBy( {
					left: -500,
					top: 0,
					behavior: "smooth"
				} );
				break;
			}
		}
		return this;
	}
	galleryNext ()
	{
		for ( let i = 0; i < items.length; i++ )
		{
			if ( slider_feed.scrollLeft < items[i].offsetLeft ) 
			{
				slider_feed.scrollBy( {
					left: 500,
					top: 0,
					behavior: "smooth"
				} );
				break;
			}
		}
		return this;
	}
}
customElements.define( "nv-gallery-slider", NVGallerySlider );



class NVItems extends NVElement { constructor() { super(); } connectedCallback() { super.connectedCallback(); } }
customElements.define( "nv-items", NVItems );
class NVItem extends NVElement { constructor() { super(); } connectedCallback() { super.connectedCallback(); } }
customElements.define( "nv-item", NVItem );
class NVFeedItem extends NVElement { constructor() { super(); } connectedCallback() { super.connectedCallback(); } }
customElements.define( "nv-feed-item", NVFeedItem );
class NVGalleryItem extends NVElement { constructor() { super(); } connectedCallback() { super.connectedCallback(); } }
customElements.define( "nv-gallery-item", NVGalleryItem );



/*

						MESSAGEBOX 

*/


class NVMessageBox extends NVElement
{
	constructor () { super(); }

	connectedCallback() {
		setTimeout(()=>
		{
			// to access messagebox from nv-feed
			if ( this.parentElement.nodeName === "NV-FEED" ) this.parentElement.messagebox = this;
			// to access from global nv object
			if ( this.attr("id") ) window.nv.messagebox[ this.attr["id"] ] = this;

		}, 5 );
	}

	addMessage ( message, type = "info", icon = "" )
	{
		if ( icon !== "" ) icon = "<nv-icon class='nvicon-"+icon+"'></nv-icon>";
		this.insert( createNode( "div", [ "message", type ] ).html( icon + message ) );
		return this;
	}
	showMessage ( message, type = "info", icon = "" )
	{
		this.clearMessages();
		this.addMessage( message, type, icon );
		return this;
	}
	clearMessages ()
	{
		this.q( ".message" ).remove();
		return this;
	}
}
customElements.define( "nv-messagebox", NVMessageBox );
window.nv.messagebox = {};




window.nv.logged = !! ( document.cookie.replace(/(?:(?:^|.*;\s*)wordpress_logged_in_\S*\s*\=\s*([^;]*).*$)|^.*$/, "$1") );
class NVLoggedIn extends NVElement
{
	constructor () { super(); }
	connectedCallback ()
	{
		super.connectedCallback();
		if ( ! nv.logged ) this.remove();
	}
}
class NVLoggedOut extends NVElement
{
	constructor () { super(); }
	connectedCallback ()
	{
		super.connectedCallback();
		if ( nv.logged ) this.remove();
	}
}
customElements.define( "nv-logged-in", NVLoggedIn );
customElements.define( "nv-logged-out", NVLoggedOut );