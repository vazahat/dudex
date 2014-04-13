package {
	
	import flash.display.*;
	import flash.events.*;
	import flash.text.*;
	import flash.text.TextField; 
	
	import flash.net.FileReference;
	import flash.net.FileReferenceList;
	import flash.net.FileFilter;
	import flash.net.URLRequest;
	import flash.utils.Timer;
	import flash.events.TimerEvent;
	import flash.external.*; 
	
	public class Uploader extends MovieClip {
		
		var fileSelect:FileReferenceList;
		var fileList:Array;
		var instance_id:String;
		var currUpload:int;
		var uploadEnd:Function;
		
		//for debug
		private var output:TextField;
		
		public function Uploader()
		{
			instance_id = stage.loaderInfo.parameters["instance_id"];
			fileSelect 	= new FileReferenceList();
			fileList 	= new Array();
			
			fileSelect.addEventListener( Event.SELECT, onSelectFiles );
			select_btn.addEventListener( MouseEvent.CLICK, browseFiles );

			ExternalInterface.addCallback("uploadFile", uploadFile);
			ExternalInterface.addCallback("removeFile", removeFile);
			ExternalInterface.addCallback("stopUpload", stopUpload);
					
			output = new TextField();
            output.y = 0;
			output.x = 5;
            output.width = 450;
            output.height = 325;
            output.multiline = true;
            output.wordWrap = true;
            output.border = true;
            //addChild(output);
		}
		
		private function onSelectFiles( e:Event )
		{
   			var item:FileReference;
			var maxFilesNum:int = parseInt(ExternalInterface.call("jQuery('#"+instance_id+"').ajaxupload", "getMaxFileNum").toString());
			if(isNaN(maxFilesNum)) maxFilesNum=9999;
			
			var to_add:Array = new Array();
			for(var i=0; i<fileSelect.fileList.length; i++)
			{
				if(fileList.length < maxFilesNum)
				{
					item = fileSelect.fileList[i];
					setup(item);
					fileList.push(item);
					to_add.push({'name':item.name, 'size':item.size});
				}
			}
			ExternalInterface.call("jQuery('#"+instance_id+"').ajaxupload", "addFlash", to_add);
		}
		
		private function browseFiles(e:Event)
		{
			try {
				var allowedExt:String = ExternalInterface.call("jQuery('#"+instance_id+"').ajaxupload", "getAllowedExt").toString();

				var extArr = allowedExt.split('|');
				var filter:Array = new Array();
				for(var i=0; i<extArr.length; i++)
				{
					var ext:String = extArr[i];
					if(ext!='')
						filter.push('*.'+ext);
				}
				
				if(filter.length>0)
					fileSelect.browse( [new FileFilter( filter.join(', '), filter.join(';') )] );
				else
					fileSelect.browse();
			}
			catch (errObject:Error) {
				fileSelect.browse();
			}
		}
		
		/**
		* Single file upload action
		*/
		private function uploadFile(pos:int, all:Boolean)
		{
			var item:FileReference = fileList[pos];
			
			try {
				if(item)
				{
					var req:URLRequest = new URLRequest();
					req.url = ExternalInterface.call("jQuery('#"+instance_id+"').ajaxupload", "getUrlFlash", item.name, item.size).toString();

						//output.text=req.url;
					item.addEventListener(DataEvent.UPLOAD_COMPLETE_DATA, uploadEnd = function(e:DataEvent):void{
						ExternalInterface.call("jQuery('#"+instance_id+"').ajaxupload", "onFinishFlash", e.data, pos, all);
						item.removeEventListener(DataEvent.UPLOAD_COMPLETE_DATA, uploadEnd);
					});
					
					currUpload = pos;
					item.upload(req);
				}
			}
			catch (errObject:Error) {
			 // output.text=errObject.getStackTrace();
			}

		}
		
		//remove file from list
		private function removeFile(pos:int){
			stopUpload(pos);
			fileList.splice(pos,1);
		}
		
		
		private function stopUpload(pos:int)
		{
			//may fail so use try catch
			try {
				var file:FileReference = fileList[pos];
				file.cancel();
				file.removeEventListener(DataEvent.UPLOAD_COMPLETE_DATA, uploadEnd);
			}
			catch (errObject:Error) {
			}
		}
		
		private function setup( file:FileReference )
		{
			//file.addEventListener( Event.CANCEL, cancel_func );
			file.addEventListener( Event.COMPLETE, complete_func );
			file.addEventListener( IOErrorEvent.IO_ERROR, io_error );
			file.addEventListener( Event.OPEN, open_func );
			file.addEventListener( ProgressEvent.PROGRESS, progress_func );
		}
		
		private function cancel_func( e:Event )
		{
			//something to do on cancel
		}
		
		private function complete_func( e:Event )
		{
			//trace( 'File Uploaded' );
		}
		
		private function io_error( e:IOErrorEvent )
		{
			//ExternalInterface.call("jQuery('#"+instance_id+"').ajaxupload('testa')", 'The file could not be uploaded.');
		}
		
		private function open_func( e:Event )
		{
		}
		
		private function progress_func( e:ProgressEvent )
		{
			var pr = Math.round( (e.bytesLoaded/e.bytesTotal)*100);
			ExternalInterface.call("jQuery('#"+instance_id+"').ajaxupload('progressFlash', "+pr+","+currUpload+")");
		}
	}	
}