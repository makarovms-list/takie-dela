<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php the_title(); ?></title>
    <link rel="preconnect" href="https://design.nuzhnapomosh.ru">
    <link href="https://design.nuzhnapomosh.ru/fonts/fonts-futura-leksa-romanovsky.css" rel="stylesheet">
    <link rel='stylesheet' href='<?= get_template_directory_uri() ?>/css/styles.css?ver=1.0.0' media='all' />
    <!--
    <script src='<?= get_template_directory_uri() ?>/js/jquery.min.js'></script>
    <script src='<?= get_template_directory_uri() ?>/js/main.js'></script>
    -->
    <?php wp_head(); ?>
    <script>
        let ajaxurl = '<?php echo site_url() ?>/wp-admin/admin-ajax.php';
    </script>
</head>







				