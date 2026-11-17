<?php
// Modificado el 2026-11-16 por varreaga (proyecto MiHelpdesk, fork de osTicket): cambio de marca. Ver NOTICE.md.
if(!defined('SETUPINC')) die('Kwaheri!');
?>
    <div id="main">
            <h1 style="color:#FF7700;"><?php echo brand(__('osTicket is already installed?'));?></h1>
            <div id="intro">
             <p><?php echo sprintf(brand(__('Configuration file already changed - which could mean osTicket is already installed or the config file is corrupted. If you are trying to upgrade osTicket, then go to %s Admin Panel %s.')), '<a href="../scp/admin.php" >', '</a>');?></p>

             <p><?php echo __('If you believe this is in error, please try replacing the config file with a unchanged template copy and try again or get technical help.');?></p>
             <p><?php echo sprintf(__('Refer to the %s Installation Guide %s on the wiki for more information.'), '<a target="_blank" href="https://docs.osticket.com/en/latest/Getting%20Started/Installation.html">', '</a>');?></p>
            </div>
    </div>
    <div id="sidebar">
            <h3><?php echo __('Need Help?');?></h3>
            <p>
            <?php echo __('Professional installation services and commercial support are available.');?>
            </p>
    </div>
