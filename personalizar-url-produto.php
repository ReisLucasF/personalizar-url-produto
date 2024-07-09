<?php
/*
Plugin Name: Personalizar URL
Description: Adiciona um campo personalizado para a URL externa na página de edição do produto e substitui o botão "Adicionar ao Carrinho" por um botão "Comprar agora" que redireciona para essa URL.
Version: 1.0
Author: Lucas Reis
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields' );

function woocommerce_product_custom_fields() {
    global $woocommerce, $post;

    $label_text = get_option( 'personalizar_url_produto_label_text', 'URL Externa' );

    echo '<div class="product_custom_field">';
    woocommerce_wp_text_input( 
        array( 
            'id' => '_external_product_url', 
            'placeholder' => $label_text, 
            'label' => __( $label_text, 'woocommerce' ), 
            'desc_tip' => 'true'
        )
    );
    echo '</div>';
}

add_action( 'woocommerce_process_product_meta', 'woocommerce_process_product_custom_fields_save' );

function woocommerce_process_product_custom_fields_save( $post_id ){
    $woocommerce_custom_product_text_field = $_POST['_external_product_url'];
    if( !empty( $woocommerce_custom_product_text_field ) ) {
        update_post_meta( $post_id, '_external_product_url', esc_attr( $woocommerce_custom_product_text_field ) );
    }
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

add_action( 'woocommerce_single_product_summary', 'remove_additional_buy_buttons', 25 );

function remove_additional_buy_buttons() {
    remove_action( 'woocommerce_single_product_summary', 'single_add_to_cart_button', 30 );
}

add_action( 'woocommerce_single_product_summary', 'replace_add_to_cart_button_single_product_page', 30 );

function replace_add_to_cart_button_single_product_page() {
    global $product;
    $link = get_post_meta( $product->get_id(), '_external_product_url', true );
    $button_text = get_option( 'personalizar_url_produto_button_text', 'Comprar agora' );

    if ( $link ) {
        echo '<a href="' . esc_url( $link ) . '" class="button addtocartbutton">' . esc_html( $button_text ) . '</a>';
    }
}

add_action( 'admin_menu', 'personalizar_url_produto_add_admin_menu' );
add_action( 'admin_init', 'personalizar_url_produto_settings_init' );

function personalizar_url_produto_add_admin_menu() {
    add_options_page(
        'Configurações de URL Personalizada do Produto', 
        'Personalizar URL do Produto', 
        'manage_options', 
        'personalizar_url_produto', 
        'personalizar_url_produto_options_page'
    );
}

function personalizar_url_produto_settings_init() {
    register_setting( 'personalizar_url_produto_settings', 'personalizar_url_produto_label_text' );
    register_setting( 'personalizar_url_produto_settings', 'personalizar_url_produto_button_text' );

    add_settings_section(
        'personalizar_url_produto_section', 
        __( 'Configurações de URL Personalizada do Produto', 'personalizar_url_produto' ), 
        'personalizar_url_produto_settings_section_callback', 
        'personalizar_url_produto_settings'
    );

    add_settings_field( 
        'personalizar_url_produto_label_text', 
        __( 'Texto do rótulo', 'personalizar_url_produto' ), 
        'personalizar_url_produto_label_text_render', 
        'personalizar_url_produto_settings', 
        'personalizar_url_produto_section' 
    );

    add_settings_field( 
        'personalizar_url_produto_button_text', 
        __( 'Texto do botão', 'personalizar_url_produto' ), 
        'personalizar_url_produto_button_text_render', 
        'personalizar_url_produto_settings', 
        'personalizar_url_produto_section' 
    );
}

function personalizar_url_produto_label_text_render() {
    $option = get_option( 'personalizar_url_produto_label_text' );
    ?>
    <input type='text' name='personalizar_url_produto_label_text' value='<?php echo esc_attr( $option ); ?>'>
    <?php
}

function personalizar_url_produto_button_text_render() {
    $option = get_option( 'personalizar_url_produto_button_text' );
    ?>
    <input type='text' name='personalizar_url_produto_button_text' value='<?php echo esc_attr( $option ); ?>'>
    <?php
}

function personalizar_url_produto_settings_section_callback() {
    echo __( 'Configure os textos para o rótulo do campo e o botão.', 'personalizar_url_produto' );
}

function personalizar_url_produto_options_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Configurações de URL Personalizada do Produto', 'personalizar_url_produto' ); ?></h1>
        <form action='options.php' method='post'>
            <?php
            settings_fields( 'personalizar_url_produto_settings' );
            do_settings_sections( 'personalizar_url_produto_settings' );
            submit_button();
            ?>
        </form>
        <footer style="margin-top: 20px; padding: 10px; border-top: 1px solid #ccc;">
            <p>Este é um plugin desenvolvido pela <strong>Devos - Tecnologias</strong></p>
        </footer>
    </div>
    <?php
}
?>
