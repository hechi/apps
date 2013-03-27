<form id="modForm">
        <label for="groupname"><?php p($l->t('Groupname')); ?></label><input type="text" id="groupname" name="groupname" value="<?php $_['groupname']; ?>"><br>
        <label for="members"><?php p($l->t('Members')); ?></label> <input type="text" id="members" name="members" value="<?php $_['member']; ?>"><br>
        <label for="groupadmin"><?php p($l->t('Groupadmin')); ?></label> <input type="text" id="groupadmin" name="groupadmin" value="<?php $_['groupadmin']; ?>"><br>
        <label for="description"><?php p($l->t('Description')); ?></label> <textarea rows="4" cols="50" id="description" name="description" value="<?php $_['description']; ?>"> </textarea><br>
</form>

<button id="modify" ><?php p($l->t('Modify')); ?></button>
<button id="cancel" ><?php p($l->t('Cancel')); ?></button>

