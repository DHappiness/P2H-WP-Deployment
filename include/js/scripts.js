// page init
jQuery(function(){
	initFieldsCloning();
});

function initFieldsCloning() {
	var pageID = 0;
	jQuery('.pages').each(function(){
		var currentHolder = jQuery(this);
		var defEl = currentHolder.find('.page').last().clone();
		currentHolder.on('click', '.add-page, .add-subpage', function(e) {
			e.preventDefault();
			var clone = defEl.clone(),
			currentButton = jQuery(this);
			if ( jQuery(e.target).hasClass('add-page') ) {
				var newID = ++pageID;
				clone.find('input').attr('name', 'pages['+newID+'][title]');
				currentHolder.append(clone);
			} else {
				var currentPageName = currentButton.parent().find('input').attr('name'),
				parentPages = currentButton.parent('.page');
				var lastSubpageID = parentPages.children('.page').length;
				clone.find('input').attr('name', currentPageName.replace(/title/g, 'subpages')+'['+lastSubpageID+'][title]');
				clone.appendTo(currentButton.parent());
			}
		});
	});
}