<form id="newForm">
        <label for="groupname">{{trans('Groupname')}}</label><input type="text" id="groupname" name="groupname" value="{{groupname}}"><br>
        <label for="members">{{trans('Members')}}</label> <input type="text" id="members" name="members" value="{{members}}"><br>
        <label for="groupadmin">{{trans('Groupadmin')}}</label> <input type="text" id="groupadmin" name="groupadmin" value="{{groupadmin}}"><br>
        <label for="description">{{trans('Description')}}</label> <br>
        <textarea rows="4" cols="50" id="description" name="description" value="{{description}}">{{description}}</textarea><br>
</form>

<button id="save" >{{trans('Save')}}</button>
<button id="cancel" >{{trans('Cancel')}}</button>

