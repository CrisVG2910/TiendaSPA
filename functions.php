<?php 

function carolinaspa_admin_estilos() {
    wp_enqueue_style('admin-estilos', get_stylesheet_directory_uri() . '/login/login.css');
}
add_action('login_enqueue_scripts', 'carolinaspa_admin_estilos');

function carolinaspa_redireccionar_admin() {
    return home_url();
}
add_filter('login_headurl', 'carolinaspa_redireccionar_admin');

add_filter('loop_shop_per_page', 'carolinaspa_productos_por_pagina', 20 );

function carolinaspa_productos_por_pagina( $productos ) {
    $productos = 15;
    return $productos;
}

// Columnas por pagina en la tienda
add_filter('loop_shop_columns', 'carolinaspa_columnas', 20);
function carolinaspa_columnas($columnas) {
    return 4;
}

// Cambiar a pesos Chilenos (CLP)
add_filter('woocommerce_currency_symbol', 'carolinaspa_clp', 10, 2);

function carolinaspa_clp($simbolo, $moneda) {
    $simbolo = 'CLP $';
    return $simbolo;
}

//Modificar footer
function carolinaspa_creditos() {
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    add_action( 'storefront_after_footer', 'carolinaspa_nuevo_footer', 20);
}
add_action( 'init', 'carolinaspa_creditos' );

function carolinaspa_nuevo_footer() {
    echo "<div class='reservados'>";
    echo 'Derechos Reservados &copy; ' . get_bloginfo('name') . " " . get_the_date('Y');
    echo "</div>";
}

// Agregar imagen al homepage

function carolinaspa_descuento() {
    $imagen = '<div class="destacada">';
    $imagen .= '<img src="' . get_stylesheet_directory_uri() . '/img/cupon.jpg">';
    $imagen .= '</div>';
    echo $imagen;
}
add_action('homepage', 'carolinaspa_descuento', 5);

//Crear nueva sección en el Home
// Crear nueva sección en el Home
add_action('homepage', 'carolinaspa_spacasa_homepage', 30);
function carolinaspa_spacasa_homepage() {
    echo "<div class='spa-en-casa'>";
    
    // Obtener la categoría de producto por el slug y obtener su ID
    $categoria = get_term_by('slug', 'spa-en-casa', 'product_cat');
    if ($categoria) {
        $imagen_id = get_term_meta($categoria->term_id, 'thumbnail_id', true); // Obtener ID de la imagen destacada de la categoría
        $imagen_categoria = wp_get_attachment_image_src($imagen_id, 'full');
        
        echo "<div class='imagen-categoria'>";
        if ($imagen_categoria) {
            echo "<div class='imagen-destacada' style='background-image:url(" . esc_url($imagen_categoria[0]) . ")'></div>";
        }
        
        echo "<h1>" . esc_html($categoria->name) . "</h1>"; // Mostrar el nombre de la categoría
        echo "</div>"; // Cierre de div imagen-categoria
    }
    
    // Contenedor de los productos
    echo "<div class='productos'>";
    echo do_shortcode('[product_category columns="3" category="spa-en-casa"]');
    echo "</div>";
    
    echo "</div>"; // Cierre del contenedor principal spa-en-casa
}



// Mostrar 4 Categorias en el homepage

function carolinaspa_categorias($args) {
    $args['limit'] = 4;
    $args['columns'] = 4;
    return $args;
}
add_filter('storefront_product_categories_args', 'carolinaspa_categorias', 100);

// Cambiar texto a filtro

add_filter('woocommerce_catalog_orderby', 'carolinaspa_cambiar_sort', 40);
function carolinaspa_cambiar_sort($filtro) {
    $filtro['date'] = __('Nuevos productos primero', 'woocommerce');
    return $filtro;
}

// Remover Tabs 

//add_filter('woocommerce_product_tabs', 'carolinaspa_remover_tabs', 11, 1);
//function carolinaspa_remover_tabs($tabs) {
    //unset($tabs['description']);
    //return $tabs;
//}

// Mostrar descuento de cantidad

//add_filter('woocommerce_get_price_html', 'carolinspa_cantidad_ahorrada', 10, 2 );

//function carolinspa_cantidad_ahorrada($precio, $producto) {
    //if($producto->sale_price) {
        //$ahorro = wc_price($producto->regular_price - $producto->sale_price);
        //return $precio . sprintf(__('<span class="ahorro"> Ahorro %s </span>', 'woocommerce'), $ahorro);
    //}
    //return $precio;
//}

// Mostrar en canidad y porcentaje de ahorro
function carolinaspa_mostrar_ahorro($precio, $producto) {
    $precio_regular = $producto->get_regular_price();

    if($producto->sale_price) {
        if($precio_regular < 100000) {
            $porcentaje = round ((($producto->regular_price - $producto->sale_price) / $producto->regular_price ) * 100);
            return $precio . sprintf( __('<br> <span class="ahorro"> Ahorro %s &#37;</span>', 'woocommerce'), $porcentaje);
        } else {
            $ahorro = wc_price($producto->regular_price - $producto->sale_price);
            return $precio . sprintf(__('<br> <span class="ahorro"> Ahorro %s </span>', 'woocommerce'), $ahorro);
        }
    }
    return $precio;
}
add_filter('woocommerce_get_price_html', 'carolinaspa_mostrar_ahorro', 10, 2);

// Mostrar imagen cuando no haya imagen destacada

function carolinaspa_no_imagen_destacada($imagen_url) {
    $imagen_url = get_stylesheet_directory_uri() . '/img/no-image.png';
    return $imagen_url;
}
add_filter('woocommerce_placeholder_img_src', 'carolinaspa_no_imagen_destacada');

// Cambiar tab Descripción por el titulo del producto

add_filter('woocommerce_product_tabs', 'carolinaspa_titulo_tab_descripcion', 10, 1);

function carolinaspa_titulo_tab_descripcion($tabs) {
    global $post;
    if(isset($tabs['description']['title'])) {
        $tabs['description']['title'] = $post->post_title;
    }
    return $tabs;
}

add_filter( 'woocommerce_product_description_heading', 'carolinaspa_titulo_contenido_tab', 10,  1);

function carolinaspa_titulo_contenido_tab($titulo) {
    global $post;
    return $post->post_title;
}

// Imprimir Subtitulo con Advanced Custom Fields 

add_action('woocommerce_single_product_summary', 'carolinaspa_imprimir_subtitulo', 6); 
function carolinaspa_imprimir_subtitulo() {
    global $post;
    echo "<p class='subtitulo'>" . get_field('subtitulo', $post->ID) . "</p>";
}

// Nuevo Tab para Video con ACE
add_filter('woocommerce_product_tabs', 'carolinaspa_agregar_tab_video', 11, 1);
function carolinaspa_agregar_tab_video($tabs) {
    $tabs['video'] = array(
        'title' => 'Video',
        'priority' => 15,
        'callback' => 'carolinaspa_video'
    );
    return $tabs;
}
function carolinaspa_video() {
    global $post;
    $video = get_field('video', $post->ID);
    if($video) {
        echo "<video controls autoplay loop>";
        echo "<source src='" . $video . "'>";
        echo "</video>";
    } else {
        echo "No hay video disponible";
    }
}

// Boton para Vaciar el carrito

add_action('woocommerce_cart_actions', 'carolinaspa_limpiar_carrito');
function carolinaspa_limpiar_carrito() {
    echo '<a class="button" href="?vaciar-carrito=true">' . __('Vaciar Carrito', 'woocommerce') . '</a>';
}

// Vaciar el carrito 

add_action('init', 'carolinaspa_vaciar_carrito');
function carolinaspa_vaciar_carrito() {
    if(isset($_GET['vaciar-carrito'])) {
        global $woocommerce;
        $woocommerce->cart->empty_cart();
    }
}

// Imprimir banner de ACF en el Carrito

add_action('woocommerce_check_cart_items', 'carolina_imprimir_banner_carrito', 10);
function carolina_imprimir_banner_carrito() {
    global $post;
    $imagen = get_field('imagen', $post->ID);
    if($imagen) {
        echo "<div class='cupon-carrito'>";
        echo "<img src='" . $imagen . "'>";
        echo "</div>";
    }
}

// Eliminar un campo del checkout 
add_filter('woocommerce_checkout_fields', 'carolinaspa_remover_telefono_checkout', 10);
function carolinaspa_remover_telefono_checkout($campos) {
    unset($campos['billing']['billing_phone']);
    return $campos;
}

// Agregar campos en Checkout
add_filter('woocommerce_checkout_fields', 'carolinaspa_rfc', 40);

function carolinaspa_rfc($fields) {
    // Campo RFC en la sección de facturación
    $fields['billing']['billing_rfc'] = array(
        'type' => 'text',
        'class' => array('form-row-wide'),
        'label' => __('RFC'),
        'placeholder' => __('Ingrese su RFC'),
        'required' => true,
    );

    // Campo "¿Cómo te enteraste de nosotros?" en la sección de pedido
    $fields['order']['order_escuchaste_nosotros'] = array(
        'type' => 'select',
        'class' => array('form-row-wide'),
        'label' => __('¿Cómo te enteraste de nosotros?'),
        'options' => array(
            'default' => __('Seleccione...'),
            'tv' => __('TV'),
            'radio' => __('Radio'),
            'periodico' => __('Periódico'),
            'internet' => __('Internet'),
            'facebook' => __('Facebook')
        )
    );

    return $fields;
}


/* Mostrar iconos en la tienda*/
function carolinaspa_mostrar_iconos() { ?>
            </main>
        </div>
    </div>
    <div class="iconos-inicio">
        <div class="col-full">
            <div class="columns-4">
                <?php the_field('icono_1'); ?>
                <p><?php the_field('descripcion_icono_1'); ?></p>
            </div>
            <div class="columns-4">
                <?php the_field('icono_2'); ?>
                <p><?php the_field('descripcion_icono_2'); ?></p>
            </div>
            <div class="columns-4">
                <?php the_field('icono_3'); ?>
                <p><?php the_field('descripcion_icono_3'); ?></p>
            </div>
        </div>
    </div>

    <div class="col-full">
        <div class="content-area">
            <main class="site-main">

<?php

}
add_action( 'homepage', 'carolinaspa_mostrar_iconos', 15);

/* Imprimir entradas de blog en el Inicio */
function carolinaspa_entrada_blog() {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'orderby' => 'date',
        'order' => 'DESC'
    );
    $entradas = new WP_Query($args); ?>
    <div class="entradas-blog">
        <h2 class="section-title">Últimas entradas del blog</h2>
        <ul>
            <?php while($entradas->have_posts()): $entradas->the_post(); ?>
                <li>
                    <?php the_post_thumbnail('shop_catalog'); ?>
                    <?php the_title('<h3>', '</h3>'); ?>
                    <div class="contenido-entrada">
                        <header class="encabezado-entrada">
                            <p>Por: <?php the_author(); ?> | <?php the_time(get_option('date_format')); ?></p>
                        </header>
                        <?php 
                            $contenido = wp_trim_words(get_the_content(), 20, '');
                            echo $contenido;

                        ?>
                        <footer class="footer-entrada">
                            <a href="<?php the_permalink(); ?>" class="button enlace-entrada">Ver más &raquo</a>
                        </footer>
                    </div>
                </li>
            <?php endwhile; wp_reset_postdata(); ?>
        </ul>
    </div>
    

<?php
}
add_action('homepage', 'carolinaspa_entrada_blog', 80);

/* Productos Relacionados **/
function carolinaspa_productos_relacionados(){
    global $post;
    $productos_relacionados = get_field('productos_relacionados', $post->ID);

    if($productos_relacionados && is_array($productos_relacionados)): ?>
        <div class="productos_relacionadoss">
            <h2 class="section-title">Productos Relacionados</h2>
            <?php $ids = join(', ', $productos_relacionados); ?>
            <?php echo do_shortcode('[products ids="'.$ids.'"]') ?> 
        </div>
    <?php endif;
}
add_action('storefront_post_content_after', 'carolinaspa_productos_relacionados');

// Shortcode Slider
function carolinaspa_slider() {
    echo do_shortcode('[wcslider]');
}
add_action('homepage', 'carolinaspa_slider', 4);