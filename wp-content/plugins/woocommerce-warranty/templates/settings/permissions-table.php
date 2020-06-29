<table class="wp-list-table widefat fixed posts permissions-table">
    <thead>
    <tr>
        <th scope="col" id="status" class="manage-column column-status" width="200"><?php _e('Status', 'wc_warranty'); ?></th>
        <th scope="col" id="users" class="manage-column column-users" style=""><?php _e('Users with Access', 'wc_warranty'); ?></th>
    </tr>
    </thead>
    <tbody id="permissions_tbody">
    <?php
    foreach ($all_statuses as $status):
        $slug = $status->slug;
        ?>
        <tr>
            <td><?php echo $status->name; ?></td>
            <td>
                <select name="permission[<?php echo $slug; ?>][]" class="multi-select2" multiple data-placeholder="All Managers and Administrators" style="width: 500px;">
                    <?php
                    foreach ($all_permitted_users as $user):
                        $selected = (isset($permissions[$slug]) && in_array($user->ID, $permissions[$slug])) ? true : false;
                        ?>
                        <option value="<?php echo $user->ID; ?>" <?php selected(true, $selected, true); ?>><?php echo $user->display_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<style type="text/css">
    table.permissions-table.widefat th, table.permissions-table.widefat td {overflow: visible;}
</style>
<script type="text/javascript">
    jQuery("select.multi-select2").select2();
</script>