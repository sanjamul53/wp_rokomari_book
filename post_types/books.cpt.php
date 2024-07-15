<?php

if (!class_exists('Books_Post_Type')) {

  class Books_Post_Type {


    public $cp_name;
    public $book_table;
    public $author_table;

    function __construct() {

      // init db 
      global $wpdb;

      $this->cp_name = 'rokomari_books';
      $this->book_table = $wpdb->prefix . "rokomari_books";
      $this->author_table = $wpdb->prefix . "rokomari_author_book";

      add_action('init', array($this, 'create_post_type'));
      add_action('init',  array($this, 'create_custom_taxonomy'));

      add_action('add_meta_boxes', array($this, 'handle_meta_boxes'));
      add_action('save_post', array($this, 'save_post'));

    }

    public function create_post_type() {

      register_post_type(
        $this->cp_name,
        array(
          'label' => 'Book',
          'description'   => 'Books',
          'labels' => array(
            'name'  => 'Books',
            'singular_name' => 'Book'
          ),
          'public'    => true,
          'supports'  => array(
            'title', 'editor', 
            'thumbnail'
          ),
          'hierarchical'  => false,
          'show_ui'   => true,
          'has_archive'   => false,
          'show_in_rest'  => true,
          'menu_icon' => 'dashicons-book-alt',
          // 'register_meta_box_cb'  =>  array( $this, 'handle_meta_boxes' ) // you can use
          // this callback instead of add_action('handle_meta_boxes');
        )
      );
    

    }

    // create custom taxonomy
    public function create_custom_taxonomy() {

      // language taxonomy
      register_taxonomy(
        "$this->cp_name"."_language", 
        $this->cp_name, 
        array(
          'label' => __( 'Languages' ),
          // 'rewrite' => array( 'slug' => 'story-type' ),
          'hierarchical' => false,
          // 'show_in_rest' => true
        )
      );

    }


    // add metabox
    public function handle_meta_boxes() {

      add_meta_box(
        'rokomari_book_metabox',
        'Book data',
        array($this, 'price_metabox_cb'),
        $this->cp_name,
        'normal',
        'high'
      );

    }

    // callback of metabox
    public function price_metabox_cb($post) {

      require_once(ROOT_PATH.'views/metabox/book_metabox.php');

    }


    // =============================================================================
    // ============================== save handler =================================
    public function save_post($p_id) {

      require_once(ROOT_PATH.'func/book_cpt/save_post_handler.php');

      $post_id = (int)$p_id;

      book_cpt_save_hanlder($post_id);

    }











  }


}
