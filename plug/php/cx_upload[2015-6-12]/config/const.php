<?php
	/**
	 * 常量
	 */
	
	/**
	 * 非路径常量
	 */
	defined('I_MAX_UPLOAD_COUNT') or define('I_MAX_UPLOAD_COUNT',20); // 最大上传数
	


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
