jQuery(document).ready(function() { 
	jQuery("#jump_list_entries").sortable({
		axis: 'y',
		opacity: 0.6,
		scroll: true,
		connectWith: 'ul',
        containment: 'parent',
		update : function () {
			doUpdateEntriesOrder('jump_list');
		}
	});
	
	jQuery("#task_entries").sortable({
		axis: 'y',
		opacity: 0.6,
		scroll: true,
		connectWith: 'ul',
		containment: 'parent',
		update : function () {
			doUpdateEntriesOrder('task');
		}
	});
	
	jQuery("#jump_list_entries, #task_entries").disableSelection();
	
	//Categories search
	jQuery("#categories-search").result(function(event, data, formatted) {
		jQuery('#categories').css('display','block');
		jQuery('#categories li').css('display','none');
		jQuery("#categories li:contains('" + data + "')").css("display", "block");
		
		jQuery('#show-categories').hide();
		jQuery('#hide-categories').show();
	});
				
	jQuery('#categories').css('display','none');
	jQuery('#hide-categories').hide();
	
	//Pages search
	jQuery("#pages-search").result(function(event, data, formatted) {
		jQuery('#pages').css('display','block');
		jQuery('#pages li').css('display','none');
		jQuery("#pages li:contains('" + data + "')").css("display", "block");
		
		jQuery('#show-pages').hide();
		jQuery('#hide-pages').show();
	});
				
	jQuery('#pages').css('display','none');
	jQuery('#hide-pages').hide();
	
	//Tags search
	jQuery("#tags-search").result(function(event, data, formatted) {
		jQuery('#tags').css('display','block');
		jQuery('#tags li').css('display','none');
		jQuery("#tags li:contains('" + data + "')").css("display", "block");
		
		jQuery('#show-tags').hide();
		jQuery('#hide-tags').show();
	});
				
	jQuery('#tags').css('display','none');
	jQuery('#hide-tags').hide();
	
	doUpdateEntriesOrder('task');
	doUpdateEntriesOrder('jump_list');
});

function doUpdateEntriesOrder(entryType){
    var entries = jQuery('#' + entryType + '_entries').sortable('toArray').join('|');
	jQuery('#' + entryType + '_order').val(entries);
}

function doViewAll(groupName){
	jQuery("#" + groupName).css('display','block');
	jQuery("#"  + groupName + "-search").attr('value','');
	jQuery("#"  + groupName + " li").css('display','block');
	jQuery("#show-"  + groupName).hide();
	jQuery("#hide-"  + groupName).show();
}

function doHideAll(groupName){
	jQuery("#" + groupName).css('display','none');
	jQuery("#"  + groupName + "-search").attr('value','Search');
	jQuery("#"  + groupName + " li").css('display','none');
	jQuery("#show-"  + groupName).show();
	jQuery("#hide-"  + groupName).hide();
}

function appendCategoryToList(source, entryType, categoryName, termId){
	if ('task'==entryType) {
		message = 'Task';
	}else{
		message = 'Jump List';
	}
	
	jQuery('#' + source).parent().effect( "transfer", { to: '#' + entryType + '_entries', className: "ui-effects-transfer" }, 500);
	
	jQuery('.pagetitle').after('<div id="message" class="updated fade below-h2"><p>' + message + ' entry added!</p></div>');
	jQuery('#message').animate({ opacity: 1.0 },2000).fadeOut(300, function(){ jQuery(this).remove();});

	var dataObject = {
		entryId: parseInt(jQuery('#' + entryType + '_index').val()) + 1,
		categoryName: categoryName,
		termId: termId,
		entryType: entryType,
		entryTypeFriendly: message
	};
	
    jQuery('#' + entryType + '_index').val(dataObject.entryId);
	jQuery('#category_tmpl').tmpl(dataObject).appendTo('#' + entryType + '_entries');
	doUpdateEntriesOrder(entryType);
}

function appendTagToList(source, entryType, tagName, termId){
	if ('task'==entryType) {
		message = 'Task';
	}else{
		message = 'Jump List';
	}
	
	jQuery('#' + source).parent().effect( "transfer", { to: '#' + entryType + '_entries', className: "ui-effects-transfer" }, 500);
	
	jQuery('.pagetitle').after('<div id="message" class="updated fade below-h2"><p>' + message + ' entry added!</p></div>');
	jQuery('#message').animate({ opacity: 1.0 },2000).fadeOut(300, function(){ jQuery(this).remove();});

	var dataObject = {
		entryId: parseInt(jQuery('#' + entryType + '_index').val()) + 1,
		tagName: tagName,
		termId: termId,
		entryType: entryType,
		entryTypeFriendly: message
	};
	
    jQuery('#' + entryType + '_index').val(dataObject.entryId);
	jQuery('#tag_tmpl').tmpl(dataObject).appendTo('#' + entryType + '_entries');
	doUpdateEntriesOrder(entryType);
}

function appendPageToList(source, entryType, pageTitle, pageId){
	if ('task'==entryType) {
		message = 'Task';
	}else{
		message = 'Jump List';
	}
	
	jQuery('#' + source).parent().effect( "transfer", { to: '#' + entryType + '_entries', className: "ui-effects-transfer" }, 500);
	
	jQuery('.pagetitle').after('<div id="message" class="updated fade below-h2"><p>' + message + ' entry added!</p></div>');
	jQuery('#message').animate({ opacity: 1.0 },2000).fadeOut(300, function(){ jQuery(this).remove();});
	
	var dataObject = {
		entryId: parseInt(jQuery('#' + entryType + '_index').val()) + 1,
		entryType: entryType,
		entryTypeFriendly: message,
		pageTitle: pageTitle,
		pageId: pageId
	};
	
    jQuery('#' + entryType + '_index').val(dataObject.entryId);
	jQuery('#page_tmpl').tmpl(dataObject).appendTo('#' + entryType + '_entries');
	doUpdateEntriesOrder(entryType);
}

function appendURLToList(entryType){
	if ('task'==entryType) {
		message = 'Task';
	}else{
		message = 'Jump List';
	}
  
	jQuery('.pagetitle').after('<div id="message" class="updated fade below-h2"><p>' + message + ' entry added!</p></div>');
	jQuery('#message').animate({ opacity: 1.0 },2000).fadeOut(300, function(){ jQuery(this).remove();});
	
	var dataObject = {
		entryType: entryType,
		entryId: parseInt(jQuery('#' + entryType + '_index').val()) + 1,
		text: jQuery('#custom_text').val(),
		url: jQuery('#custom_url').val(),
		icon: jQuery('#custom_icon').val()
	};

	jQuery('#custom_url_tmpl').tmpl(dataObject).appendTo('#' + entryType + '_entries');
	
    jQuery('#' + entryType + '_index').val(dataObject.entryId);
	jQuery('#custom_url').attr('value','http://');
	jQuery('#custom_text').attr('value','');
	jQuery('#custom_icon').attr('value','http://');
	
	doUpdateEntriesOrder(entryType);
}

function appendCustomTask(taskType){ 
	jQuery('.pagetitle').after('<div id="message" class="updated fade below-h2"><p>Task entry added!</p></div>');
	jQuery('#message').animate({ opacity: 1.0 },2000).fadeOut(300, function(){ jQuery(this).remove();});
	
	var dataObject = {
		taskType: taskType,
		entryId: parseInt(jQuery('#task_index').val()) + 1,
		taskName: jQuery('#customTask :selected').text()
	};
	
	jQuery('#custom_task_tmpl').tmpl(dataObject).appendTo('#task_entries');
	
	jQuery('#task_index').val(dataObject.entryId);
	doUpdateEntriesOrder('task');
}

function deleteEntry(entryType, entryId){
	if ('task'==entryType) {
		message = 'Task';
	}else{
		message = 'Jump List';
	}
	
	jQuery('.pagetitle').after('<div id="message" class="updated fade below-h2"><p>' + message + ' entry removed!</p></div>');
	jQuery('#message').animate({ opacity: 1.0 },2000).fadeOut(300, function(){ jQuery(this).remove();});
	
	jQuery('#' + entryType + '_' + entryId).remove();
	
	doUpdateEntriesOrder(entryType);
}

function onInputBlur(fieldName, defaultText){
	var field = jQuery('#' + fieldName);
	
	if(field.val() == ''){
		field.val(defaultText);
		field.addClass('inputDefault');
	}else{
		field.removeClass('inputDefault');
	}
}

function onInputFocus(fieldName, defaultText){
	var field = jQuery('#' + fieldName);
	
	field.removeClass('inputDefault');
	if(field.val() == defaultText){
		field.val('');
	}
}