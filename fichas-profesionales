<?php
/*
Plugin Name: Fichas Profesionales
Description: Permite a los usuarios crear y gestionar sus fichas profesionales con campos personalizados y diseño moderno.
Version: 2.0
Author: Sergio LN
Author URI: https://www.publikate.cl
Text Domain: fichas-profesionales
Domain Path: /languages
*/

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('FICHAS_VERSION', '2.0');
define('FICHAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FICHAS_PLUGIN_URL', plugin_dir_url(__FILE__));

// =====================================================
// Incluir archivos necesarios
// =====================================================
require_once FICHAS_PLUGIN_DIR . 'includes/meta-boxes.php';
require_once FICHAS_PLUGIN_DIR . 'includes/template-display.php';

// =====================================================
// Registrar tipos personalizados
// =====================================================
function register_ficha_post_type() {
    $labels = array(
        'name' => __('Fichas Profesionales', 'fichas-profesionales'),
        'singular_name' => __('Ficha Profesional', 'fichas-profesionales'),
        'add_new' => __('Añadir Nueva', 'fichas-profesionales'),
        'add_new_item' => __('Añadir Nueva Ficha', 'fichas-profesionales'),
        'edit_item' => __('Editar Ficha', 'fichas-profesionales'),
        'new_item' => __('Nueva Ficha', 'fichas-profesionales'),
        'view_item' => __('Ver Ficha', 'fichas-profesionales'),
        'search_items' => __('Buscar Fichas', 'fichas-profesionales'),
        'not_found' => __('No se encontraron fichas', 'fichas-profesionales'),
        'not_found_in_trash' => __('No hay fichas en la papelera', 'fichas-profesionales'),
        'all_items' => __('Todas las Fichas', 'fichas-profesionales'),
        'menu_name' => __('Fichas', 'fichas-profesionales'),
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'ficha', 'with_front' => false),
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-id-alt',
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
    );
    
    register_post_type('ficha', $args);
}
add_action('init', 'register_ficha_post_type');

// =====================================================
// Flush rewrite rules on activation
// =====================================================
function fichas_activation() {
    register_ficha_post_type();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'fichas_activation');

function fichas_deactivation() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'fichas_deactivation');

// =====================================================
// Asignar rol al registrar usuario
// =====================================================
function assign_custom_role($user_id) {
    $user = new WP_User($user_id);
    if (empty($user->roles)) {
        $user->set_role('subscriber');
    }
}
add_action('user_register', 'assign_custom_role');

// =====================================================
// Guardar relación ficha-usuario
// =====================================================
function save_ficha_user_meta($post_id, $post, $update) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if ($post->post_type !== 'ficha') {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $author_id = $post->post_author;
    update_user_meta($author_id, 'ficha_id', $post_id);
}
add_action('save_post', 'save_ficha_user_meta', 10, 3);

// =====================================================
// Agregar menú en admin bar
// =====================================================
function add_ficha_admin_bar_menu($wp_admin_bar) {
    if (!is_user_logged_in() || !current_user_can('edit_posts')) {
        return;
    }
    
    $user_id = get_current_user_id();
    $ficha_id = get_user_meta($user_id, 'ficha_id', true);
    
    $args = array(
        'id' => 'fichas-menu',
        'title' => __('Mi Ficha', 'fichas-profesionales'),
        'href' => $ficha_id ? get_edit_post_link($ficha_id) : admin_url('post-new.php?post_type=ficha'),
    );
    $wp_admin_bar->add_node($args);
    
    $wp_admin_bar->add_node(array(
        'parent' => 'fichas-menu',
        'id' => 'ver-fichas',
        'title' => __('Ver Todas', 'fichas-profesionales'),
        'href' => admin_url('edit.php?post_type=ficha'),
    ));
    
    if ($ficha_id) {
        $wp_admin_bar->add_node(array(
            'parent' => 'fichas-menu',
            'id' => 'ver-ficha-frontend',
            'title' => __('Ver en Sitio', 'fichas-profesionales'),
            'href' => get_permalink($ficha_id),
        ));
    }
}
add_action('admin_bar_menu', 'add_ficha_admin_bar_menu', 100);

// =====================================================
// Estilos y scripts frontend
// =====================================================
function fichas_profesionales_styles() {
    $css_file = FICHAS_PLUGIN_DIR . 'css/style.css';
    
    if (file_exists($css_file)) {
        wp_enqueue_style(
            'fichas-profesionales-style', 
            FICHAS_PLUGIN_URL . 'css/style.css',
            array(),
            FICHAS_VERSION
        );
    }
    
    // JavaScript para galería y lightbox
    $js_file = FICHAS_PLUGIN_DIR . 'js/frontend-scripts.js';
    if (file_exists($js_file)) {
        wp_enqueue_script(
            'fichas-frontend-scripts',
            FICHAS_PLUGIN_URL . 'js/frontend-scripts.js',
            array(),
            FICHAS_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'fichas_profesionales_styles');

// =====================================================
// Estilos y scripts admin
// =====================================================
function fichas_admin_styles($hook) {
    global $post_type;
    
    if ($post_type !== 'ficha') {
        return;
    }
    
    // CSS Admin
    $admin_css = FICHAS_PLUGIN_DIR . 'css/admin-style.css';
    if (file_exists($admin_css)) {
        wp_enqueue_style(
            'fichas-admin-style',
            FICHAS_PLUGIN_URL . 'css/admin-style.css',
            array(),
            FICHAS_VERSION
        );
    }
    
    // JavaScript Admin
    $admin_js = FICHAS_PLUGIN_DIR . 'js/admin-scripts.js';
    if (file_exists($admin_js)) {
        wp_enqueue_script(
            'fichas-admin-scripts',
            FICHAS_PLUGIN_URL . 'js/admin-scripts.js',
            array('jquery', 'jquery-ui-sortable'),
            FICHAS_VERSION,
            true
        );
    }
    
    // Media Uploader
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'fichas_admin_styles');

// =====================================================
// Restricción de edición
// =====================================================
function restrict_ficha_editing($allcaps, $caps, $args) {
    if (!isset($args[0]) || !in_array($args[0], array('edit_post', 'delete_post'))) {
        return $allcaps;
    }
    
    if (!isset($args[2])) {
        return $allcaps;
    }
    
    $post = get_post($args[2]);
    
    if (!$post || $post->post_type !== 'ficha') {
        return $allcaps;
    }
    
    if (isset($allcaps['edit_others_posts']) && $allcaps['edit_others_posts']) {
        return $allcaps;
    }
    
    if ($post->post_author == get_current_user_id()) {
        return $allcaps;
    }
    
    $allcaps[$caps[0]] = false;
    
    return $allcaps;
}
add_filter('user_has_cap', 'restrict_ficha_editing', 10, 3);

// =====================================================
// Columnas personalizadas en listado
// =====================================================
function add_ficha_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        if ($key === 'title') {
            $new_columns['ficha_author'] = __('Profesional', 'fichas-profesionales');
            $new_columns['ficha_email'] = __('Email', 'fichas-profesionales');
        }
    }
    
    return $new_columns;
}
add_filter('manage_ficha_posts_columns', 'add_ficha_columns');

function display_ficha_column_content($column, $post_id) {
    switch ($column) {
        case 'ficha_author':
            $author_id = get_post_field('post_author', $post_id);
            $author = get_userdata($author_id);
            echo esc_html($author->display_name);
            break;
            
        case 'ficha_email':
            $email = get_post_meta($post_id, '_ficha_email', true);
            if ($email) {
                echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_ficha_posts_custom_column', 'display_ficha_column_content', 10, 2);

// =====================================================
// Dashboard Widget
// =====================================================
function fichas_dashboard_widget() {
    wp_add_dashboard_widget(
        'fichas_stats_widget',
        __('Estadísticas de Fichas', 'fichas-profesionales'),
        'fichas_dashboard_widget_content'
    );
}
add_action('wp_dashboard_setup', 'fichas_dashboard_widget');

function fichas_dashboard_widget_content() {
    $total_fichas = wp_count_posts('ficha');
    $published = $total_fichas->publish ?? 0;
    $draft = $total_fichas->draft ?? 0;
    $pending = $total_fichas->pending ?? 0;
    
    ?>
    <div class="ficha-dashboard-widget">
        <div class="ficha-stats">
            <div class="ficha-stat-box">
                <span class="ficha-stat-number"><?php echo esc_html($published); ?></span>
                <span class="ficha-stat-label"><?php _e('Publicadas', 'fichas-profesionales'); ?></span>
            </div>
            <div class="ficha-stat-box">
                <span class="ficha-stat-number"><?php echo esc_html($draft); ?></span>
                <span class="ficha-stat-label"><?php _e('Borradores', 'fichas-profesionales'); ?></span>
            </div>
            <div class="ficha-stat-box">
                <span class="ficha-stat-number"><?php echo esc_html($pending); ?></span>
                <span class="ficha-stat-label"><?php _e('Pendientes', 'fichas-profesionales'); ?></span>
            </div>
        </div>
        <p style="margin-top: 15px;">
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=ficha')); ?>" class="button button-primary">
                <?php _e('Ver Todas las Fichas', 'fichas-profesionales'); ?>
            </a>
        </p>
    </div>
    <?php
}

// =====================================================
// Mensajes de ayuda
// =====================================================
function fichas_admin_notices() {
    $screen = get_current_screen();
    
    if ($screen->post_type !== 'ficha') {
        return;
    }
    
    if ($screen->base === 'post' && isset($_GET['post'])) {
        ?>
        <div class="notice notice-info ficha-help-box">
            <p>
                <strong><?php _e('Consejo:', 'fichas-profesionales'); ?></strong>
                <?php _e('Completa todos los campos para que tu ficha profesional sea más atractiva. Los campos con * son obligatorios.', 'fichas-profesionales'); ?>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'fichas_admin_notices');

// =====================================================
// Template personalizado para single ficha
// =====================================================
function fichas_single_template($template) {
    if (is_singular('ficha')) {
        $custom_template = FICHAS_PLUGIN_DIR . 'templates/single-ficha.php';
        
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    
    return $template;
}
add_filter('single_template', 'fichas_single_template');

// =====================================================
// Cargar traducciones
// =====================================================
function fichas_load_textdomain() {
    load_plugin_textdomain(
        'fichas-profesionales',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
}
add_action('plugins_loaded', 'fichas_load_textdomain');

// =====================================================
// Agregar enlaces en página de plugins
// =====================================================
function fichas_plugin_action_links($links) {
    $settings_link = '<a href="' . esc_url(admin_url('edit.php?post_type=ficha')) . '">' . __('Ver Fichas', 'fichas-profesionales') . '</a>';
    array_unshift($links, $settings_link);
    
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'fichas_plugin_action_links');
?>
