<?php

/**
 * Plugin Name: custom metabox
 * Description: Display a metabox in pages and posts.
 * Version: 0.1
 * Author: Anandu Ravikumar
 */


function add_custom_meta_box()  //to create a custom metabox.
{
    add_meta_box("demo-meta-box", "Custom Meta Box", "custom_meta_box_markup", null, "side", "high", null);
}

add_action("add_meta_boxes", "add_custom_meta_box");

function custom_meta_box_markup($object) //to create the fields and contents needed in the custom metabox created.
{
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
    $post_id= $object->ID;
    ?>
    <div>
    	<label for="meta-box-text">Text</label>
    	<input id="mbtext" name="meta-box-text" type="text" value="<?php echo esc_attr( get_post_meta( $object->ID, 'meta-box-text', true ));?>" required="">

    	<br>
    	<label for="meta-box-checkbox">Check Box</label>
    	<?php
    	$checkbox_value = esc_attr(get_post_meta($object->ID, "meta-box-checkbox", true));
    	if($checkbox_value == ""){
    		?>
    		<input name="meta-box-checkbox" type="checkbox" value="true" >
    		<?php
    	}
    	else if($checkbox_value == "true")
    	{
    		?>
    		<input name="meta-box-checkbox" type="checkbox" value="true" checked>
	<?php
    	}
       
    	?>


    </div>
    <?php 
}


add_action("save_post", "save_custom_meta_box", 10, 2);

function save_custom_meta_box($post_id, $post)
{
	//to save the contents entered in the custom metabox to the  database
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    global $meta_box_text_value;
    $meta_box_checkbox_value="";

    if(isset($_POST["meta-box-text"]))
    {
        $meta_box_text_value = sanitize_text_field($_POST["meta-box-text"]);
    }   
    if($meta_box_text_value!=""){
        update_post_meta($post_id, "meta-box-text", $meta_box_text_value);
    }
    else{
        echo "Field Can't be empty";
    }
    if(isset($_POST["meta-box-checkbox"]))
    {
        $meta_box_checkbox_value = $_POST["meta-box-checkbox"];
    }   
    update_post_meta($post_id, "meta-box-checkbox", $meta_box_checkbox_value);
}
function display_post( $content ) {
	//to display the contents on the frontend

    global $post;

    $checkbox_value = esc_attr(get_post_meta($post->ID, "meta-box-checkbox", true));
    if($checkbox_value == "true"){
    // retrieve the global notice for the current post
        $display_post = esc_attr( get_post_meta( $post->ID, 'meta-box-text', true ));
        $post1 = "<div class='sp_display_post'>$display_post</div>";
        return $post1 . $content;
    }
    else{
        
        return  $content;
    }
    
}
add_filter( 'the_content', 'display_post' );
