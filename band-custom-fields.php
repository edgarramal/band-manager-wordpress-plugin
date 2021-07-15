<?php
/**
 * Plugin Name: Band Custom Post Types Plugin
 * Plugin URI: mailto:edgar.ramal@gmail.com
 * Description: Plugin to addapt wodpress cms to music bands.
 * Version: 1.0
 * Author: Edgar Ramal
 */

add_action('init', 'crear_un_cpt');
function crear_un_cpt()
{
    $labels = array(
        'name' => _x('Conciertos', 'Post type general name', 'textdomain'),
        'singular_name' => _x('Concierto', 'Post type singular name', 'textdomain'),
        'menu_name' => _x('Conciertos', 'Admin Menu text', 'textdomain'),
        'name_admin_bar' => _x('Concierto', 'Add New on Toolbar', 'textdomain'),
        'add_new' => __('Añadir nuevo', 'textdomain'),
        'add_new_item' => __('Añadir nuevo Concierto', 'textdomain'),
        'new_item' => __('Nuevo Concierto', 'textdomain'),
        'edit_item' => __('Editar Concierto', 'textdomain'),
        'view_item' => __('Ver Concierto', 'textdomain'),
        'all_items' => __('Todos los conciertos', 'textdomain'),
        'search_items' => __('Buscar Conciertos', 'textdomain'),
        'parent_item_colon' => __('Parent Conciertos:', 'textdomain'),
        'not_found' => __('Conciertos no encontrados.', 'textdomain'),
        'not_found_in_trash' => __('No hay conciertos en la papelera.', 'textdomain'),
        'featured_image' => _x('Cartel del concierto', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain'),
        'set_featured_image' => _x('Cambiar cartel del concierto', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'remove_featured_image' => _x('Borrar cartel', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'use_featured_image' => _x('Usar como cartel', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'archives' => _x('Archivos del concierto', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain'),
        'insert_into_item' => _x('Insertar en el concierto', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain'),
        'uploaded_to_this_item' => _x('Subir al concierto', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain'),
        'filter_items_list' => _x('Filter conciertos list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain'),
        'items_list_navigation' => _x('Lista de navegacion de conciertos', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain'),
        'items_list' => _x('Lista de conciertos', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain'),
    );
    $args = array(
        'public' => true,
        'label' => 'Conciertos',
        'labels' => $labels,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => array('title', 'editor', 'thumbnail'),
    );
    register_post_type('concierto', $args);
}


function twp_register_meta_boxes()
{
    add_meta_box('mi-meta-box-id', __('Informacion del Concierto', 'tutorialeswp'), 'twp_mi_display_callback', 'concierto');
}

add_action('add_meta_boxes', 'twp_register_meta_boxes');
/*
**** Meta box display callback ****
*/
function twp_mi_display_callback($post)
{
    $date = get_post_meta($post->ID, 'date', true);
    $location = get_post_meta($post->ID, 'location', true);

    // Usaremos este nonce field más adelante cuando guardemos en twp_save_meta_box()
    wp_nonce_field('mi_meta_box_nonce', 'meta_box_nonce');

    echo '<p><label for="date">Fecha: </label> <input type="date" name="date" id="date" value="' . $date . '" /></p>';
    echo '<p><label for="location">Localización: </label> <input type="text" name="location" id="location" value="' . $location . '" /></p>';
}

/*
**** Save meta box content ****
*/
function twp_save_meta_box($post_id)
{
    // Comprobamos si es auto guardado
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    // Comprobamos el valor nonce creado en twp_mi_display_callback()
    if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'mi_meta_box_nonce')) return;
    // Comprobamos si el usuario actual no puede editar el post
    //if(!current_user_can('edit_post')){return false;}
    // Guardamos...
    if (isset($_POST['date']))
        update_post_meta($post_id, 'date', $_POST['date']);
    if (isset($_POST['location']))
        update_post_meta($post_id, 'location', $_POST['location']);
}

add_action('save_post', 'twp_save_meta_box');


// Change POSTS to NOTICIAS in WP dashboard
add_action('admin_menu', 'change_post_menu_label');
add_action('init', 'change_post_object_label');
function change_post_menu_label()
{
    global $menu;
    global $submenu;
    $menu[5][0] = 'Noticias';
    $submenu['edit.php'][5][0] = 'Noticias';
    $submenu['edit.php'][10][0] = 'Añadir Noticias';
    $submenu['edit.php'][16][0] = 'Tags de noticias';
    echo '';
}

function change_post_object_label()
{
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $wp_post_types['post']->menu_icon = 'dashicons-rss';
    $labels->name = 'Noticias';
    $labels->singular_name = 'Noticia';
    $labels->add_new = 'Añadir Noticia';
    $labels->add_new_item = 'Añadir Noticias';
    $labels->edit_item = 'Editar Noticias';
    $labels->new_item = 'Noticias';
    $labels->view_item = 'Ver Noticias';
    $labels->search_items = 'Buscar Noticias';
    $labels->not_found = 'No se encontraron noticias';
    $labels->not_found_in_trash = 'No hay noticias en la papelera';
}
