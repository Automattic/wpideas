<?php
define( 'WPCOMIDEAS_PATH',  get_theme_root() . '/wpideas/' );
define( 'WPCOMIDEAS_URL', "/wp-content/themes/wpideas/" );
define( 'WPCOMIDEAS_JS_PATH',  WPCOMIDEAS_PATH . 'js/' );
define( 'WPCOMIDEAS_JS_URL', WPCOMIDEAS_URL . 'js/' );

// WP.com version uses the Post Ratings plugin to vote on ideas.
// require_once( WP_CONTENT_DIR . '/plugins/wp-postratings/wp-postratings.php' );

/**
* Loads the required JavaScript files.
*/
function wpcomideas_load_scripts() {
	if ( ! is_admin() ) {
		// Live search
		$livesearch_js = 'jquery.livesearch.js';
		wp_enqueue_script( 'livesearch', WPCOMIDEAS_JS_URL . $livesearch_js, false, filemtime( WPCOMIDEAS_JS_PATH . $livesearch_js ) );

		$wpcomideas_js = 'wpcomideas.js';
		wp_enqueue_script( 'wpcomideas', WPCOMIDEAS_JS_URL . $wpcomideas_js, false, filemtime( WPCOMIDEAS_JS_PATH . $wpcomideas_js ) );
	}
}
add_action( 'init', 'wpcomideas_load_scripts' );

/**
* Displays just the bookmarklet.
*/
function wpcomideas_bookmarklet( $query ) {
	$bookmarklet = ( !empty( $query->query_vars['suggestion']) ) ? $query->query_vars['suggestion'] : '';

	if ( !empty( $bookmarklet ) )
		add_filter( 'template_redirect', 'wpcomideas_bookmarklet_template' );
}
add_action( 'parse_query', 'wpcomideas_bookmarklet' );

/**
* Loads and returns the bookmarklet.php template/flow
*/
function wpcomideas_bookmarklet_template(  ) {
	include( "bookmarklet.php" );
	exit;
}

/*
* Register query parameters needed to access the bookmarklet and livesearch
* @return array An expanded list of query strings to watch for.
*/
function wpcomideas_query_vars($vars) {
	$vars[] = "suggestion";
	$vars[] = "livesearch";
	return $vars;
}
add_filter( 'query_vars',  'wpcomideas_query_vars' );

/**
* Edits the posts request query so that we can do a live search from the tags field.
* @return string Final live search query.
*/
function wpcomideas_livesearch_query_where( $where ) {
	global $wpdb, $wpquery;

	$terms = ( !empty( $_GET['livesearch']) ) ? $_GET['livesearch'] : '';

	$where .= " AND (";

	foreach( explode( ' ', $terms ) as $term ) {
		$where .= " post_title LIKE '%{$term}%' OR post_content LIKE '%{$term}%' OR ";
	}

	$where = substr( $where, 0, -3 );
	$where .= ")";
	return $where;
}

function wpcomideas_livesearch_query_orderby( $orderby ) {
	return $orderby;
}

/**
* Processes a live search request
*/
function wpcomideas_livesearch( $query ) {
	global $wpdb, $post;

	$term = ( !empty( $query->query_vars['livesearch']) ) ? $query->query_vars['livesearch'] : '';

	if ( !empty( $term ) ) {
		$url = esc_url( $_GET['u'] );
		$text = urlencode( $_GET['s'] );

		add_filter( 'posts_where', "wpcomideas_livesearch_query_where", $term );
		query_posts('');

		if ( have_posts() ) : ?>
			<p> <?php _e("Please review the following related postings for possible duplicates."); ?> </p>
			<p><a href="?suggestion=bookmarklet&amp;flow=new&amp;u=<?php echo $url; ?>&amp;s=<?php echo $text; ?>">&raquo; New</a></p>
			<hr />
		<?php while ( have_posts() ) : the_post(); $permalink = "?suggestion=bookmarklet&amp;flow=existing&amp;u={$url}&amp;id=" . get_the_ID();
		?>
			<?php $permalink = str_replace( 'http://http', 'http', $permalink ); ?>
			<h4><?php the_title(); ?> <a href="<?php echo $permalink; ?>">+1</a></h4>
			<div class="livesearch-excerpt"><?php the_excerpt(); ?>
			</div>

		<?php
		endwhile;
		else :
			echo 'test';
		endif;

		//Reset Query
		wp_reset_query();
		die;
	}
}
add_action( 'parse_query', 'wpcomideas_livesearch' );

/**
 * Registers sidebar widgets for the footer.
 */
register_sidebar( array(
	'name' => __( 'Footer Widgets', 'wpideas' ),
	'id' => 'sidebar-1',
	'description' => __( 'An optional widget area for your site footer', 'wpideas' ),
	'before_widget' => '<aside id="%1$s" class="widget %2$s">',
	'after_widget' => "</aside>",
	'before_title' => '<h3 class="widget-title">',
	'after_title' => '</h3>',
) );
