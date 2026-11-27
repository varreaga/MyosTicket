<?php
// Modificado el 2026-11-16, 2026-11-19, 2026-11-21 y 2026-11-23 por varreaga (proyecto MiHelpdesk, fork de osTicket): cambio de marca; el botón de actualizar lleva a las versiones de MiHelpdesk; sin la alerta de la extensión imap, que no se usa, ni la de cgi.fix_pathinfo cuando PHP corre dentro de Apache; compatibilidad con PHP 8.5; el 2026-11-27, versión de MiHelpdesk y versión de osTicket en la que se basa. Ver NOTICE.md.
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');

$commit = GIT_VERSION != '$git' ? GIT_VERSION : (
    @shell_exec('git rev-parse HEAD | cut -b 1-8') ?: '?');

$extensions = array(
        'gd' => array(
            'name' => 'gdlib',
            'desc' => __('Used for image manipulation and PDF printing')
            ),
        'iconv' => array(
            'name' => 'iconv',
            'desc' => __('Useful for email processing')
            ),
        // MiHelpdesk: la extensión imap ya no se usa (desde osTicket 1.17 el
        // correo se descarga con laminas-mail, en PHP puro) y PHP 8.4 dejó de
        // incluirla; Debian 13 no la trae. Mostrarla solo causaba una alerta.
        'ctype' => array(
            'name' => 'ctype',
            'desc' => __('Required for email fetching')
            ),
        'xml' => array(
            'name' => 'xml',
            'desc' => __('XML API')
            ),
        'dom' => array(
            'name' => 'xml-dom',
            'desc' => __('Used for HTML email processing')
            ),
        'json' => array(
            'name' => 'json',
            'desc' => __('Improves performance creating and processing JSON')
            ),
        'mbstring' => array(
            'name' => 'mbstring',
            'desc' => __('Highly recommended for non western european language content')
            ),
        'phar' => array(
            'name' => 'phar',
            'desc' => __('Highly recommended for plugins and language packs')
            ),
        'intl' => array(
            'name' => 'intl',
            'desc' => __('Highly recommended for non western european language content')
            ),
        'fileinfo' => array(
            'name' => 'fileinfo',
            'desc' => __('Used to detect file types for uploads')
            ),
        'zip' => array(
            'name' => 'zip',
            'desc' => __('Used for ticket and task exporting')
            ),
        'apcu' => array(
            'name' => 'APCu',
            'desc' => __('Improves overall performance')
            ),
        'Zend Opcache' => array(
            'name' => 'Zend Opcache',
            'desc' => __('Improves overall performance')
            ),
        );

?>
<h2><?php echo brand(__('About this osTicket Installation')); ?></h2>
<table class="list" width="100%";>
<thead>
    <tr><th colspan="2"><?php echo __('Server Information'); ?></th></tr>
</thead>
<tbody>
    <tr><td><?php echo Format::htmlchars(sprintf(__('%s version'), APP_NAME)); ?></td>
        <td><span class="ltr"><?php
            echo Format::htmlchars(sprintf("%s (%s)", APP_VERSION, trim($commit))); ?></span>
        — <a href="<?php echo APP_URL; ?>/releases" target="_blank" rel="noopener"><?php echo __('See releases'); ?></a>
        </td></tr>
    <?php // MiHelpdesk: versión de osTicket en la que se basa; el aviso de
          // "disponible" compara contra osTicket (parches de seguridad). ?>
    <tr><td><?php echo __('Based on osTicket'); ?></td>
        <td><span class="ltr"><?php
            echo Format::htmlchars($tv0 = (THIS_VERSION[0] == 'v' ? THIS_VERSION : APP_UPSTREAM_VERSION)); ?></span>
<?php
// MiHelpdesk: se compara la base con la última versión de osTicket de la
// misma rama (registro DNS de osTicket). Si hay una más nueva, se avisa que
// llegará en una próxima versión de MiHelpdesk; no se actualiza desde aquí.
// Sin conexión o en una copia de desarrollo no se muestra nada.
$lv = $ost->getLatestVersion('core', MAJOR_VERSION);
if ($lv && $tv0[0] == 'v' && $lv[0] == 'v') {
    if (version_compare($tv0, $lv, '>=')) { ?>
    — <span style="color:green"><i class="icon-check"></i> <?php echo __('Up to date'); ?></span>
<?php
    } else { ?>
    — <strong><?php echo Format::htmlchars(sprintf(
        __('osTicket %1$s is available; it will be included in a future version of %2$s'),
        $lv, APP_NAME)); ?></strong>
<?php
    }
}
?>
    </td></tr>
    <tr><td><?php echo __('Web Server Software'); ?></td>
        <td><span class="ltr"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span></td></tr>
    <tr><td><?php echo __('MySQL Version'); ?></td>
        <td><span class="ltr"><?php echo db_version(); ?></span></td></tr>
    <tr><td><?php echo __('PHP Version'); ?></td>
        <td><span class="ltr"><?php echo phpversion(); ?></span></td></tr>
</tbody>
<thead>
    <tr><th colspan="2"><?php echo __('PHP Extensions'); ?></th></tr>
</thead>
<tbody>
    <?php
    foreach($extensions as $ext => $info) { ?>
    <tr><td><?php echo $info['name']; ?></td>
        <td><?php
            echo sprintf('<i class="icon icon-%s"></i> %s',
                    extension_loaded($ext) ? 'check' : 'warning-sign',
                    $info['desc']);
            ?>
        </td>
    </tr>
    <?php
    } ?>
</tbody>
<thead>
    <tr><th colspan="2"><?php echo __('PHP Settings'); ?></th></tr>
</thead>
<tbody>
<?php
    // MiHelpdesk: cgi.fix_pathinfo solo existe cuando PHP corre como CGI o
    // FPM. Con PHP dentro de Apache (mod_php) no aplica: ini_get() devuelve
    // false y la alerta salía aunque todo funcionara.
    $mh_cgi = (strpos(PHP_SAPI, 'cgi') !== false || strpos(PHP_SAPI, 'fpm') !== false);
    if ($mh_cgi) { ?>
    <tr>
        <td><span class="ltr"><code>cgi.fix_pathinfo</code></span></td>
        <td><i class="icon icon-<?php
                echo ini_get('cgi.fix_pathinfo') == 1 ? 'check' : 'warning-sign'; ?>"></i>
                <span class="faded"><?php echo __('"1" is recommended if AJAX is not working'); ?></span>
        </td>
    </tr>
<?php } ?>
    <tr>
        <td><span class="ltr"><code>date.timezone</code></span></td>
        <td><i class="icon icon-<?php
                echo ini_get('date.timezone') ? 'check' : 'warning-sign'; ?>"></i>
                <span class="faded"><?php
                    echo ini_get('date.timezone')
                    ?: __('Setting default timezone is highly recommended');
                    ?></span>
        </td>
    </tr>
</tbody>
<thead>
    <tr><th colspan="2"><?php echo __('Database Information and Usage'); ?></th></tr>
</thead>
<tbody>
    <tr><td><?php echo __('Schema'); ?></td>
        <td><?php echo sprintf('<span class="ltr">%s (%s)</span>', DBNAME, DBHOST); ?> </td></tr>
    </tr>
    <tr><td><?php echo __('Schema Signature'); ?></td>
        <td><?php echo $cfg->getSchemaSignature(); ?> </td>
    </tr>
    <tr><td><?php echo __('Space Used'); ?></td>
        <td><?php
        $sql = 'SELECT sum( data_length + index_length ) / 1048576 total_size
            FROM information_schema.TABLES WHERE table_schema = '
            .db_input(DBNAME);
        $space = db_result(db_query($sql));
        echo sprintf('%.2f MiB', $space); ?></td>
    <tr><td><?php echo __('Space for Attachments'); ?></td>
        <td><?php
        $sql = 'SELECT
                    (DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024
                FROM
                    information_schema.TABLES
                WHERE
                    TABLE_SCHEMA = "'.DBNAME.'"
                AND
                    TABLE_NAME = "'.FILE_CHUNK_TABLE.'"
                ORDER BY
                    (DATA_LENGTH + INDEX_LENGTH)
                DESC';
        $space = db_result(db_query($sql));
        echo sprintf('%.2f MiB', $space); ?></td></tr>
    <tr><td><?php echo __('Timezone'); ?></td>
        <td><?php echo $dbtz = db_timezone(); ?>
          <?php if ($cfg->getDbTimezone() != $dbtz) { ?>
            (<?php echo sprintf(__('Interpreted as %s'), $cfg->getDbTimezone()); ?>)
          <?php } ?>
        </td></tr>
</tbody>
</table>
<br/>
<h2><?php echo __('Installed Language Packs'); ?></h2>
<div style="margin: 0 20px">
<?php
    foreach (Internationalization::availableLanguages() as $info) {
        $p = $info['path'];
        if ($info['phar'])
            $p = 'phar://' . $p;
        $manifest = (file_exists($p . '/MANIFEST.php')) ? (include $p . '/MANIFEST.php') : null;
?>
    <h3><strong><?php echo Internationalization::getLanguageDescription($info['code']); ?></strong>
        <?php if ($manifest) { ?>
            &mdash; <?php echo $manifest['Language']; ?>
        <?php } ?>
<?php   if ($info['phar'])
            PluginManager::showVerificationBadge($info['path']); ?>
        </h3>
        <div><?php echo sprintf('<code>%s</code> — %s', $info['code'],
                str_replace(ROOT_DIR, '', $info['path'])); ?>
<?php   if ($manifest) { ?>
            <br/> <?php echo __('Version'); ?>: <?php echo $manifest['Version'];
                ?>, <?php echo sprintf(__('for version %s'),
                    'v'.($manifest['Phrases-Version'] ?: '1.9')); ?>
            <br/> <?php echo __('Built'); ?>: <?php echo $manifest['Build-Date']; ?>
<?php   } ?>
        </div>
<?php
    } ?>
</div>
