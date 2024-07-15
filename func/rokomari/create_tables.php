<?php

function create_rokomari_db_tables() {

  // init db 
  global $wpdb;
  $charset = $wpdb->get_charset_collate();
  $book_table = $wpdb->prefix . "rokomari_books";
  $author_table = $wpdb->prefix . "rokomari_author_book";
  $category_table = $wpdb->prefix . "rokomari_category_book";
  $post_table = $wpdb->prefix . "posts";
  $term_taxonomy_table = $wpdb->prefix . "term_taxonomy";


  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  // ================== book table ==================
  dbDelta("CREATE TABLE IF NOT EXISTS  $book_table (

      id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      post_id  bigint(20) UNSIGNED NOT NULL,
      price int NOT NULL,
      discount smallint NOT NULL DEFAULT 0,
      language bigint(20) UNSIGNED DEFAULT NULL,
      release_date date NOT NULL,
      PRIMARY KEY  (id),
      FOREIGN KEY  (post_id) REFERENCES {$post_table}(id) ON DELETE CASCADE,
      FOREIGN KEY  (language) REFERENCES {$term_taxonomy_table}(term_id) ON DELETE SET NULL
    ) 
    $charset;"
  );

  $query = "CREATE UNIQUE INDEX rokomari_book_post_id_idx ON  $book_table(post_id)" ;
  $wpdb->query($query) ;
  unset($query);

  $query = "CREATE INDEX rokomari_book_language_idx ON  $book_table(language)" ;
  $wpdb->query($query) ;
  unset($query);

  $query = "CREATE INDEX rokomari_book_price_idx ON  $book_table(price)" ;
  $wpdb->query($query) ;
  unset($query);



  // ================== author table ==================
  dbDelta("CREATE TABLE IF NOT EXISTS  $author_table (

      id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      book_id  bigint(20) UNSIGNED NOT NULL,
      author_id  bigint(20) UNSIGNED NOT NULL,
      is_main boolean NOT NULL DEFAULT false,
      PRIMARY KEY  (id),
      FOREIGN KEY  (book_id) REFERENCES {$book_table}(post_id) ON DELETE CASCADE,
      FOREIGN KEY  (author_id) REFERENCES {$post_table}(id) ON DELETE CASCADE
    ) 
    $charset;"
  );

  $query = "CREATE INDEX rokomari_author_book_book_id_idx ON  $author_table(book_id)";
  $wpdb->query($query) ;
  unset($query);

  $query = "CREATE INDEX rokomari_author_book_author_id_idx ON  $author_table(author_id)";
  $wpdb->query($query) ;
  unset($query);


  // ================== category table ==================
  dbDelta("CREATE TABLE IF NOT EXISTS  $category_table (

      id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      book_id  bigint(20) UNSIGNED NOT NULL,
      category_id  bigint(20) UNSIGNED NOT NULL,
      PRIMARY KEY  (id),
      FOREIGN KEY  (book_id) REFERENCES {$book_table}(post_id) ON DELETE CASCADE,
      FOREIGN KEY  (category_id) REFERENCES {$term_taxonomy_table}(term_id) ON DELETE CASCADE
    ) 
    $charset;"
  );

  $query = "CREATE INDEX rokomari_category_book_book_id_idx ON  $category_table(book_id)";
  $wpdb->query($query) ;
  unset($query);

  $query = "CREATE INDEX rokomari_category_book_category_id_idx ON  $category_table(category_id)";
  $wpdb->query($query) ;
  unset($query);

}