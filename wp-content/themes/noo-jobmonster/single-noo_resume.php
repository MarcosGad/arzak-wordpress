<?php get_header();?>
<div class="container-boxed max offset main-content">
	<div class="row">
		<div class="<?php noo_main_class(); ?>" role="main">
			<?php jm_resume_detail()?>
		</div>
	</div> <!-- /.row -->
</div> <!-- /.container-boxed.max.offset -->
<?php get_footer(); ?>
<?php
if( Noo_Member::is_employer() ) :
    $user_id = get_current_user_id();
    $package = (!empty($user_id)) ? get_user_meta($user_id, '_job_package', true) : '';
?>
    <?php if( isset( $package['can_view_resume'] ) && $package['can_view_resume'] === '1' && isset( $package['resume_view_limit'] ) ):
        $view_remain = jm_get_resume_view_remain();
        if($view_remain == -1) {
            $view_remain = sprintf((__('You have %s view left','noo')), esc_html__('Unlimited','noo'));
        }else{
            $view_remain = sprintf((__('You have %s view left','noo')), $view_remain);
        }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $.notify("<?php echo $view_remain?>",{
                position: "right bottom",
                className:"success",
            })
        })
    </script>
    <?php endif; ?>
<?php endif; ?>
