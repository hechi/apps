


$(document).ready(function () {
	OC.Router.registerLoadedCallback(function(){
	        var url = OC.Router.generate('groupmanager_getGroups');
	        $.get(url,function(result){
	                if(result.status=='success'){
	                        var data = result.data;
	                        data.forEach(function(element,index,array){
	                                var li = $('<li>'+escapeHTML(element.groupname)+'</li>');
                                        li.click(function(){
                                                var url=OC.Router.generate('groupmanager_getRightContent', {id:element.groupid});
			                        $.get(url,function(result){
			                                $('#rightcontent').html(result);
			                                $('#modify').click(function(){
			                                        //TODO mod DB
			                                        var url2 = OC.Router.generate('groupmanager_createGroup');
			                                        var post = $('#modForm').serialize();
			                                        console.log(post);
			                                        $.post(url2,post,function(){
			                                                console.log("send saved");
			                                        },"json");
			                                 });
			                        });    
                                        });
                                        $('#leftcontent').append(li);
	                        });
	                }                        	            
	        },"json");
	
	        $('#new').click(function(){
			        var url=OC.Router.generate('groupmanager_getRightContent', {id:'new'});
			        $.get(url,function(result){
			                $('#rightcontent').html(result);
			                $('#save').click(function(){
			                        var url2 = OC.Router.generate('groupmanager_createGroup');
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

