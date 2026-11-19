<?php
// Modificado el 2026-11-16 y el 2026-11-19 por varreaga (proyecto MiHelpdesk, fork de osTicket): cambio de marca y diseño del tema (título y etiquetas de los campos). Ver NOTICE.md.
include_once(INCLUDE_DIR.'staff/login.header.php');
$info = ($_POST && $errors)?Format::htmlchars($_POST):array();


if ($thisstaff && $thisstaff->is2FAPending())
    $msg = "2FA Pending";

?>
<div id="brickwall"></div>
<div id="loginBox">
    <div id="blur">
        <div id="background"></div>
    </div>
    <h1 id="logo"><a href="index.php">
        <span class="valign-helper"></span>
        <img src="logo.php?login" alt="<?php echo Format::htmlchars(APP_NAME); ?> :: <?php echo __('Staff Control Panel');?>" />
    </a></h1>
<?php if (app_theme()) { ?>
    <h2 class="mh-login-title"><?php echo __('Sign in to the agent panel'); ?></h2>
<?php } ?>
<?php
// MiHelpdesk: el mensaje por omisión ("Authentication Required") no es un
// error; con el tema se oculta porque el título ya indica qué hacer.
$mh_default_msg = ($msg == __('Authentication Required'));
?>
    <h3 id="login-message"<?php if (app_theme() && $mh_default_msg) echo ' class="mh-default"'; ?>><?php echo Format::htmlchars($msg); ?></h3>
    <div class="banner"><small><?php echo ($content) ? Format::display($content->getLocalBody()) : ''; ?></small></div>
    <div id="loading" style="display:none;" class="dialog">
        <h1><i class="icon-spinner icon-spin icon-large"></i>
        <?php echo __('Verifying');?></h1>
    </div>
    <form action="login.php" method="post" id="login" onsubmit="attemptLoginAjax(event)">
        <?php csrf_token();
        if ($thisstaff
                &&  $thisstaff->is2FAPending()
                && ($bk=$thisstaff->get2FABackend())
                && ($form=$bk->getInputForm($_POST))) {
            // Render 2FA input form
            include STAFFINC_DIR . 'templates/dynamic-form-simple.tmpl.php';
            ?>
            <fieldset style="padding-top:10px;">
            <input type="hidden" name="do" value="2fa">
            <button class="submit button pull-center" type="submit"
                name="submit"><i class="icon-signin"></i>
                <?php echo __('Verify'); ?>
            </button>
             </fieldset>
        <?php
        } else { ?>
            <input type="hidden" name="do" value="scplogin">
            <fieldset>
<?php if (app_theme()) { ?>
            <label for="name" class="mh-login-label"><?php echo __('Email or Username'); ?></label>
<?php } ?>
            <input type="text" name="userid" id="name" value="<?php
                echo $info['userid'] ?? null; ?>" placeholder="<?php echo __('Email or Username'); ?>"
                autofocus autocorrect="off" autocapitalize="off">
<?php if (app_theme()) { ?>
            <label for="pass" class="mh-login-label"><?php echo __('Password'); ?></label>
<?php } ?>
            <input type="password" name="passwd" id="pass" maxlength="128" placeholder="<?php echo __('Password'); ?>" autocorrect="off" autocapitalize="off">
                <h3 style="display:inline"><a id="reset-link" class="<?php
                    if (!$show_reset || !$cfg->allowPasswordReset()) echo 'hidden';
                    ?>" href="pwreset.php"><?php echo __('Forgot My Password'); ?></a></h3>
                <button class="submit button pull-right" type="submit"
                    name="submit"><i class="icon-signin"></i>
                    <?php echo __('Log In'); ?>
                </button>
            </fieldset>
        <?php
        } ?>
    </form>
<?php
if (($bks=StaffAuthenticationBackend::getExternal())) { ?>
<div class="or">
    <hr/>
</div><?php
    foreach ($bks as $bk) { ?>
<div class="external-auth"><?php $bk->renderExternalLink(); ?></div><br/><?php
    }
} ?>

    <div id="company">
        <div class="content">
            <?php echo __('Copyright'); ?> &copy; <?php echo Format::htmlchars($ost->company) ?: date('Y'); ?>
        </div>
    </div>
</div>
<div id="poweredBy"><?php echo __('Powered by'); ?>
    <a href="http://www.osticket.com" target="_blank">
        <img alt="<?php echo Format::htmlchars(APP_NAME); ?>" src="images/osticket-grey.png" class="osticket-logo">
    </a>
</div>
<?php if (app_theme()) { ?>
    <script>
    // MiHelpdesk: cuando el inicio de sesión falla, el mensaje deja de ser el
    // de bienvenida y se muestra como error.
    (function () {
        var m = document.getElementById('login-message');
        if (m && window.MutationObserver)
            new MutationObserver(function () { m.classList.remove('mh-default'); })
                .observe(m, { childList: true, characterData: true, subtree: true });
    })();
    </script>
<?php } ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (undefined === window.getComputedStyle(document.documentElement).backgroundBlendMode) {
            document.getElementById('loginBox').style.backgroundColor = 'white';
        }
    });

    function attemptLoginAjax(e) {
        $('#loading').show();
        var objectifyForm = function(formArray) { //serialize data function
            var returnArray = {};
            for (var i = 0; i < formArray.length; i++) {
                returnArray[formArray[i]['name']] = formArray[i]['value'];
            }
            return returnArray;
        };
        if ($.fn.effect) {
            // For some reason, JQuery-UI shake does not considere an element's
            // padding when shaking. Looks like it might be fixed in 1.12.
            // Thanks, https://stackoverflow.com/a/22302374
            var oldEffect = $.fn.effect;
            $.fn.effect = function (effectName) {
                if (effectName === "shake") {
                    $('#loading').hide();
                    var old = $.effects.createWrapper;
                    $.effects.createWrapper = function (element) {
                        var result;
                        var oldCSS = $.fn.css;

                        $.fn.css = function (size) {
                            var _element = this;
                            var hasOwn = Object.prototype.hasOwnProperty;
                            return _element === element && hasOwn.call(size, "width") && hasOwn.call(size, "height") && _element || oldCSS.apply(this, arguments);
                        };

                        result = old.apply(this, arguments);

                        $.fn.css = oldCSS;
                        return result;
                    };
                }
                return oldEffect.apply(this, arguments);
            };
        }
        var form = $(e.target),
            data = objectifyForm(form.serializeArray())
        data.ajax = 1;
        $('button[type=submit]', form).attr('disabled', 'disabled');
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: data,
            cache: false,
            success: function(json) {
                 $('button[type=submit]', form).removeAttr('disabled');
                if (!typeof(json) === 'object' || !json.status)
                    return;
                switch (json.status) {
                case 401:
                    if (json && json.redirect)
                        document.location.href = json.redirect;
                    if (json && json.message)
                        $('#login-message').text(json.message)
                    if (json && json.show_reset)
                        $('#reset-link').show()
                    if ($.fn.effect) {
                        $('#loginBox').effect('shake')
                    }
                    // Clear the password field
                    $('#pass').val('').focus();
                    break
                case 302:
                    if (json && json.redirect)
                        document.location.href = json.redirect;
                    break
                }
            },
        });
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        return false;
    }
    </script>
    <!--[if IE]>
    <style>
        #loginBox:after { background-color: white !important; }
    </style>
    <![endif]-->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.13.2.custom.min.js"></script>
</body>
</html>
