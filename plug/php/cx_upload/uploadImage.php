<?php
	
	require_once __DIR__.'/config/const.php';

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<link type="text/css" rel="styleSheet" href="<?php echo S_CSS_URL; ?>/chu.css" />
		<script type="text/javascript" src="<?php echo S_JS_URL; ?>/jquery-1.11.1.js"></script>
		<script type="text/javascript" src="<?php echo S_JS_URL; ?>/chu.js"></script>
		<style type="text/css">
		<!--
			/*单个图片的外围*/
			div.signle-image-wrap{
				width:120px;
				height:120px;
				float:left;
				border-radius:5px;
				position:relative;
				overflow:hidden;
				display:inline;
			}


			
			/*当前的图片*/
			div.image-current-wrap{
				width:100%;
				height:100%;
				overflow:hidden;
			}

			div.image-current-wrap img{
				width:100%;
			}

			/*加载,加载完成*/
			div.image-loadding-wrap,div.image-has-loaded-wrap{
				position:absolute;
				left:0px;
				top:0px;
				width:100%;
				height:100%;
				z-index:0;
			}

			/*加载中*/
			div.image-loadding-wrap{
				/*background-color:#ccc;*/
				z-index:3;
				display:none;
				border-radius:5px;
			}

			div.image-loadding-wrap img{
				display:inline-block;
				width:100%;
				/*height:*/
			}

			/*图片上传域*/
			div.image-upload-field-ico-wrap{
				background-image:url(./images/static/img_upload.png);
				border-radius:5px;
				background-repeat:no-repeat;
				background-position:0px 0px;
				cursor:pointer;
				/*overflow:auto;*/
			}

			input.image-upload-file-field-input{
				display:block;
				position:absolute;

				left:0px;
				top:0px;
				_left:-5px; /*IE6*/
				*left:-5px; /*IE7*/
				width:100%;
				height:100%;
				font-size:40px;
				line-height:120px;
				cursor:pointer;
				opacity:0;
				filter:alpha(opacity:0);
			}

			input.image-upload-file-field-input:focus{
				outline:none;
				blr:expression(this.onFocus=this.blur());
			}
		-->
		</style>
	</head>
	<body>
		
		<form action="<?php echo S_HANDLE_URL; ?>" method="post" target="upload_image_iframe" id="image_upload_form" enctype="multipart/form-data">

			<!-- 已经上传的图片数量 s -->
			<input type="hidden" id="hidden_uploaded_count" name="uploaded_count" />
			<!-- 已经上传的图片数量 e -->
	 
			<!-- 上传域 s -->
			<div class="signle-image-wrap image-upload-filed-wrap">

				

				<!-- 当图片还没有加载完成显示的内容 s -->
				<div class="image-loadding-wrap" id="upload_loadding_wrap">
					<img src="<?php echo S_IMG_URL; ?>/static/image_loadding.gif" />
				</div>
				<!-- 当图片还没有加载完成显示的内容 e -->


				<!-- 当图片加载完成后,显示的内容 -->
				<div class="image-has-loaded-wrap" id="image_has_loaded_wrap">

					<!-- 当前图片 s -->
					<div class="image-current-wrap image-upload-field-ico-wrap">
						
						<input type="file" name="aUploadImageFileFieldListName[]" <?php echo 1==I_MAX_UPLOAD_COUNT?'':' multiple="multiple"'; ?> accept="image/*" id="image_upload_file_field" class="image-upload-file-field-input" />

					</div>
					<!-- 当前图片 e -->

				</div>
			</div>
			<!-- 上传域 e -->
		</form>

		<iframe src="" name="upload_image_iframe" style="width:900px;height:1000px;"></iframe>
		<script type="text/javascript">
		<!--
			var oForm=$('#image_upload_form'),
			oUploadLoaddingWrap=$('#upload_loadding_wrap'), // 显示现在上传
			oHasLoadedWrap=$('#image_has_loaded_wrap'),
			oParentWin=window.top, // 父窗口
			
			oUploadedCount=document.getElementById('hidden_uploaded_count'), // 已经上传的图片数量
			oFileField=$('#image_upload_file_field').change(function (){
				
				oUploadedCount.value=$('<?php echo S_WRAP_SELECTOR; ?>:eq(<?php echo I_WRAP_INDEX; ?>)',oParentWin.document).find('li.signle-image-public-wrap-no-style-for-js').size();
				
				oForm.submit();
				beforeUpload();
			});

			/**
			 * 上传前
			 * 目前只有一个地方调用
			 * @return {[type]} [description]
			 */
			function beforeUpload(){
				oUploadLoaddingWrap.fadeIn();
				oHasLoadedWrap.hide();
			}

			/**
			 * 上传完成后
			 * @param  {[type]} aImgList [description]
			 * @return {[type]}          [description]
			 */
			function afterUpload(aImgList){

				// 清空上传域的值
				oFileField.val('');

				oUploadLoaddingWrap.fadeOut();
				oHasLoadedWrap.show();

				// 调用父窗口的上传成功后的方法
				typeof oParentWin.afterUpload=='function' && oParentWin.afterUpload({
					aImgList:aImgList,
					sWrapSelector:'<?php echo S_WRAP_SELECTOR; ?>',
					iIndex:<?php echo I_WRAP_INDEX; ?>,
					sHiddenName:'<?php echo S_HIDDEN_NAME ?>',
					iMaxImageUploadCount:<?php echo I_MAX_UPLOAD_COUNT; ?>,
				});
			}

			/**
			 * 上传失败
			 * @param  {[type]} sMsg [description]
			 * @return {[type]}      [description]
			 */
			function uploadError(sMsg){
				// 清空上传域的值
				oFileField.val('');

				oUploadLoaddingWrap.fadeOut();
				oHasLoadedWrap.show();
				oParentWin.uploadError(sMsg);
			}
		//-->
		</script>
	</body>
</html>
