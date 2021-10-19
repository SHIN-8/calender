<?php
/*--------------------------------------------------------------

	item_detail_wp_mod

	@history
	shinko_edit_20190312  　<span style="color:#c00;"※必須</span>　追記
						   →　納品先エリア
						   → order2
						   scroll → price
	shinko_edit_20190314   GAイベント用関数追加
	shinko_edit_20190319   短納期商品の文言変更
						   入稿形式にID付与
	shinko_edit_201904089  自動見積もり用スクロールボタン追加
	shinko_edit_20190419   2,1営業日一時停止  line549~
	shinko_edit_20191108   「平日の<span class="caution">正午12時</span>まで受付いたします。<br>」削除、他文言変更


---------------------------------------------------------------*/

$GET_post_id = ( isset( $_GET[ 'id' ] ) && $_GET[ 'id' ] ) ? $_GET[ 'id' ] : 56;

##　base

	/* setting */
	// swithc_blog_id
	$switch_blog_id = 10;         // blog_num_id
	// archives_list_num
	$display_posts_per_page = -1; // -1:all

	/* path */
	$DB_class_fpath              = '/php/lib/db.class.php';
	$CD_class_fpath              = '/php/lib/changedate.class.php';
	// 納期自動算出
	$schedule_parts_func_fpath   = '/php/contents/wp_item_schedule_parts_func.php';

	/* func */
	require_once( ROOTREALPATH . $DB_class_fpath );
	require_once( ROOTREALPATH . $CD_class_fpath );
	require_once( ROOTREALPATH . $schedule_parts_func_fpath );

	/* base_info */
	// date : today
	$today  = date( 'Ymd' );

##	data

	/* get_posts */
	$arr = array();
	if( have_posts() ) {
		the_post();
		// id
		$this_post_id                      = $post->ID;
		$arr[ 'post_title' ]               = get_the_title( $this_post_id );
		$arr[ 'permalink' ]                = get_permalink( $this_post_id );
		// dete
		$arr[ 'post_date' ]                = get_the_date( 'Ymd' );
		// category
		$the_categories                    = get_the_category( $this_post_id );
		$arr[ 'the_cat_id' ]               = $the_categories[ 0 ]->cat_ID;
		$arr[ 'the_cat_slug' ]             = $the_categories[ 0 ]->slug;
		$arr[ 'the_cat_parent' ]           = $the_categories[ 0 ]->parent;
		$arr[ 'the_cat_name' ]             = $the_categories[ 0 ]->name;
		// acf
		$arr[ 'soldout' ]                  = get_field( 'soldout', $this_post_id );
		$arr[ 'item_id' ]                  = get_field( 'item_id', $this_post_id );
		$arr[ 'item_name_01' ]             = get_field( 'item_name_01', $this_post_id );
		$arr[ 'item_name_02' ]             = get_field( 'item_name_02', $this_post_id );
		$arr[ 'draft_template' ]           = get_field( 'draft_template', $this_post_id );
		$arr[ 'text_catch' ]               = get_field( 'text_catch', $this_post_id );
		$arr[ 'text_copy' ]                = get_field( 'text_copy', $this_post_id );
		$arr[ 'spec_01_contents' ]         = get_field( 'spec_01_contents', $this_post_id );
		$arr[ 'spec_02_contents' ]         = get_field( 'spec_02_contents', $this_post_id );
		$arr[ 'spec_03_contents' ]         = get_field( 'spec_03_contents', $this_post_id );
		$arr[ 'spec_04_contents' ]         = get_field( 'spec_04_contents', $this_post_id );
		$arr[ 'spec_05_contents' ]         = get_field( 'spec_05_contents', $this_post_id );
		$arr[ 'spec_06_contents' ]         = get_field( 'spec_06_contents', $this_post_id );
		$arr[ 'spec_07_name' ]             = get_field( 'spec_07_name', $this_post_id );
		$arr[ 'spec_07_contents' ]         = get_field( 'spec_07_contents', $this_post_id );
		$arr[ 'spec_08_name' ]             = get_field( 'spec_08_name', $this_post_id );
		$arr[ 'spec_08_contents' ]         = get_field( 'spec_08_contents', $this_post_id );
		$arr[ 'spec_10_contents' ]         = get_field( 'spec_10_contents', $this_post_id );
		$arr[ 'disp_price_table' ]         = get_field( 'disp_price_table', $this_post_id );
		$arr[ 'sendunit' ]                 = get_field( 'sendunit', $this_post_id );
		$arr[ 'unit' ]                     = get_field( 'unit', $this_post_id );
		// acf for shinkokikaku
		$arr[ 'add_html_info' ]            = get_field( 'add_html_info', $this_post_id ); // acf 190412 追加
		$arr[ 'add_html_send' ]            = get_field( 'add_html_send', $this_post_id ); // acf 190412 追加
		$arr[ 'add_html_info_sp' ]            = get_field( 'add_html_info_sp', $this_post_id ); // acf 190610 追加
		$arr[ 'add_html_send_sp' ]            = get_field( 'add_html_send_sp', $this_post_id ); // acf 190610 追加
		////
		$arr[ 'disp_supple_send' ]         = get_field( 'disp_supple_send', $this_post_id );
		$arr[ 'disp_supple_shipping' ]     = get_field( 'disp_supple_shipping', $this_post_id );
		$arr[ 'disp_supple_fee' ]          = get_field( 'disp_supple_fee', $this_post_id );
		// acf seo
		$arr[ 'seo_title' ]                = get_field( 'seo_title', $this_post_id );
		$arr[ 'seo_h1' ]                   = get_field( 'seo_h1', $this_post_id );
		$arr[ 'seo_description' ]          = get_field( 'seo_description', $this_post_id );
		$arr[ 'send_type' ]                = get_field( 'send_type', $this_post_id );
		$arr[ 'draft_type' ]               = get_field( 'draft_type', $this_post_id );
		// acf : checkbox
		$icon_object                       = get_field_object( 'icon', $this_post_id );
		$arr[ 'icon_value_array' ]         = $icon_object[ 'value' ];
		$icon_choices_array                = $icon_object[ 'choices' ];
		$temp_arr = array();
		foreach( $arr[ 'icon_value_array' ] as $v ) {
			$temp_arr[ $v ] = $icon_choices_array[ $v ];
		}
		$arr[ 'icon_checked_array' ]       = $temp_arr;
		$spec_num_object                   = get_field_object( 'spec_num_cb', $this_post_id );
		$temp_arr                          = $spec_num_object[ 'value' ];
		$arr[ 'spec_num_value_array' ]     = is_array( $temp_arr ) ? $temp_arr : array();
		$spec_num_checked_array            = $spec_num_object[ 'choices' ];
		$temp_arr = array();
		foreach( $arr[ 'spec_num_value_array' ] as $v ) {
			$temp_arr[ $v ] = $spec_num_checked_array[ $v ];
		}
		$arr[ 'spec_num_checked_array' ]   = $temp_arr;
		//tables
		$table_array = array();
		$table_th_array = array(
			'商品サイズ',
			'印刷エリア',
			'主な材質',
			'印刷',
			'最低ロット',
			'納期',
			'',
			'',
			'',
			'備考'
		);
		foreach( $table_th_array as $k => $v ){
			$k = sprintf( "%02d", $k + 1 );
			if( get_field( 'spec_' . $k . '_contents', $this_post_id ) ){
				$temp_arr = array();
				if( $v != '' ){
					$temp_arr[ 'th' ] = $v;
				} else {
					$temp_arr[ 'th' ] = get_field( 'spec_' . $k . '_name', $this_post_id );
				}
				$temp_arr[ 'td' ] = get_field( 'spec_' . $k . '_contents', $this_post_id );
				$table_array[] = $temp_arr;
			}
		}
		$arr[ 'spec_table' ]               = $table_array;

		// ocf : pic
		$main_pic_array = array();
		$sub_pic_array  = array();
		for( $i = 0; $i < 7; $i++ ){
			$num = sprintf( "%02d", $i + 1 );
			if( get_post_meta( $this_post_id, 'pic_' . $num . '_fit_390_390', true ) ){
				$temp_arr = array();
				$temp_arr[ 'pic' ]              = get_post_meta( $this_post_id, 'pic_' . $num . '_fit_390_390', true );
				$temp_arr[ 'thumb' ]            = get_post_meta( $this_post_id, 'pic_' . $num . '_fit_120_120', true );
				$temp_arr[ 'menu' ]             = get_post_meta( $this_post_id, 'pic_' . $num . '_fit_60_60', true );
				if( get_post_meta( $this_post_id, 'caption' . $num, true ) ){
					$temp_arr[ 'caption' ]      = get_post_meta( $this_post_id, 'caption' . $num, true );
				}
				if( $i === 0 ) {
					$main_pic_array = $temp_arr;
				} else {
					$sub_pic_array[] = $temp_arr;
				}
			}
		}
		$arr[ 'main_pic' ]                 = $main_pic_array[ 'pic' ];
		$arr[ 'main_pic' ]                 = wp_adjust_path_image( $arr[ 'main_pic' ], '/images/common/noimage_item_main.jpg' );
		$arr[ 'sub_pic' ]                  = $sub_pic_array;
	}
	$wp_item_single_array = $arr;

//print '$wp_item_single_array'.'：';var_dump($wp_item_single_array);print '<br>'."\n";

##　master

	// nouki
	$nouki_arr = array();
	$CD = new change_date( 'm月d日(W)' );

	if( $wp_item_single_array[ 'item_id' ] === 'normal_regular_none' ) {
		$nouki_arr[ 'normal' ][ 0 ][ 'print' ] = $CD->res_date( results_nouki_date( 1, 12, 'start', 1 ) ); // arg : 納品までの営業日,切替え時刻,日程,複数書き出しナンバー
		$nouki_arr[ 'normal' ][ 0 ][ 'send' ]  = $CD->res_date( results_nouki_date( 1, 12, 'send', 1 ) );
	} elseif( $wp_item_single_array[ 'item_id' ] === 'normal_regular_label' ) {
		$nouki_arr[ 'normal' ][ 0 ][ 'print' ] = $CD->res_date( results_nouki_date( 4, 15, 'start', 1 ) );
		$nouki_arr[ 'normal' ][ 0 ][ 'send' ]  = $CD->res_date( results_nouki_date( 4, 15, 'send', 1 ) );
		$nouki_arr[ 'normal' ][ 1 ][ 'print' ] = $CD->res_date( results_nouki_date( 4, 15, 'start', 2 ) );
		$nouki_arr[ 'normal' ][ 1 ][ 'send' ]  = $CD->res_date( results_nouki_date( 4, 15, 'send', 2 ) );
		$nouki_arr[ 'normal' ][ 2 ][ 'print' ] = $CD->res_date( results_nouki_date( 4, 15, 'start', 3 ) );
		$nouki_arr[ 'normal' ][ 2 ][ 'send' ]  = $CD->res_date( results_nouki_date( 4, 15, 'send', 3 ) );
		$nouki_arr[ '1day' ][ 0 ][ 'print' ]  = $CD->res_date( results_nouki_date( 1, 12, 'start', 1 ) );
		$nouki_arr[ '1day' ][ 0 ][ 'send' ]   = $CD->res_date( results_nouki_date( 1, 12, 'send', 1 ) );
	}
	// nouki_static
	$nouki_name_arr[ 'normal' ] = '通常発送';
	$nouki_name_arr[ '2days' ]  = '2営業日発送';
	$nouki_name_arr[ '1day' ]  = '1営業日発送';

##　data

	/* from wp */
	$category_id = ( $arr[ 'the_cat_slug' ] ) ? $arr[ 'the_cat_slug' ] : 'pt';
	$item_id     = ( $arr[ 'item_id' ] )      ? $arr[ 'item_id' ]      : 'normal_regular_none';
	$send_type   = ( $arr[ 'send_type' ] )    ? $arr[ 'send_type' ]    : 'pt';
	$draft_type  = ( $arr[ 'draft_type' ] )   ? $arr[ 'draft_type' ]   : 'pt_label';

	/* db */
	include_once( ROOTREALPATH . '/php/lib/db.class.php' );
	$host   = 'localhost';
	$user   = 'master_pt';
	$path   = 'lets53biki';
	$dbname = 'master_pt';
	$DB = new db( $host, $user, $path, $dbname );

	// item_db_table
	if( $category_id == 'pt' ) {
		$db_table_item_price = 'pt_price';
	} else {
		$db_table_item_price = 'other_price';
	}

	// pt_price - nouki:0
	$sql = '
		SELECT
			ID,
			item_id,
			price_category_name,
			price_category_id,
			lot,
			price,
			price_c,
			nouki,
			cart_id
		FROM
			' . $db_table_item_price . '
			WHERE
			item_id = "' . $item_id . '"
			AND
			nouki = ""
	';
	$DB->query( $sql );
	$price_arr[ 'normal' ] = $DB->result_arr();
	// pt_price - nouki:1day
	$sql = '
		SELECT
			ID,
			item_id,
			price_category_name,
			price_category_id,
			lot,
			price,
			price_c,
			nouki,
			cart_id
		FROM
			' . $db_table_item_price . '
			WHERE
			item_id = "' . $item_id . '"
			AND
			nouki = 1
	';
	$DB->query( $sql );
	$price_arr[ '1day' ] = $DB->result_arr();
	// pt_price - nouki:2day
	$sql = '
		SELECT
			ID,
			item_id,
			price_category_name,
			price_category_id,
			lot,
			price,
			price_c,
			nouki,
			cart_id
		FROM
			' . $db_table_item_price . '
			WHERE
			item_id = "' . $item_id . '"
			AND
			nouki = 2
	';
	$DB->query( $sql );
	$price_arr[ '2days' ] = $DB->result_arr();

/********** debug **************************/
/** check_on start **/
if(isset($_GET['check']) && $_GET['check']=='on'){
print '$price_arr' . ' : ';var_dump( $price_arr );print '<br />';
}
/** check_on end **/
/********** debug end **********************/

	// draft
	$sql = '
		SELECT
			ID,
			draft_type,
			draft_eccube_name,
			draft_display_name,
			draft_price,
			cart_id
		FROM
			draft
		WHERE
			draft_type = "' . $draft_type . '"
	';
	$DB->query( $sql );
	$draft_db_arr = $DB->result_arr();

/********** debug **************************/
//print '$draft_db_arr' . ' : ';var_dump( $draft_db_arr );print '<br />';
/********** debug end **********************/

	// send
	$sql = '
		SELECT
			ID,
			send_type,
			send_eccube_name,
			send_display_name,
			send_price,
			cart_id
		FROM
			send
		WHERE
			send_type = "' . $send_type . '"
	';
	$DB->query( $sql );
	$send_db_arr = $DB->result_arr();

/********** debug **************************/
//print '$send_db_arr' . ' : ';var_dump( $send_db_arr );print '<br />';
/********** debug end **********************/

##　func

	/* func : current_price_arr */
	// price_category
	function res_pricecategory( $arg_price_arr ) {
		$pricecategory_arr         = array();
		for( $i = 0; $i < count( $arg_price_arr ); $i++ ) {
			if( ! in_array( $arg_price_arr[ $i ][ 'price_category_name' ], $pricecategory_arr ) ) {
				$temp_arr = array();
				$key                         = $arg_price_arr[ $i ][ 'price_category_id' ];
				$pricecategory_arr[ $key  ]  = $arg_price_arr[ $i ][ 'price_category_name' ];
			}
		}
		return $pricecategory_arr;
	}
	// pricelot
	function res_pricelot( $arg_price_arr ) {
		$pricelot_arr              = array();
		for( $i = 0; $i < count( $arg_price_arr ); $i++ ) {
			if( ! in_array( $arg_price_arr[ $i ][ 'lot' ], $pricelot_arr ) ) {
				$pricelot_arr[]              = $arg_price_arr[ $i ][ 'lot' ];
			}
		}
		sort( $pricelot_arr );
		return $pricelot_arr;
	}

	/* disp_form_cont  */
	function disp_form_cont( $arg_form_name = 'unknown' ) {

		global $wp_item_single_array, $price_arr, $nouki_arr, $draft_db_arr, $send_db_arr, $nouki_name_arr ;

		$tag = '';
		$tb = "\t\t\t\t\t";
		$arr = $wp_item_single_array;

		$tag .= $tb . "\t\t\t" . '<form class="form_cont">' . "\n";
		$tag .= $tb . "\t\t\t" . '<p>下記のメニューを選択してから、各ボタンをクリックして下さい。</p>' . "\n";
		// item_id
		$tag.= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_itemid" name="itemid" type="hidden" value="' . $arr[ 'item_id' ] . '">' . "\n";
		$tag.= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_itemtype" name="itemtype" type="hidden" value="' . $arr[ 'the_cat_slug' ] . '">' . "\n";
		$tag.= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_sendunit" name="sendunit" type="hidden" value="' . $arr[ 'sendunit' ] . '">' . "\n";
		$tag.= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_itemunit" name="itemunit" type="hidden" value="' . $arr[ 'unit' ] . '">' . "\n";
		if( $arr[ 'disp_price_table' ] === 'disp' ) {
			if( count( res_pricecategory( $price_arr[ 'normal' ] ) ) > 0 ) {
				$tag.= $tb . "\t\t\t\t" . '<div class="select_wrap pricecategory_select_wrap">' . "\n";
				$tag.= $tb . "\t\t\t\t\t" . '<p class="select_legend">商品<span class="caution">*</span></p>' . "\n";
				$tag.= $tb . "\t\t\t\t\t" . '<select id="' . $arg_form_name . '_printcategory" name="pricecategory">' . "\n";
				$temp_arr = array();
				foreach( res_pricecategory( $price_arr[ 'normal' ] ) as $k => $v ) {
					$tag.= $tb . "\t\t\t\t\t\t" . '<option value="' . $k . '">' . $v . '</option>' . "\n";
				}
				$tag.= $tb . "\t\t\t\t\t" . '</select>' . "\n";
				$tag.= $tb . "\t\t\t\t" . '</div>' . "\n";
			}
			// lot
			if( count( res_pricelot( $price_arr[ 'normal' ] )) > 0 ) {
				$tag.= $tb . "\t\t\t\t" . '<div class="select_wrap lot_select_wrap">' . "\n";
				$tag.= $tb . "\t\t\t\t\t" . '<p class="select_legend">数量<span class="caution">*</span></p>' . "\n";
				$tag.= $tb . "\t\t\t\t\t" . '<select id="' . $arg_form_name . '_lot" name="lot">' . "\n";
				foreach( res_pricelot( $price_arr[ 'normal' ] ) as $k => $v ) {
					$tag.= $tb . "\t\t\t\t\t\t" . '<option value="' . $v . '">' . $v . $arr[ 'unit' ] . '</option>' . "\n";
				}
				$tag.= $tb . "\t\t\t\t\t" . '</select>' . "\n";
				$tag.= $tb . "\t\t\t\t" . '</div>' . "\n";
			}
			// nouki
			if( count( $nouki_arr ) > 1 ) {
				$tag.= $tb . "\t\t\t\t" . '<div class="select_wrap nouki_select_wrap">' . "\n";
				$tag.= $tb . "\t\t\t\t\t" . '<p class="select_legend">出荷日<span class="caution">*</span></p>' . "\n";
				$tag.= $tb . "\t\t\t\t\t" . '<select id="' . $arg_form_name . '_nouki" name="nouki">' . "\n";
				foreach( $nouki_arr as $k => $v ) {
					$tag.= $tb . "\t\t\t\t\t\t" . '<option value="'. $k .'">' . $nouki_name_arr[ $k ] . '</option>' . "\n";
				}
				$tag.= $tb . "\t\t\t\t\t" . '</select>' . "\n";
				$tag.= $tb . "\t\t\t\t" . '</div>' . "\n";
			}
			// draft
			if( count( $draft_db_arr ) > 1 ) {
				$tag.= $tb . "\t\t\t\t" . '<div class="select_wrap draft_select_wrap">' . "\n";
				$tag.= $tb . "\t\t\t\t\t" . '<p class="select_legend">入稿形式<span class="caution">*</span></p>' . "\n";
				$tag.= $tb . "\t\t\t\t\t" . '<select id="' . $arg_form_name . '_draft" name="draft" class="draft_cart_id">' . "\n";
				foreach( $draft_db_arr as $v ) {
					$tag.= $tb . "\t\t\t\t\t\t" . '<option value="' . $v[ 'cart_id' ] . '" data-draft_price="' . $v[ 'draft_price' ] . '" data-draftid="' . $v[ 'ID' ] . '">' . $v[ 'draft_display_name' ] . '</option>' . "\n";
				}
				$tag.= $tb . "\t\t\t\t\t" . '</select>' . "\n";
				$tag.= $tb . "\t\t\t\t" . '</div>' . "\n";
			}
			// send
			if( count( $send_db_arr ) > 1 ) {
				$tag.= $tb . "\t\t\t\t" . '<div class="select_wrap send_select_wrap">' . "\n";
				$tag.= $tb . "\t\t\t\t\t" . '<p class="select_legend">納品先エリア<span class="caution">*</span></p>' . "\n";
				$tag.= $tb . "\t\t\t\t\t" . '<select id="' . $arg_form_name . '_send" name="send" class="send_cart_id">' . "\n";
				foreach( $send_db_arr as $v ) {
				$tag.= $tb . "\t\t\t\t\t\t" . '<option value="' . $v[ 'cart_id' ] . '" data-send_price="' . $v[ 'send_price' ] . '" data-sendid="' . $v[ 'ID' ] . '">' . $v[ 'send_display_name' ] . '</option>' . "\n";
				}
				$tag.= $tb . "\t\t\t\t\t" . '</select>' . "\n";
				$tag.= $tb . "\t\t\t\t" . '</div>' . "\n";
			}
		}
		$tag .= $tb . "\t\t\t" . '</form>' . "\n";

		return $tag;
	}

	/* disp_printestimate_form  */
	function disp_printestimate_form( $arg_form_name = 'unknown' ) {

		global $wp_item_single_array ;
		$printestimate_id = substr( base_convert( md5( uniqid() ), 16, 36 ), 0, 3 ) . time();

		$tag = '';
		$tb = "\t\t\t\t\t";
$teston = isset( $_GET[ 'test' ] ) && $_GET[ 'test' ] === 'on' ? '&test=on' : '';
		$tag .= $tb . "\t\t\t" . '<form id="' . $arg_form_name . '" method="post" action="' . PUBLICDIR . '/item/printestimate/?id=' . $printestimate_id . $teston . '" target="_blank" data-ajax="false">' . "\n";

		$tag .= $tb . "\t\t\t\t" . '<div class="input_wrap">' . "\n";
		$tag .= $tb . "\t\t\t\t\t" . '<input id="' . $arg_form_name . '_user_company" type="text" name="form_user_company" value="" class="input_text" placeholder="御社名">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '</div>' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<div class="input_wrap">' . "\n";
		$tag .= $tb . "\t\t\t\t\t" . '<input id="' . $arg_form_name . '_user_name" type="text" name="form_user_name" value="" class="input_text" placeholder="お名前">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '</div>' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<div class="input_wrap">' . "\n";
		$tag .= $tb . "\t\t\t\t\t" . '<input id="' . $arg_form_name . '_user_pref" type="text" name="form_user_pref" value="" class="input_text" placeholder="都道府県">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '</div>' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<div class="input_wrap">' . "\n";
		$tag .= $tb . "\t\t\t\t\t" . '<input id="' . $arg_form_name . '_user_tel" type="text" name="form_user_tel" value="" class="input_text" placeholder="電話番号">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '</div>' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<div class="input_wrap">' . "\n";
		$tag .= $tb . "\t\t\t\t\t" . '<input id="' . $arg_form_name . '_user_mail" type="text" name="form_user_mail" value="" class="input_text" placeholder="メールアドレス">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '</div>' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<p class="printestimate_supple text icon_kome supple">こちらからご連絡させていただく場合がございます</p>' . "\n";

		$tag .= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_printestimate_id" type="hidden" name="form_printestimate_id" value="' . $printestimate_id . '">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_item_name" type="hidden" name="form_item_name" value="' . $wp_item_single_array[ 'item_name_01' ] . ' ' . $wp_item_single_array[ 'item_name_02' ] . '">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_nouki_name" type="hidden" name="form_nouki_name" value="">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_pricecategory" type="hidden" name="form_item_pricecategory" value="error">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_item_lot" type="hidden" name="form_item_lot" value="error">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_item_unit" type="hidden" name="form_item_unit" value="' . $wp_item_single_array[ 'unit' ] . '">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_item_price" type="hidden" name="form_item_price" value="error">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_draft_id" type="hidden" name="form_draft_id" value="none">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_send_id" type="hidden" name="form_send_id" value="error">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_sendquantity" type="hidden" name="form_sendquantity" value="error">' . "\n";
		$_SESSION[ 'ticket' ] = md5( uniqid() . mt_rand() );
		$tag .= $tb . "\t\t\t\t" . '<input id="' . $arg_form_name . '_ticket" type="hidden" name="ticket" value="' . htmlspecialchars( $_SESSION[ 'ticket' ], ENT_QUOTES ) . '">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<input type="hidden" name="act" value="on">' . "\n";
		$tag .= $tb . "\t\t\t\t" . '<button type="submit"><img src="' . PUBLICDIR . '/images/item/item_main_btn_printestimate.png" alt="印刷用見積書へ"></button>' . "\n";
		$tag .= $tb . "\t\t\t" . '</form>' . "\n";

		return $tag;
	}

	/* disp_cart_form  */
	function disp_cart_form( $arg_form_name = 'unknown' ) {

		global $draft_db_arr, $send_db_arr;

		$tag = '';
		$tb = "\t\t\t\t\t";

		$tag .= $tb . "\t\t\t\t\t" . '<form id="' . $arg_form_name . '" method="post" action="https://www.hs-honpo.com/cart/html/products/detail2.php" data-ajax="false">' . "\n";
		$tag .= $tb . "\t\t\t\t\t\t" . '<input type="hidden" name="product_id[0]" value="" class="item_cart_id">' . "\n";
		$tag .= $tb . "\t\t\t\t\t\t" . '<input type="hidden" name="quantity[0]" value="1" class="item_quantity">' . "\n";
		if( count( $draft_db_arr ) > 1 ) {
			$tag .= $tb . "\t\t\t\t\t\t" . '<input type="hidden" name="product_id[1]" value="" class="draft_cart_id">' . "\n";
			$tag .= $tb . "\t\t\t\t\t\t" . '<input type="hidden" name="quantity[1]" value="1" class="draft_quantity">' . "\n";
		}
		if( count( $send_db_arr ) > 1 ) {
			$tag .= $tb . "\t\t\t\t\t\t" . '<input type="hidden" name="product_id[2]" value="" class="send_cart_id">' . "\n";
			$tag .= $tb . "\t\t\t\t\t\t" . '<input type="hidden" name="quantity[2]" value="" class="send_quantity">' . "\n";
		}
		$tag .= $tb . "\t\t\t\t\t\t" . '<input type="hidden" name="mode" value="cart">' . "\n";
		$tag .= $tb . "\t\t\t\t\t\t" . '<button type="submit"><img src="' . PUBLICDIR . '/images/item/item_main_btn_new_cart.png" alt="ショッピングカートへ"></button>' . "\n";
		$tag .= $tb . "\t\t\t\t\t" . '</form>' . "\n";

		return $tag;
	}

	/* disp_price_cont  */
	function disp_price_cont( $price_arr_with_num, $pricecategory_arr = array(), $price_lot_arr = array(), $item_id = '', $nouki_id = '', $nouki_arr = array() ) {

		$tag = '';
		$tb = "\t\t\t\t\t";
		$yoneigyobi = '<p class="text"><span class="caution">9月22日(水) 15：00</span> 締切りの<span class="caution">校了</span>・入金分が<span class="caution">9月29日(水) </span> 発送予定です。</p>
						<p class="text"><span class="caution">9月24日(金) 15：00</span> 締切りの<span class="caution">校了</span>・入金分が<span class="caution">9月30日(木) </span> 発送予定です。</p>
						<p class="text"><span class="caution">9月27日(月) 15：00</span> 締切りの<span class="caution">校了</span>・入金分が<span class="caution">10月1日(金) </span> 発送予定です。</p>
						<p class="text"><span class="caution">9月28日(火) 15：00</span> 締切りの<span class="caution">校了</span>・入金分が<span class="caution">10月4日(月) </span> 発送予定です。</p>';
		// $ichieigyobi = '<p class="text"><span class="caution">9月4日(水) 12：00</span> 締切りの<span class="caution">校了</span>・入金分が<span class="caution">9月5日(木) </span> 発送予定です。</p>';
		$ichieigyobi = '<p class="text"><span class="caution">9月24日(金) 12：00</span> 締切りの<span class="caution">校了</span>・入金分が<span class="caution">9月27日(月) </span> 発送予定です。</p>
						<p class="text"><span class="caution">9月27日(月) 12：00</span> 締切りの<span class="caution">校了</span>・入金分が<span class="caution">9月28日(火) </span> 発送予定です。</p>
						<p class="text"><span class="caution">9月28日(火) 12：00</span> 締切りの<span class="caution">校了</span>・入金分が<span class="caution">9月29日(水) </span> 発送予定です。</p>';



		$shohinnomi = '<p class="text"><span class="caution">9月22日(水)</span> ご注文・入金分が<span class="caution">9月24日(金) </span> 発送予定です。</p>
						<p class="text"><span class="caution">9月24日(金)</span> ご注文・入金分が<span class="caution">9月27日(月) </span> 発送予定です。</p>
						<p class="text"><span class="caution">9月27日(月)</span> ご注文・入金分が<span class="caution">9月28日(火) </span> 発送予定です。</p>
						<p class="text"><span class="caution">9月28日(火)</span> ご注文・入金分が<span class="caution">9月29日(水) </span> 発送予定です。</p>';
		global $nouki_name_arr, $wp_item_single_array;

		$arr = $price_arr_with_num;
		if( count( $arr ) > 1 ) {
			$current_class = ( $nouki_id == 'normal' ) ? ' current' : '' ;
			// item_single_price_shipping_part
			if( $item_id === 'normal_regular_label' || $item_id === 'normal_regular_none' ) {
				$tag .= $tb . "\t\t" . '<div id="shipping_' . $nouki_id . '" class="item_single_price_shipping_part' . $current_class . '">' . "\n";
				$tag .= $tb . "\t\t\t" . '<div class="item_deliverydate_cont">' . "\n";


				if( $item_id === 'normal_regular_label' ) {
					$tag .= $tb . "\t\t\t\t" . '<h3 class="heading03">ラベル印刷（' . $nouki_name_arr[ $nouki_id ] . '）の納期</h3>' . "\n";

					for( $i = 0; $i < count( $nouki_arr ); $i++ ) {
						// normal - 15:00 / 1day,2days - 12:00
						$nouki_time = ( $nouki_id === 'normal' ) ? '15：00' : '12：00';
						$tag .= $tb . "\t\t\t\t" . '<p class="text"><span class="caution">' . $nouki_arr[ $i ][ 'print' ] . ' ' . $nouki_time . '</span> 締切りの<span class="caution">校了</span>・入金分が<span class="caution">' . $nouki_arr[ $i ][ 'send' ] . ' </span> 発送予定です。</p>' . "\n";
						// $tag .= $tb .$yoneigyobi . "\n";
					}
				} else
				if( $item_id === 'normal_regular_none' ) {
					$tag .= $tb . "\t\t\t\t" . '<h3 class="heading03">商品のみの納期</h3><div class="mb10"><p class="font_bold">最短当日発送</p><p class="text"><span class="text_red">ご注文当日（平日）の出荷が可能です！！</span><br>15時までのお申し込み完了とご入金確認・カード決済確認にて当日出荷が可能です。<br>（代引きご利用の場合は15時までのお申込み完了が必要となります）<br>※30000個以上ご注文の場合は、出荷日が変わる場合がございます。</p></div><p class="font_bold">通常発送</p>' . "\n";
					for( $i = 0; $i < count( $nouki_arr ); $i++ ) {
						$tag .= $tb . "\t\t\t\t" . '<p class="text"><span class="caution">' . $nouki_arr[ $i ][ 'print' ] . ' </span> ご注文分が<span class="caution">' . $nouki_arr[ $i ][ 'send' ] . ' </span> 発送予定です。</p>' . "\n";
					}
					$tag .= $tb . "\t\t\t\t" . $shohinnomi;

				}
				if( $nouki_id == '2days' ) {
					$tag .= $tb . "\t\t\t\t" . '<p class="text">平素は、販促本舗をご愛顧いただき、誠にありがとうございます。<br /><br />新型コロナウイルス感染症対策として感染リスクの軽減と安全確保を目的としてリモート対応をさせていただいております。<br />何卒ご理解賜りますようお願い申し上げます。<br /><br />お客様にはご迷惑をお掛けし申し訳ございませんが<br />皆様の営業再開に向けての準備に少しでもお役に立てるように尽力させていただきますので、お急ぎの場合は一度、お問い合わせいただくよう宜しくお願い申し上げます。</p>' . "\n";
					}
				//以下は手打ちで追加してるやつ。手打ち必要なくなったら外してOK！
				// if( $nouki_id == 'normal' ) {
				// 	if( $item_id === 'normal_regular_label' ) {
				// 		// $tag .= $tb . "\t\t\t\t" . '<h3 class="heading03">ラベル印刷（通常発送）の納期</h3>' . "\n";
				// 		$tag .= $tb .$yoneigyobi . "\n";
				// 		}
				// 	}
				// if( $nouki_id == '1day' ) {
				// 	if( $item_id === 'normal_regular_label' ) {
				// 		// $tag .= $tb . "\t\t\t\t" . '<h3 class="heading03">ラベル印刷（1営業日発送）の納期</h3>' . "\n";
				// 		$tag .= $tb .$ichieigyobi . "\n";
				// 		}
				// 	}
				//手打ちで追加してるやつここまで
				$tag .= $tb . "\t\t\t" . '</div>' . "\n";
				$tag .= $tb . "\t\t" . '</div>' . "\n";
			}

			// item_single_price_table_part
			$tag .= $tb . "\t\t" . '<div id="price_' . $nouki_id . '" class="item_single_price_table_part part' . $current_class . '">' . "\n";
			$tag .= $tb . "\t\t" . '<h2 class="heading03">商品の作成ロットと価格</h2>' . "\n";
			$tag .= $tb . "\t\t\t" . '<p class="text caution icon_kome">ご注文は該当の料金をクリックしてください。</p>' . "\n";
			$tag .= $tb . "\t\t\t" . '<p class="text caution icon_kome">該当商品がない場合は、<a href="' . PUBLICDIR . '/item/order/?item=' . $wp_item_single_array[ 'item_id' ] . '">お見積りフォーム</a>をご利用ください。</p>' . "\n";
			$tag .= $tb . "\t\t\t" . '<div class="price_table_wrap">' . "\n";


			if( $nouki_id == 'normal' ) {

			$tag .= $tb . "\t\t\t\t" . '<table class="table_item_price table_sp">' . "\n";
			$tag .= $tb . "\t\t\t\t\t" . '<tr>' . "\n";
			$tag .= $tb . "\t\t\t\t\t\t" . '<th scope="col">数量</th>' . "\n";
			foreach( $pricecategory_arr as $k => $v ) {
				$tag .= $tb . "\t\t\t\t\t\t" . '<th scope="col">' .  $v . '</th>' . "\n";
			}
			$tag .= $tb . "\t\t\t\t\t" . '</tr>' . "\n";
			for( $i = 0; $i < count( $price_lot_arr ); $i++ ) {
				$tag .= $tb . "\t\t\t\t\t" . '<tr>' . "\n";
				$tag .= $tb . "\t\t\t\t\t\t" . '<th scope="row">' . $price_lot_arr[ $i ] . '</th>' . "\n";
				foreach( $pricecategory_arr as $k => $v ) {
					$check = true;
					for( $n = 0; $n < count( $arr ); $n++ ) {
						if( $arr[ $n ][ 'price_category_id' ] == $k && $arr[ $n ][ 'lot' ] == $price_lot_arr[ $i ] ) {
							$tag .= $tb . "\t\t\t\t\t\t" . '<td>' . "\n";
							if( ! $arr[ $n ][ 'price' ] ) {
								$tag .= $tb . "\t\t\t\t\t\t" . '<span>－</span>' . "\n";
							} elseif( $arr[ $n ][ 'price_c' ] ) {
								$tag .= $tb . "\t\t\t\t\t\t" . '<a href="#item_singl_modal_area" data-rel="popup" data-transition="pop" data-price="' . $arr[ $n ][ 'price_c' ] . '" data-price_category_id="' . $k . '" data-price_category_name="' . $v . '" data-lot="' . $price_lot_arr[ $i ] . '" data-nouki="' . $nouki_id . '" data-eccube_id="' . $arr[ $n ][ 'cart_id' ] . '" class="for_modal_property handle_modal"><span class="del_price"><del>' . tax_adjust( $arr[ $n ][ 'price' ] ) . '円</del> → </span><span class="price campaign_price add_cart_popup">' . tax_adjust( $arr[ $n ][ 'price_c' ] ) . '円</span></a>' . "\n";
							} else {
								$tag .= $tb . "\t\t\t\t\t\t" . '<a href="#item_singl_modal_area" data-rel="popup" data-transition="pop" data-price="' . $arr[ $n ][ 'price' ] . '" data-price_category_id="' . $k . '" data-price_category_name="' . $v . '" data-lot="' . $price_lot_arr[ $i ] . '" data-nouki="' . $nouki_id . '" data-eccube_id="' . $arr[ $n ][ 'cart_id' ] . '" class="for_modal_property handle_modal"><span class="price add_cart_popup">' . tax_adjust( $arr[ $n ][ 'price' ] ) . '円</span></a>' . "\n";
							}
							$tag .= $tb . "\t\t\t\t\t\t" . '</td>' . "\n";
							$check = false;
							break;
						}
					}
					if( $check ) {
						$tag .= $tb . "\t\t\t\t\t\t" . '<td>' . "\n";
						$tag .= $tb . "\t\t\t\t\t\t\t" . '<p><span>－</span></p>' . "\n";
						$tag .= $tb . "\t\t\t\t\t\t" . '</td>' . "\n";
					}
				}
				$tag .= $tb . "\t\t\t\t\t" . '</tr>' . "\n";
			}
			$tag .= $tb . "\t\t\t\t" . '</table>' . "\n";
			}




			if( $nouki_id == '1day' ) {

			$tag .= $tb . "\t\t\t\t" . '<table class="table_item_price table_sp">' . "\n";
			$tag .= $tb . "\t\t\t\t\t" . '<tr>' . "\n";
			$tag .= $tb . "\t\t\t\t\t\t" . '<th scope="col">数量</th>' . "\n";
			foreach( $pricecategory_arr as $k => $v ) {
				$tag .= $tb . "\t\t\t\t\t\t" . '<th scope="col">' .  $v . '</th>' . "\n";
			}
			$tag .= $tb . "\t\t\t\t\t" . '</tr>' . "\n";
			for( $i = 0; $i < count( $price_lot_arr ); $i++ ) {
				$tag .= $tb . "\t\t\t\t\t" . '<tr>' . "\n";
				$tag .= $tb . "\t\t\t\t\t\t" . '<th scope="row">' . $price_lot_arr[ $i ] . '</th>' . "\n";
				foreach( $pricecategory_arr as $k => $v ) {
					$check = true;
					for( $n = 0; $n < count( $arr ); $n++ ) {
						if( $arr[ $n ][ 'price_category_id' ] == $k && $arr[ $n ][ 'lot' ] == $price_lot_arr[ $i ] ) {
							$tag .= $tb . "\t\t\t\t\t\t" . '<td>' . "\n";
							if( ! $arr[ $n ][ 'price' ] ) {
								$tag .= $tb . "\t\t\t\t\t\t" . '<span>－</span>' . "\n";
							} elseif( $arr[ $n ][ 'price_c' ] ) {
								$tag .= $tb . "\t\t\t\t\t\t" . '<a href="#item_singl_modal_area" data-rel="popup" data-transition="pop" data-price="' . $arr[ $n ][ 'price_c' ] . '" data-price_category_id="' . $k . '" data-price_category_name="' . $v . '" data-lot="' . $price_lot_arr[ $i ] . '" data-nouki="' . $nouki_id . '" data-eccube_id="' . $arr[ $n ][ 'cart_id' ] . '" class="for_modal_property handle_modal"><span class="del_price"><del>' . tax_adjust( $arr[ $n ][ 'price' ] ) . '円</del> → </span><span class="price campaign_price add_cart_popup">' . tax_adjust( $arr[ $n ][ 'price_c' ] ) . '円</span></a>' . "\n";
							} else {
								$tag .= $tb . "\t\t\t\t\t\t" . '<a href="#item_singl_modal_area" data-rel="popup" data-transition="pop" data-price="' . $arr[ $n ][ 'price' ] . '" data-price_category_id="' . $k . '" data-price_category_name="' . $v . '" data-lot="' . $price_lot_arr[ $i ] . '" data-nouki="' . $nouki_id . '" data-eccube_id="' . $arr[ $n ][ 'cart_id' ] . '" class="for_modal_property handle_modal"><span class="price add_cart_popup">' . tax_adjust( $arr[ $n ][ 'price' ] ) . '円</span></a>' . "\n";
							}
							$tag .= $tb . "\t\t\t\t\t\t" . '</td>' . "\n";
							$check = false;
							break;
						}
					}
					if( $check ) {
						$tag .= $tb . "\t\t\t\t\t\t" . '<td>' . "\n";
						$tag .= $tb . "\t\t\t\t\t\t\t" . '<p><span>－</span></p>' . "\n";
						$tag .= $tb . "\t\t\t\t\t\t" . '</td>' . "\n";
					}
				}
				$tag .= $tb . "\t\t\t\t\t" . '</tr>' . "\n";
			}
			$tag .= $tb . "\t\t\t\t" . '</table>' . "\n";
			}




			$tag .= $tb . "\t\t\t" . '</div>' . "\n";
			$tag .= $tb . "\t\t\t" . '<p class="text caption icon_kome">表示の金額は' . TAXWORD . 'です。</p>' . "\n";
			$tag .= $tb . "\t\t" . '</div>' . "\n";
		}
		return $tag;
	}

	/* str */
	$str_seo_title       = $wp_item_single_array[ 'seo_title' ];
	$str_seo_h1          = $wp_item_single_array[ 'seo_h1' ];
	$str_seo_description = $wp_item_single_array[ 'seo_description' ];
	$str_item_id         = ( isset( $wp_item_single_array[ 'item_id' ] ) && $wp_item_single_array[ 'item_id' ] ) ? $wp_item_single_array[ 'item_id' ] : 'no_select';
	$str_category_id     = $wp_item_single_array[ 'the_cat_id' ];
	$str_item_name       = $wp_item_single_array[ 'post_title' ];
	$str_item_short_name = $wp_item_single_array[ 'item_name_01' ] . ' ' . $wp_item_single_array[ 'item_name_02' ];
	$str_item_unit       = $wp_item_single_array[ 'unit' ];
	$str_sendunit        = $wp_item_single_array[ 'sendunit' ];

