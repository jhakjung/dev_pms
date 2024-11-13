<?php


// 여기서부터 custom
function restrict_admin_menu_for_subscribers() {
	if (is_admin()) {
		$user = wp_get_current_user();
		if (in_array('subscriber', (array) $user->roles)) {
			// 관리자 화면의 "글" 메뉴를 제외한 나머지 메뉴를 숨깁니다.
			remove_menu_page('index.php'); // 대시보드
			remove_menu_page('edit.php?post_type=page'); // 페이지
			remove_menu_page('upload.php'); // 미디어
			remove_menu_page('edit-comments.php'); // 댓글
			remove_menu_page('themes.php'); // 테마
			remove_menu_page('plugins.php'); // 플러그인
			remove_menu_page('users.php'); // 사용자
			remove_menu_page('tools.php'); // 도구
			remove_menu_page('options-general.php'); // 설정
			remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category'); // 카테고리
			remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag'); // 태그
		  }
		}
	}
  add_action('admin_menu', 'restrict_admin_menu_for_subscribers', 99);

  function hide_add_new_category_and_most_used_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		echo '<style>
			#category-add-toggle, /* "새 카테고리 추가" 링크 */
			.categorydiv .tabs-panel.tabs-panel-active { display: none !important; } /* "가장 많이 사용됨" 탭 */
		</style>';
	}
  }
  add_action('admin_head-post.php', 'hide_add_new_category_and_most_used_for_subscribers');
  add_action('admin_head-post-new.php', 'hide_add_new_category_and_most_used_for_subscribers');

  function hide_category_options_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		echo '<style>
			#category-add-toggle, /* "새 카테고리 추가" 링크 */
			.categorydiv .hide-if-no-js.tabs { display: none !important; } /* "가장 많이 사용됨" 탭 */
		</style>';
	}
  }
  add_action('admin_head-post.php', 'hide_category_options_for_subscribers');
  add_action('admin_head-post-new.php', 'hide_category_options_for_subscribers');

  function customize_tag_box_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		echo '<style>
			/* 태그 리스트에 고정 높이와 스크롤 추가 */
			.tag-checkbox-list {
				max-height: 200px; /* 고정 높이 */
				overflow-y: auto; /* 스크롤 추가 */
				border: 1px solid #ddd; /* 박스 테두리 */
				padding: 10px;
				margin-bottom: 20px;
			}
			/* 한 줄에 여러 개의 태그 체크박스 배치 */
			.tag-checkbox-list label {
				display: inline-block;
				width: 45%; /* 한 줄에 두 개씩 배치 */
				margin-bottom: 5px;
				white-space: nowrap; /* 텍스트 줄바꿈 방지 */
			}
		</style>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("#post_tag .ajaxtag").css("margin-top", "20px");

				// 모든 태그를 체크박스 형태로 나열
				$.ajax({
					url: ajaxurl,
					type: "POST",
					data: {
						action: "get_all_tags"
					},
					success: function(response) {
						var tagList = JSON.parse(response);
						var checkboxHTML = "<div class=\'tag-checkbox-list\'><p>태그를 선택하세요:</p>";
						tagList.forEach(function(tag) {
							checkboxHTML += "<label><input type=\'checkbox\' name=\'tax_input[post_tag][]\' value=\'" + tag.term_id + "\'> " + tag.name + "</label>";
						});
						checkboxHTML += "</div>";
						$("#post_tag").prepend(checkboxHTML);
					}
				});
			});
		</script>';
	}
  }
  add_action('admin_footer-post.php', 'customize_tag_box_for_subscribers');
  add_action('admin_footer-post-new.php', 'customize_tag_box_for_subscribers');

  // 태그 데이터를 가져오는 Ajax 콜백 함수
  function get_all_tags_callback() {
	$tags = get_terms(array(
		'taxonomy' => 'post_tag',
		'hide_empty' => false
	));
	echo json_encode($tags);
	wp_die();
  }
  add_action('wp_ajax_get_all_tags', 'get_all_tags_callback');

  function hide_most_used_tags_link_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		echo '<style>
			#tagsdiv-post_tag .tagcloud-link { display: none !important; } /* "가장 많이 사용된 태그 중 선택" 링크 숨기기 */
		</style>';
	}
  }
  add_action('admin_head-post.php', 'hide_most_used_tags_link_for_subscribers');
  add_action('admin_head-post-new.php', 'hide_most_used_tags_link_for_subscribers');

  function redirect_subscribers_to_home() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles) && is_admin()) {
		$current_screen = get_current_screen();
		// post-new.php 페이지가 아닌 경우에만 리디렉트
		if ($current_screen && $current_screen->base !== 'post') {
			wp_redirect(home_url());
			exit;
		}
	}
  }
  add_action('admin_init', 'redirect_subscribers_to_home');


  function hide_admin_bar_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		add_filter('show_admin_bar', '__return_false');
	}
  }
  add_action('after_setup_theme', 'hide_admin_bar_for_subscribers');


  function hide_admin_elements_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		echo '<style>
			#postimagediv { display: none !important; } /* 특성이미지 설정 탭 숨기기 */
			#edit-slug-box { display: none !important; } /* 고유주소 편집 라인 숨기기 */
			#contextual-help-link-wrap, /* 도움말 메뉴 숨기기 */
			#screen-options-link-wrap { display: none !important; } /* 화면 옵션 메뉴 숨기기 */
		</style>';
	}
  }
  add_action('admin_head-post.php', 'hide_admin_elements_for_subscribers');
  add_action('admin_head-post-new.php', 'hide_admin_elements_for_subscribers');

  function hide_publish_box_elements_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		echo '<style>
			/* 공개 박스에서 특정 요소 숨기기 */
			#save-post, /* 임시글로 저장 버튼 숨기기 */
			#post-preview, /* 미리보기 버튼 숨기기 */
			.misc-pub-section, /* 상태, 가시성, 즉시발행 숨기기 */
			#minor-publishing { display: none !important; } /* 공개 박스 하단 숨기기 */
		</style>';
	}
  }
  add_action('admin_head-post.php', 'hide_publish_box_elements_for_subscribers');
  add_action('admin_head-post-new.php', 'hide_publish_box_elements_for_subscribers');

  function customize_editor_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		echo '<style>
			/* 비주얼 탭 숨기기 */
			#content-tmce, /* 비주얼 탭 버튼 */
			#wp-content-editor-tools .wp-editor-tabs span { display: none !important; }

			/* 입력 박스 맨 아래 상태 정보 숨기기 */
			#post-status-info { display: none !important; }
		</style>';

		echo '<script type="text/javascript">
			jQuery(document).ready(function($) {
				/* "미디어 추가" 버튼의 텍스트를 "파일 추가"로 변경 */
				$("#insert-media-button").attr("title", "파일 추가").text("파일 추가");

				/* 텍스트 탭만 활성화 (비주얼 탭 강제 비활성화) */
				if ($("#content-tmce").hasClass("active")) {
					$("#content-tmce").removeClass("active");
					$("#content-html").addClass("active");
					$("#wp-content-wrap").removeClass("tmce-active").addClass("html-active");
				}
			});
		</script>';
	}
  }
  add_action('admin_head-post.php', 'customize_editor_for_subscribers');
  add_action('admin_head-post-new.php', 'customize_editor_for_subscribers');

  function load_child_categories() {
	if (isset($_POST['parent_id'])) {
		$parent_id = intval($_POST['parent_id']);
		$child_categories = get_terms(array(
			'taxonomy' => 'category',
			'hide_empty' => false,
			'parent' => $parent_id,
		));
		if (!empty($child_categories) && !is_wp_error($child_categories)) {
			echo json_encode($child_categories);
		} else {
			echo json_encode([]);
		}
	}
	wp_die();
  }
  add_action('wp_ajax_load_child_categories', 'load_child_categories');
  add_action('wp_ajax_nopriv_load_child_categories', 'load_child_categories');

  function customize_category_selection_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				// 초기 설정: 부모 카테고리만 표시
				$("#categorychecklist li").each(function() {
					if ($(this).has("ul").length) {
						$(this).find("ul").remove(); // 자식 카테고리를 미리 제거
					}
				});

				// 부모 카테고리 클릭 시 자식 카테고리 로드
				$("#categorychecklist input[type=checkbox]").on("change", function() {
					var parent_id = $(this).val();
					var $this = $(this);

					// 선택한 부모 카테고리에 자식 카테고리 불러오기
					if ($(this).is(":checked")) {
						$.ajax({
							url: ajaxurl,
							type: "POST",
							data: {
								action: "load_child_categories",
								parent_id: parent_id
							},
							success: function(response) {
								var children = JSON.parse(response);
								if (children.length > 0) {
									var childHTML = "<ul style=\'margin-left: 20px;\'>";
									children.forEach(function(child) {
										childHTML += "<li><label><input type=\'checkbox\' name=\'post_category[]\' value=\'" + child.term_id + "\'> " + child.name + "</label></li>";
									});
									childHTML += "</ul>";
									$this.closest("li").append(childHTML);
								}
							}
						});
					} else {
						// 부모 카테고리 선택 해제 시 자식 카테고리 제거
						$(this).closest("li").find("ul").remove();
					}
				});
			});
		</script>
		<?php
	}
  }
  add_action('admin_head-post.php', 'customize_category_selection_for_subscribers');
  add_action('admin_head-post-new.php', 'customize_category_selection_for_subscribers');

  function customize_category_box_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		echo '<style>
			/* "가장 많이 사용됨" 링크 숨기기 */
			.category-tabs .hide-if-no-js { display: none !important; }
		</style>';

		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				// 모든 카테고리를 기본으로 표시하고 자식 카테고리는 부모 선택 시 표시
				$("#categorychecklist li ul").remove(); // 자식 카테고리 초기 제거

				// 부모 카테고리를 클릭할 때 자식 카테고리를 동적으로 표시
				$("#categorychecklist input[type=checkbox]").on("change", function() {
					var parent_id = $(this).val();
					var $this = $(this);

					// 선택한 부모 카테고리에 자식 카테고리 불러오기
					if ($(this).is(":checked")) {
						$.ajax({
							url: ajaxurl,
							type: "POST",
							data: {
								action: "load_child_categories",
								parent_id: parent_id
							},
							success: function(response) {
								var children = JSON.parse(response);
								if (children.length > 0) {
									var childHTML = "<ul style=\'margin-left: 20px;\'>";
									children.forEach(function(child) {
										childHTML += "<li><label><input type=\'checkbox\' name=\'post_category[]\' value=\'" + child.term_id + "\'> " + child.name + "</label></li>";
									});
									childHTML += "</ul>";
									$this.closest("li").append(childHTML);
								}
							}
						});
					} else {
						// 부모 카테고리 선택 해제 시 자식 카테고리 제거
						$(this).closest("li").find("ul").remove();
					}
				});
			});
		</script>
		<?php
	}
  }
  add_action('admin_head-post.php', 'customize_category_box_for_subscribers');
  add_action('admin_head-post-new.php', 'customize_category_box_for_subscribers');

  function customize_editor_ui_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		echo '<style>
			/* 상단의 "새 글 추가" 버튼 숨기기 */
			.wrap h1.wp-heading-inline { display: none !important; }
		</style>';

		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				// 제목 입력 부분의 placeholder 값 변경
				$("#title-prompt-text").text("제목");
			});
		</script>
		<?php
	}
  }
  add_action('admin_head-post.php', 'customize_editor_ui_for_subscribers');
  add_action('admin_head-post-new.php', 'customize_editor_ui_for_subscribers');

  function customize_tag_box_help_text_for_subscribers() {
	$user = wp_get_current_user();
	if (in_array('subscriber', (array) $user->roles)) {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				// 태그 안내 문구 변경
				$("#new-tag-post_tag-desc").text("태그 추가(복수 태그 추가는 쉼표로 분리)");
			});
		</script>
		<?php
	}
  }
  add_action('admin_head-post.php', 'customize_tag_box_help_text_for_subscribers');
  add_action('admin_head-post-new.php', 'customize_tag_box_help_text_for_subscribers');

  function theme_register_menus() {
	register_nav_menus(array(
		'primary-menu' => __('Primary Menu'),
		'footer-menu'  => __('Footer Menu')
	));
  }
  add_action('after_setup_theme', 'theme_register_menus');


  function redirect_to_post_new() {
    if (is_page('글작성')) { // 페이지 슬러그 또는 제목이 "글작성"인지 확인
        wp_redirect(admin_url('post-new.php'));
        exit;
    }
}
add_action('template_redirect', 'redirect_to_post_new');

function customize_media_upload_screen_for_subscribers() {
    $user = wp_get_current_user();
    if (in_array('subscriber', (array) $user->roles)) {
        ?>
        <style>
            /* 1. 왼쪽 패널의 "작업" 부분 숨기기 */
            .media-frame-menu { display: none !important; }

            /* 3. "미디어 라이브러리" 탭 숨기기 */
            #menu-item-browse { display: none !important; }
        </style>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // 2. "미디어 추가" 타이틀을 "파일 추가"로 변경
                $(".media-frame-title h1").text("파일 추가");
            });
        </script>
        <?php
    }
}
add_action('print_media_templates', 'customize_media_upload_screen_for_subscribers');