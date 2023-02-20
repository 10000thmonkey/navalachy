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

			} );
	}

	connectedCallback()
	{
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
customElements.define( "nv-form", NVForm );