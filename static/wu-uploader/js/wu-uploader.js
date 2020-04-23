;(function(jQuery, window, document,undefined) {

          jQuery.fn.wuUploader=function(options) { 

               var defaults = {
                   url: './up.php',
                   inputName:'pic'
                   
               };
               var settings = $.extend({},defaults, options);//接收外部参数
               console.log(settings);
                          
               var ss=jQuery(this);

               var isMulti=true;            

               if( ss.find('.wu-uploader-file').attr('multiple')===undefined ){

               	  isMulti=false;
               }

               var imgNum=ss.find('li').size();	

               //初始化控件
               var init=function(){
                    
                    if(imgNum==0){
                         ss.find('.wu-uploader-msg').text(  "请选择文件"  );//信息显示
                    }else{
                         ss.find('.wu-uploader-msg').text(  ss.find('li').length+"个文件已上传"  );//信息显示
                         if(!isMulti){
                         	ss.find('.wu-uploader-add').hide();
                         }
                    }

                    if( jQuery('body').find('.wu-uploader-view').size() <1 ){
                         //增加准备大图查看
                         jQuery('body').append("<div class='wu-uploader-view'><img src=''></div>");
                    }

                    //绑定点击大图片退出图片事件
                    jQuery('body').on('click','.wu-uploader-view',function(){
                         jQuery(this).fadeOut(200);
                         return false;
                    });
               }

               init();

               
                              
               //绑定选择文件
               ss.on('click','.wu-uploader-add',function(){  
					ss.find('.wu-uploader-file').click();
               })


               //绑定删除文件
               ss.on('click','.wu-uploader-do-del',function(){
	               	jQuery(this).parents('li').remove();
	               	ss.find('.wu-uploader-msg').text(  ss.find('li').length+"个文件已上传"  );//信息显示
	               	if(!isMulti){
	               		ss.find('.wu-uploader-add').show();
	               	}
	               	return false;
               })

               //绑定移动事件,左移
               ss.on('click','.wu-uploader-do-left',function(){
               	var moveele=jQuery(this).parents('li');
               	moveele.prev('li').before(moveele);
               	return false;         	
               	
               })


               //绑定移动事件,左移
               ss.on('click','.wu-uploader-do-right',function(){
                    var moveele=jQuery(this).parents('li');
                    moveele.next('li').after(moveele);
                    return false;            
                    
               })

               //绑定点击图片事件
               ss.on('click','.wu-uploader-img',function(){
               	jQuery('.wu-uploader-view img').attr('src',jQuery(this).find('img').attr('src') ).parent().fadeIn(200);               	
               	return false;
               })

               
               //文件上传控件change事件
               ss.on('change','.wu-uploader-file',function(){                   
                    var fileTypeError=0;//文件错误
	          		var formData = new FormData();//使用formdata对象
	          		jQuery.each(jQuery(this)[0].files,function(i,e){
	          			
	          			if(  e.type!='image/jpeg' && e.type!='image/png' && e.type!='mage/bmp' && e.type!='image/gif'  ){//判断文件类型
	          				ss.find('.wu-uploader-msg').text(  "文件格式不支持"  );//信息显示                              
	                              fileTypeError=1;
	                              return false;        				
	          			}

	                         if( e.size/1024/1024 > 2 ){
	                              ss.find('.wu-uploader-msg').text(  "文件大小不能超过2MB"  );//信息显示
	                              fileTypeError=1;
	                              return false;
	                         }
	          			formData.append('file[]', e);

	          		});

	                    if(fileTypeError){
	                        return false; 
	                    }
	              
	          		//首先封装一个方法 传入一个监听函数 返回一个绑定了监听函数的XMLHttpRequest对象
				    var xhrOnProgress=function(fun) {
				      xhrOnProgress.onprogress = fun; //绑定监听
				      //使用闭包实现监听绑
				      return function() {
				        //通过$.ajaxSettings.xhr();获得XMLHttpRequest对象
				        var xhr = jQuery.ajaxSettings.xhr();
				        //判断监听函数是否为函数
				        if (typeof xhrOnProgress.onprogress !== 'function')
				          return xhr;
				        //如果有监听函数并且xhr对象支持绑定时就把监听函数绑定上去
				        if (xhrOnProgress.onprogress && xhr.upload) {
				          xhr.upload.onprogress = xhrOnProgress.onprogress;
				        }
				        return xhr;
				      }
				    }

					jQuery.ajax({//上传服务器  
					    url: settings.url,  
					    type: "POST",
	                        dataType:"json",
	                        timeout:20000, 				   
					    cache: false,
		          	    contentType: false, //必须false才会自动加上正确的Content-Type 
		          	    processData: false, //必须false才会避开jQuery对 formdata 的默认处理
					    data: formData,
						//进度条要调用原生xhr
					    xhr:xhrOnProgress(function(evt){
					        var percent = Math.floor(evt.loaded / evt.total*100);//计算百分比
					        ss.find('.wu-uploader-msg').text(  "上传中..." + percent + '%'  );//信息显示				        
					    }),			     
					    success: function(res){     				    	
	     				    	if(res.c){//上传成功code=1
	     				    		var html='';
	     				    		jQuery.each(res.d, function(i, item){
	     				    			//html
	     				    			html += '<li>';
	                                        html += '<div class="wu-uploader-img">';
	     				    			html += '<img src='+item.url+'>';
	                                        html += '</div>';
	                                        html += '<div class="wu-uploader-do">';
	                                        html += ' <span class="wu-uploader-do-left">⇐</span>';
	                                        html += ' <span class="wu-uploader-do-right">⇒</span>';
	                                        html += ' <span class="wu-uploader-do-del">⊗</span>';
	                                        html += '</div>';
	                                        if(isMulti){
	                                             html += '<input type="hidden" name="'+settings.inputName+'[]" value='+item.url+' >';
	                                        }else{
	                                             html += '<input type="hidden" name="'+settings.inputName+'" value='+item.url+' >';
	                                        }    				    			

	     				    			html += '</li>';

	     				    			if(!isMulti){
	     				    				ss.find('.wu-uploader-add').hide();
	     				    			}
	     				    			
	     				    		})//循环数据

	     				    		ss.find('ul').prepend(html);//更新图片预览

	     				    		ss.find('.wu-uploader-msg').text(  ss.find('li').length+"个文件已上传"  );//信息显示
	     				    		
	     				    	}else{
	     				    		ss.find('.wu-uploader-msg').text(  "上传出错"  );//信息显示
	     				    	}

					    },
					    error: function(e){
					    	ss.find('.wu-uploader-msg').text(  "上传出错"  );//信息显示
					    },
					    beforeSend:function(){
					    	ss.find('.wu-uploader-msg').text(  "正在上传..."  );//信息显示
					    },
					    complete:function(){
			 				
					    }  
					});
	          		

	          		


		          	//可重复选择文件处理
		          	
		          	jQuery(this).remove();//删除当前元素
		          	if(isMulti==false){
		          		ss.append(   '<input class="wu-uploader-file" type="file" style="display: none;">'  );
		          	}else{
		          		ss.append(   '<input class="wu-uploader-file" type="file" multiple="multiple" style="display: none;">'  );
		          	}
	                

	            })             
                      
          };       
})(jQuery, window, document);