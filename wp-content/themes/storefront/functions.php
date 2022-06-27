<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */

    // Пишем код формы с полями.  
    function my_extra_fields_content( $post )  
    {     
        // URL-ы загруженных изображений будем сохранять в мета-полях  
        $background = get_post_meta($post->ID, 'post_background', 1);  
        $type_product = get_post_meta($post->ID, 'post_type_product', 1);  
      
        ?>  
            <label for="post_background">  
            <h4>Фото</h4>  
			<?php if($background){ ?>
			<img src="<?php echo $background; ?>">
			<?php } ?>
            <input id="post_background" type="hidden" size="45" name="post_background" value="<?php echo $background; ?>" />  
            <input id="post_background_button" type="button" class="button" value="Загрузить" />  
            <a style="color:#b32d2e;position: absolute;margin: 4px 0 0 13px;" id="post_background_delete">Удалить</a>  
            <br />  
            </label>  
            <br />  
			<label for="post_type_product">Тип родукта</label> 
			<select name="post_type_product" id="post_type_product">
				<option value="rare" <?php if($type_product == 'rare'){ ?>selected="selected"<?php } ?>>rare</option>
				<option value="frequent" <?php if($type_product == 'frequent'){ ?>selected="selected"<?php } ?>>frequent</option>
				<option value="unusual" <?php if($type_product == 'unusual'){ ?>selected="selected"<?php } ?>>unusual</option>
			</select>
			<br>
			<br>
			<input type="button" id="publish_new" class="button button-primary button-large" value="">
            <!-- Создаем проверочное поле для проверки того, что данные пришли с нашей формы -->  
            <input type="hidden" name="extra_field_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />  
        <?php  
    }  
      
    // Добавляем мета-блок с нашей формой на странице редактирования записи  
    function my_add_extra_fields() {  
        add_meta_box( 'extra_fields', 'Характерные изображения', 'my_extra_fields_content', 'product', 'normal', 'high'  );  
    }  
      
    // Запускаем вышенаписанный код в действие  
    if( is_admin() )  
        add_action('admin_init', 'my_add_extra_fields', 1); 
	
function my_add_upload_scripts() {  
    wp_enqueue_script('media-upload');  
    wp_enqueue_script('thickbox');  
    wp_register_script(  
                'my-upload-script'  
                /* Подключаем JS-код задающий поведение  
                 * загрузчика и указывающий, куда вставлять  
                 * ссылку после загрузки изображения 
                 * Его код будет приведен ниже. 
                 */  
                ,get_bloginfo('template_url').'/assets/js/upload.js'  
                /* Указываем скрипты, от которых  
                 * зависит наш JS-код 
                 */  
                ,array('jquery','media-upload','thickbox')  
    );  
    wp_enqueue_script('my-upload-script');  
}  
  
// Запускаем функцию подключения загрузчика  
if( is_admin() )  
add_action('admin_print_scripts', 'my_add_upload_scripts'); 

function my_extra_fields_content_update( $post_id ){  
  
    // Если данные пришли не из нашей формы, ничего не делаем  
    if ( !wp_verify_nonce($_POST['extra_field_nonce'], __FILE__) )  
            return false;             
    // Если это автосохранение, то ничего не делаем       
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  )  
            return false;  
    // Проверяем права пользователя       
    if ( !current_user_can('edit_post', $post_id) )  
            return false;  
  
    $extra_fields = array(  
        'post_background' => $_POST['post_background'],
        'post_type_product' => $_POST['post_type_product']
    );  
  
    $extra_fields = array_map('trim', $extra_fields);  
  
    foreach( $extra_fields as $key=>$value ){  
            // Очищаем, если пришли пустые значения полей  

            if($value == '')  
                delete_post_meta($post_id, $key);  
            // Обновляем, (или создаем) в случае не пустых значений  
            if($value)  
                update_post_meta($post_id, $key, $value);  
    }  
  
    return $post_id;  
}  
  
// Запускаем обработчик формы во время сохранения записи  
if( is_admin() )  
add_action('save_post', 'my_extra_fields_content_update', 0);  
  

include_once __DIR__ . '/woocommerce/class-wc-admin-list-table-products.php';
