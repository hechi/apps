<form id="newForm">
        <label for="groupname"><?php p($l->t('Groupname')); ?></label><input type="text" id="groupname" name="groupname" value="<?php p($l->t('Groupname')); ?>"><br>
        <label for="members"><?php p($l->t('Members')); ?></label> <input type="text" id="members" name="members" value="<?php p($l->t('Members')); ?>"><br>
        <label for="groupadmin"><?php p($l->t('Groupadmin')); ?></label> <input type="text" id="groupadmin" name="groupadmin" value="<?php p($l->t('Groupadmin')); ?>"><br>
        <label for="description"><?php p($l->t('Description')); ?></label> <textarea rows="4" cols="50" id="description" name="description" value="<?php p($l->t('Description')); ?>"> </textarea><br>
</form>

<button id="save" ><?php p($l->t('Save')); ?></button>
<button id="cancel" ><?php p($l->t('Cancel')); ?></button>

