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

// 1.1
add_action("init", "slb_register_shortcodes");

/* 2. SHORTCODES */

/* 2.1 */
function slb_register_shortcodes() {

    add_shortcode("slb_form", "slb_form_shortcode");

}

/* 2.1 slb_form_shortcode() */
function slb_form_shortcode( $args, $content="" ) {

    // setup our output variable - the form html
    $output = '
        <div class="slb">
            <form id="slb_form" name="slb_form" class="slb-form" method="post">
                <p class="slb-input-container">
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
