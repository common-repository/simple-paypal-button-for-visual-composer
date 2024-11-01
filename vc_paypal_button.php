<?php
/*
Plugin Name: Simple PayPal Button for Visual Composer
Plugin URI: http://oliveconcepts.com
Description: Simple PayPal Button is a Visual Composer plugin that allows for fast creation of Paypal "Buy It Now" buttons.
Version: 1.1
Author: Kirit Dholakiya
Author URI: http://oliveconcepts.com
License: GPLv2 or later
*/

// don't load directly
if (!defined('ABSPATH')) die('-1');

class VCPaypalAddonClass {
    function __construct() {
        // We safely integrate with VC with this hook
        add_action( 'init', array( $this, 'integrateWithVC' ) );
 
        // Use this when creating a shortcode addon
        add_shortcode( 'olv_paypal_btn', array( $this, 'renderPaypalButton' ) );

        // Register CSS and JS
        add_action( 'wp_enqueue_scripts', array( $this, 'loadCssAndJs' ) );
    }
 
    public function integrateWithVC() {
        // Check if Visual Composer is installed
        if ( ! defined( 'WPB_VC_VERSION' ) ) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array( $this, 'showVcVersionNotice' ));
            return;
        }
 
        /*
        Add your Visual Composer logic here.
        Lets call vc_map function to "register" our paypal buynow shortcode within Visual Composer interface.

        More info: http://kb.wpbakery.com/index.php?title=Vc_map
        */
        vc_map( array(
            "name" => __("Paypal Button", 'vc_extend'),
          //  "description" => __("Bar tag description text", 'vc_extend'),
            "base" => "olv_paypal_btn",
            "class" => "",
            "controls" => "full",
          // "icon" => plugins_url('assets/asterisk_yellow.png', __FILE__), // or css class name which you can reffer in your css file later. Example: "vc_extend_my_class"
            "category" => __('Content', 'js_composer'),
            //'admin_enqueue_js' => array(plugins_url('assets/vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
            //'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
            "params" => array(
                array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Paypal Account Email", 'vc_extend'),
                  "param_name" => "paypal_email",
                  "admin_label" => false,
                //  "value" => __("Default params value", 'vc_extend'),
                  "description" => __("Enter the primary email address associated with the account that money will be sent to when the order is completed.", 'vc_extend')
              ),
              array(
                  "type" => "checkbox",
                  "holder" => "div",
                  "class" => "",
                  "admin_label" => "",
                  "heading" => __("Sandbox mode", 'vc_extend'),
                  "param_name" => "sandbox_mode",
                  "description" => __("If you checked, will work in sandbox environment.", 'vc_extend')
              ),    
              array(
                  "type" => "checkbox",
                  "holder" => "div",
                  "class" => "",
                  "admin_label" => "",
                  "heading" => __("Custom Image", 'vc_extend'),
                  "param_name" => "custom_image",
                  "description" => __("If you checked, then your Button Image use for paypal button", 'vc_extend')
              ),array(
                  "type" => "attach_image",
                  "holder" => "div",
                  "class" => "",
                  "admin_label" => false,
                  "heading" => __("Button image", 'vc_extend'),
                  "param_name" => "button_image",
                //  "value" => __("Default params value", 'vc_extend'),
                  "description" => __("Select an image that will be used for the Paypal button.", 'vc_extend')
              ),array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "admin_label" => false,
                  "heading" => __("Button Label", 'vc_extend'),
                  "param_name" => "button_label",
                //  "value" => __("Default params value", 'vc_extend'),
                  "description" => __("Enter Paypal button label text", 'vc_extend')
              ),array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "admin_label" => false,
                  "heading" => __("Button CSS Class", 'vc_extend'),
                  "param_name" => "button_css_class",
                  "value" => __("btn btn-primary", 'vc_extend'),
                //  "description" => __("Enter Paypal button label text", 'vc_extend')
              ),
        array(
            "type" => "dropdown",
                  "holder" => "div",
                  "class" => "",
                  "admin_label" => false,
                  "heading" => __("Layout", 'vc_extend'),
                  "param_name" => "layout",
                  "value" => array(
                  "Without Item & Price" =>"0", 
                "Vertical Layout"=> "1",
                "Horizontal Layout" => "2"
                     ),
                  "description" => __("Select Layout Type", 'vc_extend')
        ),
        array(
                  "type" => "dropdown",
                  "holder" => "div",
                  "class" => "",
                  "admin_label" => false,
                  "heading" => __("Open New Window", 'vc_extend'),
                  "param_name" => "open_new_window",
                  "value" => array("Yes","No"),
                  "description" => __("Selecting 'yes' will pop open a new tab when the user clicks the button.", 'vc_extend')
              ),array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Item Name", 'vc_extend'),
                  "param_name" => "item_name",
                  //"value" => array("Yes","No"),
                  "description" => __("Enter item name or product name; will be displayed on the Paypal Payment page", 'vc_extend')
              ),array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Item Amount", 'vc_extend'),
                  "param_name" => "item_amount",
                  //"value" => array("Yes","No"),
                  "description" => __("Enter amount to be charged to the customer", 'vc_extend')
              ),
              array(
                  "type" => "dropdown",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Currency", 'vc_extend'),
                  "param_name" => "currency",
                  "value" => array(
                  "Australian Dollar"=> "AUD",
                  "Brazilian Real" => "BRL",
                  "Canadian Dollar" =>"CAD",     
                  "Czech Koruna" => "CZK",    
                  "Danish Krone" => "DKK",   
                  "Euro" => "EUR",   
                  "Hong Kong Dollar" => "HKD",    
                  "Hungarian Forint" => "HUF",
                  "Israeli New Sheqel" => "ILS",
                  "Japanese Yen" => "JPY",
                  "Malaysian Ringgit" => "MYR",
                  "Mexican Peso" => "MXN",
                  "Norwegian Krone" => "NOK",
                  "New Zealand Dollar" => "NZD",
                  "Philippine Peso" => "PHP",
                  "Polish Zloty" => "PLN",
                  "Pound Sterling" => "GBP",
                  "Russian Ruble" => "RUB",
                  "Singapore Dollar" => "SGD",
                  "Swedish Krona" => "SEK",
                  "Swiss Franc" => "CHF",
                  "Taiwan New Dollar" => "TWD",
                  "Thai Baht" => "THB",
                  "Turkish Lira"=> "TRY",
                  "US Dollar" => "USD",
                  ),
                  "std"=>"USD",
                  "description" => __("Select the primary currency that the customer will be charged", 'vc_extend')
              ),
              array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "admin_label" => false,
                  "heading" => __("Shipping", 'vc_extend'),
                  "param_name" => "shipping",
                  //"value" => array("Yes","No"),
                  "description" => __("(Optional) add shipping fee charged to the customer", 'vc_extend')
              ),
              array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "admin_label" => false,
                  "heading" => __("Tax", 'vc_extend'),
                  "param_name" => "tax",
                  //"value" => array("Yes","No"),
                  "description" => __("(Optional) add Tax fee charged to the customer", 'vc_extend')
              ),
              array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "admin_label" => false,
                  "heading" => __("Return Url", 'vc_extend'),
                  "param_name" => "return_url",
                  //"value" => array("Yes","No"),
                  "description" => __("Enter URL that customer is redirected to after successfull Payment", 'vc_extend')
              ),
              array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "admin_label" => false,
                  "heading" => __("Cancel Url", 'vc_extend'),
                  "param_name" => "cancel_url",
                  //"value" => array("Yes","No"),
                  "description" => __("Enter URL that customer is redirected if the order is cancelled before completion", 'vc_extend')
              ),
            )
        ) );
    }
    
    /*
    Shortcode logic how it should be rendered
    */
    public function renderPaypalButton( $atts, $content = null ) {
      extract( shortcode_atts( array(
        'paypal_email' => '',
        'sandbox_mode' => false,
        'currency' => 'USD',
        'custom_image' => false,
        'button_label' => 'Buy Now',
        'button_css_class' => 'btn btn-primary',
        'button_image' => '',
        'open_new_window' => 'YES',
        'item_name' => '',
        'item_amount' => '',
        'shipping' => '',
        'tax' => '',
        'cancel_url' => '',
        'return_url' => '',
    'layout' => '0',
      ), $atts ) );

      $paypal_btn_type=$button_mode;
      
    $content = wpb_js_remove_wpautop($content, true); // fix unclosed/unwanted paragraph tags in $content
    $final_output='';
  $output='';
  //$output.='dd'.$sandbox_mode;
  if($sandbox_mode == true)
    $link='https://www.sandbox.paypal.com/cgi-bin/webscr';
  else
    $link='https://www.paypal.com/cgi-bin/webscr';
    
  $target='';
  if($open_new_window != 'No')
    $target='target="_blank"';
  
  $set_currency = 'USD';
  if($currency != '')
    $set_currency = $currency;  
  
  $arrMedia = wp_get_attachment_image_src($button_image,'full');
  
   $output.='<form action="'.$link.'" method="post" '.$target.'>';
   
   if($paypal_email != '')
    $output.='<input type="hidden" name="business" value="'.$paypal_email.'" />';
     
   if($item_name != '')
    $output.='<input type="hidden" name="item_name" value="'.$item_name.'" />'; 
    
   if(is_numeric($item_amount) && $item_amount != '')
    $output.='<input type="hidden" name="amount" value="'.$item_amount.'" />'; 
    
   if($set_currency != '')
    $output.='<input type="hidden" name="currency_code" value="'.$set_currency.'" />'; 
    
  if(is_numeric($shipping) && $shipping != '')
    $output.='<input type="hidden" name="shipping" value="'.$shipping.'" />'; 
  
  if(is_numeric($tax) && $tax != '')
    $output.='<input type="hidden" name="tax" value="'.$tax.'" />';
  
  if($return_url != '')
    $output.='<input type="hidden" name="return" value="'.$return_url.'" />';
  
  if($cancel_url != '')
    $output.='<input type="hidden" name="cancel_return" value="'.$cancel_url.'" />';        
    
  $output.='<input type="hidden" name="cmd" value="_xclick" />';  
 
   if(!$custom_image)
   {
    $output.='<input class="paypal-btn '.$button_css_class.'" type="submit" name="buy_now" value="'.$button_label.'" />';
   }
   else
   {
    $output.='<input height="50" width="150" type="image" name="submit" border="0"  src="'.$arrMedia[0].'" />'; 
   }
   
   $output.='</form>';
   
   if($layout == 0)
   {
     $final_output.=$output;
   }
   else if($layout == 1)
   {
    $final_output.='<div class="vertical-layout">'; 
    if($item_name != '') 
      $final_output.='<div class="title-item">Item Name: </div><div class="item-name">'.$item_name.'</div>'; 
    if(is_numeric($item_amount) && $item_amount != '')
      $final_output.='<div class="item-price">'.$item_amount.' '.$currency.'</div>'; 
      
      $final_output.='<div class="button-form">'.$output.'</div>'; 
    $final_output.='</div>';  
   }
   else if($layout == 2)
   {
     $final_output.='<div class="horizontal-layout">'; 
     if($item_name != '') 
      $final_output.='<div class="item-name">'.$item_name.'</div>'; 
    if(is_numeric($item_amount) && $item_amount != '')
      $final_output.='<div class="item-price">'.$item_amount.' '.$currency.'</div>'; 
      
      $final_output.='<div class="button-form">'.$output.'</div>';  
      $final_output.='</div>';
   }
   
      return $final_output;
    }

    /*
    Load plugin css and javascript files which you may need on front end of your site
    */
    public function loadCssAndJs() {
      wp_register_style( 'vc_extend_style', plugins_url('assets/vc_extend.css', __FILE__) );
      wp_enqueue_style( 'vc_extend_style' );

      // If you need any javascript files on front end, here is how you can load them.
      //wp_enqueue_script( 'vc_extend_js', plugins_url('assets/vc_extend.js', __FILE__), array('jquery') );
    }

    /*
    Show notice if your plugin is activated but Visual Composer is not
    */
    public function showVcVersionNotice() {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="updated">
          <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/1ZqQxyX" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']).'</p>
        </div>';
    }
}
// Finally initialize code
new VCPaypalAddonClass();