	if (Browser.Engine.trident4) {
		$$('.colmask > div').each( function(el) {
			var curClass = el.hasClass('col1') ? 'col1' : el.hasClass('col2') ? 'col2' : el.hasClass('col3') ? 'col3' : '';
			var parentClass = el.getParent('div').get('class').match(/[p|e][x|m]{0,1}(\d+x{0,1})+/);
			if (parentClass[0])
				el.addClass(parentClass[0] + '_ie6_' + curClass);
			if (el.getParent('div').hasClass('equal'))
				el.addClass('equal_ie6_' + curClass);
		});
	}