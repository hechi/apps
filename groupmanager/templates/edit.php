<div type="hidden" id="permission" value="{{permission}}"></div>
<div id="notificationMod">{{notification}} </div>

<form id="modForm">
<div type="hidden" id="groupcreator" value="{{groupcreator}}"></div>
    <table border="0">
        <tr>
            <td><label for="groupname">{{trans('Groupname')}}</label></td>
            <td><input type="text" id="groupname" name="groupname" value="{{groupname}}"></td>
        </tr>
        <tr>
            <td><label for="members">{{trans('Members')}}</label></td>
            <td><input type="text" id="members" name="members" value="{{members}}"></td>
        </tr>
        <tr>
            <td><label for="groupadmin">{{trans('Groupadmin')}}</label></td>
            <td><input type="text" id="groupadmin" name="groupadmin" value="{{groupadmin}}"></td>
        </tr>
    </table>
    <label for="description">{{trans('Description')}}</label> <br>
    <textarea rows="4" cols="25" id="description" style="width: auto; height: auto;" name="description" value="{{description}}">{{description}}</textarea><br>
</form>

<table border="0">
    <tr>
        <td><button id="modify" >{{trans('Modify')}}</button></td>
        <td><button id="cancel" >{{trans('Cancel')}}</button></td>
        <td><button id="delete" >{{trans('Delete')}}</button></td>
    </tr>
</table>
