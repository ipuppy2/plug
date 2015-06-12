<?php
	require_once __DIR__.'/config/const.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<link type="text/css" rel="styleSheet" href="<?php echo S_CSS_URL; ?>/chu.css" />
		<script type="text/javascript" src="<?php echo S_JS_URL; ?>/jquery-1.11.1.js"></script>
		<script type="text/javascript">
		<!--
			(function (){

				var oParentWin=window.parent; // 获取当前 window 的父 window

				<?php
					$iMaxUploadCount=I_MAX_UPLOAD_COUNT; // 最大上传数

					if(0==I_MAX_UPLOAD_COUNT)
						$iMaxUploadCount=0;
					else{

						// 已经上传的数量
						// 这个要从 session 里获取
						$iUploadedCount=isset($_POST['uploaded_count'])?$_POST['uploaded_count']:0;
						
						$iMaxUploadCount-=$iUploadedCount;
					}

					// 上传类
					require_once __DIR__.'/Upload.php';

					$oUpload=new Upload('aUploadImageFileFieldListName',S_TEMP_DIR,array('jpg','png','gif','jpeg'),1);

					$mixUploaded=$oUpload->multiUpload($iMaxUploadCount>0?$iMaxUploadCount:1);

					if($mixUploaded):
						// 上传成功
				?>
				oParentWin.afterUpload([<?php
					$aImgListInfo=array();
					
					foreach($mixUploaded as $aImg):
						// S_IMG_TMP_URL
						$aImgListInfo[]='{
							src:"'.S_TEMP_URL.'/'.$aImg['saveName'].'",
							name:"'.$aImg['saveName'].'",
							size:"'.$aImg['size'].'"
							
						}';
					endforeach;
					echo join(',',$aImgListInfo);
				?>],<?php echo $oUpload->getOutnumberCount(); ?>);

				<?php else: ?>
				// 上传失败
				oParentWin.uploadError('上传失败');
				<?php endif; ?>
				
			})();
		//-->
		</script>


	</head>
	<body>
		<?php



			// CX::p($_FILES);
			// exit;
			// Yii::import('ext.Upload');
			// Upload::$multiArray=true;
			// Upload::$multiFileName='aUploadImageFileFieldListName';
			// $oUpload=new Upload(ucwords($this->id),'./upload',array('jpg'),1);
			
			// var_dump($oUpload->multiUpload(3));
			
			// // var_dump($oUpload->toUpload());

			// echo '总的上传文件:<br />';
			// CX::p($oUpload->getTotalFile());

			// echo '总的数量:<br />';
			// CX::p($oUpload->getTotalFileCount());

			// echo '上传成功的数量:<br />';
			// CX::p($oUpload->getSuccessCount());

			// echo '错误的文件:<br />';
			// CX::p($oUpload->getErrorFile());

			// echo '错误的数量:<br />';
			// CX::p($oUpload->getErrorFileCount());

			// echo '超出指定数量的文件:<br />';
			// CX::p($oUpload->getOutnumberFile());
			// echo '超出指定数量的文件数量:<br />';
			// CX::p($oUpload->getOutnumberCount());
		?>
	</body>
</html>