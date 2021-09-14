<?php get_header(); ?>
<?php
$pagination  = noo_get_option('noo_resumes_list_pagination_style','');
$page_resume_layout = noo_get_option('noo_resumes_layout');
$mode_type   = (isset($_GET['mode'])) ? $_GET['mode'] : '';
?>
<?php if($page_resume_layout == 'fullwidth_map' || $mode_type =='map'):
    noo_get_layout('resume/resume_archive_map')?>
<?php else: ?>
    <div class="container-wrap">
        <div class="main-content container-boxed max offset">
            <div class="row">
                <div class="<?php noo_main_class(); ?>" role="main">
                    <?php
                    $can_view_resume_list = jm_can_view_resumes_list();
                    if($can_view_resume_list){
                        jm_resume_loop();
                    }else{
                        list($title, $link) = jm_cannot_view_list_resume();
                        ?>
                        <article class="resume">
                            <h3><?php echo $title; ?></h3>
                            <?php if( !empty( $link ) ) echo $link; ?>
                        </article>
                        <?php
                    }
                    ?>
                </div> <!-- /.main -->
                <?php get_sidebar(); ?>
            </div><!--/.row-->
        </div><!--/.container-boxed-->
    </div><!--/.container-wrap-->

    <?php get_footer(); ?>
<?php endif; ?>
