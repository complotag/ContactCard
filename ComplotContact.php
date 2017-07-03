<?php
	/*
	Plugin Name: Contact Card
	Plugin URI: poiz.me
	Description: An Minimal Plugin/Interface for parsing & displaying + automating Contact-Card (via short-code).
	Author:  Poiz Campbell
	Author URI: http://poiz.me
	Version: 1.0.0
	*/

/*
➤  WP_CONTENT_URL   : Full URL to wp-content
➤  WP_CONTENT_DIR   : The server path to the wp-content directory
➤  WP_PLUGIN_URL    : Full URL to the plugins directory
➤  WP_PLUGIN_DIR    : The server path to the plugins directory
➤  WP_LANG_DIR      : The server path to the language directory
*/

use Code\ComplotPluginHelper        as CPH;
use Code\MBoxHelper                 as MBH;
use Code\vCard;

if(!class_exists("MBoxHelper")){
	require_once __DIR__ . "/Code/MBoxHelper.php";
}

if(!class_exists("vCard")){
	require_once  __DIR__ . "/Code/vCard.class.php";
}

if(!class_exists("ComplotPluginHelper")){
	require_once  __DIR__ . "/Code/ComplotPluginHelper.php";
}

if(!function_exists("prepareUpCodePoolLibraries")){
	function prepareUpCodePoolLibraries() {
		require_once __DIR__    . "/_DEFINITIONS_.php";
		$path1  = SITE_ROOT      . '/wp-admin';
		$path2  = SITE_ROOT      . '/wp-includes';
		$path3  = SITE_ROOT      . '/wp-content';
		$path4  = SITE_ROOT      . '/wp-admin/includes';
		require_once __DIR__    . "/vendor/autoload.php";
		require_once $path4     . "/plugin.php";
		set_include_path(
			get_include_path()  .    PATH_SEPARATOR . $path1 .
			PATH_SEPARATOR . $path2 .
			PATH_SEPARATOR . $path3 .
			PATH_SEPARATOR . $path4
		);
	}
}

prepareUpCodePoolLibraries();





if(isset($_GET['vcard']) && !is_admin()){
	$wwUID      = $_GET['vcard'];
	$objWW      = get_post($wwUID);

	if($objWW && !empty($objWW)) {
		$wwID       = $objWW->ID;
		$name       = get_the_title($wwID);
		$arrNames   = explode(" ", $name);
		$lName      = array_pop($arrNames);
		$fName      = implode(" ", $arrNames);
		$telNum     = ($ot = get_post_meta($wwID, 'cpl_contacts_telephone', true))?$ot:null;
		$officeTel  = (stristr($telNum, "+41 62"))? $telNum : NULL;
		$mobileTel  = (!$officeTel) ? $telNum : NULL;
		$pix_location = ABSPATH .  str_replace(get_site_url(), "", get_the_post_thumbnail_url($objWW, "post-thumbnail"));

		$vcPayload = [
			'display_name'          => get_the_title($wwID),
			'first_name'            => $fName,
			'last_name'             => $lName,
			'additional_name'       => NULL,
			'name_prefix'           => NULL,
			'name_suffix'           => NULL,
			'nickname'              => NULL,
			'title'                 => NULL,
			'role'                  => get_post_meta($wwID, 'cpl_contacts_diploma', true),
			'department'            => NULL,
			'company'               => "Woodwork AG",
			'work_po_box'           => NULL,
			'work_extended_address' => NULL,
			'work_address'          => NULL,
			'work_city'             => NULL,
			'work_state'            => NULL,
			'work_postal_code'      => NULL,
			'work_country'          => NULL,
			'home_po_box'           => NULL,
			'home_extended_address' => NULL,
			'home_address'          => NULL,
			'home_city'             => NULL,
			'home_state'            => NULL,
			'home_postal_code'      => NULL,
			'home_country'          => NULL,
			'office_tel'            => $officeTel,
			'home_tel'              => NULL,
			'cell_tel'              => $mobileTel,
			'fax_tel'               => NULL,
			'pager_tel'             => NULL,
			'email1'                => get_post_meta($wwID, 'cpl_contacts_email', true),
			//'email2' => get_post_meta($wwID, 'cpl_contacts_email', true),
			'url'                   => get_site_url(),
			'photo'                 => $pix_location,       //get_the_post_thumbnail_url($objWW, "post-thumbnail"),
			'birthday'              => NULL,
			'timezone'              => NULL,
			'sort_string'           => NULL,
			'note'                  => NULL,
		];

		createCplVCardData($vcPayload);
		exit;
	}
}

	$cplContactPluginURL  = plugin_dir_url(__FILE__);
	$GLOBALS['CONTACT_CARD_PLUGIN']    = [
		"plg_url"       => $cplContactPluginURL,
		"plg_dir"       => plugin_dir_path(__FILE__),
		"css_url"       => $cplContactPluginURL . "assets/css/",
		"js_url"        => $cplContactPluginURL . "assets/js/",
		"img_url"       => $cplContactPluginURL . "assets/img/",
	];
	/********************************************************************/
	/**********************GLOBAL VARIABLES: BEGIN***********************/
	/********************************************************************/

	/********************************************************************/
	/***********************GLOBAL VARIABLES: END************************/
	/********************************************************************/



	/********************************************************************/
	/*********************CUSTOM POST TYPES: BEGIN***********************/
	/********************************************************************/
	function ContactCardPostTypes() {
		register_post_type('cpl_contacts',
		  array(
			'label'                 => __('Contact Cards',                   'cpl'),
			'description'           => __('Contact Cards',                   'cpl'),
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'capability_type'       => 'post',
			'hierarchical'          => true,
			'rewrite'               => array('slug' => 'kontakt', 'with_front'=>true),
			'query_var'             => true,
			'has_archive'           => true,
			'publicly_queryable'    => false,
			'menu_icon'             => "dashicons-id",     //"dashicons-chart-pie",
			'exclude_from_search'   => true,
			'supports'              => array('title','editor','revisions', 'thumbnail', 'page-attributes'),
			'taxonomies'            => array("cpl_team_group"),
			'labels'                => array(
			  'name'                => __('Contact Cards',                      'cpl'),
			  'singular_name'       => __('Contact Card',                       'cpl'),
			  'menu_name'           => __('Contact Card',                       'cpl'),
			  'add_new'             => __('Neue Contact',                       'cpl'),
			  'add_new_item'        => __('Neue Contact Card',                  'cpl'),
			  'edit'                => __('Edit',                               'cpl'),
			  'edit_item'           => __('Edit Contact Card',                  'cpl'),
			  'new_item'            => __('Neue Contact',                       'cpl'),
			  'view'                => __('View Contact Card',                  'cpl'),
			  'view_item'           => __('View Contact Card',                  'cpl'),
			  'search_items'        => __('Search Contact Card',                'cpl'),
			  'not_found'           => __('Keine Contact Card gefunden',        'cpl'),
			  'not_found_in_trash'  => __('No Contact Card found in Trash',     'cpl'),
			  'parent'              => __('Parent Contact Card',                'cpl'),
			),
		  )
		);

		register_taxonomy('cpl_team_group',array(
		  0 => 'cpl_contacts',
		),array( 'hierarchical' => true, 'label' => 'Team Group','show_ui' => true, 'query_var' => true,'rewrite' => array('slug' => 'team-group'),'singular_label' => 'Team Group') );
	}

	function callMetaBoxHelperContactCards() {
		$arrName            = array(
			"domain"               => "cpl",
			"post_type"            => "cpl_contacts",
			"priority"             => "low",
			"headline"             => "Vorname / Name",
			"nonce_key"            => "cpl_contacts_edit",
			"field_key"            => "cpl_contacts_name",
			"field_type"           => "text",
			"field_name"           => "cpl_contacts_name",
			"location"             => "side",
			"field_description"    => "Vorname / Name",
		);

		$arrEmail           = array(
			"domain"               => "cpl",
			"post_type"            => "cpl_contacts",
			"priority"             => "low",
			"headline"             => "E-Mail",
			"nonce_key"            => "cpl_contacts_edit",
			"field_key"            => "cpl_contacts_email",
			"field_type"           => "text",
			"field_name"           => "cpl_contacts_email",
			"location"             => "side",
			"field_description"    => "E-Mail",
		);

		$arrTelephone       = array(
			"domain"               => "cpl",
			"post_type"            => "cpl_contacts",
			"priority"             => "low",
			"headline"             => "Telefon",
			"nonce_key"            => "cpl_contacts_edit",
			"field_key"            => "cpl_contacts_telephone",
			"field_type"           => "text",
			"field_name"           => "cpl_contacts_telephone",
			"location"             => "side",
			"field_description"    => "Telefon",
		);

		$arrOrdering       = array(
			"domain"               => "cpl",
			"post_type"            => "cpl_contacts",
			"priority"             => "low",
			"headline"             => "Reihenfolge",
			"nonce_key"            => "cpl_contacts_edit",
			"field_key"            => "cpl_contacts_ordering",
			"field_type"           => "number",
			"field_name"           => "cpl_contacts_ordering",
			"location"             => "side",
			"field_description"    => "Reihenfolge",
			"input_placeholder_text"    => "0",
		);

		$arrDiploma         = array(
			"domain"               => "cpl",
			"post_type"            => "cpl_contacts",
			"priority"             => "low",
			"headline"             => "Zertifizierung",
			"nonce_key"            => "cpl_contacts_edit",
			"field_key"            => "cpl_contacts_diploma",
			"field_type"           => "text",
			"field_name"           => "cpl_contacts_diploma",
			"location"             => "side",
			"field_description"    => "Zertifizierung",
		);
		$arrMessage         = array(
			"domain"               => "cpl",
			"post_type"            => "cpl_contacts",
			"priority"             => "low",
			"headline"             => "Qualifications / Post",
			"nonce_key"            => "cpl_contacts_edit",
			"field_key"            => "cpl_contacts_message",
			"field_type"           => "textarea",
			"field_name"           => "cpl_contacts_message",
			"location"             => "advanced",
			"field_description"    => "Qualifications / Post",
		);

		$personMBH          = new MBH($arrName);
		$telMBH             = new MBH($arrTelephone);
		$eMailMBH           = new MBH($arrEmail);
		$diplomaMBH         = new MBH($arrDiploma);
		$orderingMBH        = new MBH($arrOrdering);
		$messageMBH         = new MBH($arrMessage);
	}

	function ContactCardShortCode($attributes){
		/**
		 * @var \WP_Post $post
		 * @var string $group
		 */
		extract( shortcode_atts( array(
		  'group'     => null,
		),
		  $attributes ) );

		$taxonomy   = "cpl_team_group";       //$location;
		$postType   = "cpl_contacts";
		wp_reset_postdata();

		$output             = "";
		$taxTerm            = getTaxonomyDataForContactCardPostType();
		$termTax            = array_flip($taxTerm);
		$termID             = isset($termTax[$group]) ? $termTax[$group] : null;
		$args 		        = array(
			"post_type"         => $postType,
			'post_status'       => 'publish',
			"posts_per_page"    => -1,
			'orderby'           => 'meta_value_num post_date title ID',
			'meta_key'          => 'cpl_contacts_ordering',
			'order'             => 'ASC',
		);

		if($group){
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => array($termID),
				),
			);
		}

		$cplQuery      = new \WP_Query($args);

		$loopCount          = 0;
		$n                  = 1;

		if($cplQuery->have_posts()):
			$output            .= "<div class='pz-contact-main-wrapper' id='pz-contact-main-wrapper'>" . PHP_EOL;
			$output            .= "<div class='col-md-12 pz-contact-slot-block no-lr-pad' id='pz-contact-slot-block'>" . PHP_EOL;
			// HEADING FOR ANSPRECHSPARTNER:
			if($group !== "General" && $group !== "All"){
				$output            .= "<div class='col-md-12 pz-contact-main-head-pod no-lr-pad' id='pz-contact-main-head-pod'>" . PHP_EOL;
				$output            .= "<h2 class='pz-contact-main-heading no-lr-pad' id='pz-contact-main-heading'>" . PHP_EOL;
				$output            .= __($group, "cpl") . PHP_EOL;
				$output            .= "</h2>" . PHP_EOL;
				$output            .= "</div>" . PHP_EOL;
			}
			while($cplQuery->have_posts()) : $cplQuery->the_post();
				$n          = ($n >4 ) ? 1 : $n;
				$pid        = get_the_ID();
				$output    .= injectXYZColumnWrapper(4, $loopCount, "pz-contact-wrapper-block col-md-12 pz-col-12", $loopCount, "section") . PHP_EOL;

				// OPEN pz-contact-slot DIV
				$output    .= "<div class='col-md-3 pz-contact pz-contact-{$n} pz-contact-slot pz-contact-slot-" . $pid . "'>" . PHP_EOL;

				$output    .= "<div class='flip-container' ontouchstart='this.classList.toggle(\"hover\");'>" . PHP_EOL;
				$output    .= "<div class='front'><!-- front content -->" . PHP_EOL;
				$output    .= "<img class='pz-contact-pix ww-pix pz-contact-pix-"      . $pid . "' src='" . get_the_post_thumbnail_url($pid, "full") .  "' alt='" . get_the_title($pid) . "'  />" . PHP_EOL;
				$output    .= "<h3 class='pz-contact-name'>"        . get_the_title($pid)   . "</h3>"   . PHP_EOL;
				$output    .= "</div>" . PHP_EOL;

				$output    .= "<div class='back'> <!-- back content -->" . PHP_EOL;
				$output    .= "<div class='back-content'>" . PHP_EOL;
				$output    .= "<img class='pz-back-pix pz-back-pix-"      . $pid . "' src='" . get_the_post_thumbnail_url($pid, "full") .  "' alt='" . get_the_title($pid) . "'  />" . PHP_EOL;
				$output    .= "<div class='pz-back-text-box'>";
				$output    .= "<h3 class='pz-contact-name'>"        . get_the_title($pid)   . "</h3>"   . PHP_EOL;
				$output    .= "<span class='pz-contact-position'>"  . get_post_meta($pid, 'cpl_contacts_diploma', true)      . "</span>" . PHP_EOL;
				if($tl = get_post_meta($pid, 'cpl_contacts_telephone', true)) {
					$faClass = (stristr($tl, "+41 6")) ? "fa fa-phone" : "fa fa-mobile-phone";
					$output .= "<span class='pz-contact-position'><span class='{$faClass}'></span> " . $tl . "</span>" . PHP_EOL;
				}
				$output    .= "<a class='pz-v-card' href=\"?vcard={$pid}\" ><span class='fa fa-vcard'></span> " . __("vCard", "cpl") . "</a>" . PHP_EOL;
				if(get_post_meta($pid, 'cpl_contacts_email', true)) {
					$output .= "<a class='pz-contact-mail' href=\"mailto:" . get_post_meta($pid, 'cpl_contacts_email', true) . "\" ><span class='fa fa-envelope'></span> " . __("Mail", "cpl") . "</a>" . PHP_EOL;
				}

				$output    .= "</div>" . PHP_EOL;
				$output    .= "</div>" . PHP_EOL;
				$output    .= "</div>" . PHP_EOL;

				// CLOSE pz-contact-wrap DIV
				$output    .= "</div>" . PHP_EOL;

				$output    .= "</div>" . PHP_EOL;



				$output    .= "" . PHP_EOL;
				$loopCount++;
				$n++;

			endwhile;
			$output        .= "</section>" . PHP_EOL;
			$cplQuery->reset_postdata();
			$output            .= "<div style='clear:both;'></div>" . PHP_EOL;
			$output            .= "</div>" . PHP_EOL;
			$output            .= "</div>" . PHP_EOL;
		endif;

		return $output;
	}

	if(!function_exists("getTaxonomyDataForContactCardPostType")) {
		function getTaxonomyDataForContactCardPostType($postType = "cpl_contacts", $taxonomy = "cpl_team_group") {
			/**
			 * @var \WP_Post $post
			 */
			wp_reset_postdata();
			$args = array(
				"post_type" => $postType,
				"taxonomy" => $taxonomy,
				'post_status' => 'publish',
				"posts_per_page" => -1,
				'orderby' => array(
					"title" => "ASC",
					'post_date' => 'DESC',
					'ID' => 'ASC'
				),
			);

			$key = "cpl_contacts_ordering";
			$arrTaxData = array();
			$arrTaxDataReturn = array();
			$arrSortOpts = array();
			$zellerQuery = new \WP_Query($args);
			$bIntCounter = 1000000;
			$affOrderKey = "cpl_contacts_orderinga";

			foreach ($zellerQuery->posts as $post) {
				$arrTaxPayload = get_the_terms($post->ID, $taxonomy);
				if (is_array($arrTaxPayload)) {
					foreach ($arrTaxPayload as $iKey => $objTaxPayload) {
						$tempArr[$objTaxPayload->term_id] = $objTaxPayload;
						## AVAILABLE PROPERTIES: term_id, name, slug, term_group, term_taxonomy_id, taxonomy, description, parent, count, object_id, filter:
						if ($objTaxPayload) {
							if (!array_key_exists($objTaxPayload->term_id, $arrTaxData)) {
								$tid = $objTaxPayload->term_id;
								$tName = $objTaxPayload->name;
								$tax = $objTaxPayload->taxonomy;
								$arrTaxData[$tid] = $tName;
								$getFieldParamNr2 = $tax . "_" . $tid;

								if ($oKey = get_post_meta($post->ID, $affOrderKey, true)) {
									$zlr_tax_order = array($affOrderKey => $oKey);
								} else {
									$zlr_tax_order = get_option("taxonomy_$tid");
								}


								if (isset($zlr_tax_order[$key]) && ($zlr_tax_order[$key] != "" || !empty($zlr_tax_order[$key]))) {
									$orderNum = $zlr_tax_order[$key];
									$arrSortOpts[$orderNum] = $tid;
								} else {
									$arrSortOpts[$bIntCounter] = $tid;
								}
							}
						}
						$bIntCounter++;
					}
				}

			}

			ksort($arrSortOpts, SORT_NUMERIC);

			foreach ($arrSortOpts as $orderNum => $tmID) {
				if (!in_array($tmID, $arrTaxDataReturn)) {
					$arrTaxDataReturn[$tmID] = $arrTaxData[$tmID];
				}
			}
			wp_reset_postdata();
			return $arrTaxDataReturn;
		}
	}


	function getComplotCardExtras($currentID, $max=3, $headCaption=null, $addContainer=false, $addCol12=false){
		$postType   = "cpl_promo";
		/**
		 * @var \WP_Post $post
		 */

		$arrColOpts     = CPH::getPromoColumnOptions();
		$colMapMatrix   = CPH::getColumnMappingMatrix();
		if($arrColOpts){
			$activePageURI  = CPH::getActivePageURL();

			foreach($arrColOpts as $pageTitleID=>$numCols){
				if($activePageURI == CPH::getPostOrPageByTitleOrID($pageTitleID)){
					$colMapMatrix   = CPH::getColumnMappingMatrix($numCols);
					break;
				}
			}
		}

		wp_reset_postdata();
		$cssContainer       = ($addContainer)   ? "container "  : "";
		$cssCol12           = ($addCol12)       ? "col-md-12 "  : "";
		$args 		        = array(
			"post_type"         => $postType,
			'post_status'       => 'publish',
			"posts_per_page"    => $max,      //3,
			"orderby"           => 'rand',
			'post__not_in'      => array($currentID),
		);
		$cplQuery      = new \WP_Query($args);

		$output             = "<div class='{$cssContainer}pz-promo-main-wrapper' id='pz-promo-main-wrapper'>" . PHP_EOL;
		$output            .= CPH::getInlinePromoCustomScripts();
		$output            .= CPH::getInlinePromoCustomStyles();
		if($headCaption){
			// "WEITERE AKTIONEN"
			$output            .= "<div class='{$cssContainer}pz-promo-main-head-pod' id='pz-promo-main-head-pod'>" . PHP_EOL;
			$output            .= "<h2 class='pz-promo-main-heading' id='pz-promo-main-heading'>" . PHP_EOL;
			$output            .= __($headCaption, "cpl") . PHP_EOL;
			$output            .= "</h2>" . PHP_EOL;
			$output            .= "</div>" . PHP_EOL;
		}

		$output            .= "<div class='{$cssContainer}pz-promo-slot-block' id='pz-promo-slot-block'>" . PHP_EOL;
		$loopCount          = 0;

		if($cplQuery->have_posts()):
			while($cplQuery->have_posts()) : $cplQuery->the_post();
				$pid        = get_the_ID();
				$output    .= injectXYZColumnWrapper($colMapMatrix['colsPerRow'], $loopCount, "pz-wrapper-block pz-col-12", $loopCount, "section") . PHP_EOL;
				$output    .= "<div class='" . $colMapMatrix['class'] . " pz-promo-slot'>" . PHP_EOL;

				// $output    .= injectXYZColumnWrapper(3, $loopCount, "pz-wrapper-block pz-col-12", $loopCount, "section") . PHP_EOL;
				// $output    .= "<div class='col-md-4 pz-promo-slot'>" . PHP_EOL;


				if( $promoSlogan = get_post_meta($pid, 'cpl_promo_slogan', true) ){
					$output.= "<a href='" . get_the_permalink($pid) . "' class='pz-detail-link'>";
					$output.= "<div class='pz-promo-wrap'>" . PHP_EOL;
					$output.= "<div class='pz-pix-pod'  >" . PHP_EOL;
					$output.= "<img class='pz-promo-pix' src='" . get_the_post_thumbnail_url($pid, "full") .  "' alt='" . get_the_title($pid) . "'  />" . PHP_EOL;
					$output.= "</div>" . PHP_EOL;

					$output.= "<div class='pz-bordered-triangle-block' >" . PHP_EOL;

					$output.= "<div class='pz-bordered-triangle'>" . PHP_EOL;
					$output.= "</div>" . PHP_EOL;

					$output.= "<div class='pz-triangle-text' style='' >" . PHP_EOL;
					$output.= "<span class='pz-slogan-pod'>" . get_post_meta($pid, 'cpl_promo_slogan', true) . "</span>" . PHP_EOL;
					$output.= "<span class='pz-price-block'>" . get_post_meta($pid, 'cpl_promo_price', true) . "</span>" . PHP_EOL;
					$output.= "</div>" . PHP_EOL;

					$output.= "</div>" . PHP_EOL;
					$output.= "</a>" . PHP_EOL;

					$output.= "<div class='pz-base-collect' >" . PHP_EOL;
					$output.= "<p class='pz-base-headline-p'>" . get_post_meta($pid, 'cpl_promo_headline', true) . "</p>" . PHP_EOL;
					$output.= "<p class='pz-base-detail-link-p'><a href='" . get_the_permalink($pid) . "' class='pz-detail-link' >Details &nbsp;<span class='fa fa-chevron-right pz-link-arrow'></span></a></p>" . PHP_EOL;
					$output.= "</div>" . PHP_EOL;
					$output.= "</div>" . PHP_EOL;

					$output.= "</div>" . PHP_EOL;
				}

				$output    .= "" . PHP_EOL;
				$output    .= "" . PHP_EOL;
				$output    .= "" . PHP_EOL;
				$loopCount++;
			endwhile;
			$output        .= "</section>" . PHP_EOL;
			$cplQuery->reset_postdata();
		endif;
		$output            .= "</div>" . PHP_EOL;
		return $output;
	}

	function injectXYZColumnWrapper($cols_per_row, $closePoint, $cssClass="pz-wrapper-block col-md-12", $nthElem="0", $wrapper="div"){
		$blockDisplay       = "";
		if( ($closePoint == 0) ){
			$blockDisplay   = "<{$wrapper} class='" . $cssClass . " box-lvl-" . $nthElem . "'>"  . PHP_EOL;
		}else if( ($closePoint % $cols_per_row) == 0 && ($closePoint != 0) ){
			$blockDisplay   = "</{$wrapper}><{$wrapper} class='" . $cssClass . " box-lvl-" . $nthElem . "'>"  . PHP_EOL;
		}
		return $blockDisplay;
	}

	function ContactCardResourcesEmbed() {
		wp_enqueue_style( 'cpl-bootstrap-css',          $GLOBALS['CONTACT_CARD_PLUGIN']['css_url'] . 'bootstrap.min.css',                   array(), '' );
		wp_enqueue_style( 'cpl-fa-front-end-css',       $GLOBALS['CONTACT_CARD_PLUGIN']['css_url'] . 'font-awesom.min.css',                 array(), '' );
		wp_enqueue_style( 'cpl-contact-front-end-css',  $GLOBALS['CONTACT_CARD_PLUGIN']['css_url'] . 'cpl-contact-fe.css',                  array(), '' );
		wp_enqueue_script( 'cpl-contact-front-end-js',  $GLOBALS['CONTACT_CARD_PLUGIN']['js_url']  . 'cpl-contact-fe.js',                   array( 'jquery' ) );

		# REGISTER AJAX-BASED SCRIPTS
		# wp_enqueue_script( 'cpl-promo-ajax-js',         $GLOBALS['CONTACT_CARD_PLUGIN']['js_url']    . 'cpl-promo-ajax-manipulator.js',    array( 'jquery' ), '' );

		# LOCALIZE THE SCRIPTS
		# $php_array  = array( 'admin_ajax' => admin_url( 'admin-ajax.php' ) );
		# wp_localize_script( 'cpl-promo-ajax-js', 'php_array', $php_array );
	}

	### MANAGEMENT MENU HOOKS
	function addCplCardManagementMenu() {
		// CREATE TOP-LEVEL SETTINGS PAGE:
		$baseSlug   = "contact-card-settings";
		add_menu_page(  __("Contact Card Settings", "cpl"),
			__("Complot Promo", "cpl"),
			"administrator",
			$baseSlug,
			'renderCplCardSettingsPage',
			"\"class='hidden-img' style='display:none'/><i class='pz-admin-icon fa fa-vcard' style='display:block;padding:25% 10%;'></i><null class=\"hidden"
		);

		add_action( 'admin_init', 'registerCplCardSettings' );
	}

	function registerCplCardSettings(){
		//register our settings
		register_setting( 'cpl-c-card-settings-core',        'cpl_c-card_option_columns' );
		register_setting( 'cpl-c-card-settings-core',        'cpl_c-card_option_email' );
		register_setting( 'cpl-c-card-settings-core',        'cpl_c-card_option_url' );

		register_setting( 'cpl-c-card-settings-advanced',    'cpl_c-card_option_advanced' );
		register_setting( 'cpl-c-card-settings-render',      'cpl_c-card_option_render' );
		register_setting( 'cpl-c-card-settings-render',      'cpl_c-card_option_post_page' );
		register_setting( 'cpl-c-card-settings-docs',        'cpl_c-card_option_docs' );
		register_setting( 'cpl-c-card-settings-css',         'cpl_c-card_option_css' );
		register_setting( 'cpl-c-card-settings-js',          'cpl_c-card_option_js' );
	}

	function renderCplCardSettingsPage(){
		echo  MBH::getCCardNavBar();
	}

	function loadCplCardAdminStyles() {
		wp_enqueue_style( 'bts-ww-admin_css',   $GLOBALS['CONTACT_CARD_PLUGIN']['css_url']     . 'c-card-admin.css',  array(), '' );
		wp_enqueue_style( 'fa-ww-admin_css',    $GLOBALS['CONTACT_CARD_PLUGIN']['css_url']     . 'font-awesome.min.css',  array(), '' );
		wp_enqueue_style( 'animate-ww_css',     $GLOBALS['CONTACT_CARD_PLUGIN']['css_url']     . 'animate.css',  array(), '' );
		wp_enqueue_style( 'item-ww-admin_css',  $GLOBALS['CONTACT_CARD_PLUGIN']['css_url']     . 'c-card-styles.css',  array(), '' );
	}



	function encodeCplVCardData($vCardData){
		return base64_encode(serialize($vCardData));
	}

	function decodeCplVCardData($encodedData){
		return unserialize(base64_decode($encodedData));
	}

	function createCplVCardData($arrVCardConfig){
		$vc     = new vCard();
		$vc->set("filename", "");
		$vc->set("class", "PUBLIC");
		$vc->set("data", $arrVCardConfig);
		$vc->download();
	}
	/********************************************************************/
	/**********************CUSTOM POST TYPES: END************************/
	/********************************************************************/



	/********************************************************************/
	/******************ACTION HOOKS DECLARATIONS: BEGIN******************/
	/********************************************************************/
	add_action('init',                          'ContactCardPostTypes');
	add_shortcode('contact_cards',              'ContactCardShortCode' );
	add_action('wp_enqueue_scripts',            'ContactCardResourcesEmbed');

	
	if ( is_admin() ) {
		// CREATE THE META-BOXES FOR USE IN PLUGIN MANAGEMENT:
		add_action('admin_init',                    'callMetaBoxHelperContactCards');
	}
	/********************************************************************/
	/*******************ACTION HOOKS DECLARATIONS: END*******************/
	/********************************************************************/

