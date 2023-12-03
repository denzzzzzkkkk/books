<?php
/*
Plugin Name: books
Description: Adds a custom post type for books and provides a shortcode to display a list of books.
Version: 1.6.3
Author: Your Name
*/

// Register Custom Post Type
function custom_books_post_type() {
    $labels = array(
        'name'                  => _x( 'Books', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Book', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Books', 'text_domain' ),
        'name_admin_bar'        => __( 'Book', 'text_domain' ),
        'archives'              => __( 'Book Archives', 'text_domain' ),
        'attributes'            => __( 'Book Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Book:', 'text_domain' ),
        'all_items'             => __( 'All Books', 'text_domain' ),
        'add_new_item'          => __( 'Add New Book', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Book', 'text_domain' ),
        'edit_item'             => __( 'Edit Book', 'text_domain' ),
        'update_item'           => __( 'Update Book', 'text_domain' ),
        'view_item'             => __( 'View Book', 'text_domain' ),
        'view_items'            => __( 'View Books', 'text_domain' ),
        'search_items'          => __( 'Search Book', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into book', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this book', 'text_domain' ),
        'items_list'            => __( 'Books list', 'text_domain' ),
        'items_list_navigation' => __( 'Books list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter books list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Book', 'text_domain' ),
        'description'           => __( 'Book Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-book-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'book', $args );
}
add_action( 'init', 'custom_books_post_type', 0 );

// Shortcode to display a list of books
function display_books_list() {
    $args = array(
        'post_type'      => 'book',
        'posts_per_page' => -1,
    );

    $books_query = new WP_Query( $args );

    if ( $books_query->have_posts() ) {
        $output = '<div class="books-list">';
        while ( $books_query->have_posts() ) {
            $books_query->the_post();
            $output .= '<div class="book-item">';
            $output .= '<h2>' . get_the_title() . '</h2>';
            $output .= '<p><strong>Author:</strong> ' . get_post_meta( get_the_ID(), 'book_author', true ) . '</p>';
            $output .= '<p><strong>Publication Date:</strong> ' . get_post_meta( get_the_ID(), 'book_publication_date', true ) . '</p>';
            $output .= '</div>';
        }
        $output .= '</div>';
        wp_reset_postdata();
    } else {
        $output = 'No books found';
    }

    return $output;
}
add_shortcode( 'books', 'display_books_list' );

// Register custom fields for book post type
function add_custom_fields() {
    add_meta_box(
        'book_author',
        'Author',
        'display_author_field',
        'book',
        'normal',
        'default'
    );

    add_meta_box(
        'book_publication_date',
        'Publication Date',
        'display_publication_date_field',
        'book',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'add_custom_fields' );

// Display custom fields on the book post type edit screen
function display_author_field() {
    $author = get_post_meta( get_the_ID(), 'book_author', true );
    echo '<label for="book_author">Author:</label>';
    echo '<input type="text" id="book_author" name="book_author" value="' . esc_attr( $author ) . '" />';
}

function display_publication_date_field() {
    $publication_date = get_post_meta( get_the_ID(), 'book_publication_date', true );
    echo '<label for="book_publication_date">Publication Date:</label>';
    echo '<input type="text" id="book_publication_date" name="book_publication_date" value="' . esc_attr( $publication_date ) . '" />';
}

// Save custom fields data
function save_custom_fields( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $author = sanitize_text_field( $_POST['book_author'] );
    update_post_meta( $post_id, 'book_author', $author );

    $publication_date = sanitize_text_field( $_POST['book_publication_date'] );
    update_post_meta( $post_id, 'book_publication_date', $publication_date );
}
add_action( 'save_post', 'save_custom_fields' );

// Register styles for the books list
function enqueue_books_styles() {
    wp_enqueue_style( 'books-list-styles', plugin_dir_url( __FILE__ ) . 'styles.css' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_books_styles' );
