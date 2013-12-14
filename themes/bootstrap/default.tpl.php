<!doctype html>
<html lang='en'> 
    <head>
        <meta charset='utf-8'/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?></title>
        <link rel='shortcut icon' href='<?= theme_template_url($favicon) ?>'/>
        <link rel='stylesheet' href='<?= theme_template_url($stylesheet) ?>'/>
        <?php if (isset($inline_style)): ?><style><?= $inline_style ?></style><?php endif; ?>
    </head>
    <body>
        <div id='outer-wrap-header' class='container'>
            <div id='inner-wrap-header'>
                <div id='header'>
                    <div id='login-menu'><?= login_menu() ?></div>
                    <div id='banner'>
                        <a href='<?= base_url() ?>'><img id='site-logo' src='<?= theme_template_url($logo) ?>' alt='logo' width='<?= $logo_width ?>' height='<?= $logo_height ?>' /></a>
                        <span id='site-title'><a href='<?= base_url() ?>'><?= $header ?></a></span>
                        <span id='site-slogan'><?= $slogan ?></span>
                    </div>
                </div>
            </div>
        </div>

        <?php if (region_has_content('flash')): ?>
            <div id='outer-wrap-flash' class='container'>
                <div id='inner-wrap-flash' class='row'>
                    <div id='flash' class='col-xs-12 col-sm-12 col-md-12 col-lg-12'><?= render_views('flash') ?></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (region_has_content('featured-first', 'featured-middle', 'featured-last')): ?>
            <div id='outer-wrap-featured' class='container'>
                <div id='inner-wrap-featured' class='row'>
                    <div id='featured-first' class='col-xs-12 col-sm-6 col-md-4 col-lg-4'><?= render_views('featured-first') ?></div>
                    <div id='featured-middle' class='col-xs-12 col-sm-6 col-md-4 col-lg-4'><?= render_views('featured-middle') ?></div>
                    <div class="clearfix visible-sm"></div>
                    <div id='featured-last' class='col-xs-12 col-sm-12 col-md-4 col-lg-4'><?= render_views('featured-last') ?></div>
                </div>
            </div>
        <?php endif; ?>

        <div id='outer-wrap-main' class='container'>
            <div id='inner-wrap-main' class='row'>
                <div id='primary' class='col-xs-12 col-sm-8 col-md-8 col-lg-8'>
                    <?= get_messages_from_session() ?>
                    <?= @$main ?>
                    <?php if (region_has_content('default')) render_views('default'); ?>
                    <?php if (region_has_content('primary')) render_views('primary'); ?>
                </div>
                <?php if (region_has_content('sidebar')): ?>
                    <div id='sidebar' class='col-xs-12 col-sm-4 col-md-4 col-lg-4'><?= render_views('sidebar') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (region_has_content('triptych-first', 'triptych-middle', 'triptych-last')): ?>
            <div id='outer-wrap-triptych' class='container'>
                <div id='inner-wrap-triptych' class='row'>
                    <div id='triptych-first' class='col-xs-12 col-sm-6 col-md-4 col-lg-4'><?= render_views('triptych-first') ?></div>
                    <div id='triptych-middle' class='col-xs-12 col-sm-6 col-md-4 col-lg-4'><?= render_views('triptych-middle') ?></div>
                    <div class="clearfix visible-sm"></div>
                    <div id='triptych-last' class='col-xs-12 col-sm-12 col-md-4 col-lg-4'><?= render_views('triptych-last') ?></div>
                </div>
            </div>
        <?php endif; ?>

        <div id='outer-wrap-footer' class='container'>
            <?php if (region_has_content('footer-column-one', 'footer-column-two', 'footer-column-three', 'footer-column-four')): ?>
                <div id='inner-wrap-footer-column' class='row'>
                    <div id='footer-column-one' class='col-xs-6 col-sm-6 col-md-3 col-lg-3'><?= render_views('footer-column-one') ?></div>
                    <div id='footer-column-two' class='col-xs-6 col-sm-6 col-md-3 col-lg-3'><?= render_views('footer-column-two') ?></div>
                    <div class="clearfix visible-sm visible-xs"></div>
                    <div id='footer-column-three' class='col-xs-6 col-sm-6 col-md-3 col-lg-3'><?= render_views('footer-column-three') ?></div>
                    <div id='footer-column-four' class='col-xs-6 col-sm-6 col-md-3 col-lg-3'><?= render_views('footer-column-four') ?></div>
                </div>
            <?php endif; ?>
            <div id='inner-wrap-footer' class='row'>
                <div id='footer' class='col-xs-12 col-sm-12 col-md-12 col-lg-12'><?= render_views('footer') ?><?= $footer ?><?= get_tools() ?><?= get_debug() ?></div>
            </div>
        </div>

    </body>
</html>