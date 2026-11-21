<?php
/*
 * MiHelpdesk — Página de inicio del portal (tema visual).
 * Parte de MiHelpdesk, fork de osTicket bajo GPLv2. Archivo nuevo, no existe
 * en osTicket. Lo usa index.php cuando el tema visual está activo.
 *
 * Variables disponibles: $cfg, $thisclient.
 */
$kbEnabled = $cfg && $cfg->isKnowledgebaseEnabled();
$canOpen = $cfg->getClientRegistrationMode() != 'disabled'
    || !$cfg->isClientLoginRequired();
$isClient = $thisclient && is_object($thisclient) && $thisclient->isValid()
    && !$thisclient->isGuest();
// Textos y secciones que el administrador cambia en Personalización
require_once INCLUDE_DIR . 'mihelpdesk/class.customize.php';
$showSearch = MhCustomize::isOn('home_show_search');
$showPopular = MhCustomize::isOn('home_show_popular');
$showCategories = MhCustomize::isOn('home_show_categories');

// Artículos populares: los destacados; si no hay, los principales de las
// categorías destacadas.
$popular = array();
if ($kbEnabled) {
    foreach (FAQ::getFeatured()->select_related('category')->limit(5) as $F)
        $popular[$F->getId()] = $F;
    if (!$popular) {
        foreach (Category::getFeatured() as $C) {
            foreach ($C->getTopArticles() as $F) {
                if (count($popular) >= 5) break 2;
                $popular[$F->getId()] = $F;
            }
        }
    }
    // Categorías públicas con el número de artículos publicados
    $categories = Category::objects()
        ->filter(array('ispublic__in' => array(Category::VISIBILITY_PUBLIC,
            Category::VISIBILITY_FEATURED)))
        ->filter(Q::any(array('category_pid__isnull' => true, 'category_pid' => 0)))
        ->annotate(array('faq_count' => SqlAggregate::COUNT(
            SqlCase::N()
                ->when(array('faqs__ispublished__gt' => FAQ::VISIBILITY_PRIVATE), 1)
                ->otherwise(null))))
        ->order_by('-ispublic', 'name')
        ->limit(6);
}
?>
<div class="mh-home">
    <section class="mh-hero">
        <div class="mh-hero-text">
        <?php
        if ($cfg && ($page = $cfg->getLandingPage()))
            echo $page->getBodyWithImages();
        else
            echo '<h1>'.__('How can we help you?').'</h1>';
        ?>
        </div>
<?php if ($kbEnabled && $showSearch) { ?>
        <form class="mh-search" method="get" action="kb/faq.php" role="search">
            <input type="hidden" name="a" value="search"/>
            <label for="mh-search-q" class="mh-sr"><?php echo __('Search our knowledge base'); ?></label>
            <svg class="mh-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
            <input id="mh-search-q" type="search" name="q"
                placeholder="<?php echo __('Search our knowledge base'); ?>"/>
            <button type="submit"><?php echo __('Search'); ?></button>
        </form>
<?php } ?>
    </section>

    <div class="mh-actions">
<?php if ($canOpen) { ?>
        <a class="mh-action" href="open.php">
            <span class="mh-action-icon mh-action-icon--brand" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"></path></svg>
            </span>
            <span class="mh-action-text">
                <span class="mh-action-title"><?php echo Format::htmlchars(MhCustomize::text('home_open_title', __('Open a New Ticket'))); ?></span>
                <span class="mh-action-desc"><?php echo Format::htmlchars(MhCustomize::text('home_open_desc', __('Tell us about your problem or request. You will get a ticket number to follow up.'))); ?></span>
                <span class="mh-action-cta"><?php echo __('Get started'); ?> →</span>
            </span>
        </a>
<?php } ?>
        <a class="mh-action" href="<?php echo $isClient ? 'tickets.php' : 'view.php'; ?>">
            <span class="mh-action-icon mh-action-icon--success" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3 8-8"></path><path d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h9"></path></svg>
            </span>
            <span class="mh-action-text">
                <span class="mh-action-title"><?php echo $isClient ? __('My Tickets') : Format::htmlchars(MhCustomize::text('home_status_title', __('Check Ticket Status'))); ?></span>
                <span class="mh-action-desc"><?php echo Format::htmlchars(MhCustomize::text('home_status_desc', __('Check the status of your requests and reply to our team.'))); ?></span>
                <span class="mh-action-cta"><?php echo __('Check now'); ?> →</span>
            </span>
        </a>
    </div>

<?php
if (!$showPopular) $popular = array();
$showCats = $kbEnabled && $showCategories && $categories->exists(true);
if ($kbEnabled && ($popular || $showCats)) { ?>
    <div class="mh-kb-home">
<?php if ($popular) { ?>
        <section>
            <h2><?php echo __('Popular articles'); ?></h2>
            <ul class="mh-list">
<?php foreach ($popular as $F) { ?>
                <li><a href="<?php echo ROOT_PATH; ?>kb/faq.php?id=<?php echo urlencode($F->getId()); ?>"><?php
                    echo $F->getLocalQuestion(); ?><span aria-hidden="true">›</span></a></li>
<?php } ?>
            </ul>
        </section>
<?php } ?>
<?php if ($showCats) { ?>
        <section>
            <h2><?php echo __('Categories'); ?></h2>
            <div class="mh-category-grid">
<?php foreach ($categories as $C) { ?>
                <a class="mh-category" href="<?php echo ROOT_PATH; ?>kb/faq.php?cid=<?php echo urlencode($C->getId()); ?>">
                    <span class="mh-category-name"><?php echo Format::htmlchars($C->getLocalName()); ?></span>
                    <span class="mh-category-count"><?php echo sprintf(_N('%d article', '%d articles', $C->faq_count), $C->faq_count); ?></span>
                </a>
<?php } ?>
            </div>
        </section>
<?php } ?>
    </div>
<?php } ?>
</div>
