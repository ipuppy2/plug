<?php
	/**
	 * 常量
	 */
	
	/**
	 * 非路径常量
	 */
	defined('I_MAX_UPLOAD_COUNT') or define('I_MAX_UPLOAD_COUNT',200); // 最大上传数
	defined('S_HIDDEN_NAME') or define('S_HIDDEN_NAME','img_list'); // 隐藏的 name
	defined('S_WRAP_SELECTOR') or define('S_WRAP_SELECTOR','#uploaded_wrap'); // 外围的选择器
	defined('I_WRAP_INDEX') or define('I_WRAP_INDEX',0); // 这个外围在当前是位置(如果外围选择器是 class name)


	/**
	 * 目录常量
	 */
	defined('S_TEMP_DIR') or define('S_TEMP_DIR','./upload/temp'); // 临时保存的目录
	
	

	/**
	 * URL 常量
	 */
	defined('S_CSS_URL') or define('S_CSS_URL','./css'); // css 路径
	defined('S_JS_URL') or define('S_JS_URL','./js'); // js 路径
	defined('S_TEMP_URL') or define('S_TEMP_URL','./upload/temp'); // 临时保存的 url
	defined('S_IMG_URL') or define('S_IMG_URL','./images'); // 静态图片URL
	defined('S_HANDLE_URL') or define('S_HANDLE_URL','./toUploadImage.php'); // 处理上传的页面
