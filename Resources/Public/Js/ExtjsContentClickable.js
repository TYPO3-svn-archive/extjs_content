Ext.ns('TYPO3.ExtjsContent.Clickable');

TYPO3.ExtjsContent.Clickable.Element = Ext.extend(Ext.util.Observable, {

	element:	undefined,
	link:		undefined,
	target:		'internal',

	constructor: function(element, link, target) {
		this.element	= element;
		this.link		= link;
		this.target		= target;

		this.initElement();
		this.addHover();
		this.addClick();
	},

	initElement: function() {

		this.element.addClass('contentClickable');

	},

	addHover: function() {
		this.element.hover(
			this.hoverOver,
			this.hoverOut,
			this
		);
	},

	hoverOver: function() {
		this.element.addClass('contentClickableHover');
	},

	hoverOut: function() {
		this.element.removeClass('contentClickableHover');
	},

	addClick: function() {
		this.element.on('click', this.elementClicked, this);
	},

	elementClicked: function(e) {

		e.stopEvent();

		if(this.target == 'external') {
			window.open(this.link);
		}
		else {
			location.href = this.link;
		}

	}

});