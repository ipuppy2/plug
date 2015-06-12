<?php
	require_once __DIR__.'/config/const.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<link type="text/css" rel="styleSheet" href="./css/chu.css" />
		<link type="text/css" rel="styleSheet" href="./css/form.css" />
		<script type="text/javascript" src="./js/jQuery-1.11.1.js"></script>
		<script type="text/javascript" src="./js/chu.js"></script>
		<!-- // <script type="text/javascript" src="./js/form.js"></script> -->
		<script type="text/javascript">
		<!--
			$(document).ready(function (){
				/*window.oImgList=new ImageUploadList({
					mainWrapSelector:'#uploaded_wrap',
				});
				oImgList.getElement().hoverImage().deleteImage();*/

				var oUploadField=afterUploadImageList({
					mainWrapSelector:'#img_upload_list_wrap',
				});


				var oUploadField=afterUploadImageList({
					mainWrapSelector:'#img_upload_list_wrap2',
				});
			});
		//-->
		</script>
	</head>
	<body>
		<!-- 图片 s -->
		<div id="uploaded_wrap" class="input-list-wrap">
			<div class="img-list-wrap">
				
				<div id="img_upload_list_wrap" class="textarea-control-wrap textarea-control-public upload-image-list-wrap">
					<ul class="">
						<!-- 上传域 s -->
						<li class="signle-image-wrap image-upload-filed-wrap">
							<iframe class="upload-image-frame" src="./uploadImage.php" scrolling="no" frameborder="0"></iframe>
						</li>
						<!-- 上传域 e -->

					</ul>
				</div>
			</div>
		</div>


		<div id="" class="input-list-wrap">
			<div class="img-list-wrap">
				
				<div id="img_upload_list_wrap2" class="textarea-control-wrap textarea-control-public upload-image-list-wrap">
					<ul class="">
						<!-- 上传域 s -->
						<li class="signle-image-wrap image-upload-filed-wrap">
							<iframe class="upload-image-frame" src="./uploadImage2.php" scrolling="no" frameborder="0"></iframe>
						</li>
						<!-- 上传域 e -->

					</ul>
				</div>
			</div>
		</div>

		<!-- 图片 e -->
		<script type="text/javascript" src="./js/imageList.js"></script>

		<script type="text/javascript">
		<!--
			/*var sModelName='',
			
			sImageUrl='./images';*/




			// upload('#uploaded_wrap');

		//-->
		</script>
	</body>
</html>
