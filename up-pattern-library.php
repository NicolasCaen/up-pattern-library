<?php
/**
 * Plugin Name: Bibliothèque de Patterns FSE
 * Description: Affiche tous les patterns du thème FSE en cours via un shortcode [pattern_library]
 * Version: 1.0.0
 * Author: Claude
 * Text Domain: pattern-library
 */

// Sécurité: Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

class FSE_Pattern_Library {

    public function __construct() {
        // Enregistrer les assets
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));

        // Enregistrer le shortcode
        add_shortcode('pattern_library', array($this, 'render_pattern_library'));
    }

    /**
     * Enregistrer les styles et scripts
     */
    public function register_assets() {
        wp_register_style(
            'pattern-library-css',
            plugin_dir_url(__FILE__) . 'assets/css/pattern-library.css',
            array(),
            '1.0.0'
        );

        wp_register_script(
            'pattern-library-js',
            plugin_dir_url(__FILE__) . 'assets/js/pattern-library.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }

    /**
     * Récupérer tous les patterns du thème en cours
     */
    private function get_patterns() {
        // Utiliser WP_Block_Pattern_Categories_Registry pour les catégories
        $pattern_categories_registry = WP_Block_Pattern_Categories_Registry::get_instance();
        $pattern_categories = $pattern_categories_registry->get_all_registered();

        // Utiliser WP_Block_Patterns_Registry pour les patterns
        $pattern_registry = WP_Block_Patterns_Registry::get_instance();
        $patterns = $pattern_registry->get_all_registered();

        $organized_patterns = array();

        // Organiser les patterns par catégorie
        foreach ($pattern_categories as $category) {
            $organized_patterns[$category['name']] = array(
                'label' => $category['label'],
                'patterns' => array()
            );
        }

        // Ajouter la catégorie "All" en premier
        $all_category = array(
            'label' => __('All', 'pattern-library'),
            'patterns' => array()
        );

        // Assigner les patterns à leurs catégories
        foreach ($patterns as $pattern) {
            // Ajouter à la catégorie "All"
            $all_category['patterns'][] = $pattern;

            // Ajouter aux catégories spécifiques
            if (!empty($pattern['categories'])) {
                foreach ($pattern['categories'] as $category_name) {
                    if (isset($organized_patterns[$category_name])) {
                        $organized_patterns[$category_name]['patterns'][] = $pattern;
                    }
                }
            }
        }

        // Placer "All" au début du tableau
        $organized_patterns = array('all' => $all_category) + $organized_patterns;

        return $organized_patterns;
    }

    /**
     * Rendu du shortcode [pattern_library]
     */
    public function render_pattern_library($atts) {
        // Charger les assets
        wp_enqueue_style('pattern-library-css');
        wp_enqueue_script('pattern-library-js');

        // Récupérer les patterns organisés par catégorie
        $organized_patterns = $this->get_patterns();

        // Commencer la sortie buffer
        ob_start();
        ?>
        <div class="pattern-library">
            <div class="pattern-library-header">
                <h2 class="pattern-library-title">
                    <span class="dashicons dashicons-layout"></span>
                    <?php _e('Pattern Library', 'pattern-library'); ?>
                </h2>
                <div class="pattern-library-actions">
                    <span class="pattern-library-type"><?php _e('Free', 'pattern-library'); ?></span>
                    <span class="pattern-library-type pattern-library-pro"><?php _e('Pro', 'pattern-library'); ?></span>
                </div>
            </div>

            <div class="pattern-library-container">
                <div class="pattern-library-sidebar">
                    <div class="pattern-library-search">
                        <input type="text" placeholder="<?php _e('Search', 'pattern-library'); ?>" id="pattern-search">
                        <span class="dashicons dashicons-search"></span>
                    </div>

                    <ul class="pattern-categories">
                        <?php foreach ($organized_patterns as $category_name => $category) : ?>
                            <li class="pattern-category <?php echo $category_name === 'all' ? 'active' : ''; ?>" data-category="<?php echo esc_attr($category_name); ?>">
                                <?php echo esc_html($category['label']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="pattern-library-content">
                    <?php foreach ($organized_patterns as $category_name => $category) : ?>
                        <div class="pattern-category-content <?php echo $category_name === 'all' ? 'active' : ''; ?>" data-category="<?php echo esc_attr($category_name); ?>">
                            <div class="pattern-grid">
                                <?php foreach ($category['patterns'] as $pattern) : ?>
                                    <div class="pattern-item">
                                        <div class="pattern-preview">
                                            <div class="pattern-iframe-container">
                                                <?php
                                                // Utiliser un identifiant unique pour chaque iframe
                                                $iframe_id = 'pattern-iframe-' . sanitize_title($pattern['name']);
                                                ?>
                                                <iframe     width="100%" 
    height="100%" 
    frameborder="0" id="<?php echo esc_attr($iframe_id); ?>" srcdoc="<?php echo esc_attr('<html><head><style>' . wp_get_global_stylesheet() . '</style></head><body>' . $pattern['content'] . '</body></html>'); ?>"></iframe>
                                            </div>
                                        </div>
                                        <div class="pattern-info">
                                            <h3 class="pattern-title"><?php echo esc_html($pattern['title']); ?></h3>
                                            <div class="pattern-actions">
                                                <button class="pattern-view" data-iframe="<?php echo esc_attr($iframe_id); ?>">
                                                    <span class="dashicons dashicons-visibility"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialiser le plugin
new FSE_Pattern_Library();


register_activation_hook(__FILE__, 'pattern_library_activate');