/*function loadImage(url, callback) {
	var img = new Image(); //创建一个Image对象，实现图片的预下载
	img.src = url;
	
	if(img.complete) { // 如果图片已经存在于浏览器缓存，直接调用回调函数
	    callback.call(img);
	    alert(1);
	    return; // 直接返回，不用再处理onload事件
    }
	img.onload = function () { //图片下载完毕时异步调用callback函数。
        callback.call(img);//将回调函数的this替换为Image对象
        alert(2);
    };
};*/

/**
 * 加载图片
 * @opt {
 *      imageSelector: '', 要加载的图片选择器,不一定要是图片标签;可选参数,默认为 img 标签
 *      getImageUrl:function (){}, 返回要加载的图片地址, this 为当前的元素;可选参数,默认返回 data-src 属性
 *      beforeLoad:function (){}, 加载前执行的回调, this 为当前的元素; 可选参数;
 *      complete:function (){}, 加载完成后执行的回调, this 为当前元素,
 *      	传入当前已经加载完成的图片对象; 可选参数
 * }
 * @param {[type]} opt [description]
 */
function imageLoad(opt){
	var getImageUrl= typeof opt.getImageUrl=='function'?opt.getImageUrl:function (){return $(this).attr('data-src')},

	beforeLoad=typeof opt.beforeLoad=='function'?opt.beforeLoad:function (){}, // 加载前执行的回调

	complete = typeof opt.complete=='function'?opt.complete:function (){}, // 加载完成执行的回调
	
	oImgList=$(typeof opt.imageSelector=='undefined'?'img':opt.imageSelector).each(function (){
		var oImg=new Image(),oThis=this;

		// 加载前
		beforeLoad.call(oThis);

		// 获取要加载的图片地址
		oImg.src=getImageUrl.call(this);

		// 如果在缓存里
		if(oImg.complete){
			complete.call(this,oImg);
			return true;	
		}

		// 判断加载
		oImg.onload=function (){
			complete.call(oThis,oImg);
		};

	});
}

/**
 * 加载示例
 * @param  {[type]} false [description]
 * @return {[type]}       [description]
 */
if(false){
	imageLoad({
		// imageSelector:'', // 图片的选择器

		/**
		 * 返回要加载的图片地址
		 * @return {[type]} [description]
		 */
		getImageUrl : function (){
			return $(this).attr('data-src');
		},

		/**
		 * 加载前
		 * @return {[type]} [description]
		 */
		beforeLoad : function (){},

		/**
		 * 加载完成
		 * @return {[type]} [description]
		 */
		complete : function (oImg){
			$(this).hide().prop('src',oImg.src).fadeIn();
		}
	});
}



