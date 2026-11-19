<?php
// Modificado el 2026-11-16 y el 2026-11-19 por varreaga (proyecto MiHelpdesk, fork de osTicket): cambio de marca; créditos del pie. Ver NOTICE.md.
include_once(INCLUDE_DIR.'staff/login.header.php');
defined('OSTSCPINC') or die('Invalid path');
$info = ($_POST)?Format::htmlchars($_POST):array();
?>

<div id="brickwall"></div>
<div id="loginBox">
    <div id="blur">
        <div id="background"></div>
    </div>
    <h1 id="logo"><a href="index.php">
        <span class="valign-helper"></span>
        <img src="logo.php?login" alt="<?php echo Format::htmlchars(APP_NAME); ?> :: <?php echo __('Agent Password Reset');?>" />
    </a></h1>
    <h3><?php echo Format::htmlchars($msg); ?></h3>

    <form action="pwreset.php" method="post">
        <?php csrf_token(); ?>
        <input type="hidden" name="do" value="newpasswd"/>
        <input type="hidden" name="token" value="<?php echo Format::htmlchars($_REQUEST['token']); ?>"/>
        <fieldset>
            <input type="text" name="userid" id="name" value="<?php echo
                $info['userid']; ?>" placeholder="<?php echo __('Email or Username'); ?>"
                autocorrect="off" autocapitalize="off"/>
        </fieldset>
        <input class="submit" type="submit" name="submit" value="Login"/>
    </form>

    <div id="company">
        <div class="content">
            <?php echo __('Copyright'); ?> &copy; <?php echo Format::htmlchars($ost->company) ?: date('Y'); ?>
        </div>
    </div>
</div>
<div id="poweredBy"><?php
    // MiHelpdesk: créditos con el nombre del producto y su origen (osTicket)
    echo sprintf(__('%1$s is free software based on %2$s, licensed under the GNU GPL v2.'),
        '<a href="'.APP_URL.'" target="_blank" rel="noopener">'.Format::htmlchars(APP_NAME).'</a>',
        '<a href="'.APP_UPSTREAM_URL.'" target="_blank" rel="noopener">'.APP_UPSTREAM_NAME.'</a>'); ?>
</div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (undefined === window.getComputedStyle(document.documentElement).backgroundBlendMode) {
            document.getElementById('loginBox').style.backgroundColor = 'white';
        }
    });
    </script>
</body>
</html>
