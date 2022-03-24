<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhssoprovider','use_admin')) : ?>
    <?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/modules_menu/sso_link.tpl.php'));?>
<?php endif; ?>