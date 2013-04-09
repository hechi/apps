
$(document).ready(function () {
    // be sure that all routes from /appinfo/routes.php are loaded
	OC.Router.registerLoadedCallback(function(){
        // generate a url from the /appinfo/routes.php
        var url = OC.Router.generate('groupmanagerGetGroups');
        // get tha page from the url
        $.get(url,function(result){
            // and the status is success
            if(result.status=='success'){
                // take the result data (it is an json object)
                var data = result.data;
                // create a loop for all data in the json object
                data.forEach(function(element,index,array){
                    // create an listItem (li) for the left content and
                    // fill it with the name of the groupe
                    var li = $('<li>'+escapeHTML(element.groupname)+'</li>');
                    // register a click action on the listItem
                    li.click(function(){
                        // generate a url from the /appinfo/routes.php and
                        // take the groupid as id as an parameter for the link
                        var url=OC.Router.generate('groupmanagerGetRightContent', {id:element.groupid});
                        // get the page from the url
                        $.get(url,function(result){
                            // fill the rightcontent of the view with the result
                            // from the url
                            $('#rightcontent').html(result);
                            // get permissions field from the result
                            var $permission = $('#permission').attr('value');
                            // if the permission is false than the user 
                            // do not have the permission to modify the
                            // group
                            if($permission==='false'){
                                $('#modify').hide();
                            }else{
                                // register a click action on the buttons
                                $('#modify').click(function(){
                                    var url3 = OC.Router.generate('groupmanagerModifyGroup',{id:element.groupid});
                                    var post = $('#modForm').serialize();
                                    console.log('Stuff '+post);
                                    $.post(url3,post,function(result){
                                        console.log('print new content');
                                        $('#rightcontent').html(result);
                                    });
                                 });
                             }
                             $('#delete').click(function(){
                                //TODO mod DB
                                var url4 = OC.Router.generate('groupmanagerDeleteGroup',{id:element.groupid});
                                $.post(url4,function(result){
                                    console.log('pushed the button delete');
                                    $('#rightcontent').html(result);
                                });
                             });
                        });    
                    });
                    $('#leftcontent').append(li);
                });
            }                        	            
        },"json");
			                
	
        $('#new').click(function(){
	        var url=OC.Router.generate('groupmanagerGetRightContent', {id:'new'});
	        $.get(url,function(result){
                $('#rightcontent').html(result);
                $('#save').click(function(){
                    var url2 = OC.Router.generate('groupmanagerCreateGroup');
                    var post = $('#newForm').serialize();
                    console.log(post);
                    $.post(url2,post,function(){
                            console.log("send saved");
                    },"json");
		        });
	        });
	    });	
	});
});

