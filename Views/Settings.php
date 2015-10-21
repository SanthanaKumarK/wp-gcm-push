<div class="wrap">
    <h2>Google Cloud Messaging</h2>

    <div id="poststuff">

        <?php if (isset($_GET['settings-updated'])) : ?>
            <div id="message" class="updated">
                <p><strong><?php _e('Settings saved', 'gcm-push') ?></strong></p>
            </div>
        <?php endif; ?>

        <div id="post-body" class="metabox-holder columns-1">

            <div id="post-body-content">
                <div class="postbox">
                    <h3><?php _e('Settings', 'gcm-push'); ?></h3>
                    <div class="inside">
                        <div id="settings">
                            <form method="post" action="options.php">
                                <?php settings_fields('gcm-push-setting-group'); ?>
                                <?php do_settings_sections('gcm-push'); ?>
                                <?php submit_button(); ?>
                            </form>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>
