<?php

if (!class_exists('Books_Category')) {

  class Books_Category {

    function __construct() {
      add_action('init', array($this, 'create_category'));
    }

    public function create_category() {

      register_taxonomy(
        'book-category',
        ['rokomari_books', 'rokomari_authors'],
        array(
          'label' => __( 'Category' ),
          'rewrite' => array( 'slug' => 'book-category' ),
          'show_in_rest' => true,
          'hierarchical' => true,
          'public'            => true,
          'show_ui'           => true,
          'show_admin_column' => true,
          'query_var'         => true
        )
    );

    }


  }

}
