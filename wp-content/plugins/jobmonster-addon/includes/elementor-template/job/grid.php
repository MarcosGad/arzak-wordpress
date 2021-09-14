<?php if ( $wp_query->have_posts() ): ?>

	<div class="posts-loop-content  noo-job-grid2">
		<?php ?>
		<?php do_action( 'job_list_before', $loop_args, $wp_query ); ?>

		<div class="row is-flex <?php echo esc_attr( $mobile_class . ' ' .$tablet_class.' '.$desktop_class) ;?>">
			<?php while ( $wp_query->have_posts() ) : $wp_query->the_post();

				global $post;

				$company_id = jm_get_job_company( $post );

				$company_name = ! empty( $company_id ) ? get_the_title( $company_id ) : '';
				$company_link = ! empty( $company_id ) ? get_the_permalink( $company_id ) : '';
				$company_logo = ! empty( $company_id ) ? Noo_Company::get_company_logo($company_id, 'company-logo-square') : '';

				$date_pub = get_the_time( 'U', get_the_ID() );
				$pub_ago  = noo_relative_time( $date_pub );

				$list_job_meta[ 'show_company' ] = false;

				?>
				<?php do_action( 'job_list_single_before', $loop_args, $wp_query ); ?>

                <div class="noo-grid-item">
                    <div class="noo-job-item noo_job">
                    	<a class="job-details-link" href="<?php the_permalink(); ?>"></a>
                        <div class="company-logo">
                            <a class="loop-item-company" title="<?php echo $company_name; ?>"
                               href="<?php echo esc_url( $company_link ); ?>">
								<?php echo( $company_logo ); ?>
                            </a>
                        </div>
                        <div class="company-info">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                <span class="job-date-ago"><?php echo sprintf( __( "%s ago", 'noo' ), $pub_ago ); ?></span></h3>
                            <div class="job-company">
                                <a href="<?php echo esc_url( $company_link ) ?>"><span
                                            class="company-name"><?php echo esc_html( $company_name ); ?></span></a>
                            </div>
                            <div class="job-tools">
						<span class="noo-icon-tool noo-tool-email-job" data-id="<?php echo get_the_ID(); ?>"
                              data-title="<?php echo get_the_title(); ?>" data-url="<?php the_permalink(); ?>">
							<i class="fa fa-envelope"></i>
						</span>

                                <span class="noo-icon-tool noo-btn-bookmark <?php echo ( jm_is_job_bookmarked( 0,
									get_the_ID() ) ) ? 'bookmarked' : ''; ?>"
                                      data-job-id="<?php echo esc_attr( get_the_ID() ); ?>"
                                      data-action="noo_bookmark_job"
                                      data-security="<?php echo wp_create_nonce( 'noo-bookmark-job' ); ?>">
							<i class="fa fa-heart"></i>
						</span>

                                <span class="noo-icon-tool noo-tool-share">
							<i class="fa fa-share-alt"></i>
									<?php noo_share_social( $post->ID ); ?>
						</span>

                            </div>
                            <div class="job-meta">
								<?php jm_the_job_meta( $list_job_meta, $post ); ?>
                            </div>
                        </div>

						<?php if ( noo_get_option( 'noo_jobs_show_quick_view', 1 ) ) : ?>

                            <div class="show-quick-view">
                                <a title="<?php _e( 'Quick view', 'noo' ); ?>" href="#"
                                   class="btn-quick-view btn-quick-view-popup"
                                   data-id="<?php the_ID(); ?>"
                                   data-security="<?php echo wp_create_nonce( 'job-quick-action' ); ?>"></a>
                            </div>

						<?php endif; ?>
                    </div>


                </div>

				<?php do_action( 'job_list_single_after', $loop_args, $wp_query ); ?>

			<?php endwhile; ?>
        </div>
		<?php do_action( 'job_list_after', $loop_args, $wp_query ); ?>
	</div>

	<?php

	if ( $pagination ) {
		$pagination_args = isset( $pagination_args ) ? $pagination_args : array();
		noo_pagination( $pagination_args, $wp_query );
	}

	?>

<?php else: ?>
	<div class="jobs posts-loop">
		<?php
		if ( $no_content == 'text' || empty( $no_content ) ) {
			noo_get_layout( 'no-content' );
		} elseif ( $no_content != 'none' ) {
			echo '<h3>' . $no_content . '</h3>';
		}
		?>
	</div>
<?php endif; ?>