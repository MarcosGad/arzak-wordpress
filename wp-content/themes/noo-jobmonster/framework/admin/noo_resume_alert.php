<?php
/**
 * Created by PhpStorm.
 * User: cuong
 * Date: 3/23/2019
 * Time: 3:00 PM
 */

class Noo_Resume_Alert
{
    public static function get_setting($id = null, $default = null)
    {
        global $noo_resume_alert_setting;
        if (!isset($noo_resume_alert_setting) || empty($noo_resume_alert_setting)) {
            $noo_resume_alert_setting = get_option('noo_resume_alert');
        }
        if (isset($noo_resume_alert_setting[$id])) {
            return $noo_resume_alert_setting[$id];
        }
        return $default;
    }
    public static function enable_resume_alert(){
        return self::get_setting('enable_resume_alert','yes') == 'yes';
    }

    public static function set_alert_schedule( $resume_alert_id = null, $frequency = '' ) {
        if ( ! self::enable_resume_alert() ) {
            return;
        }

        if ( empty( $resume_alert_id ) ) {
            return;
        }
        $alert = get_post( $resume_alert_id );

        if ( ! $alert || $alert->post_status !== 'publish' || $alert->post_type !== 'noo_resume_alert' ) {
            return;
        }

        // Update the schedule time
        update_post_meta( $alert->ID, '_start_schedule_time', time() );

        // Reschedule next alert
        $frequency = empty( $frequency ) ? noo_get_post_meta( $alert->ID, '_frequency', 'weekly' ) : $frequency;
        
        switch ( $frequency ) {
            case 'daily' :
                $next = 'daily';
                break;
            case 'hourly' :
                $next = 'hourly';
                break;
            case 'weekly' :
                $next = 'weekly';
                break;
            case 'fortnight' :
                $next = 'daily';
                break;
            case 'monthly' :
                $next = 'monthly';
                break;
            default:
                $next = 'weekly';
        }
        // Create cron
        if ( ! wp_next_scheduled( 'noo-resume-alert-notify', array( $alert->ID )) ) {
            wp_schedule_event(time(), $next, 'noo-resume-alert-notify', array( $alert->ID ));
        }
    }
    public function __construct()
    {
        if(self::enable_resume_alert()){
            add_action('init', array($this, 'register_post_type'), 9);
            add_action('noo-resume-alert-notify',array(&$this,'notify'));
        }

        if (is_admin()) {
            add_filter('admin_init', array(&$this , 'resume_admin_init') );
            add_filter( 'noo_job_settings_tabs_array', array( &$this, 'add_setting_resume_alert_tab' ), 20 );
            add_action( 'noo_job_setting_resume_alert', array(&$this, 'setting_page'));
        }
        add_action('wp_ajax_noo_resume_alert_popup',array(&$this,'new_resume_alert_popup'));
        add_action('wp_ajax_nopriv_noo_resume_alert_popup',array(&$this,'new_resume_alert_popup'));
    }

    public function register_post_type()
    {
        register_post_type('noo_resume_alert', array(
            'labels' => array(
                'name' => __('Resume Alert', 'noo'),
                'singular_name' => __('Resume Alert', 'noo'),
                'all_items' => __('Parent Alert', 'noo'),
            ),
            'public' => false,
            'show_ui' => false,
            'capability_type' => 'post',
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'hierarchical' => false,
            'rewrite' => false,
            'query_var' => false,
            'supports' => false,
            'has_archive' => false,
            'show_in_nav_menus' => false,
            'delete_with_user' => true

        ));
    }
    public function resume_admin_init()
    {
        register_setting('noo_resume_alert', 'noo_resume_alert');
    }

    public function add_setting_resume_alert_tab($tabs)
    {
        $tabs['resume_alert'] = __('Resume Alert', 'noo');
        return $tabs;
    }

    public function setting_page()
    {
        if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
            flush_rewrite_rules();
        }

        $default                =array(
            'r_pos1'        =>'',
            'r_pos2'        =>'',
            'r_pos3'        =>'',
            'r_pos4'        =>'',
            'r_pos5'        =>'',
            'r_pos6'        =>'',
            'r_pos7'        =>'',
            'r_pos8'        =>'',
        );
        $custom_fields =jm_get_resume_search_custom_fields();
        unset($custom_fields['candidate']);
        $search_fields =array(
            'no'    => __('None','noo'),
        );
        foreach ($custom_fields as $k => $field){
            if($field['type']=='datepicker'){
                continue;
            }
            if(isset($field['is_default'])){
                $label = isset($field['label'])? $field['label'] : $k ;
                $id    =   $field['name'];
                $search_fields[$id] = $label;
            }else{
                $label                = __( 'Custom Field: ', 'noo' ) . ( isset( $field['label_translated'] ) ? $field['label_translated'] : ( isset( $field['label'] ) ? $field['label'] : $k ) );
                $id                   =jm_resume_custom_fields_name( $field['name'], $field );
                $search_fields[ $id ] = $label;
            }
        }
        settings_fields('noo_resume_alert'); ?>
        <h3><?php echo __('Resume Alert Options', 'noo') ?></h3>
        <table class="form-table" cellpadding="0">
            <tbody>
            <tr>
                <th>
                    <?php esc_html_e('Enable Resume Alert', 'noo') ?>
                </th>
                <td>
                    <?php $enable_resume_alert = self::get_setting('enable_resume_alert', 'yes'); ?>
                    <input type="hidden" name="noo_resume_alert[enable_resume_alert]" value="no">
                    <input type="checkbox" name="noo_resume_alert[enable_resume_alert]"
                           value="yes" <?php checked($enable_resume_alert, 'yes') ?>>
                </td>
            </tr>
            <tr>
                <th>
                    <?php esc_html_e('Max Resume for each Email', 'noo') ?>
                </th>
                <td>
                    <?php $max_resume_for_email = self::get_setting('max_resume_for_email',5);
                    ?>
                    <input type="text" name="noo_resume_alert[max_resume_for_email]"
                           value="<?php echo($max_resume_for_email ? $max_resume_for_email : '5') ?>">
                    <p>
                        <small><?php echo __('The maximum number of resumes included in each email. It helps make sure the email has reasonable length.If there are more resumes, a read more link will be added to the end of email.') ?></small>
                    </p>
                </td>
            </tr>
            <?php for($po =1; $po <=8; $po++): ?>
                <?php  $r_pos= jm_get_resume_alert_setting('resume_alert'.$po.'',5);
                ?>
                <tr>
                    <th>
                        <?php _e( 'Resume Alert Query Position #' . $po, 'noo' ); ?>
                    </th>
                    <td>
                        <select class=" resume-alert-position" name="<?php echo 'noo_resume_alert[resume_alert'. $po .']'?>">
                            <?php foreach ( $search_fields as $key => $value ) {
                                $selected = ( $r_pos == $key ) || strpos( $r_pos, $key . '|' ) !== false;
                                echo "<option value='{$key}'" . ( $selected ? ' selected' : '' ) . ">{$value}</option>";
                            } ?>
                        </select>
                    </td>
                </tr>

            <?php endfor; ?>
            </tbody>
        </table>
        <?php

    }

    public static function get_frequency() {
        $frequency = array(
            'hourly'    => __('Hourly','noo'),
            'daily'     => __( 'Daily', 'noo' ),
            'weekly'    => __( 'Weekly', 'noo' ),
            'fortnight' => __( 'Fortnightly', 'noo' ),
            'monthly'   => __( 'Monthly', 'noo' ),
        );

        return apply_filters( 'get_frequency', $frequency );
    }

    public function new_resume_alert_popup(){
        if(Noo_Member::is_candidate()){
            $result = array(
                    'success' => false,
                    'message' => '<span class="error-response">'.__('Please login as a employer.','noo').'</span>'
            );
            wp_send_json($result);
        }
        if( !check_ajax_referer('noo-resume-alert-form','security',false)){
            $result = array(
                    'success' => false,
                    'message' => '<span class="error-response">'. __('Your session has expired or you have submitted an invalid form.','noo').'</span>',
            );
            wp_send_json($result);
        }
        $employer_id = get_current_user_id();
        $fields = array();
        for($po=1;$po<=8;$po++){
            $fields[] = jm_get_resume_alert_setting('resume_alert'.$po.'',5);
        }
        $location = isset($_POST['_job_location']) ? ($_POST['_job_location']) : array();
        $category = isset($_POST['_job_category']) ? ($_POST['_job_category']) : array();
        $name = isset($_POST['resume_alert_name']) ? sanitize_text_field($_POST['resume_alert_name']) : '' ;
        $keyword = isset($_POST['resume_alert_keywords']) ? sanitize_text_field($_POST['resume_alert_keywords']) : '';
        $frequency = isset($_POST['resume_alert_frequency']) ? sanitize_text_field(($_POST['resume_alert_frequency'])) : '';
        $email = isset($_POST['resume_alert_email']) ? sanitize_email($_POST['resume_alert_email']) : '';
        $resume_alert = array();
        if(!is_user_logged_in()){
            if(empty($email)){
                $result = array(
                    'success' => false,
                    'message' => '<span class="error-response">'.__('Your resume alert needs an email.','noo').'</span>',
                );
                wp_send_json($result);
            }else{
                $resume_alert = array(
                        'post_title' => $email,
                        'post_type'  => 'noo_resume_alert',
                        'post_status'=> 'publish',
                );
            }

        }else{
            if(empty($name)){
                $result = array(
                   'success' => false,
                   'message' => '<span class="error-response">'.__('You resume alert needs a name.','noo').'</span>',
                );
                wp_send_json($result);
            }
            $resume_alert = array(
                    'post_title' => $name,
                    'post_type'  => 'noo_resume_alert',
                    'post_status'=> 'publish',
                    'post_author'=> $employer_id,
            );
        }

        $alert_id = wp_insert_post($resume_alert);
        if(!empty($alert_id)){
            foreach ($fields as $key=>$value){
                switch($value){
                    case '_job_category':
                        {
                            $cat_save = array();
                            foreach ((array)$category as  $cat){
                                $term = get_term_by('term_id',$cat,'job_category');
                                $cat_save[] = $term->term_id;
                            }
                            update_post_meta($alert_id,'_job_category',json_encode($cat_save));
                            break;
                        }
                    case '_job_location':
                        {
                            $loc_save = array();
                            foreach ((array)$location as $loc){
                                $term = get_term_by('term_id',$loc,'job_location');
                                $loc_save[] = (!empty($term)) ? $term->term_id : array();
                            }
                            update_post_meta($alert_id,'_job_location',json_encode($loc_save));
                            break;
                        }
                    default:{
                        update_post_meta($alert_id,$value,$_POST[$value]);
                    }

                }
            }
            update_post_meta($alert_id,'_keyword',$keyword);
            update_post_meta($alert_id,'_frequency',$frequency);
            update_post_meta($alert_id,'_email',$email);
            Noo_Resume_Alert::set_alert_schedule($alert_id, $frequency );

            do_action('noo_save_resume_alert',$alert_id);
            $result = array(
                 'success' => true,
                 'message' => '<span class="success-message">'.__('New resume alert successfully added.','noo'),
                 'id'      => $alert_id,
            );
            wp_send_json($result);
        }else{
            $result = array(
                'success' => false,
                'message' => '<span class="error-response">' . __( 'There\'s an unknown error. Please retry or contact Administrator.', 'noo' ),
            );

            wp_send_json( $result );
        }
    }

    public function notify($alert_id){
        $alert = get_post($alert_id);
        if(! $alert || $alert->post_status !== 'publish' || $alert->post_type !== 'noo_resume_alert'){
            return;
        }
        $user = get_user_by('id',$alert->post_author);

        $resume = $this->_get_alert_resume($alert_id);
        if($resume && $resume->found_posts > 0){
            $site_name = get_bloginfo('name');
            $email = $this->_format_email($alert,$user,$resume);
            $subject = sprintf(__('%d New Resumes - Resume Alert from %s.','noo'),$resume->found_posts,$site_name);
            $subject = apply_filters('noo_resume_alert_email_subject',$subject,$alert,$resume);

            $to = $user->user_email;
            if($email){
                if(empty($to)){
                    $to = get_post_meta($alert_id,'_email',true);
                }
                noo_mail($to,$subject,$email,'','noo_notify_resume_alert_employer');
            }
            //count
            update_post_meta($alert->ID,'_notify_count',1 + absint(noo_get_post_meta($alert->ID,'_notify_count',0)));
        }
    }
    function _get_alert_resume($alert_id=''){
        global $wpdb;

        $meta_query = array();
        $keywords        = noo_get_post_meta( $alert_id, '_keyword', '' );
        $search_keywords = array_map( 'trim', explode( ',', $keywords ) );
        $keywords_where  = array();

        if ( ! empty( $search_keywords ) && count( $search_keywords ) ) :
            foreach ( $search_keywords as $keyword ) {
                $keywords_where[] = 'post_title LIKE \'%' . esc_sql( $keyword ) . '%\' OR post_content LIKE \'%' . esc_sql( $keyword ) . '%\'';
            }

            $where    = implode( ' OR ', $keywords_where );
            $post__in = array_merge( $wpdb->get_col( "
				    SELECT DISTINCT ID FROM {$wpdb->posts}
				    WHERE ( {$where} )
				    AND post_type = 'noo_resume'
				    AND post_status = 'publish'" ), array( 0 ) ); // add 0 value to make sure there's no result if no job matchs keywords

        endif;
        $fields=array();
        for($po=1;$po<=8;$po++){
            $fields[]= jm_get_resume_alert_setting('resume_alert'.$po.'',5);
        }
        foreach ($fields as $key => $value ){
            if($value == '_job_category'){
                $categories = noo_get_post_meta($alert_id,'_job_category','');
                $categories = noo_json_decode($categories);
                if(!empty($categories)){
                    $temp_meta_query = array('relation'=>'OR');
                    foreach ($categories as $category){
                        if(empty($category)){
                            continue;
                        }
                        $temp_meta_query[] = array(
                            'key'     => '_job_category',
                            'value'   => '"' . $category . '"',
                            'compare' => 'LIKE',
                        );
                    }
                    $meta_query[] = $temp_meta_query;
                }
            }
            if($value == '_job_location'){
                $locations = noo_get_post_meta($alert_id,'_job_location','');
                $locations = noo_json_decode($locations);
                if(!empty($locations)){
                    $temp_meta_query = array('relation'=>'OR');
                    foreach ($locations as $location){
                        if(empty($location)){
                            continue;
                        }
                        $temp_meta_query[] = array(
                            'key'     => '_job_location',
                            'value'   => '"' . $location . '"',
                            'compare' => 'LIKE',
                        );
                    }
                    $meta_query[] = $temp_meta_query;
                }
            } else{
                if($value =='_job_category' || $value=='_job_location'){
                    continue;
                }
                $meta_value = noo_get_post_meta($alert_id,$value, '');
                if(!empty($meta_value)){
                    if(is_array($meta_value)){
                        $temp_meta_query = array( 'relation' => 'OR' );
                        foreach ( $meta_value as $v ) {
                            if ( empty( $v ) ) {
                                continue;
                            }
                            $temp_meta_query[] = array(
                                'key'     => $value,
                                'value'   => '"' . $v . '"',
                                'compare' => 'LIKE',
                            );
                        }
                        $meta_query[] = $temp_meta_query;
                    } else {
                        $meta_query[] = array(
                            'key'   => $value,
                            'value' => $meta_value,
                        );
                    }
                }
            }
        }
        $args = array(
            'post_type' => 'noo_resume',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'nopaging'       => true,
            'post__in'       => $post__in,
            'meta_query'     => $meta_query,
        );
        $result = new WP_Query($args);

        return $result;
    }

    private function _format_email($alert,$user,$resumes){
        $max_alert_resume_count = self::get_setting('max_resume_count_email',5);
        $site_name = get_bloginfo('name');
        $dear = $user->display_name;
        if(empty($dear)){
            $dear = $alert->post_title;
        }

        $message = sprintf(__('Dear %s','noo'),$dear).'<br/><br/>';
        $message .=sprintf(__('We found %d new resumes that match your criteria.','noo'),$resumes->found_posts).'<br/><br/>';

        if($resumes && $resumes->have_posts()){
            $count = 0;
            while ($resumes->have_posts() && $count <= $max_alert_resume_count):
                $resumes->the_post();
                global $post;
                $count++;
               $location  = noo_get_post_meta( get_the_ID(), '_job_location' );
                $jlocations = array();
                if ( ! empty( $location ) ) {
                    $location  = noo_json_decode( $location );
                    $locations = empty( $location ) ? array() : get_terms( 'job_location', array(
                        'include'    => array_merge( $location, array( - 1 ) ),
                        'hide_empty' => 0,
                        'fields'     => 'names',
                    ) );
                }
                $category   = noo_get_post_meta( get_the_ID(), '_job_category', '' );
                $categories = array();
                if ( ! empty( $category ) ) {
                    $category   = noo_json_decode( $category );
                    $categories = empty( $category ) ? array() : get_terms( 'job_category', array(
                        'include'    => array_merge( $category, array( - 1 ) ),
                        'hide_empty' => 0,
                        'fields'     => 'names',
                    ) );
                }

                $message .= sprintf( __( '%s: <a href="%s">%s</a>', 'noo' ), get_the_title( $post ), get_permalink( $post->ID ), get_permalink( $post->ID ) ) . '<br/>';
                if(!empty($jlocations)){
                    $message .= sprintf( __( '** Location: %s', 'noo' ), implode( ', ',   $jlocations ) ) . '<br/>';
                }
                if(!empty($categories)){
                    $message .= sprintf( __( '** Job Category: %s', 'noo' ), implode( ', ', $categories ) ) . '<br/>';
                }                
                $message .= sprintf(__('To cancel the notification of receiving information, please <a href="%s">login the website</a> and remove them.','noo'),Noo_Member::get_endpoint_url( 'resume-alert' )). '<br/>';
                $message .= __( '------', 'noo' ) . '<br/>';
            endwhile;
            if($resumes->found_posts > $max_alert_resume_count){
                $message .= sprintf(__('View more resume: %s','noo'),get_home_url()).'<br/>';
            }
        }
        $message .='<br/>' .__('Best regards.','noo') . '<br/>';
        $message .= $site_name;
        return apply_filters('noo_resume_alert_email_content',$message);
    }

}
new Noo_Resume_Alert();