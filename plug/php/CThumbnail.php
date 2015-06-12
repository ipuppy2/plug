<?php
	/**
	 * 缩略图类
	 * 注意要处理透明(png, gif)
	 * 判断环境先不写
	 * 
	 * 1,缩略图的格式统一为 jpg
	 * 2,有一个方法显示缩略图(在这里返回缩略图的文件名)
	 * 3,缩略图的保存的路径
	 * 4,最大宽度; 最大高度
	 * 5,根据原始图片的尺寸比例来确定画布的尺寸
	 */
	class CThumbnail{
		private $_rDraw; // 画布资源
		private $_rOri; // 原图

		private $_iOriW; // 原图的宽度
		private $_iOriH; // 原图的高度
		private $_iThumbW; // 缩略图的最终宽度
		private $_iThumbH; // 缩略的最终高度
		private $_iOriType; // 原图的类型 1:gif,2:jpg,3:png

		private $_sSavePath; // 缩略图的保存路径
		private $_sOriPath; // 原图的地址
		private $_sAccessType='/(\.jpg|\.jpeg|\.png|\.gif)$/i';
		private $_bDelOri; // 是否删除原图

		/**
		 * 构造方法
		 * 在这里要获取原图的尺寸信息,并计算缩略图的最后尺寸
		 * 
		 * @param [type]  $sOriImgPath [原图的路径]
		 * @param [type]  $sSavePath [缩略图的保存路径]
		 * @param integer $iThumbMaxW     [缩略图的最大宽度]
		 * @param integer $iThumbMaxH     [缩略图的最大高度]
		 * @param boolean $bDelOri   [是否删除原图;默认为 fase,不删除]
		 */
		public function __construct($sOriImgPath,$sSavePath,$iThumbMaxW=200,$iThumbMaxH=200,$bDelOri=false){
			// 初始化一些信息
			$this->_sOriPath=$sOriImgPath; // 原图的地址
			$this->_sSavePath=$sSavePath; // 缩略图的保存路径
			$this->_bDelOri=$bDelOri; // 是否删除原图

			// 检测环境
			$this->checkEnvAndFile();

			// 获取缩略图的最终尺寸
			$this->getThumbSize(getImageSize($sOriImgPath),$iThumbMaxW,$iThumbMaxH);
			// 打开原图
			$this->openOriImg();
			// 创建缩略图的画面,并根据原图获取画布的颜色值
			$this->createThumbDraw($this->getColorByOriImg());
		}

		/**
		 * 计算缩略图最终尺寸
		 * @param  [type] $aImgInfo [原图的尺寸信息]
		 * @param  [type] $iThumbMaxW    [缩略图的最大宽度]
		 * @param  [type] $iThumbMaxH    [缩略图的最大高度]
		 * @return [type]           [无返回值]
		 */
		private function getThumbSize($aImgInfo,$iThumbMaxW,$iThumbMaxH){
			// 记录原图的尺寸
			$this->_iOriW=$aImgInfo[0]; // 宽度
			$this->_iOriH=$aImgInfo[1]; // 高度
			$this->_iOriType=$aImgInfo[2]; // 原图的类型

			// 在这里计算缩略图的最终尺寸
			if($this->_iOriW>$this->_iOriH){
				// 这里是宽度最大
				// 如果原图的宽度大于高度
				// 判断图的宽度是否大于指定的宽度]
				// 这里已经确定缩略图的宽度了
				$this->_iThumbW=$this->_iOriW>$iThumbMaxW?$iThumbMaxW:$this->_iOriW;
				// 判断原图 的高度和指定的缩略图高度
				if($this->_iOriH>$iThumbMaxH and $this->_iThumbW==$this->_iOriW){
					// 在这里重新计算缩略图的最终宽度
					$this->_iThumbH=$iThumbMaxH;
					$this->_iThumbW=round($iThumbMaxH/$this->_iOriH*$this->_iOriW);
				}else{
					$this->_iThumbH=round($this->_iThumbW/$this->_iOriW*$this->_iOriH); // 四舍五入
				}
			}else{
				$this->_iThumbH=$this->_iOriH>$iThumbMaxH?$iThumbMaxH:$this->_iOriH;
				if($this->_iOriW>$iThumbMaxW and $this->_iThumbH==$this->_iOriH){
					// 在这里重新计算缩略图的最终高度
					$this->_iThumbW=$iThumbMaxW;
					$this->_iThumbH=round($iThumbMaxW/$this->_iOriW*$this->_iOriH);
				}else{
					$this->_iThumbW=round($this->_iThumbH/$this->_iOriH*$this->_iOriW); // 四舍五入
				}
			}
		}

		/**
		 * 根据原图获取画布的颜色值
		 * 因为要处理透明,所以要这样做
		 * @return [type] [description]
		 */
		private function getColorByOriImg(){
			if(1==$this->_iOriType){
				// 如果是 GIF
				// 判断 gif 是否有透明色
				if(-1!=($iGifTransColorIndex=imageColorTransparent($this->_rOri))){
					// 说明有透明色
					return imageColorsForIndex($this->_rOri,$iGifTransColorIndex);
				}
			}

			// 如果是 PNG ,则要保存 alpha
			3==$this->_iOriType and imageSaveAlpha($this->_rOri,true);

			// 返回最终颜色
			return array('red'=>255,'green'=>255,'blue'=>255);
		}

		/**
		 * 打开原图
		 * @return [type] [description]
		 */
		private function openOriImg(){
			switch($this->_iOriType){
				//  GIF
				case 1:
					$this->_rOri=imageCreateFromGif($this->_sOriPath);
				break;

				// jpg,jpeg
				case 2:
					$this->_rOri=imageCreateFromJpeg($this->_sOriPath);
				break;

				// PNG
				case 3:
					$this->_rOri=imageCreateFromPng($this->_sOriPath);
				break;
			}
		}

		/**
		 * 创建缩略图的画布
		 * @param  [type] $aThumbBgColor [画布的颜色值:array('red'=>值,'green'=>值,'blue'=>值)]
		 * @return [type]                [description]
		 */
		private function createThumbDraw($aThumbBgColor){

			// $this->_iThumbW=200;
			// $this->_iThumbH=200;

			// p($aThumbBgColor);
			$this->_rDraw=imageCreateTrueColor($this->_iThumbW,$this->_iThumbH);
			
			/**
			 * 排除 PNG 图片
			 * 只对 GIF 图片进行透明度设置处理
			 */
			// if(1==$this->_iOriType){

				// 设置背景色
				$iDrawBg=imageColorAllocateAlpha($this->_rDraw,$aThumbBgColor['red'],$aThumbBgColor['green'],$aThumbBgColor['blue'],127);
				// 填充背景色
				imageFill($this->_rDraw,0,0,$iDrawBg);
				// 设置透明色
				imageColorTransparent($this->_rDraw,$iDrawBg);
			// }

				// imageFill($this->_rDraw,imageColor);


			// 合并图片,创建缩略图
			function_exists('imageCopyResampled')?
				imageCopyResampled($this->_rDraw,$this->_rOri,0,0,0,0,$this->_iThumbW,$this->_iThumbH,$this->_iOriW,$this->_iOriH)
			:
				imageCopyResized($this->_rDraw,$this->_rOri,0,0,0,0,$this->_iThumbW,$this->_iThumbH,$this->_iOriW,$this->_iOriH);
		}

		/**
		 * 在这里检测环境和检测文件
		 * @return [type] [description]
		 */
		private function checkEnvAndFile(){
			try{
				// 检测环境
				// GD 库
				if(!extension_loaded('gd')){
					throw new Exception('the extension gd is no loaded!!');
				}

				// 检测图片文件
				if(!file_exists($this->_sOriPath)){
					// 文件不存在
					throw new Exception('the file '.$this->_sOriPath.' is not exists!!');
				}

				// 检测图片的类型
				if(!preg_match($this->_sAccessType,$this->_sOriPath)){
					throw new Exception('the image file is not allowed!!');
				}
			}catch(Exception $oE){
				die($oE->getMessage());
			}
		}

		/**
		 * 
		 * @return [type] [description]
		 */
		private function getSaveImgFunction(){
			if(1==$this->_iOriType) return 'imageGif';
			if(2==$this->_iOriType) return 'imageJpeg';
			return 'imagePng';
		}

		/**
		 * 获取图片保存的扩展名
		 * @return [type] [description]
		 */
		private function getImageExtendsion(){
			if(1==$this->_iOriType) return '.gif';
			if(2==$this->_iOriType) return '.jpg';
			return '.png';
		}

		/**
		 * 获得缩略图(只需要调用这个方法就可以了)
		 * @return [array] [如果成功,返回一个数组:array('sOriSize'=>'原图的尺寸','sThumbSize'=>'缩略图的尺寸','sThumbName'=>缩略图的名称(不包含路径))]
		 */
		public function getThumb(){
			// 删除原图
			$this->_bDelOri and unlink($this->_sOriPath);
			$sThumbName=md5(mt_rand(0,999999).microtime()).$this->getImageExtendsion(); // 缩略图的保存路径
			
			// 如果保存目录不存在,则创建目录
			is_dir($this->_sSavePath) or mkdir($this->_sSavePath,0777,true);

			$sFunc=$this->getSaveImgFunction();

			// header('Content-Type:image/png');
			// $sFunc($this->_rDraw);
			// $sFunc($this->_rDraw,rtrim($this->_sSavePath,'/').'/'.$sThumbName);
			// exit;

			// echo $sFunc;
			// exit;


			// 3==$this->_iOriType and imageSaveAlpha($this->_rDraw,true);


			// 保存缩略图
			if($sFunc($this->_rDraw,rtrim($this->_sSavePath,'/').'/'.$sThumbName)){
				// 如果保存成功,则返回缩略图的名称
				return array(
					'sOriSize'=>$this->_iOriW.'*'.$this->_iOriH, // 原图的尺寸
					'sThumbSize'=>$this->_iThumbW.'*'.$this->_iThumbH, // 缩略图的尺寸
					'sThumbName'=>$sThumbName
				);
			}else{
				return '缩略图保存失败!!';
			}
		}


		/**
		 * 析构方法
		 * 在这里销毁图片资源
		 */
		public function __destruct(){
			is_resource($this->_rOri)?imageDestroy($this->_rOri):''; // 原图
			is_resource($this->_rDraw)?imageDestroy($this->_rDraw):''; // 画布
		}
	}

	// $sSrc='./64.png';
	// $oThumb=new CThumbnail($sSrc,'./');
	// $oThumb->getThumb();
?>