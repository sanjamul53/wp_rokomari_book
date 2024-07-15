<?php

/*
 * Plugin Name: Rokomari
*/

if( ! defined( 'ABSPATH') ){
  exit;
}

if( ! class_exists( 'Rokomari' ) ){
  class Rokomari {

    // public $charset;
    // public $book_table;
    // public $author_table;
    // public $category_table;
    // public $posts_table;
    // public $term_taxonomy_table;

    function __construct(){

      $this->define_constants();

      // // init db 
      // global $wpdb;
      // $this->charset = $wpdb->get_charset_collate();
      // $this->book_table = $wpdb->prefix . "rokomari_books";
      // $this->author_table = $wpdb->prefix . "rokomari_author_book";
      // $this->category_table = $wpdb->prefix . "rokomari_category_book";
      // $this->posts_table = $wpdb->prefix . "posts";
      // $this->term_taxonomy_table = $wpdb->prefix . "term_taxonomy";


      $this->create_db_tables();

      // add admin menu
      add_action( 'admin_menu', array( $this, 'admin_menu_handler' ) );

      // add js file to admin screen
      add_action('admin_enqueue_scripts', array( $this, 'admin_js_handler' ));

      // books cpt
      require_once(ROOT_PATH.'post_types/books.cpt.php');
      $books = new Books_Post_Type();

      require_once(ROOT_PATH.'post_types/authors.cpt.php');
      $authors = new Authors_Post_Type();

      require_once(ROOT_PATH.'post_types/books.category.php');
      $book_cat = new Books_Category();

      // require_once( MV_SLIDER_PATH . 'post-types/class.mv-slider-cpt.php' );
      // $MV_Slider_Post_Type = new MV_Slider_Post_Type();


      // test template
      add_filter('template_include', array($this, 'loadTemplate'), 99);

    }


    // ============================== Define Constants ==============================
    public function define_constants(){
      define( 'ROOT_PATH', plugin_dir_path( __FILE__ ) );
      define( 'ROOT_URL', plugin_dir_url( __FILE__ ) );
    }



    // ============================== handle template ==============================
    public function loadTemplate($template) {

      if (is_page('test')) {
        return  ROOT_PATH.'test.php';
      }

      return $template;
    }

    // ============================== handle admin js ==============================
    public function admin_js_handler($hook_suffix) {

      if(!is_admin()) return null;

      $cpt = 'rokomari_books';

      if(in_array($hook_suffix, array('post.php', 'post-new.php'))) {

        $screen = get_current_screen();

        if( is_object( $screen ) && $cpt == $screen->post_type ){

          wp_enqueue_script(
            'rokomari_book_cpt', 
            ROOT_URL. 'script/rokomari_book_cpt.js', 
            array(), '1.0' 
          );

        }

      }


    }



    // ============================== create database tables ==============================
    public function create_db_tables() {

      require_once(ROOT_PATH."func/rokomari/create_tables.php");

      create_rokomari_db_tables();

    }


    // ============================== handle admin ui ==============================
    public function admin_menu_handler() {
      require_once(ROOT_PATH.'admin/admin_menu.php');
      admin_menu();
    }

  }
}


if( class_exists( 'Rokomari' ) ){

  // register_activation_hook( __FILE__, array( 'Rokomari', 'create_db_tables' ) );

  $rokomari = new Rokomari();

}