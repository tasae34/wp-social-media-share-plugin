<?php
add_action( 'init', 'custom_buttons' );
function custom_buttons() {
    add_filter( "mce_external_plugins", "add_custom_buttons" );
    add_filter( 'mce_buttons', 'register_custom_buttons' );
}
function add_custom_buttons( $plugin_array ) {
    $plugin_array['btns'] = plugins_url('../btn.js',__FILE__);
    return $plugin_array;
}
function register_custom_buttons( $buttons ) {
    array_push( $buttons, 'sm_share');
    return $buttons;
}

?>