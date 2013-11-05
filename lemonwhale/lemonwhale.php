<?php
/*
	Plugin Name: Lemonwhale
	Description: Publish lemonwhale video real easy
	Author: Svante Holmdahl
	Version: 1.1.1
	Author URL: http://lemonwhale.com

	[lemonwhale vid="" width="480" height ="360" theme="MAGAZINE" pid=""]
 */


function lemonwhale($attributes, $content = null)
{
    $vid = '';
    $pid = '';
    $uid = '';
    $width = '';
    $height = '';
    $theme = '';

    extract(shortcode_atts(array(
        'vid' => '',
        'width' => '480',
        'height' => '360',
        'theme' => 'MAGAZINE',
        'uid' => '',
        'pid' => ''), $attributes));

    if (empty($vid))
        return $content;

    $sPlayerTemplate = '<iframe src="http://ljsp.lwcdn.com/api/video/embed.jsp?id=%VID%&pi=%PID%" title="0;" byline="0;" portrait="0;" width="%WIDTH%" height="%HEIGHT%" frameborder="0"></iframe>';

    $sPlayerTemplate = str_replace('%WIDTH%', $width, $sPlayerTemplate);
    $sPlayerTemplate = str_replace('%HEIGHT%', $height, $sPlayerTemplate);
    $sPlayerTemplate = str_replace('%VID%', $vid, $sPlayerTemplate);
    $sPlayerTemplate = str_replace('%PID%', $pid, $sPlayerTemplate);

    return $sPlayerTemplate;
}

# Hooked on upload
function media_upload_lemonwhale() {
    $errors = array();
    $lwdata = array();
    $id = 0;


    if ( !empty($_POST) ) {
        //http://ljsp.lwcdn.com/api/video/embed.jsp?id=a46bc3c2-2f96-4b81-818f-03abfeb44d6e&pi=a07b0ff7-60de-457c-a232-c58f559fe176

        $lwdata = preg_split("/[\s,]+/",$_POST['url']);
        $lwdataFromUrl = preg_split("/[=&]+/",$_POST['url']);
        if(sizeof($lwdata)<3) {
            if(sizeof($lwdataFromUrl)<3){
                echo '<div class="error"><p>We can not publish your video. Maybe you did not copy the whole wordpress data. Clear and copy again.</p></div';
            }else{
                if(strpos($lwdataFromUrl[0],'lwcdn.com') !== false){
                    return media_send_to_editor('[lemonwhale vid="' . $lwdataFromUrl[1] . '" pid="' . $lwdataFromUrl[3] . '" width="' . $_POST['width'] . '" height="' . $_POST['height']. ']');
                }else{
                    echo '<div class="error"><p>We can not publish your video. Maybe you did not copy the whole wordpress data. Clear and copy again.</p></div';
                }
            }
        }
        else if(sizeof($lwdata)>3) {
            echo '<div class="error"><p>We can not publish your video. Maybe you pasted the wordpress data twice? Clear and paste again.</p></div';
        }
        else {
            return media_send_to_editor('[lemonwhale vid="' . $lwdata[0] . '" pid="' . $lwdata[1] . '" uid="' . $lwdata[2] . '" width="' . $_POST['width'] . '" height="' . $_POST['height']. '" theme="MAGAZINE"]');
        }
    }


    return wp_iframe( 'lemonwhale_popup', 'lemonwhale', $errors, $id );
}

function lemonwhale_popup()
{

    echo '<div style="padding: 30px;background-color:#FFFFFF;" ><div><img src="http://www.lemonwhale.com/wp-content/uploads/2013/10/Logo-300x98.png"></div>
			    <h3 class="media-title">Add a lemonwhale video to your blog post:</h3>
			    <p style="font-size:10px;">Copy the "link Url" from your lemonwhale account and paste it here below. Set the correct size and click on "insert video".</p>
			    <div style="margin-top:5px; padding:15px; border-width:1px; border-style:solid; border-color:#bbbbbb;"><form method="post">
			    <table border="0"><tr><td width="80px">Wordpress data</td><td><input type="text" name="url" size="55"/></td></tr>
			    <tr><td width="80px">Embed size: </td><td><input type="text" name="width" size="4" value="480"> x <input type="text" name="height" size="4" value="270"></td></tr></table>
			    <input type="submit" value="Insert video" /></form></div>
			    <p style="margin-top:20px; font-weight:bold;">Visit <a href="http://www.lemonwhale.com" target="_blank">lemonwhale.com</a>.</p>
		      </div>';
}


function lemonwhale_media_button()
{
    $out = "<a href='" . esc_url( get_upload_iframe_src('lemonwhale') ) . "' id='add_lemonwhale' class='thickbox' title='Add lemonwhale Video'><img src='" . esc_url( content_url('http://www.lemonwhale.com/wordpress-plugin/lemonwhale/LW-2.png') ) . "' alt='Add lemonwhale Video' /></a>";
    printf($out);
}

# Add a button above the wysiwyg editor
add_action('media_buttons', 'lemonwhale_media_button', 23);

# Add [lemonwhale id="23"]
add_shortcode('lemonwhale', 'lemonwhale');

# On post in lemonwhale form
add_action('media_upload_lemonwhale', 'media_upload_lemonwhale');
?>