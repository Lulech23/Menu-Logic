<?php
/*
Plugin Name:    Menu Logic
Description:    Adds a conditional Menu Logic menu item attribute to show or hide menu items on the frontend
Author:         Hassan Derakhshandeh, Paul Kirspuu, Lucas Chasteen
Version:        0.5.1
Text Domain:    menu-logic
*/

class Menu_Logic {
	protected static $instance = null;
    private $plugin_path = "";
    private $plugin_url = "";
    private $slug = "";

    private function __clone() {}

    /**
     * Primary constructor
     */
    public function __construct() {

        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->plugin_url = plugins_url("/", __FILE__);
        $this->slug = basename($this->plugin_path);

		if (!is_admin()) {
			add_filter('wp_get_nav_menu_items', [__CLASS__, 'wp_get_nav_menu_items_visibility'], 10, 3);
			return;
		}
		
		add_action('wp_nav_menu_item_custom_fields', [__CLASS__, 'wp_nav_menu_item_custom_fields']);
		add_action('wp_update_nav_menu_item', [__CLASS__, 'wp_update_nav_menu_item'], 10, 3);
		add_action('delete_post', [__CLASS__, 'delete_post'], 1, 3);
	}

	/**
     * Display condition field in menu admin
	 *
     * @param int $item_id
     * @return void
	 * @since 0.3.8
	 */
    public static function wp_nav_menu_item_custom_fields(int $item_id): void {
		$value = get_post_meta($item_id, '_menu_item_visibility', true);
		?>
		<p class="field-visibility description description-wide">
			<label for="edit-menu-item-visibility-<?php echo $item_id; ?>">
				<?php printf(__('Menu Logic (<a href="%s">?</a>)', 'menu-logic'), 'https://codex.wordpress.org/Conditional_Tags'); ?><br>
				<input type="text" class="widefat code" id="edit-menu-item-visibility-<?php echo $item_id; ?>" name="menu-item-visibility[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($value); ?>" />
			</label>
		</p>
		<?php
	}

    /**
     * Update menu item visibility post meta
     *
     * @param int $menu_id
     * @param int $menu_item_db_id
     * @param array $args
     * @return void
     * @since 0.3.8
     */
    public static function wp_update_nav_menu_item(int $menu_id, int $menu_item_db_id, array $args): void {
		if (isset($_POST['menu-item-visibility'][$menu_item_db_id])) {
			$meta_value = get_post_meta($menu_item_db_id, '_menu_item_visibility', true);
			$new_meta_value = stripcslashes($_POST['menu-item-visibility'][$menu_item_db_id]);

			if ('' == $new_meta_value) {
				delete_post_meta($menu_item_db_id, '_menu_item_visibility', $meta_value);
			} elseif ($meta_value !== $new_meta_value) {
				$visible = null;
				
				// Test new menu logic
				set_error_handler('self::error_handler');
				try {
					eval('$visible = ' . $new_meta_value . ';');
				} catch (Error $e) {
					$visible = null;
					trigger_error($e->getMessage(), E_USER_WARNING);
				}
				restore_error_handler();

				// Update menu item, if passed
				if ($visible !== null) {
					update_post_meta($menu_item_db_id, '_menu_item_visibility', $new_meta_value);
				}
			}
		}
	}

	/**
	 * Checks the menu items for their visibility options and
	 * removes menu items that are not visible
	 *
     * @param array $items
     * @param object $menu
     * @param array $args
	 * @return array
	 * @since 0.1
	 */
    public static function wp_get_nav_menu_items_visibility(array $items, object $menu, array $args): array {
		$hidden_items = array();
		foreach ($items as $key => $item) {
			$item_parent = get_post_meta($item->ID, '_menu_item_menu_item_parent', true);
            $visible     = true;
            $logic       = get_post_meta($item->ID, '_menu_item_visibility', true);

			if ($logic) {
				set_error_handler('self::error_handler');
				try {
					eval('$visible = ' . $logic . ';');
				} catch (Error $e) {
					trigger_error($e->getMessage(), E_USER_WARNING);
				}
				restore_error_handler();
			}

			if (!$visible || isset($hidden_items[$item_parent])) { // also hide the children of invisible items
				unset($items[$key]);
				$hidden_items[$item->ID] = '1';
			}
		}

		return $items;
	}

    /**
     * Remove the _menu_item_visibility meta when the menu item is removed
     *
     * @param int $post_id
     * @return void
     * @since 0.2.2
     */
    public static function delete_post(int $post_id): void {
		if (is_nav_menu_item($post_id)) {
			delete_post_meta($post_id, '_menu_item_visibility');
		}
	}

    /**
     * Handle errors in eval'd Logic field
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errFile
     * @param int $errLine
     * @param array $errContext
     * @return bool
     * @since 0.4
     */
    public static function error_handler(int $errno, string $errstr, string $errFile, int $errLine, array $errContext): bool {
		if (current_user_can('manage_options')) {
            if (!empty($errContext['item']->title)) {
                echo sprintf(__('Error in "%s" Menu Logic: ', 'menu-logic'), $errContext['item']->title);
            }

			echo $errstr;
		}

		/* Don't execute PHP internal error handler */
		return true;
	}

    /**
     * Get current instance of class
     *
     * @return object
     * @since 0.4
     */
    public static function get_instance() {
		if (self::$instance === null) {
			self::$instance = new self;
		}

        return self::$instance;
    }
}

add_action('plugins_loaded', [new Menu_Logic, 'get_instance']);