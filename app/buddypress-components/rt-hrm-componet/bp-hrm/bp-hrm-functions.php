<?php

/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Description of BP HRM functions.
 *
 * @author kishore
 */


function add_pagination( $totalPage, $page ){
    global $rt_hrm_bp_hrm;
   
    if( $totalPage > 1 ){
                                            
        $base = $rt_hrm_bp_hrm->get_component_root_url().bp_current_action().'/%_%';
        $formate = 'page/%#%';
        if( isset( $_GET['orderby'] ) ) {

                $arr_params = array( 'orderby' => $_GET['orderby'], 'order' => $_GET['order'] );
                $base = add_query_arg( $arr_params, get_component_root_url().bp_current_action() ) .'%_%' ; 
                $formate = '&paged=%#%';
        }

        $customPagHTML     =  '<div class="pagination" role="menubar" aria-label="Pagination"><span class="current">Page '.$page.' of '.$totalPage.'</span>'.paginate_links( array(
        'base' => $base,
        'format' => $formate,
        'total' => $totalPage,
        'current' => $page
        )).'</div>';
        echo $customPagHTML;
    }
}

?>