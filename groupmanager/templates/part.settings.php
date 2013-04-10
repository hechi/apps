<!-- filled by the /js/settings.js -->
<div id="notificationMod">{{notification}} </div>
<form id="settingsForm" method="post" action="#">
    <fieldset id="groupmanagerSettings" >
        <legend><strong>{{trans('Groupmanager') }}</strong></legend>
        <input type="checkbox" id="groupIdBox" name="groupIdBox" {{uniqueGroupIdCheck}}>
        <label for="groupIdentifier">{{trans('Unique groupidentifier')}}</label>
        <br>
        <input type="checkbox" name="autocompletionBox"  {{autocompCheck}} >
        <label for="autocompletion">{{trans('Autocompletion')}}</label>
        <br>
        <button type="submit" id="save" >{{trans('Save')}}</button>
    </fieldset>
</form>
