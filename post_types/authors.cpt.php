<?php

if (!class_exists('Authors_Post_Type')) {

  class Authors_Post_Type {

    function __construct() {
      add_action('init', array($this, 'create_post_type'));
    }

    public function create_post_type() {
      register_post_type(
        'rokomari_authors',
        array(
          'label' => 'Author',
          'description'   => 'Authors',
          'labels' => array(
            'name'  => 'Authors',
            'singular_name' => 'Author'
          ),
          'public'    => true,
          'supports'  => array('title', 'editor', 'thumbnail'),
          'hierarchical'  => false,
          'show_ui'   => true,
          'has_archive'   => false,
          'show_in_rest'  => true,
          'menu_icon' => 'dashicons-admin-users',
        )
      );
    }


  }


}
