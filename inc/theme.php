<?php

# Register custom image sizes.
add_action( 'init', 'hyper_base_register_image_sizes', 5 );

# Register custom menus.
add_action( 'init', 'hyper_base_register_menus', 5 );

# Register custom layouts.
add_action( 'hybrid_register_layouts', 'hyper_base_register_layouts' );

# Register sidebars.
add_action( 'widgets_init', 'hyper_base_register_sidebars', 5 );

# Add custom scripts and styles
add_action( 'wp_enqueue_scripts', 'hyper_base_enqueue_scripts', 5 );
add_action( 'wp_enqueue_scripts', 'hyper_base_enqueue_styles',  5 );

add_filter( 'hybrid_attr_sidebar', 'hyper_base_sidebar_subsidiary_class', 10, 2 );

# Embed wrap.
add_filter( 'embed_oembed_html', 'hyper_base_maybe_wrap_embed', 10, 2 );
/**
 * Registers custom image sizes for the theme.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function hyper_base_register_image_sizes() {

	// Sets the 'post-thumbnail' size.
	//set_post_thumbnail_size( 150, 150, true );
}

/**
 * Registers nav menu locations.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function hyper_base_register_menus() {
	register_nav_menu( 'primary',    esc_html_x( 'Primary',    'nav menu location', 'hyper-base' ) );
	register_nav_menu( 'secondary',  esc_html_x( 'Secondary',  'nav menu location', 'hyper-base' ) );
	register_nav_menu( 'subsidiary', esc_html_x( 'Subsidiary', 'nav menu location', 'hyper-base' ) );
}

/**
 * Registers layouts.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function hyper_base_register_layouts() {

	hybrid_register_layout( '1c',   array( 'label' => esc_html__( '1 Column',                     'hyper-base' ), 'image' => '%s/images/layouts/1c.png'   ) );
	hybrid_register_layout( '2c-l', array( 'label' => esc_html__( '2 Columns: Content / Sidebar', 'hyper-base' ), 'image' => '%s/images/layouts/2c-l.png' ) );
	hybrid_register_layout( '2c-r', array( 'label' => esc_html__( '2 Columns: Sidebar / Content', 'hyper-base' ), 'image' => '%s/images/layouts/2c-r.png' ) );
}

/**
 * Registers sidebars.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function hyper_base_register_sidebars() {

	hybrid_register_sidebar(
		array(
			'id'          => 'primary',
			'name'        => esc_html_x( 'Primary', 'sidebar', 'hyper-base' ),
			'description' => esc_html__( 'Add sidebar description.', 'hyper-base' )
		)
	);

	hybrid_register_sidebar(
		array(
			'id'          => 'subsidiary',
			'name'        => esc_html_x( 'Subsidiary', 'sidebar', 'hyper-base' ),
			'description' => esc_html__( 'Add sidebar description.', 'hyper-base' )
		)
	);
}

/**
 * Load scripts for the front end.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function hyper_base_enqueue_scripts() {
}

/**
 * Load stylesheets for the front end.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function hyper_base_enqueue_styles() {

	// Load one-five one style.
	// wp_enqueue_style( 'hyper-base-five' );

	wp_register_style( 'hyper-base-six',        trailingslashit( get_template_directory_uri() ) . "css/one-six.css" );

	//wp_enqueue_style( 'hyper-base-six' );

	wp_register_style( 'hybrid-fa',        trailingslashit( get_template_directory_uri() ) . "fonts/fa/css/font-awesome.min.css" );

	wp_enqueue_style( 'hybrid-fa' );

	// Load gallery style if 'cleaner-gallery' is active.
	if ( current_theme_supports( 'cleaner-gallery' ) )
		wp_enqueue_style( 'hybrid-gallery' );

	// Load parent theme stylesheet if child theme is active.
	if ( is_child_theme() )
		wp_enqueue_style( 'hybrid-parent' );

	// Load active theme stylesheet.
	wp_enqueue_style( 'hybrid-style' );

	//wp_enqueue_style( 'dashicons' );
}


function hyper_base_sidebar_subsidiary_class( $attr, $context ) {

	if ( 'subsidiary' === $context ) {
		global $sidebars_widgets;

		if ( is_array( $sidebars_widgets ) && !empty( $sidebars_widgets[ $context ] ) ) {

			$count = count( $sidebars_widgets[ $context ] );

			if ( 1 === $count )
				$attr['class'] .= ' sidebar-col-1';

			elseif ( ! ( $count % 3 ) || $count % 2 )
				$attr['class'] .= ' sidebar-col-3';

			elseif ( ! ( $count % 2 ) )
				$attr['class'] .= ' sidebar-col-2';
		}
	}

	return $attr;
}


function hyper_base_maybe_wrap_embed( $html, $url ) {

	if ( ! $html || ! is_string( $html ) || ! $url )
		return $html;

	$do_wrap = false;

	$patterns = array(
		'#http://((m|www)\.)?youtube\.com/watch.*#i',
		'#https://((m|www)\.)?youtube\.com/watch.*#i',
		'#http://((m|www)\.)?youtube\.com/playlist.*#i',
		'#https://((m|www)\.)?youtube\.com/playlist.*#i',
		'#http://youtu\.be/.*#i',
		'#https://youtu\.be/.*#i',
		'#https?://(.+\.)?vimeo\.com/.*#i',
		'#https?://(www\.)?dailymotion\.com/.*#i',
		'#https?://dai.ly/*#i',
		'#https?://(www\.)?hulu\.com/watch/.*#i',
		'#https?://wordpress.tv/.*#i',
		'#https?://(www\.)?funnyordie\.com/videos/.*#i',
		'#https?://vine.co/v/.*#i',
		'#https?://(www\.)?collegehumor\.com/video/.*#i',
		'#https?://(www\.|embed\.)?ted\.com/talks/.*#i' 
		);

	$patterns = apply_filters( 'hyper_base_maybe_wrap_embed_patterns', $patterns );

	foreach ( $patterns as $pattern ) {

		$do_wrap = preg_match( $pattern, $url );

		if ( $do_wrap )
			return hyper_base_wrap_embed_html( $html );
	}

	return $html;
}

function hyper_base_wrap_embed_html( $html ) {

	return $html && is_string( $html ) ? sprintf( '<div class="embed-wrap">%s</div>', $html ) : $html;
}

//add_action('wp_head','hyperbase_inline_ss');
function hyperbase_inline_ss(){
	?>
<style type="text/css">
@charset "UTF-8";
html,body,div,span,object,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,tt,var,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,main,summary,time,mark,audio,video{margin:0;padding:0;vertical-align:baseline;outline:none;font-size:100%;background:transparent;border:none;text-decoration:none}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section,main{display:block}html{font-size:16px}body{line-height:1.618}h1,h2,h3,h4,h5,h6{font-style:normal;font-weight:normal}h1{font-size:1.75em}h2{font-size:1.625em}h3{font-size:1.5em}h4{font-size:1.375em}h5{font-size:1.25em}h6{font-size:1.125em}p:empty{margin:0;padding:0;line-height:0}ul{list-style:square}ul,ol{margin-left:1.618em;list-style-position:inside}ol.disc,ul.disc{list-style:disc}ol.armenian,ul.armenian{list-style:armenian}ol.circle,ul.circle{list-style:circle}ol.cjk-ideographic,ul.cjk-ideographic{list-style:cjk-ideographic}ol.decimal,ul.decimal{list-style:decimal}ol.decimal-leading-zero,ul.decimal-leading-zero{list-style:decimal-leading-zero}ol.georgian,ul.georgian{list-style:georgian}ol.hebrew,ul.hebrew{list-style:hebrew}ol.hiragana,ul.hiragana{list-style:hiragana}ol.hiragana-iroha,ul.hiragana-iroha{list-style:hiragana-iroha}ol.katakana,ul.katakana{list-style:katakana}ol.kataka-iroha,ul.katakana-iroha{list-style:katakana-iroha}ol.lower-alpa,ul.lower-alpha{list-style:lower-alpha}ol.lower-greek,ul.lower-greek{list-style:lower-greek}ol.lower-latin,ul.lower-latin{list-style:lower-latin}ol.lower-roman,ul.lower-roman{list-style:lower-roman}ol.none,ul.none{list-style:none}ol.square,ul.square{list-style:square}ol.upper-alpha,ul.upper-alpha{list-style:upper-alpha}ol.upper-latin,ul.upper-latin{list-style:upper-latin}ol.upper-roman,ul.upper-roman{list-style:upper-roman}ol.initial,ul.initial{list-style:initial}ul ul,ol ol,ul ol,ol ul{margin-bottom:0}.comment-content ul,.comment-content ol{margin-bottom:1.618em}.comment-content li ul,.comment-content li ol{margin-bottom:0}dt{margin-left:0.618em;font-weight:bold}dd{margin-left:1.618em}dd + dd{margin-top:0.618em}dd + dt{margin-top:1.618em}*[dir="ltr"]{direction:ltr;unicode-bidi:embed}bdo[dir="ltr"]{direction:ltr;unicode-bidi:bidi-override}center{text-align:center}b,strong{font-weight:700}b b,strong strong{font-weight:400}i,em,mark,cite{font-style:italic}i i,em em,mark mark,cite cite{font-style:normal}abbr[title],acronym[title],time[title]{cursor:help}abbr,acronym{border-bottom:1px dotted}acronym{text-transform:uppercase}big{font-size:1.125em}small,sup,sub{font-size:0.8125em}sup{vertical-align:baseline;position:relative;bottom:0.3em}sub{vertical-align:baseline;position:relative;top:0.3em}address{font-style:italic;margin:0 0 1.618em}blockquote{margin:0 1.618em;font-style:italic}blockqoute i,blockquote em,blockquote cite{font-style:normal}.en-us q{quotes:'\201C' '\201D' '\2018' '\2019'}a{cursor:pointer}pre{overflow:auto;word-wrap:normal;font-family:monospace;padding:1em}code{direction:ltr;text-align:left;font-family:monospace}ins,dfn{font-style:italic;text-decoration:none;border-bottom:1px solid}del,s,strike{text-decoration:line-through}object{margin-bottom:1.5rem}input,textarea,button,select{font-family:inherit}input,textarea{box-sizing:border-box}:focus{outline:none}label,button,input[type="submit"],input[type="reset"],input[type="button"]{cursor:pointer}table{border-collapse:collapse;border-spacing:0}th,td{text-align:left}hr{height:1px;margin-top:0.809em;margin-bottom:0.809em;background:currentColor;border:none}img{max-width:100%;height:auto}img.wp-smiley,img.emoji{display:inline;box-shadow:none;max-height:1em;width:1em;margin:0 0.07em;padding:0;border:none;background:transparent}.gallery{display:block;text-align:center;margin-bottom:1.618em}.aligncenter,.alignright,.alignleft{display:block;margin:0 auto 1.618em}p .aligncenter,p .alignright,p .alignleft{margin-bottom:0}@media only screen and (min-width:480px){.alignleft{float:left;margin-right:1.618em}.alignright{float:right;margin-left:1.618em}}.alignnone{float:none}.clear{clear:both}img.alignleft,img.alignright{display:inline}blockquote.alignleft,blockquote.alignright{width:33%}.wp-audio-shortcode,.wp-video-shortcode,audio,video,object,embed,iframe{margin-bottom:1.618em}.wp-audio-shortcode,.wp-video-shortcode,audio,video{display:block}.show-if-js{display:none}.screen-reader-text,.assistive-text{position:absolute;top:-9999em;left:-9999em}::-moz-selection{color:#fff;background-color:#c00}::selection{color:#fff;background-color:#c00}.entry-content:after{content:".";display:block;height:0;clear:both;visibility:hidden}h1,h2,h3,h4,h5,h6,dl,pre,table,ol,ul,p,figure,blockquote{margin-top:0;margin-bottom:0}* + h1,* + h2,* + h3,* + h4,* + h5,* + h6,* + dl,* + pre,* + table,* + ol,* + ul,* + p,* + figure,* + blockquote{margin-top:1.618em}ul ul,ol ul,ol ol,ol ul{margin-top:0}html{font-family:"Times New Roman", Times, serif;width:100%}@media only screen and (-webkit-min-device-pixel-ratio:1.3),only screen and (-o-min-device-pixel-ratio:1.3),only screen and (min-resolution:120dpi){html{font-family:"ss_Cormorant Garamond", "Times New Roman", Times, serif}}body{line-height:1.618em;border-top:3px solid #c00;border-bottom:3px double #c00;overflow-x:hidden;width:100%}a{color:#c00;transition:all 0.5s linear}a:visited,a:hover{color:#900}h1{line-height:1.2}h2{line-height:1.2}h3{line-height:1.3}h4{line-height:1.3}h5{line-height:1.5}h6{line-height:1.5}ol h1,ul h1,ol h2,ul h2,ol h3,ul h3,ol h4,ul h4,ol h5,ul h5,ol h6,ul h6{font-size:1rem}table{width:100%}th,td{border:1px dotted;padding:0.5em 1em;-moz-box-sizing:border-box;box-sizing:border-box}label,button,input,textarea,select{font-size:inherit}label,caption,figcaption{font-variant:small-caps}input[type="number"],input[type="date"],input[type="datetime"],input[type="datetime-local"],input[type="email"],input[type="month"],input[type="password"],input[type="search"],input[type="tel"],input[type="text"],input[type="time"],input[type="url"],input[type="week"],textarea,select{width:100%;box-sizing:border-box;display:block}button,input[type="button"],input[type="reset"],input[type="submit"]{font-variant:small-caps;background-color:#c00;color:#fff;transition:all 0.5s linear}button:hover,input[type="button"]:hover,input[type="reset"]:hover,input[type="submit"]:hover{background-color:#900}input,textarea,button,select{color:inherit;border:1px solid;padding:0.5em 1em}input:focus,textarea:focus{border:1px solid #c00}.wrap,#content,.sidebar{box-sizing:border-box}.wrap{padding:20px;margin:auto}@media only screen and (max-width:480px){.site-title{text-align:center}}@media only screen and (min-width:800px){.layout-1c .wrap{width:800px}}@media only screen and (min-width:900px){.layout-1c .wrap{padding:40px 50px}.layout-1c .site-header .wrap{margin-top:50px}.layout-1c .site-footer .wrap{margin-bottom:50px}}.layout-2c-l,.layout-2c-r{}@media only screen and (min-width:700px){.layout-2c-l .wrap,.layout-2c-r .wrap{width:700px;padding-top:40px;padding-bottom:40px}.layout-2c-l .site-header .wrap,.layout-2c-r .site-header .wrap{margin-top:50px}.layout-2c-l .site-footer .wrap,.layout-2c-r .site-footer .wrap{margin-bottom:50px}}@media only screen and (min-width:800px){.layout-2c-l .wrap,.layout-2c-r .wrap{padding-left:50px;padding-right:50px;width:800px}.layout-2c-l .wrap #content,.layout-2c-r .wrap #content{width:700px;float:left}.layout-2c-l #inner .wrap:after,.layout-2c-r #inner .wrap:after{content:".";display:block;height:0;clear:both;visibility:hidden}}@media only screen and (min-width:1100px){.layout-2c-l .wrap,.layout-2c-r .wrap{width:1100px;padding-right:0}.layout-2c-l .sidebar-primary,.layout-2c-r .sidebar-primary{float:right;width:300px}}@media only screen and (min-width:1150px){.layout-2c-l .wrap,.layout-2c-r .wrap{padding-right:50px;width:1150px}}@media only screen and (min-width:800px){.layout-2c-r #content{float:right}}@media only screen and (min-width:1100px){.layout-2c-r .wrap{padding-left:0;padding-right:50px}}@media only screen and (min-width:1150px){.layout-2c-r .wrap{padding-left:50px;width:1150px}}.menu-primary ul,.menu-secondary ul,.menu-subsidiary ul{list-style:none;margin:0}.menu-primary ul ul,.menu-secondary ul ul,.menu-subsidiary ul ul{opacity:0;position:absolute;visibility:hidden;width:230px;top:38px;margin-left:0.5em;margin-top:0.5em;-webkit-transition:all 0.5s ease-in-out;-moz-transition:all 0.5s ease-in-out;-o-transition:all 0.5s ease-in-out;transition:all 0.5s ease-in-out}.menu-primary ul ul ul,.menu-secondary ul ul ul,.menu-subsidiary ul ul ul{left:230px;top:0}.menu-primary li,.menu-secondary li,.menu-subsidiary li{display:inline-block;position:relative}.menu-primary li li,.menu-secondary li li,.menu-subsidiary li li{display:block}.menu-primary li a,.menu-secondary li a,.menu-subsidiary li a{display:block;background-color:#c00;color:#fff;padding:0.38198em 1em}.menu-primary li a:hover,.menu-secondary li a:hover,.menu-subsidiary li a:hover{background-color:#900}.menu-primary li a:empty,.menu-secondary li a:empty,.menu-subsidiary li a:empty{display:none}.menu-primary li:hover > ul,.menu-secondary li:hover > ul,.menu-subsidiary li:hover > ul{opacity:1;visibility:visible;margin-left:0;margin-top:0}#menu-primary-items > li > a,#menu-secondary-items > li > a{color:#000;background-color:transparent}#menu-primary-items > li > a:hover,#menu-secondary-items > li > a:hover{color:#c00}#menu-primary-items > li.current-menu-item > a,#menu-secondary-items > li.current-menu-item > a{color:#c00}.menu-primary li.menu-item-has-children a:after,.menu-secondary li.menu-item-has-children a:after{content:"\f107";font-family:FontAwesome;font-style:normal;font-weight:normal;font-variant:normal;line-height:1;color:#c00;vertical-align:middle;padding-right:0.618em;display:inline-block;margin-left:5px;color:inherit}.menu-primary li.menu-item-has-children li.menu-item-has-children a:after,.menu-secondary li.menu-item-has-children li.menu-item-has-children a:after{content:"\f105";font-family:FontAwesome;font-style:normal;font-weight:normal;font-variant:normal;line-height:1;color:#c00;vertical-align:middle;padding-right:0.618em;display:inline-block;color:#fff}.menu-primary li a:only-child::after,.menu-secondary li a:only-child::after{content:""}#menu-secondary .wrap{border-top:1px solid;border-bottom:1px solid}body .menu-primary + .site-header .wrap{margin-top:0}body .menu-primary .wrap{padding-top:0;padding-bottom:20px}.menu-secondary{margin-top:40px}.menu-secondary .wrap{padding-top:0;padding-bottom:0}.layout-1c #menu-primary-items,.layout-1c #menu-secondary-items,.layout-1c #menu-subsidiary-items{display:table;margin:auto}.site-footer .wrap{border-top:1px dotted}.site-title{font-size:6.25em;margin:0;line-height:1}.site-description{font-size:1.375em;margin:0.618em 0 0;line-height:1}.archive-title,.entry-title{font-size:2.618em}.entry-title a{color:inherit}.entry-title a:hover{color:#c00}.archive-header{padding-bottom:20px;margin-bottom:20px;border-bottom:1px dotted}.archive-title{font-weight:bold}.archive-description{margin-top:1em;font-style:italic}.menu-sub-terms ul{list-style:none;margin-top:1em;margin-left:0}.menu-sub-terms li{display:inline-block}.menu-sub-terms li + li{margin:0.5em 1em 0 0}.menu-sub-terms a{display:inline-block;background-color:#c00;color:#fff;padding:0.5em 1em;border-radius:100px;border-radius:100px;border:1px solid transparent}.menu-sub-terms a:hover{background-color:#fff;color:#c00;border-color:#c00}.entry-byline{margin-top:0.381em}.img-featured{display:block;margin-top:1.618em}.entry-byline,.entry-byline a,.entry-byline a:before,.entry-byline .entry-author a:before,.entry-byline .entry-published:before{color:currentColor}.entry-byline a:hover{color:#c00}.entry-content,.entry-summary{margin:1em 0}article.entry{margin:1em 0;padding-bottom:1em;border-bottom:1px dotted}.entry-footer .entry-terms{font-variant:small-caps;font-weight:bold}.entry-footer .entry-terms a{border-bottom:1px dotted #c00;font-weight:normal}.entry-footer .entry-terms a:hover{border-bottom:1px dotted transparent;background-color:#c00;color:#fff}blockquote{padding-left:1.618em;border-left:1px dotted #c00}.page-links{font-variant:small-caps;font-weight:bold}.page-links .page-numbers{display:inline-block;padding:0.618em}.page-links a{font-weight:normal;display:inline-block;border-radius:100px;padding:0.381em 1em;background-color:#c00;color:#fff}.page-links a:hover{background-color:#900}.loop-nav .next,.loop-nav .prev{width:45%;float:left;font-weight:bold;font-variant:small-caps}.loop-nav .next a,.loop-nav .prev a,.loop-nav .next a:visited,.loop-nav .prev a:visited{font-variant:normal;font-weight:normal;color:#c00}.loop-nav .next a:hover,.loop-nav .prev a:hover{background-color:#c00;color:#fff}.loop-nav .next{float:right;text-align:right}.loop-nav:after{content:".";display:block;height:0;clear:both;visibility:hidden}#comments-template{margin-top:1.618em}#comments-number,#reply-title{font-size:1em;font-variant:small-caps;margin-top:1.618em}.comment-list{margin:1em 0 0;list-style:none;display:block}.comment-list li.comment{display:block;clear:both;padding-top:1em}.comment-list .avatar{float:left;margin-right:1em}.comment-list .comment-content{padding-bottom:1em;padding-top:1em;border-bottom:1px solid #c00;clear:both}.comment-list .comment-meta{font-variant:small-caps;text-transform:lowercase}.comment-list .comment-author{font-weight:bold;font-style:normal;text-transform:none}.comment-list .comment-reply-link{display:inline-block;background-color:#c00;color:#fff;padding:0.381em 0.618em;float:right}.comment-list .comment-reply-link:hover{background-color:#900}.comment-list .children{margin-left:2.618em}.comment-list .children article{padding-left:2.618em;border-left:1px dotted}.comment-list .pingback{padding-bottom:1em;margin-bottom:1em;border-bottom:1px dotted}.comment-list .pingback .comment-author{font-weight:normal;font-variant:normal}.comment-list .pingback a{color:inherit}.sidebar{font-size:0.875em;line-height:1.5}.sidebar .widget{margin-top:0}.sidebar .widget + .widget{margin-top:2.618em}.sidebar .widget > *{margin-top:0}.sidebar .widget .widget-title{margin-bottom:0.618em}.sidebar .widget > ol,.sidebar .widget > ul{margin-left:0}.widget-title{font-size:1em}.sidebar ul.menu{margin-left:0;list-style:none}.sidebar ul.menu ul{list-style:none}.sidebar ul.menu a,.sidebar ul.menu a:visited{display:inline-block;padding:0.5em 1em;border-bottom:1px dotted #000;color:#c00}.sidebar ul.menu a:hover{border-bottom:1px dotted #c00}.sidebar ul.menu a:empty{display:none}#wp-calendar td,#wp-calendar th{padding:5px 2%;vertical-align:middle;text-align:center}#wp-calendar td a{display:block;background-color:#c00;color:#fff;border-radius:500px}#wp-calendar td a:hover,#wp-calendar td a:visited{background-color:#fff;color:#c00}.widget_rss .widget-title a.rsswidget img{display:none}.sidebar-subsidiary .wrap{border-top:1px solid}.menu-subsidiary .wrap{border-top:1px dotted}.site-footer{text-align:center}.widget-title:before{content:'\f02e'}.menu-sub-terms ul:before{content:'\f04b'}.widget_archive .widget-title:before{content:'\f017'}.widget_calendar .widget-title:before{content:'\f073'}.entry-footer .category:before,.widget_categories .widget-title:before{content:'\f07c'}.widget_nav_menu .widget-title:before{content:'\f03a'}.widget_meta .widget-title:before{content:'\f05a'}.widget_pages .widget-title:before{content:'\f15b'}.widget_recent_comments .widget-title:before{content:'\f086'}.widget_recent_entries .widget-title:before{content:'\f14b'}.widget_rss .widget-title:before{content:'\f09e'}.entry-footer .post_tag:before,.widget_tag_cloud .widget-title:before{content:'\f02c'}.widget_text .widget-title:before{content:'\f249'}.menu-sub-terms ul:before,.entry-footer .category:before,.entry-footer .post_tag:before,.widget-title:before,.widget_calendar .widget-title:before,.widget_categories .widget-title:before,.widget_nav_menu .widget-title:before,.widget_meta .widget-title:before,.widget_pages .widget-title:before,.widget_recent_comments .widget-title:before,.widget_recent_entries .widget-title:before,.wisget_rss .widget-title:before,.widget_tag_cloud .widget-title:before,.widget_text .widget-title:before,.widget_archive .widget-title:before{font-family:FontAwesome;font-style:normal;font-weight:normal;font-variant:normal;line-height:1;color:#c00;vertical-align:middle;padding-right:0.618em;display:inline-block}.aligncenter,.alignright,.alignleft{margin-top:1.618em;margin-bottom:1.618em}p .aligncenter,p .alignright,p .alignleft{margin-top:1.618em;margin-bottom:1.618em}.gallery{margin-top:1.618em}.wp-caption{height:auto;max-width:100%;text-align:center}.embed-wrap{position:relative;margin-bottom:1.5rem;padding-bottom:56.25%;padding-top:30px;height:0;overflow:hidden}.embed-wrap iframe,.embed-wrap object,.embed-wrap embed{position:absolute;top:0;left:0;width:100%;max-width:100%;height:100%}.gallery-icon img{box-shadow:1px 1px 3px rgba(0, 0, 0, .50);border:15px solid #fff}span.comments-link{display:none}.layout-1c .site-header,.layout-1c .entry-title,.layout-1c .entry-header,.layout-1c .archive-header{text-align:center}.site-title a{border-bottom:1px dotted transparent}.site-title a:hover{border-bottom-color:#c00}.site-title,.site-description,.widget-title,.archive-title,.menu-primary a,.menu-secondary a,.menu-subsidiary a,.menu-sub-terms a{font-variant:small-caps}.menu-primary a,.menu-secondary a,.menu-subsidiary a{text-transform:lowercase}h1,h2,h3,h4,h5,h6,blockquote body{}.clearfix::after{content:".";display:block;height:0;clear:both;visibility:hidden}
</style>
	<?php
}