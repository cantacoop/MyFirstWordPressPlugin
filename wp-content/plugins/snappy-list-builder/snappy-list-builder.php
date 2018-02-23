<?php
/*
Plugin Name:    Snappy List Builder
Plugin URI:     http://wordpressplugincourse.com/plugins/snappy-list-builder
Description:    The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subscribers with a custom download upon op-in. Build unlimited lists. Import and export subscriber easily with .csv
Version:        1.1
Author:         Joel Funk @ Code College
Author URI:     http://joelfunk.codecollege.ca
License:        GPL2
License URI:    https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:    snappy-list-builder
*/

/* 1. HOOKS */
// 1.1 hint: register all our custom shortcodes on init
add_action( "init", "slb_register_shortcodes" );

// 1.2 hint: register custom admin column headers
add_filter( "manage_edit-slb_subscriber_columns", "slb_subscriber_column_headers" );
add_filter( "manage_edit-slb_list_columns", "slb_list_column_headers" );

// 1.3 hint: register custom admin column data
add_filter( "manage_slb_subscriber_posts_custom_column", "slb_subscriber_column_data", 1, 2 );
add_action( 'admin_head-edit.php', 'slb_register_custom_admin_titles' );
add_filter( "manage_slb_list_posts_custom_column", "slb_list_column_data", 1, 2 );

// 1.4 hint: register ajax actions
add_action( 'wp_ajax_nopriv_slb_save_subscription', 'slb_save_subscription' ); // regular website visitor
add_action( 'wp_ajax_slb_save_subscription', 'slb_save_subscription' ); // admin user

// 1.5 hint: load external files to public website
add_action( 'wp_enqueue_scripts', 'slb_public_scripts');

// 1.6 Advanced Custom Fields Settings
add_filter('acf/settings/path', 'slb_acf_settings_path');
add_filter('acf/settings/dir', 'slb_acf_settings_dir');
add_filter('acf/settings/show_admin', 'slb_acf_show_admin');
// if (!defined('ACF_LITE')) {
//     define('ACF_LITE', true); // turn off ACF plugin menu
// }

/* 2. SHORTCODES */
// 2.1
function slb_register_shortcodes() {

    add_shortcode("slb_form", "slb_form_shortcode");

}

/* 2.1 slb_form_shortcode() */
function slb_form_shortcode( $args, $content="" ) {

    // get the list id
    $list_id = 0;
    if ( isset($args['id']) ) {
        $list_id = (int)$args['id'];
    }

    // title
    $title = '';
    if ( isset($args['title']) ) {
        $title = (string)$args['title'];
    }

    // setup our output variable - the form html
    $output = '
        <div class="slb">
            <form id="slb_form" name="slb_form" class="slb-form" method="post"
                action="/wp-admin/admin-ajax.php?action=slb_save_subscription">

                <input type="hidden" name="slb_list" value="' . $list_id . '" />';

                if (strlen($title)) {
                    $output .= '<h3 class="slb-title">' . $title . '</h3>';
                }

                $output .= '<p class="slb-input-container">
                    <label>Your Name</label><br />
                    <input type="text" name="slb_fname" placeholder="First Name" />
                    <input type="text" name="slb_lname" placeholder="Last Name" />
                </p>
                <p class="slb-input-container">
                    <label>Email Address</label><br />
                    <input type="email" name="slb_email" placeholder="ex. you@email.com" />
                </p>';

                // including content in our form html if content is pass into the function 
                if (strlen($content)):
                    $output .= '<div class="slb-content">'. wpautop($content) .'</div>';
                endif;
                
                // completing our form html 
                $output .= '<p class="slb-input-container">
                    <input type="submit" name="slb_submit" value="Sign Me Up!" />
                </p>
            </form>
        </div>
    ';

    // return our result/html
    return $output;

}

/* 3. FILTERS */
// 3.1
function slb_subscriber_column_headers( $columns ) {

    // creating custom column header data
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Subscriber Name'),
        'email' => __('Email Address'),
    );

    // returning new columns
    return $columns;
}

// 3.2
function slb_subscriber_column_data( $columns, $post_id ) {

    // setup our return text
    $output = '';

    switch ( $columns ) {
        case 'title':
            // get the custom name data
            $fname = get_field('slb_fname', $post_id);
            $lname = get_field('slb_lname', $post_id);
            $output .= $fname . ' ' . $lname;
            break;
        case 'email':
            // get the custom email data
            $email = get_field('slb_email', $post_id);
            $output .= $email;
            break;
    }

    // echo the output
    echo $output;
}

// 3.2.2 hint: register special custom admin title columns
function slb_register_custom_admin_titles() {
    add_filter( 'the_title', 'slb_custom_admin_titles', 99, 2 );
}

// 3.2.3 hint: handles custom admin title "title" column data for post types without titles
function slb_custom_admin_titles( $title, $post_id ) {

    global $post;

    $output = $title;

    if ( isset($post->post_type) ) {
        switch( $post->post_type ) {
            case 'slb_subscriber': 
                $fname = get_field('slb_fname', $post_id);
                $lname = get_field('slb_lname', $post_id);
                $output = $fname . ' ' . $lname;
                break;
        }
    }

    return $output;
}

// 3.3
function slb_list_column_headers( $columns ) {

    // creating custom column header data
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('List Name'),
        'shortcode' => __('Shortcode'),
    );

    // returning new columns
    return $columns;
}

// 3.4
function slb_list_column_data( $columns, $post_id ) {

    // setup our return text
    $output = '';

    switch ( $columns ) {
        case 'shortcode':
            $output .= '[slb_form id="' . $post_id . '"]';
            break;
    }

    // echo the output
    echo $output;
}

// 3.5 hint: register custom plugin admin menu
function slb_admin_menus() {
    /* main menu */
    $top_menu_item = 'slb_dashboard_admin_page';

    add_menu_page('', 'List Builder', 'manage_options', 'slb_dashboard_admin_page',
        'slb_dashboard_admin_page', 'dashicons-email-alt');

    /* submenu item */
    // dashboard
    add_submenu_page( $top_menu_item, "", "Dashboard", "manage_options", $top_menu_item, $top_menu_item );

    // email lists
    add_submenu_page( $top_menu_item, "", "Email lists", "manage_options", "edit.php?post_type=slb_list" );

    // subscribers
    add_submenu_page( $top_menu_item, "", "Subscribers", "manage_options", "edit.php?post_type=slb_subscriber" );

    // Import subscribers
    add_submenu_page( $top_menu_item, "", "Import Subscribers", "manage_options", "slb_import_admin_page", "slb_import_admin_page" );

    // plugin options 
    add_submenu_page( $top_menu_item, "", "Plugin Options", "slb_options_admin_page", "slb_options_admin_page" );
}
add_action( 'admin_menu', 'slb_admin_menus' );

/* 4. EXTERNAL SCRIPTS */
// 4.1 Include ACF
include_once( plugin_dir_path( __FILE__ ) . 'lib/advanced-custom-fields/acf.php');

// 4.2 hint: loads external files into PUBLIC website
function slb_public_scripts() {

    // register scripts with WordPress's internal library
    wp_register_script( 'snappy-list-builder-js-public', 
        plugins_url( '/js/public/snappy-list-builder.js', __FILE__ ), 
        array('jquery'), 
        '', 
        true 
    );

    // register style with WordPress's internal library
    wp_register_style( 'snappy-list-builder-css-public', 
        plugins_url( '/css/public/snappy-list-builder.css', __FILE__ )
    );

    // add to que of scripts that get loaded into every page
    wp_enqueue_script( 'snappy-list-builder-js-public' );
    wp_enqueue_style( 'snappy-list-builder-css-public' );
}

/* 5. ACTIONS */
// 5.1 hint: saves subscription data to an existing or new subscriber
function slb_save_subscription() {

    // setup default result data
    $result = array(
        'status' => 0,
        'message' => 'Subscription was not saved.',
        'error' => '',
        'errors' => array(),
    );

    // array for storing errors
    $errors = array();

    try {
        // get list id
        $list_id = (int)$_POST['slb_list'];

        // prepare subscriber data 
        $subscriber_data = array(
            'fname' => esc_attr( $_POST['slb_fname'] ),
            'lname' => esc_attr( $_POST['slb_lname'] ),
            'email' => esc_attr( $_POST['slb_email'] ),
        );

        // setup our errors array
        $errors = array();

        // form validation
        if (!strlen($subscriber_data['fname'])) {
            $errors['fname'] = 'First name is required.';
        }

        if (!strlen($subscriber_data['email'])) {
            $errors['email'] = 'Email address is required.';
        }

        if (strlen($subscriber_data['email']) && !is_email($subscriber_data['email'])) {
            $errors['email'] = 'Email address must be valid.';
        }

        if (count($errors)) {

            // append errors to result structure for later use
            $result['error'] = 'Some fields are still required';
            $result['errors'] = $errors;
        } else {

            // attempt to create/save subscriber 
            $subscriber_id = slb_save_subscriber( $subscriber_data );

            // IF subscriber was saved successfully $subscriber_id will be generate than 0
            if ( $subscriber_id ) {
                
                // IF subscriber already has this subscription
                if ( slb_subscriber_has_subscription( $subscriber_id, $list_id )) {

                    // get list object
                    $list = get_post( $list_id );

                    // return detailed error
                    $result['error'] = esc_attr( $subscriber_data['email'] . 'is already subscribed to '
                        . $list->post_title . '.' );
                } else {

                    // save new subscription
                    $subscription_saved = slb_add_subscription( $subscriber_id, $list_id );

                    // IF subscription was saved successfully
                    if ( $subscription_saved ) {

                        // subscription saved!
                        $result['status'] = 1;
                        $result['message'] = 'Subscription saved';
                    } else {

                        // return detailed error
                        $result['error'] = 'Unable to save subscription';
                    }
                }
            }
        }
    } catch ( Exception $e ) {
        // a php error occurred
        $result['message'] = 'Caught exception: ' . $e->getMessage();
    }

    // return result an json
    slb_return_json($result);
}

// 5.2 hint: create a new subscriber or updates and existing one
function slb_save_subscriber( $subscriber_data ) {

    // setup default subscriber id
    // 0 mean the subscriber was not saved
    $subscriber_id = 0;

    try {
        $subscriber_id = slb_get_subscriber_id( $subscriber_data['email'] );

        // IF the subscriber does not already exists...
        if ( !$subscriber_id ) {

            // add new subscriber to database
            $subscriber_id = wp_insert_post(
                array(
                    'post_type' => 'slb_subscriber',
                    'post_title' => $subscriber_data['fname'] . ' ' . $subscriber_data['lname'],
                    'post_status' => 'publish',
                ),
                true
            );
        }

        // add/update custom meta data
        update_field( slb_get_acf_key('slb_fname'), $subscriber_data['fname'], $subscriber_id );
        update_field( slb_get_acf_key('slb_lname'), $subscriber_data['lname'], $subscriber_id );
        update_field( slb_get_acf_key('slb_email'), $subscriber_data['email'], $subscriber_id );
    } catch (Exception $e) {

    }

    // return subscriber_id
    return $subscriber_id;
}

// 5.3 hint: adds list to subscribers subscriptions
function slb_add_subscription($subscriber_id, $list_id) {

    // setup return default value
    $subscription_saved = false;

    // IF the subscriber does NOT have the current list subscription
    if (!slb_subscriber_has_subscription($subscriber_id, $list_id)) {

        // get subscriptions and append new $list_id
        $subscriptions = slb_get_subscriptions($subscriber_id);
        $subscriptions[] = $list_id;

        // update slb_subscriptions
        update_field(slb_get_acf_key('slb_subscriptions'), $subscriptions, $subscriber_id);

        // subscription updated
        $subscription_saved = true;
    }

    // return result
    return $subscription_saved;
}



/* 6. HELPERS */
// 6.1 hint: returns true or false
function slb_subscriber_has_subscription( $subscriber_id, $list_id ) {

    // setup return default value
    $has_subscription = false;

    // get subscriber
    $subscriber = get_post( $subscriber_id );

    // get subscriptions
    $subscriptions = slb_get_subscriptions( $subscriber_id );

    // check subscriptions for $list_id
    if ( in_array($list_id, $subscriptions) ) {

        // found the $list_id in $subscriptions
        // this subscriber is already subscribed to this list
        $has_subscription = true;
    } else {

    }

    return $has_subscription;
}

// 6.2 hint: retrieves a subscriber_id from an email address
function slb_get_subscriber_id( $email ) {

    $subscriber_id = 0;

    try {
        // check if subscriber already exists
        $subscriber_query = new WP_Query( 
            array(
                'post_type' => 'slb_subscriber',
                'posts_per_page' => 1,
                'meta_key' => 'slb_email',
                'meta_query' => array(
                    array(
                        'key' => 'slb_email',
                        'value' => $email,
                        'compare' => '=',
                    ),
                ),
            )
        );

        // IF the subscriber exists...
        if ( $subscriber_query->have_posts() ) {
            
            // get the subscriber id
            $subscriber_query->the_post();
            $subscriber_id = get_the_ID();
        }
    } catch ( Exception $e ) {

    }

    // reset the wordpress post object
    wp_reset_query();

    return (int) $subscriber_id;
}

// 6.3 hint: return an array of list_id's
function slb_get_subscriptions( $subscriber_id ) {

    $subscriptions = array();

    // get subscription (return array of list objects)
    $lists = get_field( slb_get_acf_key('slb_subscriptions'), $subscriber_id );

    // IF $lists return somthing
    if ($lists) {

        // IF $lists is an array and there is one or more items
        if ( is_array($lists) && count($lists) ) {
            // build subscriptions: array of list id's
            foreach ($lists as &$list) {
                $subscriptions[] = (int)$list->ID;
            }
        } else if ( is_numeric($lists) ) {
            // single result returned
            $subscriptions[] = $lists;
        }
    }

    return $subscriptions;
}

// 6.4 
function slb_return_json( $php_array ) {

    // endcode result as json string
    $json_result = json_encode($php_array);

    // return result
    die($json_result);

    // stop all other processing
    exit;
}

// 6.5 hint: get unique act field key from the field name
function slb_get_acf_key( $field_name ) {

    $field_key = $field_name;

    switch( $field_name ) {

        case 'slb_fname':
            $field_key = 'field_5a8b88596f739';
            break;
        case 'slb_lname':
            $field_key = 'field_5a8b88916f73a';
            break;
        case 'slb_email':
            $field_key = 'field_5a8b88de6f73b';
            break;
        case 'slb_subscriptions':
            $field_key = 'field_5a8b89246f73c';
            break;
    }

    return $field_key;
}

// 6.6 hint: returns an array of subscriber data including subscriptions
function slb_get_subscriber_data( $subscriber_id ) {

    // setup subscriber data
    $subscriber_data = array();

    // get subscriber object
    $subscriber = get_post( $subscriber_id );

    // IF subscriber object is valid
    if (isset($subscriber->post_type) && $subscriber->post_type == 'slb_subscriber') {

        $fname = get_field(slb_get_acf_key('slb_fname'), $subscriber_id);
        $lname = get_field(slb_get_acf_key('slb_lname'), $subscriber_id);

        // build subscriber data for return
        $subscriber_data = array(
            'name' => $fname . ' ' . $lname,
            'fname' => $fname,
            'lname' => $lname,
            'email' => get_field(slb_get_acf_key('slb_email'), $subscriber_id),
            'subscriptions' => slb_get_subscriptions($subscriber_id)
        );
    }

    // return subscriber data
    return $subscriber_data;
}

/* 7. CUSTOM POST TYPES */
// 7.1 subscribers
include_once( plugin_dir_path( __FILE__ ) . 'cpt/slb_subscriber.php');

// 7.1 list
include_once( plugin_dir_path( __FILE__ ) . 'cpt/slb_list.php');

/* 8. ADMIN PAGES */
// 8.1 hint: dashboard admin page
function slb_dashboard_admin_page() {

    $output = '
        <div class="wrap">
            <?php screen_icon(); ?>

            <h2>Snappy List Builder</h2>

            <p>The ultimate email list building plugin for wordpress. Capture new subscriber.
            Reward subscribers with a custom download upon opt-in. Build ulimited lists.
            Import and Export subscribers easily with .csv</p>
        </div>
    ';
        
    echo $output;
}

// 8.2 hint: import subscribers admin page
function slb_import_admin_page() {

    $output = '
        <div class="wrap">
            <?php screen_icon(); ?>

            <h2>Import Subscribers</h2>

            <p>Page description...</p>
        </div>
    ';

    echo $output;
}

// 8.3 hint: plugin options admin page
function slb_options_admin_page() {

    $output = '
        <div class="wrap">
            <?php screen_icon(); ?>

            <h2>Snappy List Builder Options</h2>

            <p>Page description...</p>
        </div>
    ';

    echo $output;
}
