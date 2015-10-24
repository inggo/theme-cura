<?php namespace Inggo\WordPress\ThemeCura;

use Inggo\WordPress\ThemeHelper;
use Inggo\WordPress\CustomizerInterface;
use Inggo\WordPress\ThemeCura\CustomPostRegistrar;

class ThemeCura
{
    private $theme_data;
    public $helper;
    public $customizer;
    public $cpt_registrar;

    public function __construct(ThemeHelper $helper, CustomizerInterface $customizer)
    {
        $this->theme_data = \wp_get_theme();
        $this->helper = $helper;
        $this->customizer = $customizer;
        
        \add_action('after_setup_theme', array($this, 'setup'));
        \add_action('init', array($this, 'registerStyles'));
        \add_action('init', array($this, 'registerScripts'));
        \add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
        \add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        \add_action('admin_notices', array($this, 'checkDependencies'));
        \add_action('after_setup_theme', array($this, 'disableAdminBar'));
        \add_action('customize_register', array($this->customizer, 'register'));
        \add_action('init', array($this, 'registerPostTypes'));
    }

    /**
     * Set-up the theme features
     */
    public function setup()
    {
        \add_theme_support('custom-header', array(
            'flex-width'    => true,
            'width'         => 120,
            'flex-height'   => true,
            'height'        => 120,
            'uploads'       => true,
            'default-image' => \get_template_directory_uri() . '/images/logo.png',
        ));
    }

    /**
     * Register Custom Post Types
     */
    public function registerPostTypes()
    {
        $this->cpt_registrar = new CustomPostRegistrar();
        $this->cpt_registrar->register();
    }

    /**
     * Show a plugin error
     * @param  string
     * @return string
     */
    private function __pluginError($plugin)
    {
        return '<div class="error"><p>' .
            \__('Warning: The theme needs <strong>' . $plugin . '</strong> to function', 'theme-cura' ) .
            '</p></div>';
    }

    /**
     * Check for theme plugin dependencies
     */
    public function checkDependencies()
    {
        if (!function_exists('get_field')) {
            echo $this->__pluginError('Advanced Custom Fields');
        }
    }

    /**
     * Disable the admin bar
     */
    public function disableAdminBar()
    {
        \show_admin_bar(false);
    }

    /**
     * Register theme stylesheet
     */
    public function registerStyles()
    {
        \wp_register_style('theme-cura', \get_stylesheet_uri(), array(), $this->theme_data->Version);
    }

    /**
     * Register theme scripts
     */
    public function registerScripts()
    {
        \wp_register_script('modernizr', \get_template_directory_uri() . '/js/modernizr.min.js', array(), '3.1.0');
        \wp_register_script('jquery', \get_template_directory_uri() . '/js/jquery.min.js', array(), '1.11.3', true);
        \wp_register_script('bootstrap', \get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '3.3.5', true);
        \wp_register_script('theme-cura', \get_template_directory_uri() . '/js/theme-cura.js', array('modernizr', 'bootstrap'), $this->theme_data->Version, true);
    }

    /**
     * Enqueue theme stylesheet
     */
    public function enqueueStyles()
    {
        \wp_enqueue_style('theme-cura');
    }

    /**
     * Enqueue theme scripts
     */
    public function enqueueScripts()
    {
        \wp_enqueue_script('modernizr');
        \wp_enqueue_script('bootstrap');
        \wp_enqueue_script('theme-cura');
    }
}
