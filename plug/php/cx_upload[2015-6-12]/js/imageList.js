
var oUploadFileField=$('li.image-upload-filed-wrap');

/**
 * 上传成功
 * @return {[type]} [description]
 */
function afterUpload(aImgList,iIndex,sHiddenName,iMaxImageUploadCount){
	
	
	var sImgListTag='',oImg;
	for(var i=0,iLen=aImgList.length;i<iLen;i++){
		oImg=aImgList[i];
		sImgListTag+=buildImage(oImg,sHiddenName,iMaxImageUploadCount);
	}

	var oNewImaList=$(sImgListTag);//.hide(); //.css('opacity',0);
	oUploadFileField.eq(iIndex).before(oNewImaList.fadeIn()); // 把新上传的图片追加到存放列表

	// 重新获取一次图片
	oImgList.afterAddImage();
	
	// 隐藏上传按钮
	$('li.signle-image-public-wrap-no-style-for-js').size()>=iMaxImageUploadCount && oUploadFileField.eq(iIndex).hide();
}

/**
 * 上传失败
 * @return {[type]} [description]
 */
function uploadError(sMsg){
	alert(sMsg);
}


/**
 * [图片上传列表
 * @param {[type]} oOpt [description]
 */
function ImageUploadList(oOpt){

	this._sParentSelector=typeof oOpt.parentSelector=='undefined'?'.upload-image-list-wrap':oOpt.parentSelector; // 事件委托的元素

	this._oParent=$(this._sParentSelector);
	this._oUploadListWrap=$('div.upload-image-list-wrap'); // 
	this.listParentWrpa; // 列表的外围
	// this.maxUploadCount=typeof oOpt.maxUploadCount=='undefined'?false:oOpt.maxUploadCount;

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
	fixedCurrentUpload : function (){
		var oThis=this;
		$('div.input-list-wrap').on('mouseenter',function (){

			oThis.listParentWrpa=oThis._oUploadListWrap.eq($(this).index('div.input-list-wrap'));
		});

		return this;
	},

	/**
	 * 获取元素
	 * @return {[type]} [description]
	 */
	getElement : function (){

		this.hideLayerWrap=$('div.image-hide-layer');
		this.imageBtnWrap=$('div.image-btn-wrap');

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
		this._oParent.on('mouseenter','li.image-has-loaded',function (){
			
			iCurrentIndex=$(this).index('li.signle-image-public-wrap-no-style-for-js'); // 获取当前的索引			
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
		this._oParent.on('click.delete_img','a.image-delete-btn',function (){
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
		if(0!==iMaxUploadCount && this.listParentWrpa.find('li.signle-image-public-wrap-no-style-for-js').size()==(iMaxUploadCount-1)){
			// alert(1);
			this.listParentWrpa.find('li.image-upload-filed-wrap:first').fadeIn();
		}
	}
};


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
