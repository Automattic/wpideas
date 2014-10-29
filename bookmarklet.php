<?php
/**
 * Bookmarklet template.
 *
 */
?>
<!DOCTYPE html>
<html <?php language_attributes();?>>
<head>
	<meta charset="<?php bloginfo('charset');?>" />
	<title>
	<?php bloginfo('name');?>
	</title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url');?>" />
	<?php wp_head();?>

	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#livesearch").bind("keypress", function(e) {
			if ( e.keyCode == 13 )
				return false;
		});

		jQuery('#livesearch').focus();
	});
	</script>
</head>

<body class="bookmarklet">



<div id="postbox">

<?php
global $current_user;
get_currentuserinfo();

if ( empty( $_GET['flow'] ) ) { ?>

	<h2>
		<?php _e('What would you like to propose?', 'wpcomideas');?>
	</h2>

	<div class="inputarea">

	<form id="new_post" name="new_post" method="get" action="" autocomplete="off">

		<input type="hidden" name="suggestion" value="bookmarklet" />

		<input id="livesearch" name="livesearch" type="text" value="" size="30" aria-required='true' />

		<div id="livesearch-results" class="hide-if-no-js"></div>
	</form>

	</div>

<?php } elseif ( $_GET['flow'] == 'new' && empty( $_GET['id'] ) ) {

	$url = esc_url( $_GET['u'] );
	$url = str_replace( 'http://http', 'http', $url );

	if ( !empty( $_POST[ 'post_title' ] ) && !empty( $_POST['post_content'] ) ) {
		// Create post object
		$post = array(
		'post_title' => $_POST['post_title'],
		'post_content' => $_POST['post_content'],
		'post_status' => 'publish',
		'tags_input'	=> $_POST['post_tags'],
		'post_category' => $_POST['post_categories'],
		);

		$post_id = wp_insert_post( $post );
		$content = esc_url( $_GET['u'] );

		if ( preg_match( '/ticketid=([0-9]+)/i', $content, $matches ) )
			$content = "<a href='{$content}'>#{$matches[1]}-t</a>";

		$data = array (
			'comment_post_ID' => $post_id,
			'comment_content' => $content,
			'comment_date' => current_time( 'mysql' ),
			'comment_approved' => 1,
			'user_id' => $current_user->ID,
		);

		wp_insert_comment( $data );

		echo "<a href='" . get_permalink( $post_id ) . "'>View</a> | <a href='javascript:window.close();'>Close</a>";
		die;
	}

	?>

	<h2>
		<?php _e('What would you like to propose?', 'wpcomideas');?>
	</h2>

	<div class="inputarea">

	<form id="new_post" name="new_post" method="post" action="?suggestion=bookmarklet&amp;flow=new&u=<?php echo urlencode($url);?>">
		<input type="hidden" name="suggestion" value="bookmarklet" />

		<p><label for="post_title"><strong>Title</strong></label></p>
		<p> <input type="text" id="post_title" name="post_title" value="<?php echo esc_attr($_POST['post_title']);?>" /> </p>

		<p><label for="post_content"><strong>Description</strong></label></p>
		<?php $content = ( empty($_POST['post_content'])) ? urldecode($_GET['s']) : $_POST['post_content'];?>
		<p><textarea id="post_content" name="post_content"><?php echo $content;?></textarea></p>

		<p class="postbox-categories">
			<label for="postcategories"><strong><?php _e('Area', 'ideapress');?></strong></label> <br />
			<?php
			$categories = get_categories();

			foreach ($categories as $cat) {
				echo "<input type='checkbox' name='post_categories[]' value='" . $cat -> term_id . "'>&nbsp;" . $cat -> cat_name . " <br />";
			}
			?>
		</p>

		<p><label for="post_tags"><strong>Tag it</strong></label></p>
		<p> <input type="text" id="post_tags" name="post_tags" value="<?php echo esc_attr($_POST['post_tags']);?>" /> </p>

		<p><label for="post_title"><strong>URL</strong></label></p>

		<p><?php echo $url;?></p>

		<br />
		<p><input type="submit" value="Create" /></p>
	</form>

	</div>

<?php } elseif ( $_GET['flow'] == "existing" && !empty( $_GET['id'] ) ) {
		$time = current_time( 'mysql' );
		$content = esc_url( $_GET['u'] );
		$content = str_replace( 'http://http', 'http', $content );

		if ( preg_match( '/ticketid=([0-9]+)/i', $content, $matches ) )
			$content = "<a href='{$content}'>#{$matches[1]}-t</a>";

		$data = array(
			'comment_post_ID' => $_GET['id'],
			'comment_content' => $content,
			'comment_date' => $time,
			'comment_approved' => 1,
			'user_id' => $current_user->ID,
		);

		wp_insert_comment($data);

		echo "<a href='" . get_permalink( $_GET['id'] ) . "'>View</a> | <a href='javascript:window.close();'>Close</a>";
		die;
}
?>