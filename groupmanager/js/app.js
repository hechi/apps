function getSettings(){
    var getUrl = OC.Router.generate('getSettings');
    var ret = new Array();
    $.post(getUrl,function(result){
        if(result.status == 'success'){
                ret["uniqueGroupIdCheck"]=(result.data.uniqueGroupIdCheck);
                ret["autocompCheck"]=(result.data.autocompCheck);
        }
    },"json");
    return ret;
}

function initDropDown(tagName) {
        console.log("init DropDown");
        $(tagName).autocomplete({
            minLength : 1,
            source : function(search, response) {
            console.log("was das: "+search.term);
                var getUrl = OC.Router.generate('getUsers',{searchString:search.term});
                var ret = new Array();
                $.post(getUrl,function(result){
                    if(result.status == 'success' && result.data.length > 0) {
                       console.log(result.data);
                       response(result.data);
                    }                
                });
                                //$('#shareWith').appentTo("hihi");
                //$('#shareWith').append($( "<li>" ).append($('<a>').text("hallo")));
            },            
            focus : function(event, focused) {
                event.preventDefault();
            },
            select : function(event, selected) {
                /*
                var member = selected.item.value.shareWith;
                $.post(OC.filePath('group_custom', 'ajax', 'addmember.php'), { member : member , group : OC.GroupCustom.groupSelected } , function ( jsondata ){
                    if(jsondata.status == 'success' ) {
                        $('#shareWith').val('');
                        OC.GroupCustom.groupMember[OC.Share.SHARE_TYPE_USER].push(member);
                        $('#rightcontent').html(jsondata.data.page);
                        OC.GroupCustom.initDropDown() ;
                    }else{
                        OC.dialogs.alert( jsondata.data.message , jsondata.data.title ) ;
                    }           
                });                
                */
                var selectedValue = selected.item.value;
                if(event.target.id==='searchMember'){
                    addMember(selectedValue);
                }
                if(event.target.id==='searchAdmin'){
                    addAdmin(selectedValue);
                }
                return true;
            },
        
        });    
}

// add a new Member in the ui list of members
function addMember(member){
    $('#memberList').append($('<li>').append($('<a>').text(member)));
}

// add a new admin in the ui list of members
function addAdmin(admin){
    $('#adminList').append($('<li>').append($('<a>').text(admin)));
}


$(document).ready(function () {
    // be sure that all routes from /appinfo/routes.php are loaded
	OC.Router.registerLoadedCallback(function(){
	    // load settings
	    var settings = getSettings();
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
                    if(settings["uniqueGroupIdCheck"]){
                        // fill it with the name of the groupe and the creator
                        var li = $('<li>'+escapeHTML(element.groupcreator+':'+element.groupname)+'</li>');
                    }else{
                        // fill it with the name of the groupe
                        var li = $('<li>'+escapeHTML(element.groupname)+'</li>');
                    }
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
                                    var url3 = OC.Router.generate('groupmanagerModifyGroup',{id:element.groupid,creator:element.groupcreator});
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
		        initDropDown('#searchMember');
		        initDropDown('#searchAdmin');
	        });
	    });	
	    
	   // var appendTo = $("#findMember").data();
	   // OC.Share.showDropDown('file', $('#users'), appendTo, false, OC.PERMISSION_READ);
	   // console.log(appendTo);
	});
});

