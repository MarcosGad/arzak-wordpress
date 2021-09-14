<?php get_header(); ?>
<?php

$pagination   = noo_get_option( 'noo_jobs_list_pagination_style', '' );
$featured_num = noo_get_option( 'noo_jobs_featured_num', 4 );
$display_type = noo_job_list_display_type();
$mode_type    = (isset($_GET['mode'])) ? $_GET['mode'] : '';
$page_layout  = get_page_layout();

?>
<?php if($page_layout =='map_full_width' || $mode_type=='map'):
    noo_get_layout('job/job_archive_map');
    ?>
<?php else: ?>
    <div class="container-wrap">
        <div class="main-content container-boxed max offset">
            <div class="row">
                <div class="<?php noo_main_class(); ?>" role="main">
                    <?php
                    if ( noo_get_option( 'noo_jobs_featured', false ) && is_post_type_archive( 'noo_job' ) && ! is_search() ) {
                        echo do_shortcode( '[noo_jobs show=featured posts_per_page=' . $featured_num . ' title="' . __( 'Featured Jobs', 'noo' ) . '" no_content="none" show_pagination="yes" choice_paginate="nextajax"]' );
                    }
                    ?>
                    <?php //do_action( 'noo_before_job_loop' ); ?>
                    <?php
                    jm_job_loop( array(
                        'paginate'      => $pagination,
                        'title'         => '',
                        'display_style' => $display_type,
                    ) );
                    ?>

                    <?php //do_action( 'noo_after_job_loop' ); ?>

                </div> <!-- /.main -->
                <?php get_sidebar(); ?>
            </div><!--/.row-->
        </div><!--/.container-boxed-->
    </div><!--/.container-wrap-->

    <?php get_footer(); ?>
<?php endif; ?>
