<?php
/* 
Plugin Name: tinySource
Plugin URI: http://studio.tiny.lt/plugins/tinysource
Description: Simple tool to note text source
Author: tinyStudio
Version: 0.2.2
Author URI: http://studio.tiny.lt/
*/

load_plugin_textdomain('tinysource');

add_action( 'init', 'create_tinysource_post_type' );

function create_tinysource_post_type() {
  register_post_type( 'tinysource', array(
    'labels' => array(
      'name' => __( 'Sources', 'tinysource' ),
      'singular_name' => __( 'Source', 'tinysource' )
    ),
    'public' => true,
    'exclude_from_search' => true,
    'menu_position' => 10,
    'supports' => array(
      'title','editor','thumbnail'
    ),
    'rewrite' => array('slug'=>'source'),
  ));
  flush_rewrite_rules();
}

add_action("admin_init", "tinysource_admin_init");
 
function tinysource_admin_init(){
  add_meta_box("source_data-meta", __("Source link", 'tinysource'), "tinysource_data", "tinysource", "normal", "low");
  add_meta_box("source_select_meta", __("Source",'tinysource'), "tinysource_select", "post", "side", "low");
}
 
function tinysource_data(){
  global $post;
  $custom = get_post_custom($post->ID);
//  $text = $custom["text"][0];
  $link = $custom["link"][0];
  $order = $custom["tinysource_order"][0];
//  $producers = $custom["producers"][0];
  ?>
  <p><label><?php _e('Link','tinysource'); ?>:</label><br />
  <input type="text" name="link" value="<?php echo $link; ?>"/></p>
  <p><label><?php _e('Priority','tinysource'); ?>:</label><br />
  <input type="text" name="order" value="<?php echo $order; ?>"/></p>
  <?php
}

function tinysource_select(){
  global $post;
  $custom = get_post_custom($post->ID);
  $list = tinysource_get_sources_list();
  $source = $custom['_tinysource_source'][0];
?>
  <p><label><?php _e('Choose text source','tinysource'); ?>:</label><br />
  <select name="_tinysource_source">
<?php
  foreach ($list as $l) :
?>
    <option value="<?php echo $l['id']; ?>" <?php if ($source==$l['id']):echo 'selected="selected"'; endif;?>><?php echo $l['title']; ?></option>
<?php endforeach; ?>
  </select></p>
<?php 
}

function tinysource_get_sources_list() {
  $recentPosts = new WP_Query(array('post_type'=>'tinysource','orderby'=>'meta_value_num','meta_key'=>'tinysource_order','order'=>'DESC'));
  foreach ($recentPosts->posts as $p) 
    $ret[] = array('id'=>$p->ID,'title'=>$p->post_title);
  return $ret;
}

add_action('save_post', 'tinysource_save_details');
add_action('save_post', 'tinysource_save_post');

function tinysource_save_details(){
  global $post;
 
//  update_post_meta($post->ID, "text", $_POST["text"]);
  update_post_meta($post->ID, "link", $_POST["link"]);
  update_post_meta($post->ID, "tinysource_order", $_POST["order"]);
}

function tinysource_save_post(){
  global $post;
  if ($_POST['_tinysource_source']) 
    update_post_meta($post->ID, "_tinysource_source", $_POST["_tinysource_source"]);
}

function tinysource_show($arg=null){
  global $post;
  parse_str($arg,$arg);
  $default = array(
    'echo'=>false,
    'post'=>$post->ID,
    'img_size'=>'thumbnail',
    'template'=>get_option('tinysource_template')//'<div class="tinysource_source"><a href="%link%" title="%title_attr%"><img src="%img%" alt="%title_attr%"/></a>%text%</div>'
  );
  $argument = array_merge($default,$arg);
  $argument['img_size'] = explode(',',$argument['img_size']); 
  $custom = get_post_custom($argument['post']);
  $source = $custom['_tinysource_source'][0];
  $source = tinysource_get($source);
  $tags = array ('%link%','%title_attr%', '%title%','%img%','%img_h%','%img_w%', '%text%');
  if (function_exists('get_post_thumbnail_id'))
    $img = wp_get_attachment_image_src(get_post_thumbnail_id($custom['_tinysource_source'][0]),$argument['img_size']);
  else $img = array('','','');
  $cont = array (
    $source->custom['link'][0],
    esc_attr($source->post_title),
    $source->post_title,
    $img[0],
    $img[1],
    $img[2],
    wpautop($source->post_content)
  );
  echo str_replace($tags,$cont,$argument['template']);
}

function tinysource_get($id) {
  $recentPosts = new WP_Query(array('p'=>$id,'post_type'=>'tinysource','orderby'=>'meta_value_num','meta_key'=>'tinysource_order','order'=>'DESC'));
  $ret = $recentPosts->posts[0];
  $ret->custom = get_post_custom($id);
  return $ret;
}

/*
add_action("manage_posts_custom_column",  "tinyads_custom_columns");
add_filter("manage_edit-tinyads_columns", "tinyads_edit_columns");
 
function tinyads_edit_columns($columns){
  $columns = array(
    "cb" => "<input type=\"checkbox\" />",
    "title" => "Pavadinimas",
    "text" => "Tekstas",
    "link" => "Nuoroda",
  );
 
  return $columns;
}
function tinyads_custom_columns($column){
  global $post;
 
  switch ($column) {
    case "text":
      $custom = get_post_custom();
      echo $custom["text"][0];
      break;
    case "link":
      $custom = get_post_custom();
      echo $custom["link"][0];
      break;
  }
}*/

// create custom plugin settings menu
add_action('admin_menu', 'tinysource_create_menu');
function tinysource_create_menu() {
	//create new top-level menu
	add_submenu_page('edit.php?post_type=tinysource',__('tinySource settings','tinysource'), __('Settings','tinysource'), 'manage_options', 'tinysource_settings','tinysource_settings_page');
	//call register settings function
	add_action( 'admin_init', 'tinysource_register_settings' );
}


function tinysource_register_settings() {
	//register our settings
	register_setting( 'tinysource-settings-group', 'tinysource_template' );
}

function tinysource_default_settings() {
  $arr = '<div class="tinysource_source">
  <a href="%link%" title="%title_attr%"><img src="%img%" alt="%title_attr%"/></a>
  %text%
</div>';
  update_option('tinysource_template', $arr);
}

function tinysource_settings_page() {
?>
<div class="wrap">
<h2><?php _e('tinySource settings','tinysource');?></h2>

<form method="post" action="options.php">
    <?php settings_fields( 'tinysource-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e('Output template','tinysource'); ?></th>
        <td><textarea name="tinysource_template"><?php echo get_option('tinysource_template'); ?></textarea></td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php } 

if ( function_exists('register_uninstall_hook') )
    register_uninstall_hook(__FILE__, 'tinysource_uninstall_hook');

function tinysource_uninstall_hook()
{
   unregister_setting( 'tinysource-settings-group', 'tinysource_template');
}

register_activation_hook( __FILE__, 'tinysource_activate' );

function tinysource_activate() {
//  tinysource_register_settings();
	$tmp = get_option('tinysource_template');
  if($tmp!=null) {
    tinysource_default_settings();
  }
}
