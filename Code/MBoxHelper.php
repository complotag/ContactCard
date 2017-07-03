<?php
	/**
	 * Created by PhpStorm.
	 * User: poizcampbell
	 * Date: 03/10/15
	 * Time: 17:27
	 */

	namespace Code;

	class MBoxHelper {

		protected $domain                   = "cpl";
		protected $location                 = "side";  
		protected $priority                 = "high";  
		protected $headline                 = "Headline";
		protected $nonce_key                = null;
		protected $add_label                = null;
		protected $post_type                = "products";
		protected $field_key                = "field_key";
		protected $field_type               = "textarea";
		protected $field_options            = array();
		protected $field_name               = "field_name";
		protected $post_types               = array('products');
		protected $field_description        = "Dosage";
		protected $input_placeholder_text   = "";

		/**
		 * @param array $defaultParams
		 * Hook into the appropriate actions when the class is constructed.
		 */
		public function __construct(array $defaultParams=array()) {
			$defaultsMapped     = $this->auto_map_properties($defaultParams);
			if($defaultsMapped){
				add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
				add_action( 'save_post', array( $this, 'save' ) );
			}
		}

		/**
		 * @param string $post_type
		 * Adds the meta box container.
		 */
		public function add_meta_box( $post_type ) {    //limit meta box to certain post types
			if ( in_array( $post_type, $this->post_types)) {
				add_meta_box(
				  $this->field_name . "_meta_box"
				  ,__( $this->headline, $this->domain)
				  ,array( $this, 'render_meta_box_content' )
				  ,$post_type
				  ,$this->location
				  ,$this->priority
				);
			}
		}

		public function auto_map_properties($data){
			$this->post_types   = array();
			$refClass           = new \ReflectionClass(__CLASS__ ) ;        //'MBoxHelper');     //__CLASS__);
			foreach ($refClass->getProperties() as $refProperty) {
				$name           = $refProperty->getName();
				if(array_key_exists($name, $data)){
					$accessorName       = "";
					$arrName            = preg_split("#_#", $name);
					foreach($arrName as $intKey=>$propSplitName){
						$accessorName  .= ucfirst($propSplitName);
					}
					$setter             = "set" .$accessorName;
					$getter             = "get" .$accessorName;
					call_user_func("self::" . $setter, $data[$name]);

					if($name == "post_type"){
						if(!empty($this->post_types)){
							$this->post_types[] = $this->post_type;
						}else{
							$this->post_types[] = $this->post_type;
						}
					}
					#var_dump($getter);
					#var_dump(call_user_func("self::" . $getter, $data[$name]));
				}
			}
			if($this->nonce_key == null){
				$this->nonce_key    = self::generate_unique_nonce_key(26);
			}
			return true;
		}

		public function my_format_TinyMCE( $in ) {
			//var_dump($in);
			$in['remove_linebreaks'] = false;
			$in['gecko_spellcheck'] = false;
			$in['keep_styles'] = true;
			$in['accessibility_focus'] = true;
			$in['tabfocus_elements'] = 'major-publishing-actions';
			$in['media_strict'] = false;
			$in['paste_remove_styles'] = false;
			$in['paste_remove_spans'] = false;
			$in['paste_strip_class_attributes'] = 'none';
			$in['paste_text_use_dialog'] = true;
			$in['wpeditimage_disable_captions'] = true;
			$in['plugins'] = 'tabfocus,paste,media,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpfullscreen';
			$in['content_css'] = get_template_directory_uri() . "/editor-style.css";
			$in['wpautop'] = true;
			$in['apply_source_formatting'] = false;
			$in['block_formats'] = "Paragraph=p; Heading 3=h3; Heading 4=h4";
			$in['toolbar1'] = 'bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,wp_fullscreen,wp_adv ';
			$in['toolbar2'] = 'formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help ';
			$in['toolbar3'] = '';
			$in['toolbar4'] = '';
			return $in;
		}

		public static function generate_unique_nonce_key($length=18) {
			$characters     = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabacefghikjlmnopqrstuvqxyz';
			$randomString   = '';
			$returnable     = "";
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}
			$returnable .= $randomString . "_nonce";
			return $returnable;
		}

		/**
		 * Save the meta when the post is saved.
		 *
		 * @param int $post_id The ID of the post being saved.
		 */
		public function save( $post_id ) {
			$nonce      = $this->nonce_key;
			$data       = "";

			/*
			 * We need to verify this came from the our screen and with proper authorization,
			 * because save_post can be triggered at other times.
			 */

			/*
			// Check if our nonce is set.
			if ( ! isset( $_POST[$nonce] ) )
				return $post_id;

			$nonce = $_POST[$nonce];

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, $this->nonce_key ) )
				return $post_id;

			*/
			// If this is an autosave, our form has not been submitted,
			//     so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return $post_id;

			// Check the user's permissions.
			if ( 'page' == @$_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) )
					return $post_id;

			} else {

				if ( ! current_user_can( 'edit_post', $post_id ) )
					return $post_id;
			}

			/* OK, its safe for us to save the data now. */

			// Sanitize the user input.
			if($this->field_type == "textarea"){
				$data   = @$_POST[$this->field_name];
			}else if($this->field_type == "select_multi"){
				if(isset($_POST[$this->field_name])){
					$data   = implode(", ", $_POST[$this->field_name]);
				}
			}else{
				$data = isset($_POST[$this->field_name]) ? sanitize_text_field( $_POST[$this->field_name] ):null;
			}

			// Update the meta field.
			update_post_meta( $post_id, $this->field_key, $data);
			//update_post_meta( $post_id, $this->field_name . "_meta_box", $data );
		}

		/**
		 * Render Meta Box content.
		 *
		 * @param \WP_Post $post The post object.
		 */
		public function render_meta_box_content( $post ) {
			$nonce      = $this->nonce_key . "_nonce";

			// Add an nonce field so we can check for it later.
			wp_nonce_field( $this->nonce_key, $nonce );

			// Use get_post_meta to retrieve an existing value from the database.
			$value = get_post_meta( $post->ID, $this->field_key, true );
			// Display the form, using the current value.
			echo '<label class="' . $this->field_name . '" for="' . $this->field_name . '">';
			if($this->add_label){
				_e( $this->field_description, $this->domain );
			}
			echo '</label> ';
			switch($this->field_type){
				case "text":
				case "password":
				case "email":
				case "tel":
				echo '<input class="meta-box-helper" type="' . $this->field_type . '" id="' . $this->field_name . '" name="' . $this->field_name . '"';
				echo ' value="' . $value . '"   />';
				break;
				echo '<text type= class="meta-box-helper" id="' . $this->field_name . '" name="' . $this->field_name . '" >';
					break;

				case "select":
					echo '<select class="meta-box-helper" id="' . $this->field_name . '" name="' . $this->field_name . '" >';
					echo $this->getFieldOptionsHTMLForAllZellerProducts(esc_attr($value));
					echo '</select>';
					break;
				case "select_multi":
					echo '<select class="meta-box-helper" multiple="multiple" style="min-height: 300px;" id="' . $this->field_name . '" name="' . $this->field_name . '[]" >';
					echo $this->getFieldOptionsHTMLForAllZellerProducts(esc_attr($value));
					echo '</select>';
					break;
				case "number":
					echo '<input class="meta-box-helper" type="' . $this->field_type . '" id="' . $this->field_name . '" name="' . $this->field_name . '"';
					echo ' value="' . esc_attr($value) . '" placeholder="' . __($this->input_placeholder_text, $this->domain) . '"  />';
					break;
				case "checkbox":
					$status     = "" ;
					$cbValue    = esc_attr($value); //?esc_attr($value):"false";
					if($cbValue){
						$status = " checked=checked " ;
					}
					echo '<input class="meta-box-helper" type="' . $this->field_type . '" ' . $status . ' id="' . $this->field_name . '" name="' . $this->field_name . '"';
					echo ' value="true"   />'; //' . $cbValue . '    //placeholder="' . __($this->input_placeholder_text, $this->domain) . '"
					break;
				case "textarea":
					$val    = $value;       //esc_attr($value);
					$val    = (!$val)?"":$val;
					wp_editor($val, $this->field_name, array(
					  'wpautop'             => TRUE,
					  'media_buttons'       => TRUE,
					  'default_editor'      => '',
					  'drag_drop_upload'    => FALSE,
					  'tinymce'             => TRUE,
					  'textarea_name'       => $this->field_name,
					  'textarea_rows'       => 10,
					  'tabindex'            => '',
					  'tabfocus_elements'   => ':prev,:next',
					  'editor_css'          => '',
					  'editor_class'        => '',
					  'teeny'               => TRUE,
					  'dfw'                 => TRUE,
					  '_content_editor_dfw' => TRUE,
					  'quicktags'           => TRUE
					));
					break;
				default:
					echo '<input class="meta-box-helper" type="text" id="' . $this->field_name . '" name="' . $this->field_name . '"';
					echo ' value="' . esc_attr($value) . '" />';
					break;
			}
		}

		public function getFieldOptionsHTMLForAllZellerProducts($default="82, 107, 108"){
			// GET ALL ZELLER PRODUCTS: NOT THE PARENT PRODUCT-GROUP LIKE HUSTEN, ERKÄLTUNG, ETC...
			// BUILD AN OPTIONS-SET OUT OF THEM AND RETURN THE BUILT HTML OPTIONS-SET AS A STRING.

			/**
			 * @var \WP_Post $post
			 */
			wp_reset_postdata();
			$zQueryArray 	        = array(
			  "post_type"         => "products",
			  "taxonomy"          => "sections",
			  'orderby'           => 'post_title',
			  'order'             => 'ASC',
			  "posts_per_page"    => -1,
			);
			$arrOptionsData         = array();
			$strOptionsHTML         = "";
			if($this->field_type == "select_multi"){
				if($default && is_string($default)){
					$default    = explode(", ", $default);
				}
			}

			$zQuery                 = new \WP_Query($zQueryArray);
			if(!empty($zQuery->posts)){
				foreach ($zQuery->posts as $post) {
					$ID                     = $post->ID;
					$_permalink             = get_the_permalink($ID);
					$objFldData             = new \stdClass();
					$objFldData->ID         = $ID;
					$objFldData->_permalink = $_permalink;
					$objFldData->post_name  = $post->post_name;
					$objFldData->post_title = $post->post_title;
					$objFldData->guid       = $post->guid;
					$objFldData->language   = $this->getLanguageFromPermalink($_permalink);
					$arrOptionsData[$ID]    = $objFldData;

					//var_dump(get_post_meta($ID));
				}
			}
			wp_reset_postdata();

			if(!empty($arrOptionsData)){
				$selected        = "";

				if($this->field_type == "select_multi"){
					$default    = is_string($default) ? array() : $default;
					if(in_array("", $default)){
						$selected    = " selected='selected' ";
					}
				}else{
					if("" == $default){
						$selected    = " selected='selected' ";
					}
				}
				$strOptionsHTML     .= "<option value='' {$selected}>" . __("Optionally Bind to Product", "cpl") . "</option>" . PHP_EOL;
				foreach($arrOptionsData as $pID=>$pOBJ){
					var_dump($default);
					$selected        = "";
					if($this->field_type == "select_multi"){
						$default    = is_string($default) ? array() : $default;
						if(in_array($pID, $default)){
							$selected    = " selected='selected' ";
						}
					}else{
						if($pID == $default){
							$selected    = " selected='selected' ";
						}
					}
					$strOptionsHTML .= "<option value='" . $pID . "' {$selected} >" . $pOBJ->post_title . " &nbsp;&nbsp;&nbsp; [" . strtoupper($pOBJ->language) . "]</option>" . PHP_EOL;
				}
			}
			return $strOptionsHTML;
		}

		protected function getLanguageFromPermalink($_permalink){
			if(preg_match("#\/en(\/)?#", $_permalink)){
				return "en";
			}else if(preg_match("#\/fr(\/)?#", $_permalink)){
				return "fr";
			}
			return "de";
		}

		public function getTaxonomyDataForCustomPostType($postType="products", $taxonomy="sections"){
			/**
			 * @var \WP_Post $post
			 */
			wp_reset_postdata();
			$args 		        = array(
			  "post_type"         => $postType,
			  "taxonomy"          => $taxonomy,
			  "posts_per_page"    => -1,
			);
			//var_dump(debug_backtrace());
			//var_dump($args);
			foreach ($cplQuery->posts as $post) {
				$arrTaxPayload      = get_the_terms($post->ID, $taxonomy);
				$objTaxPayload      = isset($arrTaxPayload[0]) ? $arrTaxPayload[0] : null;
				## AVAILABLE PROPERTIES: term_id, name, slug, term_group, term_taxonomy_id, taxonomy, description, parent, count, object_id, filter:
				if($objTaxPayload){
					if(!array_key_exists($objTaxPayload->term_id, $arrTaxData)){
						$arrTaxData[$objTaxPayload->term_id]    = $objTaxPayload->name;
					}
				}
			}
			wp_reset_postdata();
			return $arrTaxData;
		}


		/**
		 * is triggered when invoking inaccessible methods in an object context.
		 *
		 * @param $name string
		 * @param $arguments array
		 * @return mixed
		 * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
		 */
		function __call($name, $arguments) {
			// TODO: Implement __call() method.
		}

		/**
		 * @return string
		 */
		public function getDomain() {
			return $this->domain;
		}

		/**
		 * @return string
		 */
		public function getFieldDescription() {
			return $this->field_description;
		}

		/**
		 * @return string
		 */
		public function getFieldKey() {
			return $this->field_key;
		}

		/**
		 * @return string
		 */
		public function getFieldName() {
			return $this->field_name;
		}

		/**
		 * @return string
		 */
		public function getFieldType() {
			return $this->field_type;
		}

		/**
		 * @return string
		 */
		public function getHeadline() {
			return $this->headline;
		}

		/**
		 * @return string
		 */
		public function getLocation() {
			return $this->location;
		}

		/**
		 * @return null
		 */
		public function getNonceKey() {
			return $this->nonce_key;
		}

		/**
		 * @return array
		 */
		public function getPostTypes() {
			return $this->post_types;
		}

		/**
		 * @return string
		 */
		public function getPriority() {
			return $this->priority;
		}

		/**
		 * @return null
		 */
		public function getAddLabel() {
			return $this->add_label;
		}

		/**
		 * @return string
		 */
		public function getInputPlaceholderText() {
			return $this->input_placeholder_text;
		}

		/**
		 * @return string
		 */
		public function getPostType() {
			return $this->post_type;
		}

		/**
		 * @return array
		 */
		public function getFieldOptions() {
			return $this->field_options;
		}




		/**
		 * @param string $domain
		 */
		public function setDomain($domain) {
			$this->domain = $domain;
		}

		/**
		 * @param string $field_description
		 */
		public function setFieldDescription($field_description) {
			$this->field_description = $field_description;
		}

		/**
		 * @param string $field_key
		 */
		public function setFieldKey($field_key) {
			$this->field_key = $field_key;
		}

		/**
		 * @param string $field_name
		 */
		public function setFieldName($field_name) {
			$this->field_name = $field_name;
		}

		/**
		 * @param string $field_type
		 */
		public function setFieldType($field_type) {
			$this->field_type = $field_type;
		}

		/**
		 * @param string $headline
		 */
		public function setHeadline($headline) {
			$this->headline = $headline;
		}

		/**
		 * @param string $location
		 */
		public function setLocation($location) {
			$this->location = $location;
		}

		/**
		 * @param null $nonce_key
		 */
		public function setNonceKey($nonce_key, $nonceText=0){
			$this->nonce_key = ($nonceText)?$nonce_key . "_nonce": $nonce_key;
		}

		/**
		 * @param array $post_types
		 */
		public function setPostTypes($post_types) {
			$this->post_types = $post_types;
		}

		/**
		 * @param string $priority
		 */
		public function setPriority($priority) {
			$this->priority = $priority;
		}

		/**
		 * @param null $add_label
		 */
		public function setAddLabel($add_label) {
			$this->add_label = $add_label;
		}

		/**
		 * @param string $input_placeholder_text
		 */
		public function setInputPlaceholderText($input_placeholder_text) {
			$this->input_placeholder_text = $input_placeholder_text;
		}

		/**
		 * @param string $post_type
		 */
		public function setPostType($post_type) {
			$this->post_type = $post_type;
		}

		/**
		 * @param array $field_options
		 */
		public function setFieldOptions($field_options) {
			$this->field_options = $field_options;
		}

	}
