Ext.ns('TYPO3.ExtjsContent.Gallery');

TYPO3.ExtjsContent.Gallery.Element = Ext.extend(Ext.util.Observable, {

		// Options
	id:				undefined,
	effect:			undefined,
	selector:		undefined,
	description:	undefined,
	lightbox:		undefined,
	interval:		5000,
	duration:		2,
	firstRun:		true,
	counterMax:		0,
	currentCount:	0,
	fadeAuto:		true,
	goToIndex:		0,

		// Arrays
	imageArray:			new Array(),
	selectorArray:		new Array(),
	commentArray:		new Array(),

		// Class Names
	imageWrapClassName:	"csc-textpic-imagewrap",
	imageRowClassName:	"csc-textpic-imagerow",
	captionClassName:	"csc-caption",

		// Classes For Selection
	imageWrapClass:		"",
	imageRowClass:		"",
	captionClass:		"",

		// Image Wrap - Options
	imageWrapXY:		0,
	imageWrapWith:		0,
	imageWrapHeight:	0,

		// Current Image - Options
	currentImageHeight:	0,
	currentImageWidth:	0,

		// Elements
	imageWrapElement:	undefined,
	dlElement:			undefined,
	rowElement:			undefined,
	selectorElement:	undefined,
	commentElement:		undefined,
	imageComment:		undefined,
	currentImage:		undefined,
	currentSelector:	undefined,

	constructor: function(id, contentElement, effect, selector, description, interval, duration, imageWrapClassName, imageRowClassName, captionClassName, lightbox) {

		this.id			= id;
		this.effect			= effect;
		this.selector		= selector;
		this.description	= description;
		this.lightbox	= lightbox;

		if(interval) this.interval = interval;

		if(duration) this.duration = duration;

			// Class Names
		if(imageWrapClassName)	this.imageWrapClassName	= "csc-textpic-imagewrap";
		if(imageRowClassName)	this.imageRowClassName	= "csc-textpic-imagerow";
		if(captionClassName)	this.captionClassName	= "csc-caption";

			// Classes For Selection
		this.imageWrapClass		= "." + this.imageWrapClassName,
		this.imageRowClass		= "." + this.imageRowClassName,
		this.captionClass		= "." + this.captionClassName,

		this.initGallery(contentElement);
	},

	initGallery: function(contentElement) {

		var ImageWrapArray = contentElement.query(this.imageWrapClass);

		Ext.each(ImageWrapArray, function(item) {
			this.getGallery(Ext.get(item));
		}, this);

		this.runEffect();
	},

	getGallery: function(ImageWrap) {

		this.getImages(ImageWrap);
		this.getWrapSize(ImageWrap);

		this.initWrapElement(ImageWrap);
		this.initSelector();
		this.initDescription();

		Ext.each(this.imagesArray, function(item,index) {

			this.getImage(Ext.get(item), index);
			this.getSelector(index);

			this.counterMax++;

		}, this);

		ImageWrap.setWidth(this.currentImageWidth);
	},

	getImages: function(ImageWrap) {

		if(this.lightbox){
			this.imagesArray = ImageWrap.query("a");
		}
		else {
			this.imagesArray = ImageWrap.query("img");
		}
	},

	getImage: function(Image, index) {

			// General
		Image.position('absolute');
		Image.setLeft(0);
		Image.setTop(0);

			// Add Lightbox If Selected
		if(this.lightbox) {
			Image.addClass('lightbox' + this.id);
				// TODO: Removes onclick attribute, but should be done via ts -> setup
			Image.set({onclick:'function(){}'});
		}

			// Get All Descriptions
		this.getDescription(Image, index);

			// First Image To Show
		if(index == this.currentCount) {

			Image.setOpacity(1, false);
			Image.setStyle('z-index', '1');

			this.initCurrentImage(Image);

				// Set With
			if(this.imageComment != undefined)		this.imageComment.setWidth(this.currentImageWidth);
			if(this.dlElement != undefined)		this.dlElement.setWidth(this.currentImageWidth);
			if(this.imageWrapElement != undefined)	this.imageWrapElement.setHeight(this.currentImageHeight);

		}
			// Remaining Images
		else {
			Image.setOpacity(0, false);
			Image.setStyle('z-index', '100');
			Image.setDisplayed('none');
			this.imageWrapElement.appendChild(Image);

		}

	},

	initCurrentImage: function(Image) {

		this.currentImage = this.imageWrapElement.appendChild(Image);

		this.currentImageHeight = this.currentImage.getHeight();
		this.currentImageWidth = this.currentImage.getWidth();
	},

	getWrapSize: function(ImageWrap) {

		this.imageWrapWidth = ImageWrap.getWidth();

		this.imageWrapXY = ImageWrap.getXY();
		this.imageWrapWith = this.imageWrapWidth / this.getImageRowsCount(ImageWrap);
	},

	getImageRowsCount: function(ImageWrap) {

			// Counting Rows
		var ImageRows			= ImageWrap.query(this.imageRowClass);
		return ImageRows.length;

	},

	initWrapElement: function(ImageWrap) {

			// Create HTML
		ImageWrap.setXY(this.imageWrapXY);
		ImageWrap.addClass('extjsContentGallery');

			// Quotes for IE compatibility
		this.rowElement = ImageWrap.createChild({
			'class': this.imageRowClass,
			'width': this.imageWrapWith,
//			'height': this.imageWrapHeight,
			'style': 'display:block;'
		});

		this.dlElement = this.rowElement.createChild({tag:'dl',position:'relative'});
		this.imageWrapElement = this.dlElement.createChild({tag:'dt'});

	},

	initSelector: function() {

		if(this.selector) {
			var Selector = this.rowElement.createChild();
			Selector.addClass('selector');
			this.selectorElement = Selector.createChild({tag:'ul'});
		}

	},

	getSelector: function(index) {

		if(this.selector) {
			var Selector = this.selectorElement.createChild({tag:'li', html:this.counterMax+1});

			this.addSelectorHover(Selector);
			this.addSelectorClick(Selector, index);

			this.selectorArray[index] = Selector;

			if(index == this.currentCount) {
				this.setSelector(index, true);
			}
		}
	},

	setSelector: function(index, act) {

		var Selector = this.selectorArray[index];

		if(act) {
			Selector.addClass('act');
			this.currentSelector = index;
		}
		else {
			Selector.removeClass('act');
		}

	},

	addSelectorHover: function(Selector) {
		Selector.hover(
			this.hoverOver,
			this.hoverOut,
			this,
			Selector
		);
	},

	addSelectorClick: function(Selector, index) {
		Selector.on('click', function() {
			this.fadeAuto = false;
			this.goToIndex = index;
			this.fade();
		}, this);
	},

	initDescription: function() {

		if(this.description){
			this.commentElement = this.dlElement.createChild({tag:'dd'});
		}

	},

	getDescription: function(Image, index) {

		if(this.description) {

			var Comment = this.getComment(Image);
			this.commentArray[index] = Comment;

				// First Image To Show -> Add Description
			if(index == this.currentCount) {

				if(Comment != false) {
					this.imageComment = this.commentElement.appendChild(Comment);
				}

			}

		}

	},

	setDescription: function(index) {

		if(this.description) {

			if(this.commentArray[index] != false) {
				this.imageComment.remove();
				this.imageComment = this.commentElement.appendChild(this.commentArray[index]);
			}

		}

	},

	getComment: function(Image) {

		var Parent	= Image.findParentNode('dl', 4, true);
		var Comment = false;

		if(Parent) {
			Comment = Parent.query(this.captionClass);
		}

		if(Comment.constructor == Array) {
			if(Comment.length > 0) {

				Comment = Ext.get(Comment);

				Comment.addClass('csc-caption');

				return Comment;
			}
		}

		return false;

	},

	hoverOver: function(h, e, Element) {
		Element.addClass('hover');
	},

	hoverOut: function(h, e, Element) {
		Element.removeClass('hover');
	},

	runEffect: function() {

		if(this.effect == 'Slider') {

			Ext.TaskMgr.start({
				run: this.fade,
				interval: this.interval,
				scope: this
			});
		}

	},

	fade: function() {

		if(!this.fadeAuto) this.firstRun = false;

		var CurrentIndex = this.getIndex();

		if(!this.firstRun) {

			// Fade Out Old Image
			this.fadeOutCurrentImage();

			// Set New Image
			this.currentImage = Ext.get(this.imagesArray[CurrentIndex]);

			// Fade In New Image
			this.fadeInCurrentImage();

				// Selector Act
			if(this.selector) {
				this.setSelector(this.currentSelector, false);
				this.setSelector(CurrentIndex, true);
			}

			this.setDescription(CurrentIndex);

			this.setCounter();
		}
		else {
			this.firstRun = false;
			this.currentCount = 1;

		}
	},

	fadeInCurrentImage: function() {

			// IE
		this.currentImage.setStyle('z-index', '1');

			// fade in
		this.currentImage.fadeIn({
			endOpacity: 1,
			easing: 'easeOut',
			duration: this.duration,
			useDisplay: true
		});

	},

	fadeOutCurrentImage: function() {

			// fade out
		this.currentImage.fadeOut({
			endOpacity: 0,
			easing: 'easeOut',
			duration: this.duration,
			remove: false,
			useDisplay: true
		});
			// IE
		this.currentImage.setStyle('z-index', '100');
	},

	getIndex: function() {

		var CurrentIndex = 0;

		if(!this.fadeAuto) {
			CurrentIndex = this.goToIndex;
		}
		else {
			CurrentIndex = this.currentCount;
		}

			// Reset Fade Auto
		this.fadeAuto = true;

		return CurrentIndex;
	},

	setCounter: function() {

		if(this.currentCount == this.counterMax-1) {
			this.currentCount = 0;
		}
		else {
			this.currentCount++;
		}

	}

});