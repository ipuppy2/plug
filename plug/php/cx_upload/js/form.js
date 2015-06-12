$(document).ready(function (){
	
	bindControlInputEvent();

	/**
	 * 解决 ff 下iframe 显示的问题
	 */
	var oIframe=$('iframe.upload-image-frame').each(function (){
		// this.contentWindow.location.href=this.src;
	});


});

/**
 * 给 input.input-control 表单绑定某些公共的事件
 * 可传入一个参数:
 * 如果该为一个 对象,则是给该绑定事件,一般是用在动态创建有表单元素时;
 * 如果该参数不是一个对象,则是先对所有的input.input-control 的表单解绑事件,然后再绑定,
 * 这个情况一般用到异步删除有表单的元素时;
 * 如果不传入参数,则是给所有的 input.input-control 绑定事件
 * @return {[type]} [description]
 */
function bindControlInputEvent(){
	
	// input-control-wrap-public-no-style
	// input-placeholder-public-no-style
	// input-control-public-no-style

	// 如果有参数,说明是要给新的 input 对象绑定事件
	// 注意参数必须为一个 jQ 对象
	if(arguments.length){
		// 如果参数为一个对象,则直接使用对象
		// 
		var oControlInput=typeof arguments[0]=='object'?arguments[0]:$('.input-control-public-no-style').off('input.placeholder propertychange.placeholder focus.input_focus blur.input_blur');
	}else{
		// 先解绑事件
		var oControlInput=$('.input-control-public-no-style');
	}



	var oPlaceholder=$('.input-placeholder-public-no-style'), // 占位符
	oControlWrap=$('div.input-control-wrap-public-no-style'),
	iCurrentIndex;
	


	oInput=oControlInput.on('input.placeholder propertychange.placeholder',function (){
		// document.title=''==this.value?1:0;
		''==this.value?oPlaceholder.eq($(this).index('.input-control-public-no-style')).show():oPlaceholder.eq($(this).index('.input-control-public-no-style')).hide();
	}).on('focus.input_focus',function (){
		iCurrentIndex=$(this).index('.input-control-public-no-style');
		oControlWrap.eq(iCurrentIndex).addClass('on-focus-wrap'); // 外围
		oPlaceholder.eq(iCurrentIndex).addClass('on-focus-placeholder'); // 占位符

	}).on('blur.input_blur',function (){
		iCurrentIndex=$(this).index('.input-control-public-no-style');
		oControlWrap.eq(iCurrentIndex).removeClass('on-focus-wrap'); // 外围
		oPlaceholder.eq(iCurrentIndex).removeClass('on-focus-placeholder'); // 占位符
	});
}

