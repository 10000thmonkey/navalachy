/*
DOMSTER - jQuery on aSteroids

INCLUDES
	1 DOM manipulation shorthands
	2 ajax functions
	3 DOMContentReady shorthand - q(()=> {...})


1 DOM
Can be chained, e.g. q(selector).addClass().remove().content()...
	- q() - both $(selector) and $.find(selector)
	- remove()
	- toggleClass(), addClass(), removeClass(), hasClass()
	- on() = addEventListener
	- css(), attr() - either gets, or, if second arg present, sets

	ON SINGLE NODE:
	- closestParent() - finds the closest element up the DOM, by tag name now

	ON FORMS
	- serialize() - returns object of form inputs and values
*/


jax = {
	// jax.get (string url, function callback, function error)
	get:function(u,clb,err,headers = false)
	{
		var x=new XMLHttpRequest();
		x.open("GET",u,true);
		if ( typeof headers == "object" && headers.length != 0) {
			for(let h of headers) {
				x.setRequestHeader(h[0],h[1]);
			}
		}
		x.onreadystatechange=function(e){
		  if(x.readyState==4) {
		   clb ? clb(x.response): null;
		  }
		};
		x.send(null);
		},
	// jax.post (string url, object data, function callback, function error)
	post: function(u,d,cb,er,headers=false)
	{
		var x=new XMLHttpRequest();
		x.open("POST",u,true);
		if ( typeof headers == "array" && headers.length != 0) {
			for(let h of headers) {
				x.setRequestHeader(h[0],h[1]);
			}
		} else {
			x.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		}
		x.onreadystatechange=function(e){
		  if(x.readyState == 4) {
		    if(x.status == 200) {cb ? cb(x.response): null;}
		    else if(x.status != 200) {er ? er(x) : null;}
		  }
		};
		x.send(new URLSearchParams(d));
	},
	// synchronous, DEPRECATED
	sync:function(u,m,d)
	{
		var x=new XMLHttpRequest(),
		    d=d||null,
		    m=m||"GET";
		if(m=="GET"){
		  x.open("GET",u,false);
		  x.send(d);
		}
		if(m=="POST") {
		  x.open("POST",u,false);
		  x.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		  x.send(jax.params(d));
		}
		if(x.readyState==4) {
		  return x.responseText;;
		}
	},
	getJs:function(u,c){var s=document.createElement("script");s.src=u; s.classList.add("templatescript");document.head.appendChild(s)}
};



var createNode = function (element = "div", classes = undefined) {
	let el = document.createElement(element);
	if (classes) el.addClass(classes);
	return el;
};

var q = function (q) {
	if (typeof q == "function") return document.on("DOMContentLoaded", (e)=>q(e));
	let res = (this == window) ? document.querySelectorAll(q) : this.querySelectorAll(q);
	return res; 
};

Node.prototype.q = function (sel) {return this.querySelectorAll(sel)};
Node.prototype.qq = function (sel) {return qq.apply(this, [sel])};
NodeList.prototype.q = function(c) {
	var list = [];
	this.each( function() {
		list.push(...this.q(c));
	} );
	return new NodeListConstruct(list);
}

Node.prototype.insert = function (el) {
	const parent = this;
	if (el instanceof NodeList) {
		//console.log(el);
		for( let e of el ) {
			//console.log(e);
			parent.appendChild( e );
		};
	} else {
		parent.appendChild(el);
	}
	return parent;
};
NodeList.prototype.insert = function (el) { console.log(this);this[0].insert(el); return this; }

Node.prototype.remove = function () {return this.parentElement.removeChild(this);};
NodeList.prototype.remove = function () {
	return this.each( function () { this.remove() } ); }


Node.prototype.toggleClass = function (name) {this.classList.toggle(name);return this;};
Node.prototype.removeClass = function (name) {this.classList.remove(name);return this;};
Node.prototype.addClass = function (name) {
	if(typeof name == "object" || typeof name == "array") {
		for(let c of name) {
			this.classList.add(c);
		}
	} else {
		this.classList.add(name);
	}
	return this;
}
Node.prototype.hasClass = function (name) {return this.classList.contains(name);};
NodeList.prototype.toggleClass = function (c) {
	return this.each( function () { this.toggleClass(c) } ); }
NodeList.prototype.removeClass = function (c) {
	return this.each( function () { this.removeClass(c) } ); }
NodeList.prototype.addClass = function (c) {
	return this.each( function () { this.addClass(c) } ); }
NodeList.prototype.hasClass = function (c) {
	return this.each( function () { this.hasClass(c) } ); }


Node.prototype.on = function (name,callback,r = undefined) {
	if (r === undefined)
		this.addEventListener(name,callback);
	else if (r === true || r === "remove")
		this.removeEventListener(name,callback);
	return this;
};
NodeList.prototype.on = function (n, c, r = undefined) {
	return this.each( function () { this.on(n, c, r) } ); }


Node.prototype.html = function (text) {
	if (text !== undefined) {
		this.innerHTML = text;
		return this;
	} else {
		return this.innerHTML;
	}
};
NodeList.prototype.html = function (c) {
	return this.each( function() { this.html(c);
	} ); };

Node.prototype.css = function(name,value){if(value===undefined){return this.style[name];}else {this.style[name] = value;return this;}};
NodeList.prototype.css = function (c, v) {
	return this.each( function () { this.css(c, v) } ); }

Node.prototype.attr = function(name,value) {
	if(value === undefined){
		return this.getAttribute(name);
	}
	else if (value === true){
		this.attr(name,name);
	}
	else if (value === false) {
		this.removeAttribute(name);
		return this;
	} else {
		this.setAttribute(name,value);
		return this;
	}
};
NodeList.prototype.attr = function (c, v) {
	return this.each( function () { this.attr(c, v) } ); }


Node.prototype.hide = function() {this.addClass("hidden");return this;}
Node.prototype.show = function() {this.removeClass("hidden");return this;}
NodeList.prototype.hide = function() {return this.each(function(){this.hide();})}
NodeList.prototype.show = function() {return this.each(function(){this.show();})}
Node.prototype.toggleHide = function() {return this.toggleClass("hidden");};

Node.prototype.noDisplay = function() {this.addClass("nodisplay");return this;}
Node.prototype.display = function() {this.removeClass("nodisplay");return this;}
NodeList.prototype.noDisplay = function() {return this.each(function(){this.noDisplay();})}
NodeList.prototype.display = function() {return this.each(function(){this.display();})}





Node.prototype.closestParent = function (sel) {
	var el = this;
	while(el.nodeName.toLowerCase() != sel.toLowerCase()) {
		if(el.nodeName == "BODY") continue;
		el = el.parentNode;
	}
	return el;
};
NodeList.prototype.each = function(c) {
	for (var i = this.length; i > 0; i--) {
		c.apply(this[i - 1]);
	}
	return this;
};



//patch to achieve creation of artificial NodeList 
NodeList.prototype.item = function item(i) {
    return this[+i || 0];
};
function NodeListConstruct (array) {
	return Reflect.construct(Array, array, NodeList);
}

//??
var ListElement = (obj) => {
  var el = createNode("list");
  obj.selectable ? el.addClass("selectable"):null;
};
ListElement.prototype = {
  item: () => console.log(this)
}

HTMLFormElement.prototype.serialize = function () {
	var r = {};
	for (e of this.elements) r[e.name] = e.value;
	return r;
}

Node.prototype.messagebox = function (msg, type = "info", icon = "") {
	if (icon !== "") icon = "<i class='nvicon nvicon-"+icon+"'></i>"; 
	this.parentElement.q(".messagebox").remove();
	this.insert(createNode("div").addClass(["messagebox",type]).html(icon + msg));
}

String.prototype.fill = function( values ) {
	let formatted = this;
	let keys = Object.keys(values);

	for( let i = 0; i < keys.length; i++ ) {
		formatted = formatted.replace("{$" + keys[i] + "}", values[keys[i]]);
	}
	return formatted;
};
String.prototype.escapeAttr = function () {
	return this.replaceAll('"', '&#34;').replaceAll(' ', '&#32;');
}