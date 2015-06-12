/**
 * 上传成功
 * {
 * 	aImgList:[], // 上传成功后的图片列表
 * 	sWrapSelector:, // 外围的选择器
 * 	iIndex:, // 第几个外围
 * 	sHiddenName:, // 隐藏域的 name
 * 	iMaxImageUploadCount: // 最大上传数量
 * }
 * @return {[type]} [description]
 */
function afterUpload(oAfterData){
	
	


	var sImgListTag='',oImg;
	for(var i=0,iLen=oAfterData.aImgList.length;i<iLen;i++){
		oImg=oAfterData.aImgList[i];
		sImgListTag+=buildImage(oImg,oAfterData.sHiddenName,oAfterData.iMaxImageUploadCount);
	}


	// 当前的上传域
	var oCurrentUploadField=$(oAfterData.sWrapSelector+':eq('+oAfterData.iIndex+')').find('li.image-upload-filed-wrap:first').before($(sImgListTag).find('div.image-hide-layer').css('opacity',0).end().fadeIn());


	
	// 隐藏上传按钮
	// 在当前区域里找已经上传的图片
	$(oAfterData.sWrapSelector+' li.signle-image-public-wrap-no-style-for-js').size()>=oAfterData.iMaxImageUploadCount && oCurrentUploadField.hide();
}

/**
 * 上传失败
 * @return {[type]} [description]
 */
function uploadError(sMsg){
	alert(sMsg);
}





/**
 * 图片上传完成后的列表
 * @param  {[type]} oOpt [description]
 * @return {[type]}      [description]
 */
function afterUploadImageList(oOpt){
	/**
	 * [图片上传列表
	 * @param {[type]} oOpt [description]
	 */
	function ImageUploadList(){

		this._oMainWrap=$(oOpt.mainWrapSelector); // 主外围

		this._sMainWrapSelector=oOpt.mainWrapSelector;

		this.hideLayerWrap; // 遮罩
		this.imageBtnWrap; // 按钮的外围
		
	}

	/**
	 * 重写图片上传列表的原型
	 * @type {Object}
	 */
	ImageUploadList.prototype={

		/**
		 * 固定当前的上传域
		 * @return {[type]} [description]
		 */
		init : function (){
			this.hoverImage().deleteImage();
			return this;
		},

		/**
		 * 获取元素
		 * @return {[type]} [description]
		 */
		getElement : function (){

			this.hideLayerWrap=$('div.image-hide-layer',this._oMainWrap);
			this.imageBtnWrap=$('div.image-btn-wrap',this._oMainWrap);

			// alert(this.hideLayerWrap);
			if(arguments.length==0 || true===arguments[0]){
				this.hideLayerWrap.css('opacity',0);
				this.imageBtnWrap.css('opacity',0);
			}
			return this;
		},

		/**
		 * 当鼠标移入或移出图片时
		 * @return {[type]} [description]
		 */
		hoverImage : function (){

			var oThis=this,
			oLastBtnWrpa=$(),
			iHideLayerOpa=0.5, // 遮罩的透明度
			iCurrentIndex;
			// alert(2);
			this._oMainWrap.on('mouseenter','li.image-has-loaded',function (){
				// alert(1);
				// 2015-6-12
				oThis.getElement(false);
				// 2015-6-12

				// 注意查找的范围
				iCurrentIndex=$(this).index(oThis._sMainWrapSelector+' li.signle-image-public-wrap-no-style-for-js'); // 获取当前的索引

				oThis.hideLayerWrap.stop().show().animate({
					opacity:iHideLayerOpa
				}).eq(iCurrentIndex).stop().animate({
					opacity:0
				},{
					complete:function (){
						$(this).hide();
					}
				}).end(); // 获取遮罩
				// oThis.imageBtnWrap=$('div.image-btn-wrap').css('opacity',0); // 获取按钮的外围(这样做不好)

				// 上一个按钮的外围
				oLastBtnWrpa.stop().animate({
					opacity:0
				},{
					complete:function (){
						$(this).hide();
					}
				});

				// 当前的按钮外围
				oLastBtnWrpa=oThis.imageBtnWrap.eq(iCurrentIndex).stop().show().animate({
					opacity:1
				});
			}).on('mouseleave','li.image-has-loaded',function (){

				// 隐藏按钮的外围
				oThis.imageBtnWrap.eq(iCurrentIndex).stop().animate({
					opacity:0
				},{
					complete:function (){
						$(this).hide();
					}
				});

				// 隐藏所有的遮罩
				oThis.hideLayerWrap.stop().animate({opacity:0},{
					complete:function (){
						$(this).hide();
					}
				});
			});

			return this;
		},

		/**
		 * 删除图片
		 * @return {[type]} [description]
		 */
		deleteImage : function (){
			var oThis=this,iIndex,oThisBtn,bIsDeleting=false;
			this._oMainWrap.on('click.delete_img','a.image-delete-btn',function (){
				oThisBtn=$(this);

				// 清空所有的素材
				// 这样做不好,先这样做
				$('input[name="public_source"]').val('');

				$('li.image-has-loaded').eq(iIndex=oThisBtn.off('click.delete_img').index('a.image-delete-btn')).fadeOut(function (){
					// 在移除之前先触发一次鼠标移出图片的事件,目的是隐藏遮罩
					$(this).trigger('mouseleave').remove();
					oThis.afterDeleteImage(iIndex,oThisBtn.attr('data-max-count')); // 移除该图片对应的元素
				});
			});

			return this;
		},

		/**
		 * 在添加一张图片后
		 * @param  {[type]} oLayer   [遮罩对象]
		 * @param  {[type]} oBtnWrap [按钮对象]
		 * @return {[type]}          [description]
		 */
		afterAddImage : function (oLayer,oBtnWrap){
			this.getElement(false);
		},

		/**
		 * 在删除一张图片后
		 * @return {[type]} [description]
		 */
		afterDeleteImage : function (iIndex,iMaxUploadCount){
			var aHide=this.hideLayerWrap.toArray(),
			aBtnWrpa=this.imageBtnWrap.toArray();
			aHide.splice(iIndex,1); // 移除图片对应的元素
			aBtnWrpa.splice(iIndex,1); // 移除图片对应的元素
			
			// 保存移除后的元素
			this.hideLayerWrap=$(aHide);
			this.imageBtnWrap=$(aBtnWrpa);

			iMaxUploadCount=parseInt(iMaxUploadCount);
			
			// 根据情况显示上传域
			if(0!==iMaxUploadCount && this._oMainWrap.find('li.signle-image-public-wrap-no-style-for-js').size()==(iMaxUploadCount-1)){
				// alert(1);
				this._oMainWrap.find('li.image-upload-filed-wrap:first').fadeIn();
			}
		}
	};

	var oImgUpload=new ImageUploadList(oOpt);

	return (oImgUpload.init());
}



/**
* 创建图片
* @param  {[type]} oImg [description]
* @return {[type]}      [description]
*/
function buildImage(oImg){
	var iArgLen=arguments.length;
	var sHiddenName=arguments.length>1?arguments[1]:'img_list';
	var iMaxUploadCount=iArgLen>2?arguments[2]:1; // 最大上传数
	var sImgListTag='<li class="signle-image-wrap signle-image-public-wrap-no-style-for-js image-has-loaded">';

	// <!-- 隐藏域 s -->
	sImgListTag+='<input type="hidden" name="'+sHiddenName+'[]" value="'+oImg.name+'" class="upload-image-hidden-name" />'; 
	// <!-- 隐藏域 e -->

	// <!-- 当图片加载完成后,显示的内容 -->
	sImgListTag+='<div class="image-has-loaded-wrap">';
	  // <!-- 遮罩 s -->
	sImgListTag+='<div class="image-hide-layer"></div>';
	  // <!-- 遮罩 e -->

	  // <!-- 当前图片 s -->
	sImgListTag+='<div class="image-current-wrap">';
	sImgListTag+='<img class="uploaded-image" src="'+oImg.src+'" />';
	sImgListTag+='</div>';
	  // <!-- 当前图片 e -->

	  // <!-- 按钮 s -->
	sImgListTag+='<div class="image-btn-wrap">';

	    // <!-- 按钮的背景 s -->
	sImgListTag+='<div class="image-btn-wrap-bg"></div>';
	    // <!-- 按钮的背景 e -->

	    // <!-- 顶部按钮 s -->
	sImgListTag+='<div class="image-top-btn-wrap image-btn-wrap-public">';
	      // <!-- 删除按钮 s -->
	sImgListTag+='<a href="javascript:void(0);" data-max-count="'+iMaxUploadCount+'" data-delete-param="" class="image-delete-btn delete-from-temp clear-public-source"></a>';
	      // <!-- 删除按钮 e -->
	sImgListTag+='</div>';
	    // <!-- 顶部按钮 e -->

	    // <!-- 底部按钮 s -->
	sImgListTag+='<div class="image-bottom-btn-wrap image-btn-wrap-public"></div>';
	    // <!-- 底部按钮 e -->
	sImgListTag+='</div>';
	  // <!-- 按钮 e -->
	sImgListTag+='</div>';
	sImgListTag+='</li>';

	return sImgListTag;
}
