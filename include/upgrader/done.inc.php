<?php
// Modificado el 2026-11-16 por varreaga (proyecto MiHelpdesk, fork de osTicket): cambio de marca. Ver NOTICE.md.
if(!defined('OSTSCPINC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
//Destroy the upgrader - we're done!
$_SESSION['ost_upgrader']=null;
?>
<div id="upgrader">
    <div id="main">
        <h1 style="color:green;"><?php echo __('Upgrade Completed!');?></h1>
        <div id="intro">
        <p><?php echo brand(__('Congratulations! osTicket upgrade has been completed successfully.'));?></p>
        <p><?php echo sprintf(__('Please refer to %s for more information about changes and/or new features.'),
            sprintf('<a href="%s" target="_blank">%s</a>',
                'https://github.com/osTicket/osTicket/releases',
                __('Release Notes')
        ));?></p>
        </div>
        <p><?php echo brand(__('Once again, thank you for choosing osTicket.'));?></p>
        <p><?php echo sprintf(brand(__('Please feel free to %1$s let us know %2$s
                    of any other improvements and features you would like to
                    see in osTicket, so that we may add them in the future
                    as we continue to develop better and better versions of
                    osTicket.')), '<a target="_blank" href="https://osticket.com/support/">', '</a>');?></p>
        <p><?php echo __("We take user feedback seriously and we're dedicated to making changes based on your input.");?></p>
        <p><?php echo __('Good luck.');?><p>
        <p><?php echo brand(__('osTicket Team.'));?></p>
        <br>
        <p><b><?php echo __('PS');?></b>: <?php echo __("Don't just make customers happy, make happy customers!");?></p>
    </div>
    <div class="sidebar">
            <h3><?php echo __("What's Next?");?></h3>
            <p><b><?php echo __('Post-upgrade');?></b>: <?php
            echo sprintf(__('You can now go to %s to enable the system and explore the new features. For complete and up-to-date release notes see the %s'),
                sprintf('<a href="'. ROOT_PATH . 'scp/settings.php" target="_blank">%s</a>', __('Admin Panel')),
                sprintf('<a href="%s" target="_blank">%s</a>',
                    'https://github.com/osTicket/osTicket/releases',
                    __('osTicket Docs')));?></p>
            <p><b><?php echo __('Stay up to date');?></b>: <?php echo brand(__("It's important to keep your osTicket installation up to date. Get announcements, security updates and alerts delivered directly to you!"));?>
            <?php echo sprintf(__('%1$s Get in the loop %2$s today and stay
                        informed!'), '<a target="_blank" href="https://osticket.com/">', '</a>');?></p>
            <p><b><?php echo __('Commercial Support Available');?></b>:
            <?php echo __('Professional installation services and commercial support are available.');?></p>
   </div>
   <div class="clear"></div>
</div>
