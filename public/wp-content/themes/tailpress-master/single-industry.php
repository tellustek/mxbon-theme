<?php get_header(); ?>

<?php
    // $parent_item_title = '上層標題';

    // if( $menu_items = wp_get_nav_menu_items( 'main-nav' ) ) {
   
    //     $parent_id = 0;

    //     foreach( $menu_items as $menu_item ) {

    //         if($menu_item->object_id == $post->ID) {
    //             $parent_id = $menu_item->menu_item_parent;
    //             break;
    //         }
    //     }
    //     if( $parent_id !== 0 ) {
    //         foreach( $menu_items as $menu_item ) {
    //             if($menu_item->ID == $parent_id) {
    //                 $parent_item_title = get_the_title( $menu_item->object_id );
    //             }
    //         }
    //     }
    //  }
?>

<?php
    $post_name = get_post_field('post_name');
    $sub_title = str_replace( '-', ' ', $post_name );
    $banner_bg = get_the_post_thumbnail_url($post);
    $products = get_field('related_products');

    //echo "<pre>" . print_r( $products, true ) ."</pre>";
?>

<div class="breadcrumb-wrapper">
    <div class="breadcrumb">
        <?php bcn_display(); ?>
    </div>
</div>

<div class="container">
    <div class="wrapper">
        <aside class="side-navigation" x-bind:class="{ 'dropdown-collapse': collapse }" x-data="{ collapse: false, title: '<?php echo get_the_title(); ?>' }">

            <div class="wrapper">

                <div class="title"><?php echo mxbon_get_side_navigation_title(); ?></div>

                <div class="mobile-toggle-btn" x-text="title" x-on:click="collapse = !collapse"></div>
                <?php
                    wp_nav_menu( array(
                        'theme_location'  => 'primary',
                        'sub_menu' => true
                    ) );
                ?>
            </div>
        </aside>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <header class="entry-header">
                <h1 class="page-title side">
                    <?php echo get_the_title(); ?>
                    <span class="sub">
                        <?php echo $sub_title; ?>
                    </span>
                </h1>
            </header>

            <div class="entry-content">
                <section class="industry-block">
                    <div class="wrapper">
                        <div class="content" style="<?php echo "background-image: url('{$banner_bg}')"; ?>">
                            <div class="decoration" data-aos="fade-down-right" data-aos-duration="1200" >
                                <p class="desc"><?php echo mxbon_get_paragraph(0); //strip_tags( get_the_content() ); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="description"><?php echo get_the_content(); ?></div>
                </section>
                
                <section class="related-products">
                    <?php if( $products ) : ?>
                        <h3 class="content-title subline">相關產品</h3>
                        <ul class="products">
                            <?php foreach( $products as $product ) : ?>
                                <li>
                                    <a href="<?php the_permalink( $product ); ?>">
                                        <div class="image-wrapper">
                                            <?php  
                                                $product_img = get_the_post_thumbnail_url( $product, 'medium' );
                                                $categories = get_the_terms( $product, 'product-category' );

                                                $categoery_img = get_field( 'category_image', $categories[0] );
                                                $image_path = $product_img ? $product_img : $categoery_img['url'];
                                            ?>
                                            <img src="<?php echo $image_path ?>" alt="<?php echo $product->post_title; ?>">
                                        </div>
                                        <h3><?php echo $product->post_title; ?></h3>
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    <?php endif; ?>
                </section>
                
            </div>

        </article>
    </div><!--wrapper-->
</div><!--container-->

<section class="contact-us">
    <div class="container">
        <div class="section-title side">
            聯絡我們
            <span class="sub">contact us</span>
        </div>

        <?php echo do_shortcode( '[fluentform id="1"]'); ?>
    </div>
    <span class="molecular-dark" data-paroller-factor="-0.1"></span>
    <span class="molecular-light" data-paroller-factor="-0.1"></span>
</section>

<?php
    get_footer();