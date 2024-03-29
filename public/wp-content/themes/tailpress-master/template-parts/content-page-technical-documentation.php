<?php
    global $current_language;

    $categories = get_terms( array(
        'taxonomy' => 'product-category',
        'hide_empty' => true,
        'order' => 'ASC',
        'meta_key' => 'homepage_order',
        'orderby' => 'meta_value_num',
    ) );

    function mxbon_product_category_name($v) {
        if( $v === '10993' ) {
            return '10993-';
        }
        return $v." ";
    }
    function mxbon_product_post_name($v) {
        if( $v === 'ca' ) {
            return 'CA';
        }
        if( $v === 'odor' ) {
            return 'Odor /';
        }
        return ucfirst($v);
    }
?>

<p class="doc-page-desc"><?php echo wp_strip_all_tags( get_the_content() ); ?></p>

<section class="documentation" x-data="{ active: 0 }">

    <div class="container">
        <?php if( $categories ) : ?>
        
        <?php foreach( $categories as $key => $category ) : ?>

            <div class="product-category" x-bind:class="active == <?php echo $key; ?> ? 'active' : ''">

                <?php
                    $term_slug_data = array_map( 'mxbon_product_category_name', explode( "-", $category->slug ) );
                    $term_slug = implode( "", $term_slug_data );
                ?>

                <h2 class="content-title" id="<?php echo $category->slug; ?>" 
                    x-bind:class="active== <?php echo $key;?> ? 'subline' : ''"
                    x-on:click="active = active !== <?php echo $key; ?> ? <?php echo $key; ?> : null"
                    >
                    <?php echo $category->name ?>

                    <?php if( $current_language !== "en" ) : ?>
                        <span><?php echo $term_slug; ?></span>
                    <?php endif; ?>
                </h2>
                <?php 
                    $args = array(
                        'post_type' => 'product',
                        'numberposts' => -1,
                        'order' => 'asc',
                        'order_by' => 'publish_date',
                        'tax_query' => array(
                            array(
                            'taxonomy' => 'product-category',
                            'field' => 'slug',
                            'terms' => $category->slug,
                            )
                        ),
                    );
                    $products = get_posts($args);
                ?>
                
                <?php if( $products ) : ?>
                    <ul class="products" 
                        x-ref="container<?php echo $key; ?>"
                        x-bind:style="active == <?php echo $key; ?> ? 'max-height: ' + $refs.container<?php echo $key; ?>.scrollHeight + 'px' : ''"
                        >
                        <?php foreach( $products as $product ) : ?>

                            <?php
                                $subtitle_data = array_map( 'mxbon_product_post_name', explode( "-", $product->post_name ) );
                                $subtitle = implode( " ", $subtitle_data );
                            ?>

                            <?php
                                // Find if this product have at least one model document file
                                $detail = get_field( 'detail', $product );
                                $feature_key = null;
                                $document_count = 0;

                                if( $detail ) {

                                    if( $acf_fc_layout  = array_column( $detail, 'acf_fc_layout' ) ) {

                                        $feature_key = array_search( 'feature', $acf_fc_layout );

                                        if( $feature_key !== null && $detail[$feature_key]['series'] ) { //

                                            $document_count = count( array_filter( array_column( $detail[$feature_key]['series'], 'document' ) ) );
                                        }
                                    }
                                }
                            ?>

                            <?php if( $document_count ) : ?>


                            <li>
                                <h3 id="<?php echo $product->post_name; ?>">
                                    <?php echo $product->post_title; ?>
                                    
                                    <?php if( $current_language !== "en" ) : ?>
                                        <span><?php echo $subtitle; ?></span>
                                    <?php endif; ?>
                                </h3>
                                <ul class="docs">
                                    <?php foreach( $detail[$feature_key]['series'] as $item ) : ?>
                                        <?php if( $item['document'] ) : ?>
                                            <li>
                                                <a href="<?php echo $item['document']; ?>">TDS <?php echo $item['model']; ?></a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </div>

        <?php endforeach; ?>
        
        <?php endif; ?>
    </div>

</section>