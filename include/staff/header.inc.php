<?php
// Modificado el 2026-11-16, 2026-11-18, 2026-11-19 y 2026-11-21 por varreaga (proyecto MiHelpdesk, fork de osTicket): cambio de marca, barra superior y apariencia del tema; favicon y logo para modo oscuro. Ver NOTICE.md.
header("Content-Type: text/html; charset=UTF-8");
header("Content-Security-Policy: frame-ancestors ".$cfg->getAllowIframes()."; script-src 'self' 'unsafe-inline' 'unsafe-eval'; object-src 'none'");

$title = ($ost && ($title=$ost->getPageTitle()))
    ? $title : (APP_NAME.' :: '.__('Staff Control Panel'));

if (!isset($_SERVER['HTTP_X_PJAX'])) { ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html<?php
if (($lang = Internationalization::getCurrentLanguage())
        && ($info = Internationalization::getLanguageInfo($lang))
        && (@$info['direction'] == 'rtl'))
    echo ' dir="rtl" class="rtl"';
if ($lang) {
    echo ' lang="' . Internationalization::rfc1766($lang) . '"';
}

// Dropped IE Support Warning
if (osTicket::is_ie())
    $ost->setWarning(brand(__('osTicket no longer supports Internet Explorer.')));
?>>
<head>
<?php app_theme_head(); ?>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="x-pjax-version" content="<?php echo GIT_VERSION; ?>">
    <title><?php echo Format::htmlchars($title); ?></title>
<?php if (app_theme()) { ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php } ?>
    <!--[if IE]>
    <style type="text/css">
        .tip_shadow { display:block !important; }
    </style>
    <![endif]-->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/thread.css" media="all">
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/scp.css" media="all">
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/mh-editor.css?v=<?php echo @filemtime(ROOT_DIR.'css/mh-editor.css'); ?>" media="screen">
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/typeahead.css" media="screen">
    <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.13.2.custom.min.css"
         rel="stylesheet" media="screen" />
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/jquery-ui-timepicker-addon.css" media="all">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css">
    <!--[if IE 7]>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome-ie7.min.css">
    <![endif]-->
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/dropdown.css">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/loadingbar.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/select2.min.css">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/translatable.css"/>
    <!-- Favicons -->
    <?php echo app_favicon_tags(); ?>

    <?php
    if($ost && ($headers=$ost->getExtraHeaders())) {
        echo "\n\t".implode("\n\t", $headers)."\n";
    }
    ?>
    <?php app_theme_styles('staff'); ?>
</head>
<body<?php echo app_theme_body_class('staff'); ?>>
<div id="container">
    <?php
    if($ost->getError())
        echo sprintf('<div id="error_bar">%s</div>', $ost->getError());
    elseif($ost->getWarning())
        echo sprintf('<div id="warning_bar">%s</div>', $ost->getWarning());
    elseif($ost->getNotice())
        echo sprintf('<div id="notice_bar">%s</div>', $ost->getNotice());
    ?>
<?php if (app_theme()) {
    // MiHelpdesk: barra superior del tema (buscador, nuevo ticket y menú del usuario)
    $mh_initials = mb_strtoupper(mb_substr($thisstaff->getFirstName(), 0, 1)
        .mb_substr($thisstaff->getLastName(), 0, 1));
    $mh_can_create = $thisstaff->hasPerm(Ticket::PERM_CREATE, false); ?>
    <div id="header" class="mh-header">
        <a href="<?php echo ROOT_PATH ?>scp/index.php" class="no-pjax" id="logo">
            <?php echo app_logo_img('staff', ROOT_PATH.'scp/logo.php?'.strtotime($cfg->lastModified('staff_logo_id')), Format::htmlchars(APP_NAME)); ?>
        </a>
        <div class="mh-topbar no-pjax">
            <button type="button" class="mh-nav-toggle" aria-controls="nav" aria-expanded="false"
                aria-label="<?php echo __('Menu'); ?>"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
            <form class="mh-top-search" role="search" action="<?php echo ROOT_PATH ?>scp/tickets.php" method="get">
                <input type="hidden" name="a" value="search">
                <input type="hidden" name="search-type" value="">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <label for="mh-top-q" class="mh-sr"><?php echo __('Search tickets'); ?></label>
                <input id="mh-top-q" type="search" name="query" autocomplete="off"
                    placeholder="<?php echo __('Search by number, subject or user'); ?>">
            </form>
<?php if ($mh_can_create) { ?>
            <a class="mh-top-new" href="<?php echo ROOT_PATH ?>scp/tickets.php?a=open"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg><span><?php echo __('New Ticket'); ?></span></a>
<?php } ?>
            <div class="mh-user">
                <button type="button" class="mh-user-btn" aria-haspopup="true" aria-expanded="false"
                    aria-controls="mh-user-menu" title="<?php echo Format::htmlchars($thisstaff->getName()); ?>"><?php echo Format::htmlchars($mh_initials); ?></button>
                <div class="mh-user-menu" id="mh-user-menu" hidden>
                    <div class="mh-user-name"><strong><?php echo Format::htmlchars($thisstaff->getName()); ?></strong>
                        <span><?php echo Format::htmlchars($thisstaff->getEmail()); ?></span></div>
                    <div class="mh-user-section"><span><?php echo __('Appearance'); ?></span>
                        <?php echo app_theme_appearance_options(); ?></div>
<?php if ($thisstaff->isAdmin() && !defined('ADMINPAGE')) { ?>
                    <a href="<?php echo ROOT_PATH ?>scp/admin.php"><?php echo __('Admin Panel'); ?></a>
<?php } elseif (defined('ADMINPAGE')) { ?>
                    <a href="<?php echo ROOT_PATH ?>scp/index.php"><?php echo __('Agent Panel'); ?></a>
<?php } ?>
                    <a href="<?php echo ROOT_PATH ?>scp/profile.php"><?php echo __('Profile'); ?></a>
                    <a href="<?php echo ROOT_PATH ?>scp/logout.php?auth=<?php echo $ost->getLinkToken(); ?>"><?php echo __('Log Out'); ?></a>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div id="header">
        <p id="info" class="pull-right no-pjax"><?php echo sprintf(__('Welcome, %s.'), '<strong>'.$thisstaff->getFirstName().'</strong>'); ?>
           <?php
            if($thisstaff->isAdmin() && !defined('ADMINPAGE')) { ?>
            | <a href="<?php echo ROOT_PATH ?>scp/admin.php" class="no-pjax"><?php echo __('Admin Panel'); ?></a>
            <?php }else{ ?>
            | <a href="<?php echo ROOT_PATH ?>scp/index.php" class="no-pjax"><?php echo __('Agent Panel'); ?></a>
            <?php } ?>
            | <a href="<?php echo ROOT_PATH ?>scp/profile.php"><?php echo __('Profile'); ?></a>
            | <a href="<?php echo ROOT_PATH ?>scp/logout.php?auth=<?php echo $ost->getLinkToken(); ?>" class="no-pjax"><?php echo __('Log Out'); ?></a>
        </p>
        <a href="<?php echo ROOT_PATH ?>scp/index.php" class="no-pjax" id="logo">
            <span class="valign-helper"></span>
            <?php echo app_logo_img('staff', ROOT_PATH.'scp/logo.php?'.strtotime($cfg->lastModified('staff_logo_id')), Format::htmlchars(APP_NAME).' &mdash; '.__('Customer Support System')); ?>
        </a>
    </div>
<?php } # app_theme ?>
    <div id="pjax-container" class="<?php if ($_POST) echo 'no-pjax'; ?>">
<?php } else {
    header('X-PJAX-Version: ' . GIT_VERSION);
    if ($pjax = $ost->getExtraPjax()) { ?>
    <script type="text/javascript">
    <?php foreach (array_filter($pjax) as $s) echo $s.";"; ?>
    </script>
    <?php }
    foreach ($ost->getExtraHeaders() as $h) {
        if (strpos($h, '<script ') !== false)
            echo $h;
    } ?>
    <title><?php echo ($ost && ($title=$ost->getPageTitle()))?$title:APP_NAME.' :: '.__('Staff Control Panel'); ?></title><?php
} # endif X_PJAX ?>
    <ul id="nav">
<?php include STAFFINC_DIR . "templates/navigation.tmpl.php"; ?>
    </ul>
    <?php include STAFFINC_DIR . "templates/sub-navigation.tmpl.php"; ?>

        <div id="content">
        <?php if(isset($errors['err'])) { ?>
            <div id="msg_error"><?php echo $errors['err']; ?></div>
        <?php }elseif($msg) { ?>
            <div id="msg_notice"><?php echo $msg; ?></div>
        <?php }elseif($warn) { ?>
            <div id="msg_warning"><?php echo $warn; ?></div>
        <?php }
        foreach (Messages::getMessages() as $M) { ?>
            <div class="<?php echo strtolower($M->getLevel()); ?>-banner"><?php
                echo (string) $M; ?></div>
<?php   } ?>
