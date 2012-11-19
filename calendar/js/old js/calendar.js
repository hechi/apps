/*
 * Copyright (c) 2012 Georg Ehrke <ownclouddev at georgswebsite dot de>
 * Copyright (c) 2011 Bart Visscher <bartv@thisnet.nl>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 

<<<<<<< HEAD
CalendarManagement={
	create:function(){
		
	},
	edit:function(){
		
	},
	remove:function(){
		
	},
	merge:function(){
		
	},
	validate:function(){
		
	}
}
/*





	*
	 * This wrapper contains all methods for calendar management
	 * Methods: 
	 *  - create
	 *  - edit
	 *  - remove
	 *  - merge
	 
	Calendar:{
		create:function(){
			//TODO
		},
		edit:function(){
			//TODO
=======
Calendar={
	Util:{
		dateTimeToTimestamp:function(dateString, timeString){
			dateTuple = dateString.split('-');
			timeTuple = timeString.split(':');
			
			var day, month, year, minute, hour;
			day = parseInt(dateTuple[0], 10);
			month = parseInt(dateTuple[1], 10);
			year = parseInt(dateTuple[2], 10);
			hour = parseInt(timeTuple[0], 10);
			minute = parseInt(timeTuple[1], 10);
			
			var date = new Date(year, month-1, day, hour, minute);
			
			return parseInt(date.getTime(), 10);
		},
		formatDate:function(year, month, day){
			if(day < 10){
				day = '0' + day;
			}
			if(month < 10){
				month = '0' + month;
			}
			return day + '-' + month + '-' + year;
		},
		formatTime:function(hour, minute){
			if(hour < 10){
				hour = '0' + hour;
			}
			if(minute < 10){
				minute = '0' + minute;
			}
			return hour + ':' + minute;
		}, 
		adjustDate:function(){
			var fromTime = $('#fromtime').val();
			var fromDate = $('#from').val();
			var fromTimestamp = Calendar.Util.dateTimeToTimestamp(fromDate, fromTime);

			var toTime = $('#totime').val();
			var toDate = $('#to').val();
			var toTimestamp = Calendar.Util.dateTimeToTimestamp(toDate, toTime);

			if(fromTimestamp >= toTimestamp){
				fromTimestamp += 30*60*1000;
				
				var date = new Date(fromTimestamp);
				movedTime = Calendar.Util.formatTime(date.getHours(), date.getMinutes());
				movedDate = Calendar.Util.formatDate(date.getFullYear(),
						date.getMonth()+1, date.getDate());

				$('#to').val(movedDate);
				$('#totime').val(movedTime);
			}
		}
	},
	UI:{
		scrollcount: 0,
		loading: function(isLoading){
			if (isLoading){
				$('#loading').show();
			}else{
				$('#loading').hide();
			}
		},
		startEventDialog:function(){
			Calendar.UI.loading(false);
			$('.tipsy').remove();
			$('#fullcalendar').fullCalendar('unselect');
			Calendar.UI.lockTime();
			$( "#from" ).datepicker({
				dateFormat : 'dd-mm-yy',
				onSelect: function(){ Calendar.Util.adjustDate(); }
			});
			$( "#to" ).datepicker({
				dateFormat : 'dd-mm-yy'
			});
			$('#fromtime').timepicker({
				showPeriodLabels: false,
				onSelect: function(){ Calendar.Util.adjustDate(); }
			});
			$('#totime').timepicker({
				showPeriodLabels: false
			});
			$('#category').multiple_autocomplete({source: categories});
			Calendar.UI.repeat('init');
			$('#end').change(function(){
				Calendar.UI.repeat('end');
			});
			$('#repeat').change(function(){
				Calendar.UI.repeat('repeat');
			});
			$('#advanced_year').change(function(){
				Calendar.UI.repeat('year');
			});
			$('#advanced_month').change(function(){
				Calendar.UI.repeat('month');
			});
			$( "#event" ).tabs({ selected: 0});
			$('#event').dialog({
				width : 500,
				height: 600,
				close : function(event, ui) {
					$(this).dialog('destroy').remove();
				}
			});
			Calendar.UI.Share.init();
>>>>>>> master
		},
		remove:function(){
			//TODO
		},
		merge:function(){
			//TODO
		}
	},
	*
	 * This wrapper contains all methods for event management
	 * Methods: 
	 *  - create
	 *  - edit
	 *  - remove
	 *  - move
	 *  - resize
	 *  - validate
	 
	Event:{
		*
		 * validate the user's input and create a new event
		 *
		 * @brief creates an event
		 * @return Boolean
		 
		create:function(){
			//check if the user's inputs are valid
			if(Calendar.Event.validate()){
				return false;
			}
			Calendar.UI.loading(true);
			//send form data to the server
			$.post(OC.filePath('calendar', 'ajax/event', 'new.php'), $('#event_form').serialize(), function(result){
				Calendar.UI.loading(false);
				if(result.status == 'success'){
					//remove the dialog
					$('#event').dialog('destroy').remove();
					//refetch the Events to show the new event
					$('#fullcalendar').fullCalendar('refetchEvents');
				}else{
					
				}
			},'json');
			return true;
		},
		*
		 * validate the user's input and edit an existing event
		 *
		 * @brief edits an event
		 * @return Boolean
		 
		edit:function(){
			//check if the user's inputs are valid
			if(Calendar.Event.validate()){
				return false;
			}
			Calendar.UI.loading(true);
			//send form data to the server
			$.post(OC.filePath('calendar', 'ajax/event', 'edit.php'), $('#event_form').serialize(), function(result){
				Calendar.UI.loading(false);
				if(result.status == 'success'){
					//remove the dialog
					$('#event').dialog('destroy').remove();
					//refetch the Events to show the new event
					$('#fullcalendar').fullCalendar('refetchEvents');
				}else{
					
				}
			},'json');
			return true;
		},
		remove:function(){
			//TODO rewrite
			var post = $( '#event_form' ).serialize();
			$('#errorbox').empty();
			Calendar.UI.loading(true);
			$.post(url, post, function(data){
					Calendar.UI.loading(false);
					if(data.status == 'success'){
						$('#fullcalendar').fullCalendar('removeEvents', $('#event_form input[name=id]').val());
						$('#event').dialog('destroy').remove();
					} else {
						$('#errorbox').html(t('calendar', 'Deletion failed'));
					}

			}, 'json');	
		},
		move:function(event, dayDelta, minuteDelta, allDay, revertFunc){
			//remove fancy event info
			$('.tipsy').remove();
			//show fancy loading icon
			Calendar.UI.loading(true);
			//send informations to the server
			$.post(OC.filePath('calendar', 'ajax/event', 'move.php'), { id: event.id, dayDelta: dayDelta, minuteDelta: minuteDelta, allDay: allDay?1:0, lastmodified: event.lastmodified},
			function(data) {
				//remove fancy loading icon
				Calendar.UI.loading(false);
				if (data.status == 'success'){
					//update lastmodified informations
					event.lastmodified = data.lastmodified;
					//celebrate
					console.log('Event moved successfully');
				}else{
					revertFunc();
					$('#fullcalendar').fullCalendar('refetchEvents');
				}
			});	
		},
		resize:function(event, dayDelta, minuteDelta, revertFunc){
			//remove fancy event info
			$('.tipsy').remove();
			//show fancy loading icon
			Calendar.UI.loading(true);
			//send information to the server
			$.post(OC.filePath('calendar', 'ajax/event', 'resize.php'), { id: event.id, dayDelta: dayDelta, minuteDelta: minuteDelta, lastmodified: event.lastmodified},
			function(data) {
				//remove fancy loading icon
				Calendar.UI.loading(false);
				if (data.status == 'success'){
					//update lastmodified informations
					event.lastmodified = data.lastmodified;
					//celebrate
					console.log('Event resized successfully');
				}else{
					revertFunc();
					$('#fullcalendar').fullCalendar('refetchEvents');
				}
			});
		},
		validate:function(){
			//TODO rewrite
		}
	},
	*
	 * This wrapper contains all methods for advanced export
	 * Methods: 
	 *  - generate
	 *  - catchFile
	 
	Export:{
		*
		 * @brief generates a list of selected events to export and transfers them to the server
		 * @return Boolean
		 
		generate:function(){
			//TODO
			return true;
		},
<<<<<<< HEAD
		*
		 * @brief catches the file from the server
		 * @return Boolean
		 
		catchFile:function(){
			$.post(OC.filePath('calendar', 'ajax/export', 'catchFile.php'), {'data':event.target.result},function(result) {
				if(result.status == 'success'){
					//load the new file
					window.location = result.location;
					return true;
				}else{
					//show some info to the user
					$('#notification').html(t('calendar', 'Something went terribly wrong.'));
					$('#notification').slideDown();
					//show more info after 5 seconds
					window.setTimeout(function(){
						$('#notification').html(t('calendar', 'Please ask your admin for more informations.'));
					}, 5000);
					//hide info after 5 seconds
					window.setTimeout(function(){
						$('#notification').slideUp();
					}, 5000);
					return false;
				}
			});
		}
	},
	*
	 * This wrapper contains all methods for Drag&Drop import
	 * Methods: 
	 *  - init
	 *  - ondrop
	 
	Import:{
		*
		 * @brief initializes the Drag&Drop import
		 * @return Boolean
		 
		init:function(){
			//check if the FileReader API is available
			if (typeof window.FileReader === 'undefined') {
				console.log('Drag&Drop is not yet supported in your webbrowser. Please update or use an more modern webbrowser');
				return false;
=======
		getEventPopupText:function(event){
			if (event.allDay){
				var timespan = $.fullCalendar.formatDates(event.start, event.end, 'ddd d MMMM[ yyyy]{ - [ddd d] MMMM yyyy}', {monthNamesShort: monthNamesShort, monthNames: monthNames, dayNames: dayNames, dayNamesShort: dayNamesShort}); //t('calendar', "ddd d MMMM[ yyyy]{ - [ddd d] MMMM yyyy}")
			}else{
				var timespan = $.fullCalendar.formatDates(event.start, event.end, 'ddd d MMMM[ yyyy] ' + defaulttime + '{ - [ ddd d MMMM yyyy]' + defaulttime + '}', {monthNamesShort: monthNamesShort, monthNames: monthNames, dayNames: dayNames, dayNamesShort: dayNamesShort}); //t('calendar', "ddd d MMMM[ yyyy] HH:mm{ - [ ddd d MMMM yyyy] HH:mm}")
				// Tue 18 October 2011 08:00 - 16:00
			}
			var html =
				'<div class="summary">' + event.title + '</div>' +
				'<div class="timespan">' + timespan + '</div>';
			if (event.description){
				html += '<div class="description">' + event.description + '</div>';
>>>>>>> master
			}
			//initializes drop area
			droparea = document.getElementById('fullcalendar');
			//define ondrop function
			droparea.ondrop = function(e){
				e.preventDefault();
				Calendar.Import.ondrop(e);
			}
			return true;
		},
<<<<<<< HEAD
		*
		 * @brief catch files and import them
		 * @param Object e
		 * @return Boolean
		 
		ondrop:function(e){
			//get all files
			var files = e.dataTransfer.files;
			//read every single file
			for(var i = 0;i < files.length;i++){
				var file = files[i];
				//initialize FileReader
				reader = new FileReader();
				//define function that runs on import
				reader.onload = function(event){
					//send raw data to server
					$.post(OC.filePath('calendar', 'ajax/import', 'dropimport.php'), {'data':event.target.result},function(result) {
						if(result.status == 'success'){
							//add new calendar source
							$('#fullcalendar').fullCalendar('addEventSource', result.eventSource);
							//show some info to the user
							$('#notification').html(result.message);
							$('#notification').slideDown();
							//hide info after 5 seconds
							window.setTimeout(function(){$('#notification').slideUp();}, 5000);
							return true;
						}else{
							//show some info to the user
							$('#notification').html(result.message);
							$('#notification').slideDown();
							//hide info after 5 seconds
							window.setTimeout(function(){$('#notification').slideUp();}, 5000);
							return false;
						}
					});
					//catch events
					$('#fullcalendar').fullCalendar('refetchEvents');
=======
		showCalDAVUrl:function(username, calname){
			$('#caldav_url').val(totalurl + '/' + username + '/' + calname);
			$('#caldav_url').show();
			$("#caldav_url_close").show();
		},
		initScroll:function(){
			if(window.addEventListener)
				document.addEventListener('DOMMouseScroll', Calendar.UI.scrollCalendar, false);
			//}else{
				document.onmousewheel = Calendar.UI.scrollCalendar;
			//}
		},
		scrollCalendar:function(event){
			$('#fullcalendar').fullCalendar('option', 'height', $(window).height() - $('#controls').height() - $('#header').height() - 15);
			$('.tipsy').remove();
			var direction;
			if(event.detail){
				if(event.detail < 0){
					direction = 'top';
				}else{
					direction = 'down';
>>>>>>> master
				}
				//set Data url for reading the file
				reader.readAsDataURL(file);
			}
		}
	},
	*
	 * This wrapper contains all methods for repeating events management
	 * Methods: 
	 *  - create
	 *  - edit
	 *  - remove
	 
	Repeat:{
		//TODO
	},
	Share:function(){
		var itemShares = [OC.Share.SHARE_TYPE_USER, OC.Share.SHARE_TYPE_GROUP];
			$('#sharewith').autocomplete({minLength: 2, source: function(search, response) {
				$.get(OC.filePath('core', 'ajax', 'share.php'), { fetch: 'getShareWith', search: search.term, itemShares: itemShares }, 
					function(result) {
						if (result.status == 'success' && result.data.length > 0) {
							response(result.data);
						}
				});
			},
			focus: function(event, focused) {
				event.preventDefault();
			},
			select: function(event, selected) {
				var itemType = 'event';
				var itemSource = $('#sharewith').data('item-source');
				var shareType = selected.item.value.shareType;
				var shareWith = selected.item.value.shareWith;
				$(this).val(shareWith);
				// Default permissions are Read and Share
				var permissions = OC.PERMISSION_READ | OC.PERMISSION_SHARE;
				OC.Share.share(itemType, itemSource, shareType, shareWith, permissions, function(data) {
					var newitem = '<li data-item-type="event"'
						+ 'data-share-with="'+shareWith+'" '
						+ 'data-permissions="'+permissions+'" '
						+ 'data-share-type="'+shareType+'">'+shareWith+' ('+(shareType == OC.Share.SHARE_TYPE_USER ? t('core', 'user') : t('core', 'group'))+')'
						+ '<span class="shareactions"><input class="update" type="checkbox" title="'+t('core', 'Editable')+'">'
						+ '<input class="share" type="checkbox" title="'+t('core', 'Shareable')+'" checked="checked">'
						+ '<input class="delete" type="checkbox" title="'+t('core', 'Deletable')+'">'
						+ '<img class="svg action delete" title="Unshare" src="'+ OC.imagePath('core', 'actions/delete.svg') +'"></span></li>';
					$('.sharedby.eventlist').append(newitem);
					$('#sharedWithNobody').remove();
					$('#sharewith').val('');
				});
				return false;
			}
		});
		
		$('.shareactions > input:checkbox').change(function() {
			var container = $(this).parents('li').first();
			var permissions = parseInt(container.data('permissions'));
			var itemType = container.data('item-type');
			var shareType = container.data('share-type');
			var itemSource = container.data('item');
			var shareWith = container.data('share-with');
			var permission = null;
			if($(this).hasClass('update')) {
				permission = OC.PERMISSION_UPDATE;
			} else if($(this).hasClass('share')) {
				permission = OC.PERMISSION_SHARE;
			} else if($(this).hasClass('delete')) {
				permission = OC.PERMISSION_DELETE;
			}
<<<<<<< HEAD
			// This is probably not the right way, but it works :-P
			if($(this).is(':checked')) {
				permissions += permission;
			} else {
				permissions -= permission;
=======

			var scroll = $(document).scrollTop(),
				doc_height = $(document).height(),
				win_height = $(window).height();
			if(direction == 'down'/* && win_height == (doc_height - scroll)*/){
				$('#fullcalendar').fullCalendar('next');
				$(document).scrollTop(0);
				event.preventDefault();
			}else/* if (direction == 'top' && scroll == 0) */{
				$('#fullcalendar').fullCalendar('prev');
				$(document).scrollTop(win_height);
				event.preventDefault();
>>>>>>> master
			}
			OC.Share.setPermissions(itemType, itemSource, shareType, shareWith, permissions);
		});
		
		$('.shareactions > .delete').click(function() {
			var container = $(this).parents('li').first();
			var itemType = container.data('item-type');
			var shareType = container.data('share-type');
			var itemSource = container.data('item');
			var shareWith = container.data('share-with');
			OC.Share.unshare(itemType, itemSource, shareType, shareWith, function() {
				container.remove();
			});
		});
	},
	UI:{
		Calendar:{
			caldavurl:function(id){
				$.post(OC.filePath('calendar', 'ajax/calendar', 'getcaldav.php'), { calendarid: id},
				function(data) {
					$('#caldav_url').val(data.url);
					$('#caldav_url').show();
					$('#caldav_url_close').show();
				});
			}
		},
		Event:{
			init:function(){
				//disable loading icon
				Calendar.UI.loading(false);
				//remove all fancy event infos
				$('.tipsy').remove();
				//unselect the day in calendar
				$('#fullcalendar').fullCalendar('unselect');
				Calendar.UI.lockTime();
				//initialize date picker
				$( '#from' ).datepicker({
					dateFormat : 'dd-mm-yy'
				});
				$( '#to' ).datepicker({
					dateFormat : 'dd-mm-yy'
				});
				//initialize timepicker
				$('#fromtime').timepicker({
				    showPeriodLabels: false
				});
				$('#totime').timepicker({
				    showPeriodLabels: false
				});
				$('#category').multiple_autocomplete({source: categories});
				//initialize tabs
				$( '#event' ).tabs({ selected: 0});
				$('#event').dialog({
					width : 500,
					height: 600,
					close : function(event, ui) {
						$(this).dialog('destroy').remove();
					}
				});
				Calendar.Share();
			},
			create:function(){
				//estimate start time
				start = Math.round(start.getTime()/1000);
				//estimate end time if it's available
				if (end){
					end = Math.round(end.getTime()/1000);
				}
				//check if there is already a dialog
				if($('#event').dialog('isOpen') == true){
					// TODO: save event
					$('#event').dialog('destroy').remove();
				}else{
					Calendar.UI.loading(true);
					// TODO: use OC.dialog!!!
					$('#dialog_holder').load(OC.filePath('calendar', 'ajax/event', 'new.form.php'), {start:start, end:end, allday:allday?1:0}, Calendar.UI.startEventDialog);
				}
			},
			edit:function(calEvent, jsEvent, view){
				//check if the event is writable at all
				if (calEvent.editable == false || calEvent.source.editable == false) {
					return;
				}
				//get event id
				var id = calEvent.id;
				//check if there is already a dialog
				if($('#event').dialog('isOpen') == true){
					// TODO: save event
					$('#event').dialog('destroy').remove();
				}else{
					Calendar.UI.loading(true);
					// TODO: use OC.dialog!!!
					$('#dialog_holder').load(OC.filePath('calendar', 'ajax/event', 'edit.form.php'), {id: id}, Calendar.UI.startEventDialog);
				}
			},
			advanced:function(){
				$('#advanced_options').slideDown('slow');
				$('#advanced_options_button').css('display', 'none');			
			},
			locktime:function(forceunlock){
				if(typeof forceunlock = typeof undefined){
					forceunlock = false;
				}
				if($('#allday_checkbox').is(':checked') && !forceunlock) {
					$('#fromtime').attr('disabled', true)
						.addClass('disabled');
					$('#totime').attr('disabled', true)
						.addClass('disabled');
				} else {
					$('#fromtime').attr('disabled', false)
						.removeClass('disabled');
					$('#totime').attr('disabled', false)
						.removeClass('disabled');
				}
			},
			
		},
		Repeat:{
			advanced:function(){
				if($('#advanced_options_repeating').is(':hidden')){
					$('#advanced_options_repeating').slideDown('slow');
				}else{
					$('#advanced_options_repeating').slideUp('slow');
				}		
			}
		},
		loading: function(isLoading){
			if (isLoading){
				$('#loading').show();
			}else{
				$('#loading').hide();
			}
			return true;
		},
		resize:function(){
			$('.tipsy').remove();
			$('#fullcalendar').fullCalendar('option', 'height', $(window).height() - $('#controls').height() - $('#header').height() - 15);
		},
		getEventPopupText:function(event){
			if (event.allDay){
				var timespan = $.fullCalendar.formatDates(event.start, event.end, 'ddd d MMMM[ yyyy]{ -[ddd d] MMMM yyyy}',
								{monthNamesShort: monthNamesShort, monthNames: monthNames, dayNames: dayNames, dayNamesShort: dayNamesShort});
								//t('calendar', 'ddd d MMMM[ yyyy]{ -[ddd d] MMMM yyyy}')
			}else{
				var timespan = $.fullCalendar.formatDates(event.start, event.end, 'ddd d MMMM[ yyyy] ' + defaulttime + '{ -[ ddd d MMMM yyyy]' + defaulttime + '}', {monthNamesShort: monthNamesShort, monthNames: monthNames, dayNames: dayNames, dayNamesShort: dayNamesShort}); //t('calendar', 'ddd d MMMM[ yyyy] HH:mm{ -[ ddd d MMMM yyyy] HH:mm}')
				// Tue 18 October 2011 08:00 - 16:00
			}
<<<<<<< HEAD
			var html =
				'<div class="summary">' + event.title + '</div>' +
				'<div class="timespan">' + timespan + '</div>';
			if (event.description){
				html += '<div class="description">' + event.description + '</div>';
			}
			return html;
=======

>>>>>>> master
		},
		setViewActive: function(view){
			$('#view input[type="button"]').removeClass('active');
			var id;
			switch (view) {
				case 'agendaWeek':
					id = 'week';
					break;
				case 'month':
					id = 'month';
					break;
				case 'list':
					id = 'list';
					break;
			}
			$('#'+id).addClass('active');
		},
		categoriesChanged:function(newcategories){
			categories = $.map(newcategories, function(v) {return v;});
			console.log('Calendar categories changed to: ' + categories);
			$('#category').multiple_autocomplete('option', 'source', categories);
		},
		Calendar:{
			overview:function(){
				if($('#choosecalendar_dialog').dialog('isOpen') == true){
					$('#choosecalendar_dialog').dialog('moveToTop');
				}else{
					Calendar.UI.loading(true);
					$('#dialog_holder').load(OC.filePath('calendar', 'ajax/calendar', 'overview.php'), function(){
						$('#choosecalendar_dialog').dialog({
							width : 600,
							height: 400,
							close : function(event, ui) {
								$(this).dialog('destroy').remove();
							}
						});
						Calendar.UI.loading(false);
					});
				}
			},
			activation:function(checkbox, calendarid)
			{
				Calendar.UI.loading(true);
				$.post(OC.filePath('calendar', 'ajax/calendar', 'activation.php'), { calendarid: calendarid, active: checkbox.checked?1:0 },
				  function(data) {
					Calendar.UI.loading(false);
					if (data.status == 'success'){
						checkbox.checked = data.active == 1;
						if (data.active == 1){
							$('#fullcalendar').fullCalendar('addEventSource', data.eventSource);
						}else{
							$('#fullcalendar').fullCalendar('removeEventSource', data.eventSource.url);
						}
					}
				  });
			},
			newCalendar:function(object){
				var tr = $(document.createElement('tr'))
					.load(OC.filePath('calendar', 'ajax/calendar', 'new.form.php'),
						function(){Calendar.UI.Calendar.colorPicker(this)});
				$(object).closest('tr').after(tr).hide();
			},
			edit:function(object, calendarid){
				var tr = $(document.createElement('tr'))
					.load(OC.filePath('calendar', 'ajax/calendar', 'edit.form.php'), {calendarid: calendarid},
						function(){Calendar.UI.Calendar.colorPicker(this)});
				$(object).closest('tr').after(tr).hide();
			},
			deleteCalendar:function(calid){
				var check = confirm('Do you really want to delete this calendar?');
				if(check == false){
					return false;
				}else{
					$.post(OC.filePath('calendar', 'ajax/calendar', 'delete.php'), { calendarid: calid},
					  function(data) {
						if (data.status == 'success'){
							var url = 'ajax/events.php?calendar_id='+calid;
							$('#fullcalendar').fullCalendar('removeEventSource', url);
							$('#choosecalendar_dialog').dialog('destroy').remove();
							Calendar.UI.Calendar.overview();
							$('#calendar tr[data-id="'+calid+'"]').fadeOut(400,function(){
								$('#calendar tr[data-id="'+calid+'"]').remove();
							});
							$('#fullcalendar').fullCalendar('refetchEvents');
						}
					  });
				}
			},
			submit:function(button, calendarid){
				var displayname = $.trim($('#displayname_'+calendarid).val());
				var active = $('#edit_active_'+calendarid+':checked').length;
				var description = $('#description_'+calendarid).val();
				var calendarcolor = $('#calendarcolor_'+calendarid).val();
				if(displayname == ''){
					$('#displayname_'+calendarid).css('background-color', '#FF2626');
					$('#displayname_'+calendarid).focus(function(){
						$('#displayname_'+calendarid).css('background-color', '#F8F8F8');
					});
				}

				var url;
				if (calendarid == 'new'){
					url = OC.filePath('calendar', 'ajax/calendar', 'new.php');
				}else{
					url = OC.filePath('calendar', 'ajax/calendar', 'update.php');
				}
				$.post(url, { id: calendarid, name: displayname, active: active, description: description, color: calendarcolor },
					function(data){
						if(data.status == 'success'){
							$(button).closest('tr').prev().html(data.page).show().next().remove();
							$('#fullcalendar').fullCalendar('removeEventSource', data.eventSource.url);
							$('#fullcalendar').fullCalendar('addEventSource', data.eventSource);
							if (calendarid == 'new'){
								$('#choosecalendar_dialog > table:first').append('<tr><td colspan="6"><a href="#" onclick="Calendar.UI.Calendar.newCalendar(this);"><input type="button" value="' + newcalendar + '"></a></td></tr>');
							}
						}else{
							$('#displayname_'+calendarid).css('background-color', '#FF2626');
							$('#displayname_'+calendarid).focus(function(){
								$('#displayname_'+calendarid).css('background-color', '#F8F8F8');
							});
						}
					}, 'json');
			},
			cancel:function(button, calendarid){
				$(button).closest('tr').prev().show().next().remove();
			},
			colorPicker:function(container){
				// based on jquery-colorpicker at jquery.webspirited.com
				var obj = $('.colorpicker', container);
				var picker = $('<div class="calendar-colorpicker"></div>');
				//build an array of colors
				var colors = {};
				$(obj).children('option').each(function(i, elm) {
					colors[i] = {};
					colors[i].color = $(elm).val();
					colors[i].label = $(elm).text();
				});
				for (var i in colors) {
					picker.append('<span class="calendar-colorpicker-color ' + (colors[i].color == $(obj).children(':selected').val() ? ' active' : '') + '" rel="' + colors[i].label + '" style="background-color: ' + colors[i].color + ';"></span>');
				}
				picker.delegate('.calendar-colorpicker-color', 'click', function() {
					$(obj).val($(this).attr('rel'));
					$(obj).change();
					picker.children('.calendar-colorpicker-color.active').removeClass('active');
					$(this).addClass('active');
				});
				$(obj).after(picker);
				$(obj).css({
					position: 'absolute',
					left: -10000
				});
			}
<<<<<<< HEAD
=======
		},
		Share:{
			init:function(){
				var itemShares = [OC.Share.SHARE_TYPE_USER, OC.Share.SHARE_TYPE_GROUP];
				$('#sharewith').autocomplete({minLength: 2, source: function(search, response) {
					$.get(OC.filePath('core', 'ajax', 'share.php'), { fetch: 'getShareWith', search: search.term, itemShares: itemShares }, function(result) {
						if (result.status == 'success' && result.data.length > 0) {
							response(result.data);
						}
					});
				},
				focus: function(event, focused) {
					event.preventDefault();
				},
				select: function(event, selected) {
					var itemType = 'event';
					var itemSource = $('#sharewith').data('item-source');
					var shareType = selected.item.value.shareType;
					var shareWith = selected.item.value.shareWith;
					$(this).val(shareWith);
					// Default permissions are Read and Share
					var permissions = OC.PERMISSION_READ | OC.PERMISSION_SHARE;
					OC.Share.share(itemType, itemSource, shareType, shareWith, permissions, function(data) {
						var newitem = '<li data-item-type="event"'
							+ 'data-share-with="'+shareWith+'" '
							+ 'data-permissions="'+permissions+'" '
							+ 'data-share-type="'+shareType+'">'+shareWith+' ('+(shareType == OC.Share.SHARE_TYPE_USER ? t('core', 'user') : t('core', 'group'))+')'
							+ '<span class="shareactions"><input class="update" type="checkbox" title="'+t('core', 'Editable')+'">'
							+ '<input class="share" type="checkbox" title="'+t('core', 'Shareable')+'" checked="checked">'
							+ '<input class="delete" type="checkbox" title="'+t('core', 'Deletable')+'">'
							+ '<img class="svg action delete" title="Unshare"src="'+ OC.imagePath('core', 'actions/delete.svg') +'"></span></li>';
						$('.sharedby.eventlist').append(newitem);
						$('#sharedWithNobody').remove();
						$('#sharewith').val('');
					});
					return false;
				}
				});

				$('.shareactions > input:checkbox').change(function() {
					var container = $(this).parents('li').first();
					var permissions = parseInt(container.data('permissions'));
					var itemType = container.data('item-type');
					var shareType = container.data('share-type');
					var itemSource = container.data('item');
					var shareWith = container.data('share-with');
					var permission = null;
					if($(this).hasClass('update')) {
						permission = OC.PERMISSION_UPDATE;
					} else if($(this).hasClass('share')) {
						permission = OC.PERMISSION_SHARE;
					} else if($(this).hasClass('delete')) {
						permission = OC.PERMISSION_DELETE;
					}
					// This is probably not the right way, but it works :-P
					if($(this).is(':checked')) {
						permissions += permission;
					} else {
						permissions -= permission;
					}
					OC.Share.setPermissions(itemType, itemSource, shareType, shareWith, permissions);
				});

				$('.shareactions > .delete').click(function() {
					var container = $(this).parents('li').first();
					var itemType = container.data('item-type');
					var shareType = container.data('share-type');
					var itemSource = container.data('item');
					var shareWith = container.data('share-with');
					OC.Share.unshare(itemType, itemSource, shareType, shareWith, function() {
						container.remove();
					});
				});
			}
		},
		Drop:{
			init:function(){
				if (typeof window.FileReader === 'undefined') {
					console.log('The drop-import feature is not supported in your browser :(');
					return false;
				}
				droparea = document.getElementById('fullcalendar');
				droparea.ondrop = function(e){
					e.preventDefault();
					Calendar.UI.Drop.drop(e);
				}
				console.log('Drop initialized successfully');
			},
			drop:function(e){
				var files = e.dataTransfer.files;
				for(var i = 0;i < files.length;i++){
					var file = files[i];
					reader = new FileReader();
					reader.onload = function(event){
						Calendar.UI.Drop.import(event.target.result);
						$('#fullcalendar').fullCalendar('refetchEvents');
					}
					reader.readAsDataURL(file);
				}
			},
			import:function(data){
				$.post(OC.filePath('calendar', 'ajax/import', 'dropimport.php'), {'data':data},function(result) {
					if(result.status == 'success'){
						$('#fullcalendar').fullCalendar('addEventSource', result.eventSource);
						$('#notification').html(result.message);
						$('#notification').slideDown();
						window.setTimeout(function(){$('#notification').slideUp();}, 5000);
						return true;
					}else{
						$('#notification').html(result.message);
						$('#notification').slideDown();
						window.setTimeout(function(){$('#notification').slideUp();}, 5000);
					}
				});
			}
		}
	},
	Settings:{
		//
	},

}
$.fullCalendar.views.list = ListView;
function ListView(element, calendar) {
	var t = this;

	// imports
	jQuery.fullCalendar.views.month.call(t, element, calendar);
	var opt = t.opt;
	var trigger = t.trigger;
	var eventElementHandlers = t.eventElementHandlers;
	var reportEventElement = t.reportEventElement;
	var formatDate = calendar.formatDate;
	var formatDates = calendar.formatDates;
	var addDays = $.fullCalendar.addDays;
	var cloneDate = $.fullCalendar.cloneDate;
	function skipWeekend(date, inc, excl) {
		inc = inc || 1;
		while (!date.getDay() || (excl && date.getDay()==1 || !excl && date.getDay()==6)) {
			addDays(date, inc);
		}
		return date;
	}

	// overrides
	t.name='list';
	t.render=render;
	t.renderEvents=renderEvents;
	t.setHeight=setHeight;
	t.setWidth=setWidth;
	t.clearEvents=clearEvents;

	function setHeight(height, dateChanged) {
	}

	function setWidth(width) {
	}

	function clearEvents() {
		this.reportEventClear();
	}

	// main
	function sortEvent(a, b) {
		return a.start - b.start;
	}

	function render(date, delta) {
		if (!t.start){
			t.start = addDays(cloneDate(date, true), -7);
			t.end = addDays(cloneDate(date, true), 7);
		}
		if (delta) {
			if (delta < 0){
				addDays(t.start, -7);
				addDays(t.end, -7);
				if (!opt('weekends')) {
					skipWeekend(t.start, delta < 0 ? -1 : 1);
				}
			}else{
				addDays(t.start, 7);
				addDays(t.end, 7);
				if (!opt('weekends')) {
					skipWeekend(t.end, delta < 0 ? -1 : 1);
				}
			}
		}
		t.title = formatDates(
			t.start,
			t.end,
			opt('titleFormat', 'week')
		);
		t.visStart = cloneDate(t.start);
		t.visEnd = cloneDate(t.end);
	}

	function eventsOfThisDay(events, theDate) {
		var start = cloneDate(theDate, true);
		var end = addDays(cloneDate(start), 1);
		var retArr = new Array();
		for (i in events) {
			var event_end = t.eventEnd(events[i]);
			if (events[i].start < end && event_end >= start) {
				retArr.push(events[i]);
			}
>>>>>>> master
		}
	}
<<<<<<< HEAD:calendar/js/old js/calendar.js
<<<<<<< HEAD
}*/
=======

	function renderEvents(events, modifiedEventId) {
		events = events.sort(sortEvent);

		var table = $('<table class="fc-list-table"></table>');
		var total = events.length;
		if (total > 0) {
			var date = cloneDate(t.visStart);
			while (date <= t.visEnd) {
				var dayEvents = eventsOfThisDay(events, date);
				if (dayEvents.length > 0) {
					table.append(renderDay(date, dayEvents));
				}
				date=addDays(date, 1);
			}
		}

		this.element.html(table);
	}
}
$(document).ready(function(){
	Calendar.UI.initScroll();
	$('#fullcalendar').fullCalendar({
		header: false,
		firstDay: firstDay,
		editable: true,
		defaultView: defaultView,
		timeFormat: {
			agenda: agendatime,
			'': defaulttime
			},
		columnFormat: {
			month: t('calendar', 'ddd'),    // Mon
			week: t('calendar', 'ddd M/d'), // Mon 9/7
			day: t('calendar', 'dddd M/d')  // Monday 9/7
			},
		titleFormat: {
			month: t('calendar', 'MMMM yyyy'),
					// September 2009
			week: t('calendar', "MMM d[ yyyy]{ '&#8212;'[ MMM] d yyyy}"),
					// Sep 7 - 13 2009
			day: t('calendar', 'dddd, MMM d, yyyy'),
					// Tuesday, Sep 8, 2009
			},
		axisFormat: defaulttime,
		monthNames: monthNames,
		monthNamesShort: monthNamesShort,
		dayNames: dayNames,
		dayNamesShort: dayNamesShort,
		allDayText: allDayText,
		viewDisplay: function(view) {
			$('#datecontrol_date').val($('<p>').html(view.title).text());
			if (view.name != defaultView) {
				$.post(OC.filePath('calendar', 'ajax', 'changeview.php'), {v:view.name});
				defaultView = view.name;
			}
			Calendar.UI.setViewActive(view.name);
			if (view.name == 'agendaWeek') {
				$('#fullcalendar').fullCalendar('option', 'aspectRatio', 0.1);
			}
			else {
				$('#fullcalendar').fullCalendar('option', 'aspectRatio', 1.35);
			}
			$('#fullcalendar').fullCalendar('rerenderEvents');
		},
		columnFormat: {
		    week: 'ddd d. MMM'
		},
		selectable: true,
		selectHelper: true,
		select: Calendar.UI.newEvent,
		eventClick: Calendar.UI.editEvent,
		eventDrop: Calendar.UI.moveEvent,
		eventResize: Calendar.UI.resizeEvent,
		eventRender: function(event, element) {
			element.find('.fc-event-title').text($("<div/>").html(event.title).text())
			element.tipsy({
				className: 'tipsy-event',
				opacity: 0.9,
				gravity:$.fn.tipsy.autoBounds(150, 's'),
				fade:true,
				delayIn: 400,
				html:true,
				title:function() {
					return Calendar.UI.getEventPopupText(event);
				}
			});
		},
		eventAfterRender: function(event, element, view) {
			if(view.name == 'agendaWeek'){
				element.find('.fc-event-title').html(element.find('.fc-event-title').text());
			}
		},
		loading: Calendar.UI.loading,
		eventSources: eventSources
	});
	$('#datecontrol_date').datepicker({
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true,
		beforeShow: function(input, inst) {
			var calendar_holder = $('#fullcalendar');
			var date = calendar_holder.fullCalendar('getDate');
			inst.input.datepicker('setDate', date);
			inst.input.val(calendar_holder.fullCalendar('getView').title);
			return inst;
		},
		onSelect: function(value, inst) {
			var date = inst.input.datepicker('getDate');
			$('#fullcalendar').fullCalendar('gotoDate', date);
		}
	});
	fillWindow($('#content'));
	OCCategories.changed = Calendar.UI.categoriesChanged;
	OCCategories.app = 'calendar';
	OCCategories.type = 'event';
	$('#oneweekview_radio').click(function(){
		$('#fullcalendar').fullCalendar('changeView', 'agendaWeek');
	});
	$('#onemonthview_radio').click(function(){
		$('#fullcalendar').fullCalendar('changeView', 'month');
	});
	$('#listview_radio').click(function(){
		$('#fullcalendar').fullCalendar('changeView', 'list');
	});
	$('#today_input').click(function(){
		$('#fullcalendar').fullCalendar('today');
	});
	$('#datecontrol_left').click(function(){
		$('#fullcalendar').fullCalendar('prev');
	});
	$('#datecontrol_right').click(function(){
		$('#fullcalendar').fullCalendar('next');
	});
	Calendar.UI.Share.init();
	Calendar.UI.Drop.init();
	$('#choosecalendar .generalsettings').on('click keydown', function(event) {
		event.preventDefault();
		OC.appSettings({appid:'calendar', loadJS:true, cache:false});
	});
	$('#choosecalendar .calendarsettings').on('click keydown', function(event) {
		event.preventDefault();
		OC.appSettings({appid:'calendar', loadJS:true, cache:false, scriptName:'calendar.php'});
	});
	$('#fullcalendar').fullCalendar('option', 'height', $(window).height() - $('#controls').height() - $('#header').height() - 15);
});
>>>>>>> master:calendar/js/calendar.js
