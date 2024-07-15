<?php


function admin_menu_ui() {
  return require( ROOT_PATH . 'views/admin_ui.php' );
}

function admin_menu(){

  add_menu_page(
    'Rokomari Options',
    'Rokomari',
    'manage_options',
    'rokomari_admin',
    'admin_menu_ui',
    'dashicons-podio'
  );

}
