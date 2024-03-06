<?php
    function hierarchical_category_tree( $cat ) {
        if ($cat != 0) {
            $class = 'has-children';
        } else {
            $class = 'has-parent';
        }
        
        $next = get_categories('hide_empty=false&orderby=name&order=ASC&parent=' . $cat);
        
        if( $next ) :    
            foreach( $next as $cat ) :
                echo '<ul class="'.$class.'"><li data-term-slug="'.$cat->slug.'" data-term-name="'.$cat->name.'" data-term-id="'.$cat->term_id.'">' . $cat->name . '';
                hierarchical_category_tree( $cat->term_id );
            endforeach;    
        endif;
    
        echo '</li></ul>'; echo "\n";
    }
?>

<div class="b-page__line">
    <div class="b-title">
        <h1><?php the_title(); ?></h1>
    </div>
</div>
<div class="b-page__line">
    <div class="b-filter">
        <div class="b-filter__content">
            <div class="b-line">
                <div class="b-date">
                    <label for="date">Дата публикации</label>
                    <input type="date" id="date" name="date" value="" />
                    <!-- value="05-03-2024" min="05-03-2024" max="2018-12-31" -->
                </div>
                <div class="b-category">
                    <label for="category">Категория</label>
                    <input type="text" id="category" name="category" value="" />
                    <div class="b-categorylist hidden">
                        <?php
                            hierarchical_category_tree(0);
                        ?>
                    </div>
                </div>
            </div>
            <div class="b-line">
                <div class="b-submit">
                    <input type="submit" value="Применить фильтры" />
                </div>
            </div>
        </div>
    </div>
</div>
<div class="b-page__line b-page__grid">
    <?php
        $paged = ( get_query_var( 'page' ) ) ? absint( get_query_var( 'page' ) ) : 1;
        $posts = new WP_Query(
           array(
                'post_type' => 'post',
                'posts_per_page' => 3,
                'orderby' => 'date',
                'order' => 'DESC',
                'paged' => $paged) 
        );
        foreach( $posts->posts as $post ) :
            setup_postdata( $post );
    ?>
            <div class="b-article" data-post-id="<?=$post->ID; ?>">
                <div class="b-article__content">
                    <div class="b-content">
                        <?php
                            if ( has_post_thumbnail( $post->ID ) ) {
		                        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
                        ?>
                                <img class="b-content__img" src="<?=$image[0]; ?>"/>
                        <?php
                    		}
                        ?>
                        <div class="b-content__date"><?=get_the_date( 'd.m.Y, h:i', $post ); ?></div>
                        <?php
                            $category_detail=get_the_category( $post->ID );
		                    $category_str = '';
                            foreach($category_detail as $cd){
                                if (!empty($category_str)) $category_str .= ', ';
                                $category_str .= $cd->cat_name;
                            }
                        ?>
                        <div class="b-content__category"><?=$category_str; ?></div>
                        <div class="b-content__title"><?=$post->post_title; ?></div>
                        <div class="b-content__desc">
                            <?=apply_filters( 'the_content', $post->post_content, $post ); ?>
                        </div>
                        <div class="b-content__author">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/author.svg" />
                            <span><?=get_post_meta( $post->ID, 'author', true ); ?></span>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        endforeach;
        wp_reset_postdata();
    ?>
</div>
<div class="b-page__line">
    <div class="b-readmore">
        <div class="b-readmore__button">
            <button>Загрузить ещё</button>
        </div>
    </div>
</div>
<div class="b-page__line">
    <?php if ( $posts->max_num_pages > 1 ) : ?>
        <div class="b-pagination">
            <div class="b-pagination__list">
            <?php	
                echo wp_kses_post(
                    paginate_links(
                      [
                        'total'   => $posts->max_num_pages,
                        'current' => $paged,
                        'mid_size' => 2,
		                'end_size' => 1,
		                'prev_next' => false,
                      ]
                    )
                );
			?>
		    </div>
        </div>
    <?php endif; ?>
</div>
