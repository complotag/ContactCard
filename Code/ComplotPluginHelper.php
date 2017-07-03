<?php
/**
 * Created by PhpStorm.
 * User: poiz
 * Date: 25.05.17
 * Time: 08:14
 */


namespace Code;

class ComplotPluginHelper {

	protected static function initialize(){
		$path1  = WEB_ROOT . '/wp-admin';
		$path2  = WEB_ROOT . '/wp-includes';
		$path3  = WEB_ROOT . '/wp-content';
		$path4  = WEB_ROOT . '/wp-admin/includes';
		set_include_path(
			get_include_path() .    PATH_SEPARATOR . $path1 .
			PATH_SEPARATOR . $path2 .
			PATH_SEPARATOR . $path3 .
			PATH_SEPARATOR . $path4
		);
		//require_once $path4  . "/noop.php";
		require_once WEB_ROOT  . "/wp-config.php";
		require_once $path4  . "/plugin.php";
		require_once $path2  . "/functions.php";
		require_once $path2  . "/formatting.php";
	}


	
	public static function renderDocsSettingsInterface(){
		self::initialize();
		ob_start();
		settings_fields( 'cpl-promo-settings-docs' );
		$stnCpPromo     = ob_get_clean();
		$OptName        = get_option('cpl_promo_option_docs');
		$i18nCpDocs     = __('Complot Promo Documentation', 'cpl');
		$formPostURL    = "options.php";       //WP_CONTENT_URL . "/../wp-admin/options.php";  // http://uninorm.dev/wp-admin/

		ob_start();
		wp_editor($OptName, "cpl_promo_option_columns" );
		$strCpEditor    = ob_get_clean();
		$strCpEditor    = "<textarea id='cpl_promo_option_columns' name='cpl_promo_option_columns'>{$OptName}</textarea>";

		$csInterface    =<<<CSI
	<div class="wrap pz-col-12 pz-global-wrapper">
		<h2 class='pz-plg-title-head' id='pz-plg-title-head' >{$i18nCpDocs}</h2>

		<section id="pz-form-wrapper" class="pz-form-wrapper">
			<form id="pz-core-settings-form" class="pz-core-settings-form form-horizontal"  method="post" action="{$formPostURL}">
				{$stnCpPromo}

				<section class="pz-section-left" id="pz-section-left">
					<div class="pz-doc-block">
						Lorem ipsum dolor sit amet,sed diam nonumy eirmod tempor invidunt ut labore et
						dolore magna aliquyam erat,  At vero eos et accusam et justo duo dolores et ea rebum.
						Lorem ipsum dolor sit amet,  no sea takimata sanctus est Lorem ipsum dolor sit amet.
						Stet clita kasd gubergren,  no sea takimata sanctus est Lorem ipsum dolor sit amet.
						no sea takimata sanctus est Lorem ipsum dolor sit amet.  no sea takimata sanctus est Lorem ipsum dolor sit amet.
						sed diam voluptua.
					</div>
				</section>

				<section class="pz-section-right" id="pz-section-right">
					<div class="form-group pz-form-group">
						Lorem ipsum dolor sit amet tadnd and ajf wfhi e jiadfiej fe h
					</div>
					<div class="form-group pz-form-group">
						<input type="hidden" name="cpl_promo_option_docs" class="" id="" value="1" placeholder=""/>
					</div>
				</section>

			</form>
		</section>
	</div>
CSI;
		return ["uiView" => $csInterface];
	}

	public static function renderCoreSettingsInterface(){
		self::initialize();
		ob_start();
		settings_fields( 'cpl-promo-settings-core' );

		$stnCpPromo     = ob_get_clean();
		$OptName        = get_option('cpl_promo_option_columns');
		$optEmail       = get_option('cpl_promo_option_email');
		$optURL         = get_option('cpl_promo_option_url');;
		$i18nCpName     = __('Column Definition', 'cpl');
		$i18nCpEmail    = __('Email', 'cpl');
		$i18nCpURL      = __('URL', 'cpl');
		$i18nCpSave     = __('Save Changes', 'cpl');
		$i18nCpOptions  = __('Complot Promo Core-Settings', 'cpl');
		$formPostURL    = "options.php";
		$pHold          = "3 : Garagentoren\n3 : FlachdachHolz\n4 : 87";

		ob_start();
		wp_editor($OptName, "cpl_promo_option_name" );
		$strCpEditor    = ob_get_clean();
		$strCpEditor    = "<textarea id='cpl_promo_option_columns' name='cpl_promo_option_columns' placeholder='{$pHold}'>{$OptName}</textarea>";

		$csInterface    =<<<CSI
	<div class="wrap pz-col-12 pz-global-wrapper">
		<h2 class='pz-plg-title-head' id='pz-plg-title-head' >{$i18nCpOptions}</h2>

		<section id="pz-form-wrapper" class="pz-form-wrapper">
			<form id="pz-core-settings-form" class="pz-core-settings-form form-horizontal"  method="post" action="{$formPostURL}">
				{$stnCpPromo}

				<section class="pz-section-left" id="pz-section-left">
					<div class="pz-editor-group">
						<label class="" id="" for="">
							<span class="pz-label-name">{$i18nCpName}</span>
							<span class="pz-field-hint">
								USING THE KEY : VALUE PAIR, SPECIFY THE NUMBER OF COLUMNS THE ASSOCIATED PAGE SHOULD HAVE.</br>
								YOU COULD USE PAGE ID (OR SIMPLY THE EXACT TITLE OF THE PAGE (PAGE TITLE)</br>
								HERE WOULD BE AN EXAMPLE</br>
								3 : Garagentoren        &nbsp;&nbsp;&nbsp;&nbsp;=> MEANS THAT THE PAGE WITH THE TITLE OF Garagentoren SHOULD HAVE 3 COLUMNS</br>
								6 : FlachdachHolz       &nbsp;&nbsp;=> MEANS THAT THE PAGE WITH THE TITLE OF FlachdachHolz SHOULD HAVE 6 COLUMNS</br>
								4 : 87                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;=> MEANS THAT THE PAGE WITH AN ID OF 87 SHOULD HAVE 4 COLUMNS</br>
							</span>
						</label>
						<div class="pz-editor-box" id="pz-editor-box">{$strCpEditor}</div>
					</div>
				</section>

				<section class="pz-section-right" id="pz-section-right">
					<div class="form-group pz-form-group">
						<label class="" id="" for=""><span class="pz-label-name">{$i18nCpEmail}</span></label>
						<input type="text" name="cpl_promo_option_email" class="" id="" value="{$optEmail}" placeholder=""/>
					</div>

					<div class="form-group pz-form-group">
						<label class="" id="" for=""><span class="pz-label-name">{$i18nCpURL}</span></label>
						<input type="text" name="cpl_promo_option_url" class="" id="" value="{$optURL}" placeholder=""/>
					</div>

					<div class="form-group pz-form-group">
						<label class="pz-hidden" id="" for=""></label>
						<input type="submit" name="submit" class="button-primary" id="" value="{$i18nCpSave}" placeholder=""/>
					</div>
				</section>

			</form>
		</section>
	</div>
CSI;
		return ["uiView" => $csInterface];
	}

	public static function renderAdvancedSettingsInterface(){
		self::initialize();
		ob_start();
		settings_fields( 'cpl-promo-settings-advanced' );
		$stnCpPromo     = ob_get_clean();
		$optAdvanced    = get_option('cpl_promo_option_advanced');
		$i18nCpAdvanced = __('Template Paths', 'cpl');
		$i18nCpSave     = __('Save Changes', 'cpl');
		$i18nCpOptions  = __('Complot Promo Advanced Settings', 'cpl');
		$formPostURL    = "options.php";

		$csInterface    =<<<CSI
	<div class="wrap pz-col-12 pz-global-wrapper">
		<h2 class='pz-plg-title-head' id='pz-plg-title-head' >{$i18nCpOptions}</h2>

		<section id="pz-form-wrapper" class="pz-form-wrapper">
			<form id="pz-core-settings-form" class="pz-core-settings-form form-horizontal"  method="post" action="{$formPostURL}">
				{$stnCpPromo}

				<section class="pz-section-left" id="pz-section-left">
					<div class="form-group pz-form-group">
						<label class="" id="" for="">
							<span class="pz-label-name">{$i18nCpAdvanced}</span>
							<span class="pz-field-hint">
								Here you can set the Path to a Custom Template
								You want to use in rendering a specific Page or Post.<br>
								This is a colon-separated Key-Value Pair System with the Key corresponding Number Page/Post Title or ID in Question... Each Entry should be on a separate Line.<br />
								<strong style='color:#000;'>Note</strong> that the Path is expected to be relative &amp; should be the Active Theme<br /><br />
								<strong style='color:#000;'>Example:</strong><br />
								garagentore : themes/angle-child-theme/custom-templates/garagentore-template.php<br/>
								carports : themes/angle-child-theme/custom-templates/carports-template.php<br/>
							</span>
						</label>
						<textarea name="cpl_promo_option_advanced" class="cpl_promo_option_advanced" id="cpl_promo_option_advanced" placeholder="carports : themes/angle-child-theme/custom-templates/carports-template.php\n25 : themes/angle-child-theme/custom-templates/garagentore-template.php">{$optAdvanced}</textarea>
					</div>
				</section>

				<section class="pz-section-right" id="pz-section-right">
					<div class="form-group pz-form-group">
						<label class="pz-hidden" id="" for=""></label>
						<input type="submit" name="submit" class="button-primary" id="" value="{$i18nCpSave}" placeholder=""/>
					</div>
				</section>

			</form>
		</section>
	</div>
CSI;
		return ["uiView" => $csInterface];
	}

	public static function renderRenderSettingsInterface(){
		self::initialize();
		ob_start();
		settings_fields( 'cpl-promo-settings-render' );
		$stnCpPromo     = ob_get_clean();
		$optRender      = get_option('cpl_promo_option_render');
		$optPostPage    = get_option('cpl_promo_option_post_page');
		$i18nCpRender   = __('Render-Scheme', 'cpl');
		$i18nCpPostPage = __('Enter the Post Title or ID', 'cpl');
		$i18nCpSave     = __('Save Changes', 'cpl');
		$i18nCpOptions  = __('Complot Promo Render Settings', 'cpl');
		$formPostURL    = "options.php";
		$options        = "";

		$selectOpts     = [
			"" =>  "Choose a Render-Scheme",
			"1" =>  "TEXT=>LEFT &nbsp;&nbsp;&nbsp;IMAGE=>RIGHT&nbsp;&nbsp;&nbsp;&nbsp;: 2-GRID BLOCK",
			"2" =>  "TEXT=>RIGHT &nbsp;&nbsp;&nbsp;IMAGE=>LEFT&nbsp;&nbsp;&nbsp;&nbsp;: 2-GRID BLOCK",
			"3" =>  "TEXT=>RIGHT &nbsp;&nbsp;&nbsp;TEXT=>RIGHT&nbsp;&nbsp;&nbsp;&nbsp;: 2-GRID BLOCK",
			"4" =>  "IMAGE=>RIGHT &nbsp;&nbsp;&nbsp;IMAGE=>RIGHT&nbsp;&nbsp;&nbsp;&nbsp;: 2-GRID BLOCK",
			"5" =>  "TEXT=>RIGHT &nbsp;&nbsp;&nbsp;IMAGE=>MIDDLE &nbsp;&nbsp;&nbsp;TEXT=>LEFT&nbsp;&nbsp;&nbsp;&nbsp;: 3-GRID BLOCK",
			"6" =>  "TEXT=>RIGHT &nbsp;&nbsp;&nbsp;TEXT=>MIDDLE &nbsp;&nbsp;&nbsp;IMAGE=>LEFT&nbsp;&nbsp;&nbsp;&nbsp;: 3-GRID BLOCK",
			"7" =>  "IMAGE=>RIGHT &nbsp;&nbsp;&nbsp;TEXT=>MIDDLE &nbsp;&nbsp;&nbsp;IMAGE=>LEFT&nbsp;&nbsp;&nbsp;&nbsp;: 3-GRID BLOCK",
			"8" =>  "IMAGE=>RIGHT &nbsp;&nbsp;&nbsp;IMAGE=>MIDDLE &nbsp;&nbsp;&nbsp;IMAGE=>LEFT&nbsp;&nbsp;&nbsp;&nbsp;: 3-GRID BLOCK",
			"9" =>  "TEXT=>RIGHT &nbsp;&nbsp;&nbsp;IMAGE=>TEXT &nbsp;&nbsp;&nbsp;TEXT=>LEFT&nbsp;&nbsp;&nbsp;&nbsp;: 3-GRID BLOCK",
			"10" => "TEXT=>TOP &nbsp;&nbsp;&nbsp;IMAGE=>BOTTOM&nbsp;&nbsp;&nbsp;&nbsp;: ANY LAYOUT",
			"11" => "IMAGE=>TOP &nbsp;&nbsp;&nbsp;TEXT=>BOTTOM&nbsp;&nbsp;&nbsp;&nbsp;: ANY LAYOUT",
		];

		foreach($selectOpts as $key=>$option){
			$selected       = "";
			if($optRender == $key){
				$selected   = " selected='selected' ";
			}
			$options       .= "<option value='{$key}' {$selected}>{$option}</option>" . PHP_EOL;
		}

		$arr            = "<span class=\"fa fa-chevron-right pz-arr\"></span>";
		$arr2           = "<span class=\"fa fa-long-arrow-right pz-arr-2\"></span>";
		$csInterface    =<<<CSI
	<div class="wrap pz-col-12 pz-global-wrapper">
		<h2 class='pz-plg-title-head' id='pz-plg-title-head' >{$i18nCpOptions}</h2>

		<section id="pz-form-wrapper" class="pz-form-wrapper">
			<form id="pz-core-settings-form" class="pz-core-settings-form form-horizontal"  method="post" action="{$formPostURL}">
				{$stnCpPromo}

				<section class="pz-section-left" id="pz-section-left">

					<div class="form-group pz-form-group">
						<label class="" id="" for="">
							<span class="pz-label-name">{$i18nCpRender}</span>
							<span class="pz-field-hint">
								Here you can set the Render Settings, using our simple colon-separated Key-Value Pair
								with the Key corresponding to Scheme Keys (See Below) and the Value corresponding to the targeted Page/Post Title or ID .<br>
								<strong style="color:#900;">SCHEME KEYS</strong><br />
								<strong class="pz-nm">1</strong>{$arr}<span class="pz-tx-1">TEXT</span>{$arr2}<span class="pz-tx-2">LEFT</span><span class="pz-tx-3">IMAGE</span>{$arr2}<span class="pz-tx-4">RIGHT</span><span class="pz-tx-7">2-GRID BLOCK</span><br />
								<strong class="pz-nm">2</strong>{$arr}<span class="pz-tx-1">TEXT</span>{$arr2}<span class="pz-tx-2">RIGHT</span><span class="pz-tx-3">IMAGE</span>{$arr2}<span class="pz-tx-4">LEFT</span><span class="pz-tx-7">2-GRID BLOCK</span><br />
								<strong class="pz-nm">3</strong>{$arr}<span class="pz-tx-1">TEXT</span>{$arr2}<span class="pz-tx-2">LEFT</span><span class="pz-tx-3">TEXT</span>{$arr2}<span class="pz-tx-4">RIGHT</span><span class="pz-tx-7">2-GRID BLOCK</span><br />
								<strong class="pz-nm">4</strong>{$arr}<span class="pz-tx-1">IMAGE</span>{$arr2}<span class="pz-tx-2">LEFT</span><span class="pz-tx-3">IMAGE</span>{$arr2}<span class="pz-tx-4">RIGHT</span><span class="pz-tx-7">2-GRID BLOCK</span><br />
								<strong class="pz-nm">5</strong>{$arr}<span class="pz-tx-1">TEXT</span>{$arr2}<span class="pz-tx-2">RIGHT</span><span class="pz-tx-3">IMAGE</span>{$arr2}<span class="pz-tx-4">MIDDLE</span><span class="pz-tx-5">TEXT</span>{$arr2}<span class="pz-tx-6">LEFT</span><span class="pz-tx-7">3-GRID BLOCK</span><br />
								<strong class="pz-nm">6</strong>{$arr}<span class="pz-tx-1">TEXT</span>{$arr2}<span class="pz-tx-2">RIGHT</span><span class="pz-tx-3">TEXT</span>{$arr2}<span class="pz-tx-4">MIDDLE</span><span class="pz-tx-5">IMAGE</span>{$arr2}<span class="pz-tx-6">LEFT</span><span class="pz-tx-7">3-GRID BLOCK</span><br />
								<strong class="pz-nm">7</strong>{$arr}<span class="pz-tx-1">IMAGE</span>{$arr2}<span class="pz-tx-2">RIGHT</span><span class="pz-tx-3">TEXT</span>{$arr2}<span class="pz-tx-4">MIDDLE</span><span class="pz-tx-5">IMAGE</span>{$arr2}<span class="pz-tx-6">LEFT</span><span class="pz-tx-7">3-GRID BLOCK</span><br />
								<strong class="pz-nm">8</strong>{$arr}<span class="pz-tx-1">IMAGE</span>{$arr2}<span class="pz-tx-2">RIGHT</span><span class="pz-tx-3">IMAGE</span>{$arr2}<span class="pz-tx-4">MIDDLE</span><span class="pz-tx-5">IMAGE</span>{$arr2}<span class="pz-tx-6">LEFT</span><span class="pz-tx-7">3-GRID BLOCK</span><br />
								<strong class="pz-nm">9</strong>{$arr}<span class="pz-tx-1">TEXT</span>{$arr2}<span class="pz-tx-2">RIGHT</span><span class="pz-tx-3">IMAGE</span>{$arr2}<span class="pz-tx-4">TEXT</span><span class="pz-tx-5">TEXT</span>{$arr2}<span class="pz-tx-6">LEFT</span><span class="pz-tx-7">3-GRID BLOCK</span><br />
								<strong class="pz-nm">10</strong>{$arr}<span class="pz-tx-1">TEXT</span>{$arr2}<span class="pz-tx-2">TOP</span><span class="pz-tx-3">IMAGE</span>{$arr2}<span class="pz-tx-4">BOTTOM</span><span class="pz-tx-7">ANY LAYOUT</span><br />
								<strong class="pz-nm">11</strong>{$arr}<span class="pz-tx-1">IMAGE</span>{$arr2}<span class="pz-tx-2">TOP</span><span class="pz-tx-3">TEXT</span>{$arr2}<span class="pz-tx-4">BOTTOM</span><span class="pz-tx-7">ANY LAYOUT</span><br /><br />
								Example:<br >
								2  : Carports &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong style='color:#008;'> - This is a 2-GRID BLOCK meaning that the <span style="color:#000;text-decoration:underline;">Image</span> will float to the <span style="color:#000;text-decoration:underline;">Left</span> and the <span style="color:#000;text-decoration:underline;">Text Content</span> to the <span style="color:#000;text-decoration:underline;">right</span>.</strong><br />
								11 : Flachdachmetal &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong style='color:#008;'> - This means that the <span style="color:#000;text-decoration:underline;">Image</span> should be placed <span style="color:#000;text-decoration:underline;">Above (Top)</span>, and <span style="color:#000">Text Content Below</span> - No matter the Arrangement.</strong>
							</span>
						</label>
						<textarea name="cpl_promo_option_render" class="cpl_promo_option_render" id="cpl_promo_option_render" placeholder="2 : Carports\n11 : Flachdachmetal">{$optRender}</textarea>
					</div>


					<!--
					<div class="form-group pz-form-group">
						<label class="" id="" for="">
							<span class="pz-label-name">{$i18nCpRender}</span>
							<span class="pz-field-hint">
								Here you can choose a Render Scheme for the Specified Page or Post.
								The Rendering Scheme is Self-Explanatory.<br>
							</span>
						</label>
						<select name="cpl_promo_option_render" class="" id="" value="{$optRender}">
							{$options}
						</select>
					</div>

					<div class="form-group pz-form-group">
						<label class="" id="" for="">
							<span class="pz-label-name">{$i18nCpPostPage}</span>
							<span class="pz-field-hint">
								Enter the Title or ID of the Page to be rendered with the chosen Scheme.
							</span>
						</label>
						<input type="text" name="cpl_promo_option_post_page" class="" id="" value="{$optPostPage}" placeholder="Carports"/>
					</div>
					-->
				</section>

				<section class="pz-section-right" id="pz-section-right">
					<div class="form-group pz-form-group">
						<label class="pz-hidden" id="" for=""></label>
						<input type="submit" name="submit" class="button-primary" id="" value="{$i18nCpSave}" placeholder=""/>
					</div>
				</section>

			</form>
		</section>
	</div>
CSI;
		return ["uiView" => $csInterface];
	}

	public static function renderJSSettingsInterface(){
		self::initialize();
		ob_start();
		settings_fields( 'cpl-promo-settings-js' );

		$stnCpPromo     = ob_get_clean();
		$OptName        = get_option('cpl_promo_option_js');
		$i18nCpName     = __('Custom Javascript Snippets', 'cpl');
		$i18nCpSave     = __('Save Changes', 'cpl');
		$i18nCpOptions  = __('Complot Promo Javascript Snippets', 'cpl');
		$formPostURL    = "options.php";       //WP_CONTENT_URL . "/../wp-admin/options.php";  // http://uninorm.dev/wp-admin/

		//ob_start();
		//wp_editor($OptName, "cpl_promo_option_js" );
		//$strCpEditor    = ob_get_clean();
		$strCpEditor    = "<textarea id='cpl_promo_option_js' name='cpl_promo_option_js'>{$OptName}</textarea>";

		$csInterface    =<<<CSI
	<div class="wrap pz-col-12 pz-global-wrapper">
		<h2 class='pz-plg-title-head' id='pz-plg-title-head' >{$i18nCpOptions}</h2>

		<section id="pz-form-wrapper" class="pz-form-wrapper">
			<form id="pz-core-settings-form" class="pz-core-settings-form form-horizontal"  method="post" action="{$formPostURL}">
				{$stnCpPromo}

				<section class="pz-section-left" id="pz-section-left">
					<div class="pz-editor-group">
						<label class="" id="" for="">
							<span class="pz-label-name">{$i18nCpName}</span>
							<span class="pz-field-hint">
								You may enter your Custom Javascript Snippets/Codes Styles here.<br />
								<strong style='color:#900;'>NOTE: </strong> Your Snippets/Codes would be wrapped within jQuery's <strong style='color:#900;'>$(document).ready()</strong> Method .
							</span>
						</label>
						<div class="pz-editor-box" id="pz-editor-box">{$strCpEditor}</div>
					</div>
				</section>

				<section class="pz-section-right" id="pz-section-right">
					<div class="form-group pz-form-group">
						<label class="pz-hidden" id="" for=""></label>
						<input type="submit" name="submit" class="button-primary" id="" value="{$i18nCpSave}" placeholder=""/>
					</div>
				</section>

			</form>
		</section>
	</div>
CSI;
		return ["uiView" => $csInterface];
	}

	public static function renderCSSSettingsInterface(){
		self::initialize();
		ob_start();
		settings_fields( 'cpl-promo-settings-css' );

		$stnCpPromo     = ob_get_clean();
		$OptName        = get_option('cpl_promo_option_css');
		$i18nCpName     = __('Custom CSS Styles Snippets', 'cpl');
		$i18nCpSave     = __('Save Changes', 'cpl');
		$i18nCpOptions  = __('Complot Promo CSS Styles Snippets', 'cpl');
		$formPostURL    = "options.php";       //WP_CONTENT_URL . "/../wp-admin/options.php";  // http://uninorm.dev/wp-admin/

		//ob_start();
		//wp_editor($OptName, "cpl_promo_option_css" );
		//$strCpEditor    = ob_get_clean();
		$strCpEditor    = "<textarea id='cpl_promo_option_css' name='cpl_promo_option_css'>{$OptName}</textarea>";

		$csInterface    =<<<CSI
	<div class="wrap pz-col-12 pz-global-wrapper">
		<h2 class='pz-plg-title-head' id='pz-plg-title-head' >{$i18nCpOptions}</h2>

		<section id="pz-form-wrapper" class="pz-form-wrapper">
			<form id="pz-core-settings-form" class="pz-core-settings-form form-horizontal"  method="post" action="{$formPostURL}">
				{$stnCpPromo}

				<section class="pz-section-left" id="pz-section-left">
					<div class="pz-editor-group">
						<label class="" id="" for="">
							<span class="pz-label-name">{$i18nCpName}</span>
							<span class="pz-field-hint">
								You may enter your Custom CSS Styles here.<br />
								The Style Declarations entered here will override those declared elsewhere except any of the declarations contain the <strong style='color:#900;'>!important</strong> directive.
							</span>
						</label>
						<div class="pz-editor-box" id="pz-editor-box">{$strCpEditor}</div>
					</div>
				</section>

				<section class="pz-section-right" id="pz-section-right">
					<div class="form-group pz-form-group">
						<label class="pz-hidden" id="" for=""></label>
						<input type="submit" name="submit" class="button-primary" id="" value="{$i18nCpSave}" placeholder=""/>
					</div>
				</section>

			</form>
		</section>
	</div>
CSI;
		return ["uiView" => $csInterface];
	}

	public static function getPromoColumnOptions(){
		self::initialize();
		$cols   = get_option('cpl_promo_option_columns');
		$data   = [];
		if($cols){
			$arrUnique  = explode("\n", $cols);
			foreach($arrUnique as $setting) {
				$arrCols    = explode(":", $setting);
				/*
				$objTmp     = new stdClass();
				$objTmp->idTitle = trim($arrCols[1]);
				$objTmp->numCols = trim($arrCols[0]);
				$data[] = $objTmp;
				*/
				$data[trim($arrCols[1])] = trim($arrCols[0]);

			}
		}
		return $data;
	}

	public static function getPromoCustomScripts($optionName='cpl_promo_option_js'){
		self::initialize();
		$scripts   = trim(strip_tags(get_option($optionName)));
		return $scripts;
	}

	public static function getPromoCustomStyles($optionName='cpl_promo_option_css'){
		self::initialize();
		$styles   = trim(strip_tags(get_option($optionName)));
		return $styles;
	}

	public static function getInlinePromoCustomStyles(){
		$customCSS  = self::getPromoCustomStyles();
		$output     = "";
		if($customCSS){
			$output .= "<style type='text/css' id='pz-promo-custom-css'>" . PHP_EOL;
			$output .= $customCSS . PHP_EOL;
			$output .= "</style>" . PHP_EOL;
		}
		return $output;
	}

	public static function getInlinePromoCustomScripts(){
		$customJS   = self::getPromoCustomScripts();
		$output     = "";
		if($customJS){
			$output .= "<script type='text/javascript' id='pz-promo-custom-js'>" . PHP_EOL;
			$output .= "(function(\$){" . PHP_EOL;
			$output .= "\t\$(document).ready(function(e) {" . PHP_EOL;
			$output .= "\t\t" . $customJS .  PHP_EOL;
			$output .= "\t})"  . PHP_EOL;
			$output .= "})(jQuery)" . PHP_EOL;
			$output .= "</script>" . PHP_EOL;
		}
		return $output;
	}

	public static function getCurrentPageURL($justBase=false) {
		$pageURL = 'http';

		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on"))
			$pageURL .= "s";

		$pageURL .= "://";

		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];

		else
			$pageURL .= $_SERVER["SERVER_NAME"];

		if(!$justBase){
			$pageURL .= $_SERVER["REQUEST_URI"];
		}else{
			if($_SERVER["SERVER_NAME"] == "127.0.0.1" || $_SERVER["SERVER_NAME"] == "localhost" ){
				$requestURI             = $_SERVER["REQUEST_URI"];
				$arrServerNameExtract   = preg_split("#\/#", preg_replace("#^\/#", "", $requestURI));
				$serverNameExtract      = array_shift($arrServerNameExtract);
				$pageURL               .= "/" . $serverNameExtract;
			}
		}

		if ( function_exists('apply_filters') ) apply_filters('wppb_curpageurl', $pageURL);
		return $pageURL;
	}

	public static function getActivePageURL($justBase=false) {
		$pageURL = 'http';

		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on"))
			$pageURL .= "s";

		$pageURL .= "://";

		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];

		else
			$pageURL .= $_SERVER["SERVER_NAME"];

		if(!$justBase){
			$pageURL .= $_SERVER["REQUEST_URI"];
		}else{
			if($_SERVER["SERVER_NAME"] == "127.0.0.1" || $_SERVER["SERVER_NAME"] == "localhost" ){
				$requestURI             = $_SERVER["REQUEST_URI"];
				$arrServerNameExtract   = preg_split("#\/#", preg_replace("#^\/#", "", $requestURI));
				$serverNameExtract      = array_shift($arrServerNameExtract);
				$pageURL               .= "/" . $serverNameExtract;
			}
		}

		if ( function_exists('apply_filters') ) apply_filters('wppb_curpageurl', $pageURL);
		return $pageURL;
	}

	public static function getPostOrPageByTitleOrID($idOrTitle) {
		global $wpdb;
		$search_query   = 'SELECT * FROM wp_posts
	                         WHERE post_type = "attachment"
	                         AND post_title LIKE %s';


		$strType        = (is_numeric($idOrTitle)) ? "ID" : "post_title";
		$search_query   = "SELECT * FROM wp_posts
	                         WHERE {$strType}=%s";
		/*

		$args = array(
			'p'         => 42, // ID of a page, post, or custom type
			'post_type' => 'any'
		);
		$my_posts = new WP_Query($args);


		$args 		        = array(
			"post_type"         => $postType,
			'post_status'       => 'publish',
			"posts_per_page"    => $max,      //3,
			"orderby"           => 'rand',
			'post_in'      => array($currentID),
		);
		$cplPromoQuery      = new \WP_Query($args);
		*/

		$results        = $wpdb->get_results($wpdb->prepare($search_query, trim($idOrTitle)));
		$return         = null;
		if($results && !empty($results)){
			$postObj    = get_post($results[0]->ID);
			$return     = get_the_permalink($postObj);
		}
		return $return;
	}

	public static function getColumnMappingMatrix($numCols=3){
		$mappingMatrix   = [
			"1"     => ["class"=>"col-md-12",   "colsPerRow"=>"1"],
			"2"     => ["class"=>"col-md-6",    "colsPerRow"=>"2"],
			"3"     => ["class"=>"col-md-4",    "colsPerRow"=>"3"],
			"4"     => ["class"=>"col-md-3",    "colsPerRow"=>"4"],
			"6"     => ["class"=>"col-md-2",    "colsPerRow"=>"6"],
			"12"    => ["class"=>"col-md-1",    "colsPerRow"=>"12"],
		];
		return $mappingMatrix[$numCols];
	}
}
