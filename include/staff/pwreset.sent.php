<?php
// Modificado el 2026-11-16, 2026-11-19 y 2026-11-21 por varreaga (proyecto MiHelpdesk, fork de osTicket): cambio de marca; créditos del pie; créditos más cortos. Ver NOTICE.md.
include_once(INCLUDE_DIR.'staff/login.header.php');
defined('OSTSCPINC') or die('Invalid path');
$info = ($_POST && $errors)?Format::htmlchars($_POST):array();
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
    <h3><?php echo __('A confirmation email has been sent'); ?></h3>
    <h3 style="color:black;"><em><?php echo __(
    'If the information provided is valid a password reset email will be sent to the email address you have on file. Follow the link in the email to reset your password.'
    ); ?>
    </em></h3>

    <form action="index.php" method="get">
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
    echo sprintf(__('Based on %s, licensed under the GNU GPL v2.'),
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
