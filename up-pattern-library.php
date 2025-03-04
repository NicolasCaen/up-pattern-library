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

        // Supprimer les catégories qui n'ont pas de patterns
        foreach ($organized_patterns as $category_name => $category) {
            if (empty($category['patterns'])) {
                unset($organized_patterns[$category_name]);
            }
        }

        // Placer "All" au début du tableau
        $organized_patterns = array('all' => $all_category) + $organized_patterns;

        return $organized_patterns;
    }

    /**
     * Rendre un pattern en utilisant les fonctions natives de WordPress
     * 
     * @param array $pattern Le pattern à rendre
     * @return string Le HTML du pattern
     */
    private function render_pattern($pattern) {
        // Créer un bloc parsé à partir du contenu du pattern
        $parsed_block = parse_blocks($pattern['content']);
        
        if (empty($parsed_block)) {
            return '';
        }
        
        // Rendre chaque bloc du pattern
        $rendered_content = '';
        foreach ($parsed_block as $block) {
            $rendered_content .= render_block($block);
        }
        
        return $rendered_content;
    }

    /**
     * Rendu du shortcode [pattern_library]
     */
    public function render_pattern_library($atts) {
        // Charger les assets
        wp_enqueue_style('pattern-library-css');
        wp_enqueue_script('pattern-library-js');
        
        // S'assurer que les styles des blocs sont chargés
        wp_enqueue_style('wp-block-library');
        wp_enqueue_style('global-styles');
        
        // Récupérer les styles du thème actif
        $theme_styles = wp_get_global_stylesheet();
        
        // Ajouter les styles inline
        wp_add_inline_style('pattern-library-css', $theme_styles);

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
                                <span class="pattern-count">(<?php echo count($category['patterns']); ?>)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="pattern-library-content">
                    <?php foreach ($organized_patterns as $category_name => $category) : ?>
                        <div class="pattern-category-content <?php echo $category_name === 'all' ? 'active' : ''; ?>" data-category="<?php echo esc_attr($category_name); ?>">
                            <div class="pattern-grid">
                                <?php foreach ($category['patterns'] as $pattern) : ?>
                                    <?php
                                    // Générer un ID unique pour ce pattern
                                    $pattern_id = 'pattern-' . sanitize_title($pattern['name']);
                                    
                                    // Rendre le pattern en utilisant les fonctions natives de WordPress
                                    $rendered_pattern = $this->render_pattern($pattern);
                                    ?>
                                    <div class="pattern-item">
                                        <div class="pattern-preview">
                                            <div class="pattern-thumbnail">
                                                <div class="pattern-content-preview">
                                                    <?php echo $rendered_pattern; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pattern-info">
                                            <h3 class="pattern-title"><?php echo esc_html($pattern['title']); ?></h3>
                                            <div class="pattern-actions">
                                                <button class="pattern-view" data-pattern-id="<?php echo esc_attr($pattern_id); ?>" data-pattern-title="<?php echo esc_attr($pattern['title']); ?>" data-pattern-html="<?php echo esc_attr($rendered_pattern); ?>">
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

/**
 * Actions à effectuer lors de l'activation du plugin
 */
function pattern_library_activate() {
    // Créer les répertoires nécessaires si besoin
    $upload_dir = wp_upload_dir();
    $pattern_dir = $upload_dir['basedir'] . '/pattern-library';
    
    if (!file_exists($pattern_dir)) {
        wp_mkdir_p($pattern_dir);
    }
    
    // Vider le cache des transients
    delete_transient('pattern_library_cache');
    
    // Flush les règles de réécriture
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'pattern_library_activate');