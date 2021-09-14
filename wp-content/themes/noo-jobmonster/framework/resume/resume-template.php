<?php

if( !function_exists('jm_resume_template_loader') ) :
    function jm_resume_template_loader( $template ) {
        global $wp_query;
        if( is_post_type_archive( 'noo_resume' ) ){
            $template       = locate_template( 'archive-noo_resume.php' );
        }
        return $template;
    }

    add_action( 'template_include', 'jm_resume_template_loader' );
endif;

if( !function_exists('jm_resume_loop') ) :
    function jm_resume_loop( $args = '' ) {
        $defaults = array( 
            'query'          => '', 
            'title'          => '',
            'rows'          => '2',
            'column'          => '2',
            'autoplay'          => 'false',
            'slider_speed'          => '800',
            'pagination'     => 'true',
            'resume_style'   => 'list',
            'paginate'       => 'normal',
            'ajax_item'      => false,
            'excerpt_length' => 30,
            'no_content' => 'text',
            'posts_per_page'  => 3,
            'is_slider'   => false,
            'is_shortcode'   => false,
            'job_category'    => 'all',
            'job_location'    => 'all',
            'orderby'         => 'date',
            'order'           => 'desc',
            'live_search'     => false
        );

        $loop_args = wp_parse_args($args, $defaults);

        extract($loop_args);
        global $wp_query;
        if (!empty($loop_args['query'])) {
            $wp_query = $loop_args['query'];
        }

        ob_start();
        $arr_type = array( 'list', 'grid' );

        if($is_shortcode){
            $dl_default = $resume_style;
        } else{
            $dl_default = noo_get_option('noo_resume_display_type', 'list');

        }
        $type = isset( $_GET[ 'display' ] ) && in_array( $_GET[ 'display' ], $arr_type ) ? $_GET[ 'display' ] : $dl_default;
        
        $display_style = 'layouts/resume/loop/' . esc_attr( $type ) .'.php';
        include(locate_template( $display_style ));
        echo ob_get_clean();

        wp_reset_postdata();

    }
endif;
if( !function_exists('jm_cannot_view_list_resume')):
    function jm_cannot_view_list_resume(){
        $title = '';
        $link = '';
        $can_view_resume_setting = jm_get_action_control('view_and_search_resume');
        switch( $can_view_resume_setting ) {
            case 'public':
                $title = __( 'There\'s an unknown error. Please retry or contact Administrator.<br />', 'noo' );
                break;
            case 'user':
                $title = __('Only logged in users can view resumes.<br />','noo');
                if( !Noo_Member::is_logged_in() ) {
                    $link = Noo_Member::get_login_url();
                    $link = '<a href="' . esc_url( $link ) . '" class="btn btn-primary member-login-link">' . __( 'Login', 'noo' ) . '</a>';
                }
                break;
            case 'employer':
                $title = __('Only employers can view resumes.<br />','noo');
                if( !Noo_Member::is_logged_in() ) {
                    $link = Noo_Member::get_login_url();
                    $link = '<a href="' . esc_url( $link ) . '" class="btn btn-primary member-login-link">' . __( 'Login as Employer', 'noo' ) . '</a>';
                } elseif( !Noo_Member::is_employer() ) {
                    $link = Noo_Member::get_logout_url();
                    $link = '<a href="' . esc_url( $link ) . '" class="btn btn-primary">' . __( 'Logout', 'noo' ) . '</a>';
                }
                break;
            case 'package':
                $title = __('Only paid employers can view resumes.<br />','noo');
                $link = Noo_Member::get_endpoint_url('manage-plan');

                if( !Noo_Member::is_logged_in() ) {
                    $link = Noo_Member::get_login_url();
                    $link = '<a href="' . esc_url( $link ) . '" class="btn btn-primary  member-login-link">' . __( 'Login as Employer', 'noo' ) . '</a>';
                } elseif( !Noo_Member::is_employer() ) {
                    $link = Noo_Member::get_logout_url();
                    $link = '<a href="' . esc_url( $link ) . '" class="btn btn-primary">' . __( 'Logout', 'noo' ) . '</a>';
                } else {
                    $title = __('Your membership doesn\'t allow you to view the resumes.<br />','noo');
                    $link = Noo_Member::get_endpoint_url('manage-plan');
                    $link = '<a href="' . esc_url( $link ) . '" class="btn btn-primary">' . __( 'Click here to upgrade your Membership.', 'noo' ) . '</a>';
                }
                break;
        }
        return array( $title, $link );
    }
endif;
if( !function_exists('jm_message_cannot_view_contact_candidate')):
    function jm_message_cannot_view_contact_candidate(){
        $message = '';
        $link = '';
        $can_view_resume_contact = jm_get_action_control('view_candidate_contact');
        switch( $can_view_resume_contact ) {
            case 'public':
                $message = __( 'There\'s an unknown error. Please retry or contact Administrator.<br />', 'noo' );
                break;
            case 'user':
                $message= __('Only logged in users can view candidate\'s contact.<br />','noo');
                if( !Noo_Member::is_logged_in() ) {
                    $link = Noo_Member::get_login_url();
                    $link = '<a href="' . esc_url( $link ) . '" class=" member-login-link">' . __( 'Login', 'noo' ) . '</a>';
                }
                break;
            case 'employer':
                $message = __('Only employers can view the candidate\'s contact.<br />','noo');
                if( !Noo_Member::is_logged_in() ) {
                    $link = Noo_Member::get_login_url();
                    $link = '<a href="' . esc_url( $link ) . '" class=" member-login-link">' . __( 'Login as Employer', 'noo' ) . '</a>';
                } elseif( !Noo_Member::is_employer() ) {
                    $link = Noo_Member::get_logout_url();
                    $link = '<a href="' . esc_url( $link ) . '" class="">' . __( 'Logout', 'noo' ) . '</a>';
                }
                break;
            case 'package':
                $message = __('Only paid employers can view candidate\'s contact.<br />','noo');
                $link = Noo_Member::get_endpoint_url('manage-plan');

                if( !Noo_Member::is_logged_in() ) {
                    $link = Noo_Member::get_login_url();
                    $link = '<a href="' . esc_url( $link ) . '" class=" member-login-link">' . __( 'Login as Employer', 'noo' ) . '</a>';
                } elseif( !Noo_Member::is_employer() ) {
                    $link = Noo_Member::get_logout_url();
                    $link = '<a href="' . esc_url( $link ) . '" class="">' . __( 'Logout', 'noo' ) . '</a>';
                } else {
                    $message = __('Your membership doesn\'t allow you to view the candidate\'s contact.<br />','noo');
                    $link = Noo_Member::get_endpoint_url('manage-plan');
                    $link = '<a href="' . esc_url( $link ) . '" class="">' . __( 'Click here to upgrade your Membership.', 'noo' ) . '</a>';
                }
                break;
            case 'noone':
                    $message = __('The Candidate\'s contact information is private', 'noo');
                break;
        }
        return array($message,$link);
    }
endif;

if( !function_exists('jm_resume_detail') ) :
    function jm_resume_detail( $query = null, $hide_profile = false ) {
        if(empty($query)){
            global $wp_query;
            $query = $wp_query;
        }
        $layout = noo_get_option( 'noo_resumes_detail_layout', 'style-1' );
        $layout = !empty( $_GET['layout'] ) ? sanitize_text_field( $_GET['layout'] ) : $layout;
        while ($query->have_posts()): $query->the_post(); 

            global $post;
            $resume_id          = $post->ID;
            ob_start();
            if( jm_can_view_single_resume( $resume_id ) ) {
                if ( 'style-1' == $layout ) {
                    include( locate_template( "layouts/resume/single/detail.php" ) );
                } elseif ( 'style-2' == $layout ) {
                    include( locate_template( "layouts/resume/single/detail-style-2.php" ) );
                } elseif ( 'style-3' == $layout ) {
                    include( locate_template( "layouts/resume/single/detail-style-3.php" ) );
                } elseif ( 'style-4' == $layout ) {
                    include( locate_template( "layouts/resume/single/detail-style-4.php" ) );
                }
            } else {
                // include(locate_template("layouts/resume/cannot-view-resume.php"));
                // 
                // Fix Unregister Employer Who uses company ID to view a resume
                $company_id =(isset($_COOKIE['jm_ga_company_id'])) ? $_COOKIE[ 'jm_ga_company_ids' ] : '';
                $paged = (isset($paged)) ? $paged : '';

                $job_list = Noo_Company::get_company_jobs( $company_id );
 
                $status_filter = isset( $_REQUEST[ 'status' ] ) ? esc_attr( $_REQUEST[ 'status' ] ) : '';
                $all_statuses  = Noo_Application::get_application_status();
                unset( $all_statuses[ 'inactive' ] );
 
                $app_check_args = array(
                    'post_type'       => 'noo_application',
                    'paged'           => $paged,
                    'post_parent__in' => array_merge( $job_list, array( 0 ) ),
                    // make sure return zero application if there's no job.
                    'post_status'     => ! empty( $status_filter ) ? array( $status_filter ) : array(
                        'publish',
                        'pending',
                        'rejected',
                    ),
                );
 
                if ( ! empty( $job_filter ) && in_array( $job_filter, $job_ids ) ) {
                    $app_check_args[ 'post_parent__in' ] = array( $job_filter );
                }
 
                $check_applications = new WP_Query( $app_check_args );
               
                $check_application_status = false; //by Default false unless a application is applied
                foreach($check_applications->posts as $check_post){
                    if(Noo_application::has_applied($post->post_author,$check_post->post_parent)){
                        $check_application_status =  true; //Allows access to resume if even a single application is applied by that candidate
                    }
                }
               
                if ( ! jm_ga_check_logged() ):
                    include(locate_template("layouts/resume/cannot-view-resume.php"));
                elseif($check_application_status):
                    include(locate_template("layouts/resume/single/detail.php"));
                else:
                    include(locate_template("layouts/resume/cannot-view-resume.php"));
                endif;
                
            }
            echo ob_get_clean();
        
        endwhile;
        wp_reset_query();
    }
endif;


if ( ! function_exists( 'noo_resume_list_display_type' ) ) :

    function noo_resume_list_display_type() {

        $arr_type = array( 'list', 'grid' );

        $default = noo_get_option('noo_resume_display_type', 'list');

        $type = isset( $_GET[ 'display' ] ) && in_array( $_GET[ 'display' ], $arr_type ) ? $_GET[ 'display' ] : $default;

        return $type;
    }

endif;

if ( ! function_exists( 'noo_resume_get_location' ) ) :

    function noo_resume_get_location( $resume_id = '' ) {

        if ( empty( $resume_id ) ) {
            return false;
        }

        $location = get_post_meta( $resume_id, '_job_location', true );
        if ( !empty( $location ) && is_array( $location ) ) {
            $location = implode(', ', $location);
        }

        return $location;

    }
endif;
if( !function_exists('noo_can_post_resume_review')):
    function noo_can_post_resume_review($resume_id = null){
        if( empty( $resume_id ) )
            return false;
        // Resume's author can view his/her resume
        $candidate_id = get_post_field( 'post_author', $resume_id );
        if( $candidate_id == get_current_user_id() ) {
            return true;
        }

        $can_view_resume = false;
        // Administrator can post  review all resumes
        if( 'administrator' == Noo_Member::get_user_role(get_current_user_id()) ) {
            $can_view_resume = true;
        }elseif( isset($_GET['application_id'] ) && !empty($_GET['application_id']) ) {
            // Employers can view resumes from their applications

            $job_id = get_post_field( 'post_parent', $_GET['application_id'] );
            $company_id = noo_get_post_meta($job_id,'_company_id');
            $employer_id = get_post_field('post_author',$company_id);
            if( $employer_id == get_current_user_id() ) {
                $attachement_resume_id = noo_get_post_meta( $_GET['application_id'], '_resume', '' );
                $can_view_resume = $resume_id == $attachement_resume_id;
            }
        }
        return $can_view_resume;
    }
endif;