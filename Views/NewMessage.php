<div class="wrap gcm-push">

    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php _e('New Message', 'gcm-push'); ?></h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="postbox">
                    <div class="inside">
                        <?php if (empty($apiKey)) : ?>
                        <div id="message" class="error fade">
                            <p style="line-height: 150%">
                            <?php
                                _e('Google cloud api key is not set in settings. <a href="'. menu_page_url('gcm-push-settings', false) .'">see settings</a>', 'wp-gcm-push');
                            ?>
                            </p>
                        </div>
                        <?php endif; ?>
                        <form method="post" action="">
                            <p><label for="users"><?php _e('Select registered users', 'gcm-push'); ?></label></p>
                            <select style="width: 300px" class="chosen-select" name="users" id="users" data-placeholder="Select users" multiple>
                                <?php foreach ($users as $userId) : ?>
                                <option value="<?= $userId; ?>"><?= $userId; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p><label for="push-message"><?php _e('Enter your message', 'gcm-push'); ?></label></p>
                            <textarea id="push-message" name="push-message" type="text" cols="100" rows="5" ></textarea>
                            <p><?php _e('*Please don\'t use HTML', 'gcm-push'); ?></p>
                            <?php submit_button(__('Send', 'gcm-push'), 'primary', 'send-notification'); ?>
                        </form>
                    </div>
                </div>
                <p></p>
            </div>
        </div>
        <br class="clear">
    </div>
</div>
<script type="text/javascript">
    jQuery(function(){
        jQuery(".gcm-push .chosen-select").chosen();
    });
</script>
<style type="text/css">
    .gcm-push .chosen-container-multi .chosen-choices li.search-field input[type="text"] {
        height: 25px;
    }
    .gcm-push .chosen-select {
        width: 300px;        
    }
</style>