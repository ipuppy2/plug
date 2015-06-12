<?php
	/**
	 * 文件上传
	 * 1, 表单域的 name
	 * 2, 上传的类型
	 * 3, 最大大小
	 * 4, 保存的目录
	 * 5, 当目录不存在时,是否创建目录(默认自动创建)
	 */
	class Upload{
		
		private $_sFileName; // 表单名称
		private $_mixType='*';  //文件类型
		private $_iMaxSize=0; // 最大大小
		private $_sSaveDir='./'; // 保存的目录
		private $_bMakeDir=true; // 是否自动创建目录
		private $_iMaxCount; // 最大的上传数(如果是多文件上传)

		private $_sErrorMsg=''; // 错误的信息
		private $_aFiles; // 上传的文件
		private $_aErrorCode; // 
		private $_aAcceptImageType=array('.jpg','.jpeg','.gif','.png','.bmp'); // 可接受的图片类型
		private $_aOutnumberFile=array(); // 超过数量的文件
		private $_aErrorFile=array(); // 上传失败的文件列表
		private $_aTotalFile=array(); // 所有的上传文件
		private $_iSuccessCount=0; // 上传成功的文件数量

		/**
		 * 构造方法
		 * @param [type]  $sFileName [表单名称]
		 * @param string  $sSaveDir  [保存的目录]
		 * @param string  $mixType     [文件的类型]
		 * @param integer $iMaxSize  [最大的大小(单位:m)]
		 * @param [type]  $bMakeDir  [当目录不存在时,是否创建]
		 */
		public function __construct($sFileName,$sSaveDir='./',$mixType='*',$iMaxSize=0,$bMakeDir=true){
			$this->_sFileName=$sFileName;
			$this->_sSaveDir=rtrim(preg_replace(array('/\\\\+/','/\/+/'),array('/','/'),$sSaveDir),'/').'/';
			$this->_mixType=$mixType;
			$this->_iMaxSize=($iMaxSize>0?1024*1024*$iMaxSize:2147483247); // 最大
			$this->_bMakeDir=$bMakeDir;

			// echo $this->_iMaxSize;
			// Yii::app()->end();
		}

		/**
		 * 多文件上传
		 * @param  integer $iMaxCount [最大上传数]
		 * @return [type]             [description]
		 */
		public function multiUpload($iMaxCount=0){
			
			// var_dump(isset($_FILES[$this->_sFileName]));
			// 判断表单是否存在 并且 判断是否为数组
			if(!isset($_FILES[$this->_sFileName]) or !is_array($_FILES[$this->_sFileName])):
				$this->_sErrorMsg='没有上传的文件';
				return false;
			endif;
			
			$this->_iMaxCount=$iMaxCount; // 最大上传数
			$this->_aFiles=$_FILES[$this->_sFileName];

			// 在这里上传
			return $this->mainUpload();

		}

		/**
		 * 单文件上传
		 * @return [type] [description]
		 */
		public function toUpload(){

			if(!isset($_FILES[$this->_sFileName]) or !is_string($_FILES[$this->_sFileName]['name'])):
				$this->_sErrorMsg='没有上传的文件';
				return false;
			endif;

			foreach($_FILES[$this->_sFileName] as $sKey=>$mixValue):
				$this->_aFiles[$sKey][]=$mixValue;
			endforeach;


			return ($mixUpload=$this->mainUpload())?$mixUpload[0]:false;
		}

		/**
		 * 过滤空白的项
		 * @param  [type] $iErrorCode [description]
		 * @return [type]          [description]
		 */
		public function filterEmpty($iErrorCode){
			return 4!=$iErrorCode;
		}

		
		/**
		 * 获取所有的上传文件
		 * @return [type] [description]
		 */
		public function getTotalFile(){
			return $this->_aTotalFile;
		}

		/**
		 * 获取总的上传数量
		 * @return [type] [description]
		 */
		public function getTotalFileCount(){
			return count($this->_aTotalFile);
		}

		/**
		 * 获取上传成功的文件数量
		 * @return [type] [description]
		 */
		public function getSuccessCount(){
			return $this->_iSuccessCount;
		}

		/**
		 * 获取上传错误的文件
		 * @return [type] [description]
		 */
		public function getErrorFile(){
			return array_merge($this->_aErrorFile,$this->_aOutnumberFile);
		}

		/**
		 * 获取错误的文件数量
		 * @return [type] [description]
		 */
		public function getErrorFileCount(){
			return count($this->_aErrorFile)+count($this->_aOutnumberFile);
		}

		/**
		 * 获取超出指定数量的文件
		 * @return [type] [description]
		 */
		public function getOutnumberFile(){
			return $this->_aOutnumberFile;
		}

		/**
		 * 获取超出指定数量的文件数量
		 * @return [type] [description]
		 */
		public function getOutnumberCount(){
			return count($this->_aOutnumberFile);
		}

		/**
		 * 获取错误信息
		 * 当调用的方法返回 false 时,会保存其它错误信息
		 * @return [type] [description]
		 */
		public function getErrorMessage(){
			return $this->_sErrorMsg;
		}


		/**
		 * 主上传方法
		 * @return [type] [description]
		 */
		private function mainUpload(){

			if(!is_array($this->_aFiles['error'])){
				$this->_sErrorMsg='没有上传的文件';
				return false;
			}

			// 找出非空白项
			$this->_aErrorCode=array_filter($this->_aFiles['error'],array($this,'filterEmpty'));

			// 如果没有内容,则代表没有上传的文件
			if(empty($this->_aErrorCode) or !isset($this->_aErrorCode[0]) or is_array($this->_aErrorCode[0])):
				$this->_sErrorMsg='没有上传的文件';
				return false;
			endif;

			
			// 创建目录
			$this->_bMakeDir and !is_dir($this->_sSaveDir) and mkdir($this->_sSaveDir,0777,true);

			$aSuccess=array(); // 上传成功的文件
			$aName=$this->_aFiles['name']; // 文件名
			$aType=$this->_aFiles['type']; // 文件类型
			$aTmpName=$this->_aFiles['tmp_name']; // 临时路径
			$aSize=$this->_aFiles['size']; // 大小
			$this->_iTotalFileCount=count($aName); // 总的上传数量
			// _mixType
			// 限制的类型
			// 如果是字符串
			if(is_string($this->_mixType)){
				$this->_mixType='*'==trim($this->_mixType)?true:explode(',',str_replace(' ','',$this->_mixType));
			}

			// 如果有类型限制
			if(true!==$this->_mixType):
				foreach($this->_mixType as $iKey=>$sType):
					$this->_mixType[$iKey]='.'.strtolower($sType);
				endforeach;
				
			endif;

			
			return $this->_iMaxCount>0?$this->uploadOnHasMaxCount($aName,$aType,$aTmpName,$aSize):$this->uploadOnNoMaxCount($aName,$aType,$aTmpName,$aSize); // 返回上传成功的文件
		}

		/**
		 * 当有数量限制时的上传
		 * @param  [type] $aName    [description]
		 * @param  [type] $aType    [description]
		 * @param  [type] $aTmpName [description]
		 * @param  [type] $aSize    [description]
		 * @return [type]           [description]
		 */
		private function uploadOnHasMaxCount($aName,$aType,$aTmpName,$aSize){
			$aSuccess=array();
			foreach($this->_aErrorCode as $iKey=>$sVal):

				// 记录单个文件的信息
				$this->_aTotalFile[]=array(
					'name'=>$aName[$iKey],
					'size'=>$aSize[$iKey],
					'type'=>$aType[$iKey],
				);

				// _aErrorFile
				// 检测上传数量
				if($this->_iSuccessCount>=$this->_iMaxCount){
					
					$this->_aOutnumberFile[]=array(
						'name'=>$aName[$iKey],
						'error'=>'超出上传数量',
						'errorCode'=>3,
					);
					continue;
				}

				// 检测大小
				// $this->_iMaxSize
				if($aSize[$iKey]>$this->_iMaxSize){
					$this->_aErrorFile[]=array(
						'name'=>$aName[$iKey],
						'error'=>'文件过大',
						'errorCode'=>1,
					);

					continue;
				}

				$sFileExtension=strtolower(strrchr($aName[$iKey],'.')); // 文件扩展名

				// 检测类型
				if(true!==$this->_mixType){
					// 代表有指定类型
					if(!in_array($sFileExtension,$this->_mixType)){
						$this->_aErrorFile[]=array(
							'name'=>$aName[$iKey],
							'error'=>'文件类型不对',
							'errorCode'=>2,
						);
						continue;
					}
				}

				$aImageSize=array();

				// 检测为图片
				if(in_array($sFileExtension,$this->_aAcceptImageType) and !$aImageSize=@getImageSize($aTmpName[$iKey])){
					$this->_aErrorFile[]=array(
						'name'=>$aName[$iKey],
						'error'=>'上传的图片可能已经被损坏',
						'errorCode'=>6,
					);
					continue;
				}

				if(!is_uploaded_file($aTmpName[$iKey])){

					$this->_aErrorFile[]=array(
						'name'=>$aName[$iKey],
						'error'=>'非上传的文件',
						'errorCode'=>4,
					);
					continue;
				}


				$sSaveName=self::generateFileName().$sFileExtension; // 文件名
				$sSavePath=$this->_sSaveDir.$sSaveName; // 保存路径
				

				if(move_uploaded_file($aTmpName[$iKey],$sSavePath)){
					$aSuccess[]=array(
						'oriName'=>$aName[$iKey], // 原文件名
						'saveName'=>$sSaveName, // 保存的文件名
						'savePath'=>$sSavePath, // 保存的路径
						'fileSize'=>$aSize[$iKey], // 文件大小
						'size'=>$aImageSize?$aImageSize[0].'*'.$aImageSize[1]:'', // 如果是图片,则是尺寸;否则为空
					);
					++$this->_iSuccessCount; // 已经上传的数量
				}else{
					$this->_aErrorFile[]=array(
						'name'=>$aName[$iKey],
						'error'=>'文件上传失败',
						'errorCode'=>5,
					);
				}

			endforeach;

			return $aSuccess;
		}

		/**
		 * 当没有上传数量限制时
		 * @param  [type] $aName    [description]
		 * @param  [type] $aType    [description]
		 * @param  [type] $aTmpName [description]
		 * @param  [type] $aSize    [description]
		 * @return [type]           [description]
		 */
		private function uploadOnNoMaxCount($aName,$aType,$aTmpName,$aSize){
			$aSuccess=array();
			foreach($this->_aErrorCode as $iKey=>$sVal):
					
				$this->_aTotalFile[]=array(
					'name'=>$aName[$iKey],
					'size'=>$aSize[$iKey],
					'type'=>$aType[$iKey],
				);
				
				// 检测大小
				// $this->_iMaxSize
				if($aSize[$iKey]>$this->_iMaxSize){
					$this->_aErrorFile[]=array(
						'name'=>$aName[$iKey],
						'error'=>'文件过大',
						'errorCode'=>1,
					);

					continue;
				}

				$sFileExtension=strtolower(strrchr($aName[$iKey],'.')); // 文件扩展名

				// 检测类型
				if(true!==$this->_mixType){
					// 代表有指定类型
					if(!in_array($sFileExtension,$this->_mixType)){
						$this->_aErrorFile[]=array(
							'name'=>$aName[$iKey],
							'error'=>'文件类型不对',
							'errorCode'=>2,
						);
						continue;
					}
				}

				// 检测为图片
				if(in_array($sFileExtension,$this->_aAcceptImageType) and !$aImageSize=@getImageSize($aTmpName[$iKey])){
					$this->_aErrorFile[]=array(
						'name'=>$aName[$iKey],
						'error'=>'上传的图片可能已经被损坏',
						'errorCode'=>6,
					);
					continue;
				}


				if(!is_uploaded_file($aTmpName[$iKey])){

					$this->_aErrorFile[]=array(
						'name'=>$aName[$iKey],
						'error'=>'非上传的文件',
						'errorCode'=>4,
					);
					continue;
				}

				$sSaveName=self::generateFileName().$sFileExtension; // 文件名
				$sSavePath=$this->_sSaveDir.$sSaveName; // 保存路径
				if(move_uploaded_file($aTmpName[$iKey],$sSavePath)){
					$aSuccess[]=array(
						'oriName'=>$aName[$iKey], // 原文件名
						'saveName'=>$sSaveName, // 保存的文件名
						'savePath'=>$sSavePath, // 保存的路径
						'fileSize'=>$aSize[$iKey], // 文件大小
						'size'=>$aImageSize?$aImageSize[0].'*'.$aImageSize[1]:'', // 如果是图片,则是尺寸;否则为空

					);
					++$this->_iSuccessCount;
				}else{
					$this->_aErrorFile[]=array(
						'name'=>$aName[$iKey],
						'error'=>'文件上传失败',
						'errorCode'=>5,
					);
				}

			endforeach;

			return $aSuccess;
		}

		/**
		 * 生成文件名
		 * @return [type] [description]
		 */
		private static function generateFileName(){
			return md5(microtime(true).mt_rand(0,2147483247));
		}

		// private static function 
	}