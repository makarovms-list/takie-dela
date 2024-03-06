<?php

add_action( 'wp_enqueue_scripts', 'understrap_child_styles' );
function understrap_child_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/css/style.css', array(), null  );
    wp_enqueue_script( 'child-js', get_stylesheet_directory_uri() . '/js/main.js', array(), null);
    wp_enqueue_script( 'jquery-js', get_stylesheet_directory_uri() . '/js/jquery.min.js', array(), null);
}

add_filter( 'the_content', function( $content ) {
  return '<h1>Тестовый заголовок</h1>'.$content;
}, 0);


?>