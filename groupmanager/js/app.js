var memberList = new Array();
var adminList = new Array();

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

// add a new Member in the ul list of members
function addMember(member){
    console.log("In list? "+inMemberList(member));
    if(inMemberList(member)==false){
        var li = $('<li>').append($('<a>').text(member));
        
        // create delete button for each li element
        var button = $('<a></a>');
        var pic = $('<img></img>');
        var picFile = OC.imagePath('core', 'actions/delete.svg');
        pic.attr('src',picFile);
        button.append(pic);
        console.log(pic);
        console.log(button);
        
        // register a delete action for the li element
        // delete the actual li element
        button.click(function () {
             $(this).parent().remove();
        });        
        
        // add button to li element
        li.append(button);
        console.log(li);
        $('#memberList').append(li);
        memberList.push(member);
    }
}

// add a new admin in the ul list of members
function addAdmin(admin){
    if(inAdminList(admin)==false){
        var li = $('<li>').append($('<a>').text(admin));
        
        // create delete button for each li element
        var button = $('<a></a>');
        var pic = $('<img></img>');
        var picFile = OC.imagePath('core', 'actions/delete.svg');
        pic.attr('src',picFile);
        button.append(pic);
        console.log(pic);
        console.log(button);
        
        // register a delete action for the li element
        // delete the actual li element
        button.click(function () {
             $(this).parent().remove();
        }); 
        
        // add button to li element
        li.append(button);
        console.log(li);
        $('#adminList').append(li);
        adminList.push(admin);
    }
}

function inMemberList(member){
    for(var i in memberList){
        if(memberList[i]==member){
            return true; 
        }
    }
    return false;
}

function inAdminList(admin){
    for(adm in adminList){
        if(adm==admin){
            return true; 
        }
    }
    return false;
}


// searialize a list to a jsonstring
// this jsonstring can be used in an php document to parse it into an array
function serializeListToJSON(tagName){
    var serialized = '{';
    var len = $('li', tagName).length - 1;
    var delim;
    
    $('li', tagName).each(function(i) {
        var $li = $(this);
        var $text = $li.text();
        delim = (i < len) ? ',' : '';
        var name = $li[0].tagName.toLowerCase();
        serialized += '"'+ i + '":' + '"' + $text + '"' + delim;

    });

    serialized += '}';    
    //console.log(serialized);
    
    return serialized;
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
                    var li;
                    if(settings["uniqueGroupIdCheck"]){
                        // fill it with the name of the groupe and the creator
                        li = $('<li>'+escapeHTML(element.groupcreator+':'+element.groupname)+'</li>');
                    }else{
                        // fill it with the name of the groupe
                        li = $('<li>'+escapeHTML(element.groupname)+'</li>');
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
                            // get the memberList as a json string and parse it
                            // to a json object/array
                            var getMemberList = $.parseJSON($('#memberJSON').attr('value'));
                            // clear list because if some one clicks twice
                            memberList = new Array();
                            // add all members from the list to the page
                            $.each(getMemberList, function(i, member) {
                                addMember(member);
                            });
                            
                            // get the adminList as a json string and parse it
                            // to a json object/array
                            var getAdminList = $.parseJSON($('#adminJSON').attr('value'));
                            // clear list because if some one clicks twice
                            adminList = new Array();
                            // add all admins from the list to the page
                            $.each(getAdminList, function(i, admin) {
                                addAdmin(admin);
                            });

            		        initDropDown('#searchMember');
		                    initDropDown('#searchAdmin');

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
                                    
                                    var memberList = serializeListToJSON('#memberList');
                                    var adminList = serializeListToJSON('#adminList');
                   
                                    post+="&memberList="+memberList;
                                    post+="&adminList="+adminList;
                                    
                                    console.log('Stuff '+post);
                                    $.post(url3,post,function(result){
                                        console.log('print new content');
                                        $('#rightcontent').html(result);
                                    },"json");
                                 });
                             }
                             $('#delete').click(function(){
                                //TODO mod DB
                                var url4 = OC.Router.generate('groupmanagerDeleteGroup',{id:element.groupid});
                                $.post(url4,function(result){
                                    console.log('pushed the button delete');
                                    $('#rightcontent').html(result);
                                });
                                // removes the li element on the leftside
                                // if we click delete
                                li.remove();
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
                    
                    var memberList = serializeListToJSON('#memberList');
                    var adminList = serializeListToJSON('#adminList');
                   
                    post+="&memberList="+memberList;
                    post+="&adminList="+adminList;
                    
                    console.log(post);
                    $.post(url2,post,function(result){
                            console.log("send saved");
                    },"json");
		        });
		        initDropDown('#searchMember');
		        initDropDown('#searchAdmin');
	        });
	    });	
    });
});

