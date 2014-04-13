(function(d){
    function f(a){
        return l?l[a]||a:a
    }
    var n={
        it_IT:{
            "Add files":"Aggiungi file",
            "Start upload":"Inizia caricamento",
            "Remove all":"Rimuvi tutti",
            Close:"Chiudi",
            "Select Files":"Seleziona",
            Preview:"Anteprima",
            "Remove file":"Rimuovi file",
            Bytes:"Bytes",
            KB:"KB",
            MB:"MB",
            GB:"GB",
            "Upload aborted":"Interroto",
            "Upload all files":"Carica tutto",
            "Select Files or Drag&Drop Files":"Seleziona o Trascina qui i file",
            "File uploaded 100%":"File caricato 100%"
        },
        sq_AL:{
            "Add files":"Shto file",
            "Start upload":"Fillo karikimin", 
            "Remove all":"Hiqi te gjith\u00eb",
            Close:"Mbyll",
            "Select Files":"Zgjith filet",
            Preview:"Miniatur\u00eb",
            "Remove file":"Hiqe file-in",
            Bytes:"Bytes",
            KB:"KB",
            MB:"MB",
            GB:"GB",
            "Upload aborted":"Karikimi u nd\u00ebrpre",
            "Upload all files":"Kariko t\u00eb gjith\u00eb",
            "Select Files or Drag&Drop Files":"Zgjith ose Zvarrit dokumentat k\u00ebtu",
            "File uploaded 100%":"File u karikua 100%"
        },
        nl_NL:{
            "Add files":"Bestanden toevoegen",
            "Start upload":"Start uploaden",
            "Remove all":"Verwijder alles",
            Close:"Sluiten", 
            "Select Files":"Selecteer bestanden",
            Preview:"Voorbeeld",
            "Remove file":"Verwijder bestand",
            Bytes:"Bytes",
            KB:"KB",
            MB:"MB",
            GB:"GB",
            "Upload aborted":"Upload afgebroken",
            "Upload all files":"Upload alle bestanden",
            "Select Files or Drag&Drop Files":"Selecteer bestanden of Drag&Drop bestanden",
            "File uploaded 100%":"Bestand ge\u00fcpload 100%"
        },
        de_DE:{
            "Add files":"Bestanden toevoegen",
            "Start upload":"Start uploaden",
            "Remove all":"Verwijder alles",
            Close:"Sluiten",
            "Select Files":"Selecteer bestanden",
            Preview:"Voorbeeld", 
            "Remove file":"Verwijder bestand",
            Bytes:"Bytes",
            KB:"KB",
            MB:"MB",
            GB:"GB",
            "Upload aborted":"Upload afgebroken",
            "Upload all files":"Upload alle bestanden",
            "Select Files or Drag&Drop Files":"Selecteer bestanden of Drag&Drop bestanden",
            "File uploaded 100%":"Bestand ge\u00fcpload 100%"
        },
        fr_FR:{
            "Add files":"Ajouter",
            "Start upload":"Envoyer",
            "Remove all":"Tout supprimer",
            Close:"Fermer",
            "Select Files":"Parcourir",
            Preview:"Visualiser",
            "Remove file":"Supprimer fichier",
            Bytes:"Bytes",
            KB:"Ko",
            MB:"Mo",
            GB:"Go", 
            "Upload aborted":"Envoi annul\u00e9",
            "Upload all files":"Tout envoyer",
            "Select Files or Drag&Drop Files":"Parcourir ou Glisser/D\u00e9poser",
            "File uploaded 100%":"Fichier envoy\u00e9 100%"
        }
    },l={},h=function(a,b,c,g,e){
        this.file=a;
        this.status=this.currentByte=0;
        this.name=b;
        this.size=c;
        this.info=this.xhr=null;
        this.ext=g;
        this.pos=e.files.length;
        this.AU=e;
        c=this.sizeFormat();
        this.li=d("<li />").appendTo(this.AU.fileList).attr("title",b);
        this.prevContainer=d('<a class="ax-prev-container" />').appendTo(this.li);
        this.prevImage=d('<img class="ax-preview" src="" alt="'+f("Preview")+'" />').appendTo(this.prevContainer);
        this.details=d('<div class="ax-details" />').appendTo(this.li);
        this.nameContainer=d('<div class="ax-file-name">'+b+"</div>").appendTo(this.details);
        this.sizeContainer=d('<div class="ax-file-size">'+c+"</div>").appendTo(this.details);
        this.progressInfo=d('<div class="ax-progress" />').appendTo(this.li);
        this.progressBar=d('<div class="ax-progress-bar" />').appendTo(this.progressInfo);
        this.progressPer= d('<div class="ax-progress-info">0%</div>').appendTo(this.progressInfo);
        this.buttons=d('<div class="ax-toolbar" />').appendTo(this.li);
        this.uploadButton=d('<a title="'+f("Start upload")+'" class="ax-upload ax-button" />').appendTo(this.buttons).append('<span class="ax-upload-icon ax-icon"></span>');
        this.removeButton=d('<a title="Remove file" class="ax-remove ax-button" />').appendTo(this.buttons).append('<span class="ax-clear-icon ax-icon"></span>');
        e.hasHtml4&&(c=e.getUrl(b,0),c=d('<form action="'+ c+'" method="post" target="ax-main-frame" encType="multipart/form-data" />').hide().appendTo(this.li),c.append(a),c.append('<input type="hidden" value="'+encodeURIComponent(b)+'" name="ax-file-name" />'),this.xhr=c);
        this.bindEvents();
        this.doPreview()
    };
    
    h.prototype.sizeFormat=function(){
        var a=this.size;
        "undefined"==typeof precision&&(precision=2);
        for(var b=[f("Bytes"),f("KB"),f("MB"),f("GB")],c=0;1024<=a&&c<b.length-1;)a/=1024,c++;
        var d=Math.round(a),e=Math.pow(10,precision),a=Math.round(a*e%e);
        return d+ "."+a+" "+b[c]
    };
    
    h.prototype.bindEvents=function(){
        this.uploadButton.bind("click",this,function(a){
            a.data.AU.settings.enable&&(2!=a.data.status?(a.data.startUpload(),d(this).addClass("ax-abort")):(a.data.stopUpload(),d(this).removeClass("ax-abort")))
        });
        this.removeButton.bind("click",this,function(a){
            a.data.AU.settings.enable&&a.data.AU.removeFile(a.data.pos)
        });
        this.AU.settings.editFilename&&this.nameContainer.bind("dblclick",this,function(a){
            if(a.data.AU.settings.enable){
                a.stopPropagation();
                var b= a.data.ext,a=a.data.name.replace("."+b,"");
                d(this).html('<input type="text" value="'+a+'" />.'+b)
            }
        }).bind("blur focusout",this,function(a){
            a.stopPropagation();
            var b=d(this).children("input").val();
            "undefined"!=typeof b&&(b=b.replace(/[|&;$%@"<>()+,]/g,"")+"."+a.data.ext,d(this).html(b),a.data.name=b,a.data.AU.hasAjaxUpload||a.data.xhr.children('input[name="ax-file-name"]').val(b))
        })
    };

    h.prototype.doPreview=function(){
        if(this.AU.hasAjaxUpload&&this.file.type.match(/image.*/)&&("jpg"==this.ext||"gif"== this.ext||"png"==this.ext)&&"undefined"!==typeof FileReader){
            var a=this.name,b=this;
            this.prevContainer.css("background","none");
            var c=this.prevImage,g=new FileReader;
            g.onload=function(e){
                c.css("cursor","pointer").attr("src",e.target.result).click(function(){
                    var c=new Image;
                    c.onload=function(){
                        var c=Math.min(d(window).width()/this.width,(d(window).height()-100)/this.height),g=1>c?this.width*c:this.width,c=1>c?this.height*c:this.height,f=d(window).scrollTop()-20+(d(window).height()-c)/2,p=(d(window).width()- g)/2,f=d("#ax-box").css({
                            top:f,
                            height:c,
                            width:g,
                            left:p
                        });
                        f.children("img").attr({
                            width:g,
                            height:c,
                            src:e.target.result
                        });
                        f.find("span").html(a+" ("+b.sizeFormat()+")");
                        f.fadeIn(500);
                        d("#ax-box-shadow").css("height",d(document).height()).show()
                    };
                    
                    c.src=e.target.result;
                    d("#ax-box-shadow").css("z-index",1E4);
                    d("#ax-box").css("z-index",10001)
                })
            };
            
            g.readAsDataURL(this.file)
        }else this.prevContainer.addClass("ax-filetype-"+this.ext).children("img:first").remove()
    };
        
    h.prototype.startUpload=function(a){
        this.AU.settings.beforeUpload(this.name, this.file)&&(this.progressBar.css("width","0%"),this.progressPer.html("0%"),this.uploadButton.addClass("ax-abort"),this.status=2,this.AU.hasAjaxUpload?this.uploadAjax():this.AU.hasFlash?this.AU.flashObj.uploadFile(this.pos,a):this.uploadStandard(a))
    };
    
    h.prototype.uploadAjax=function(){
        var a=this.AU.settings,b=this.file,c=this.currentByte,d=this.name,e=this.size,f=a.chunkSize,h=f+c,i=0>=e-h,k=b,l=h/f;
        this.xhr=new XMLHttpRequest;
        0==c&&this.AU.slots++;
        0==f?(k=b,i=!0):b.mozSlice?k=b.mozSlice(c,h):b.webkitSlice? k=b.webkitSlice(c,h):b.slice?k=b.slice(c,h):(k=b,i=!0);
        var j=this;
        this.xhr.upload.addEventListener("abort",function(){
            j.AU.slots--
        },!1);
        this.xhr.upload.addEventListener("progress",function(a){
            a.lengthComputable&&(a=Math.round(100*(a.loaded+l*f-f)/e),j.onProgress(a))
        },!1);
        this.xhr.upload.addEventListener("error",function(){
            j.onError(this.responseText)
        },!1);
        this.xhr.onreadystatechange=function(){
            if(4==this.readyState&&200==this.status)try{
                var a=JSON.parse(this.responseText);
                0==c&&(j.name=a.name);
                if(-1== parseInt(a.status))throw a.info;
                i?(j.AU.slots--,j.onFinish(a.name,a.size,a.status,a.info)):(j.currentByte=h,j.uploadAjax())
            }catch(b){
                j.AU.slots--,j.onError(b)
            }
        };
        
        b=this.AU.getUrl(d,e);
        this.xhr.open("POST",b+"&ax-start-byte="+c+"&isLast="+i,a.async);
        this.xhr.setRequestHeader("Cache-Control","no-cache");
        this.xhr.setRequestHeader("X-Requested-With","XMLHttpRequest");
        this.xhr.setRequestHeader("Content-Type","application/octet-stream");
        this.xhr.send(k)
    };
    
    h.prototype.uploadStandard=function(a){
        this.progressBar.css("width", "50%");
        this.progressPer.html("50%");
        d("#ax-main-frame").unbind("load").bind("load",this,function(b){
            var c;
            this.contentDocument?c=this.contentDocument:this.contentWindow&&(c=this.contentWindow.document);
            var g=null;
            try{
                g=d.parseJSON(c.body.innerHTML),b.data.onProgress(100),b.data.onFinish(g.name,g.size,g.status,g.info)
            }catch(e){
                b.data.onError(c.body.innerHTML)
            }
            void 0!==a&&void 0!==b.data.AU.files[b.data.pos+1]&&b.data.AU.files[b.data.pos+1].startUpload(a)
        });
        this.xhr.submit()
    };
    
    h.prototype.stopUpload= function(){
        if(this.AU.hasAjaxUpload)null!==this.xhr&&(this.xhr.abort(),this.xhr=null);
        else if(this.AU.hasFlash)this.AU.flashObj.stopUpload(this.pos);
        else{
            var a=document.getElementById("ax-main-frame");
            try{
                a.contentWindow.document.execCommand("Stop")
            }catch(b){
                a.contentWindow.stop()
            }
        }
        this.uploadButton.removeClass("ax-abort");
        this.status=this.currentByte=0;
        this.progressBar.css("width",0);
        this.progressPer.html(f("Upload aborted"))
    };

    h.prototype.onError=function(a){
        this.currentByte=0;
        this.status=-1;
        this.info= a;
        this.progressPer.html(a);
        this.progressBar.css("width","0%");
        this.uploadButton.removeClass("ax-abort");
        this.AU.settings.error(a,this.name);
        this.AU.settings.removeOnError&&this.AU.removeFile(this.pos)
    };
    
    h.prototype.onFinish=function(a,b,c,d){
        this.name=a;
        this.status=parseInt(c);
        this.info=d;
        !this.AU.hasAjaxUpload&&!this.AU.hasFlash&&(this.size=b,b=this.sizeFormat(),this.sizeContainer.html(b));
        this.currentByte=0;
        this.nameContainer.html(a);
        this.li.attr("title",a);
        this.onProgress(100);
        this.uploadButton.removeClass("ax-abort");
        this.progressBar.width(0);
        this.progressPer.html(f("File uploaded 100%"));
        this.AU.settings.success(a);
        a=!0;
        for(b=0;b<this.AU.files.length;b++)1!=this.AU.files[b].status&&-1!=this.AU.files[b].status&&(a=!1);
        a&&this.AU.finish();
        this.AU.settings.removeOnSuccess&&this.AU.removeFile(this.pos)
    };
    
    h.prototype.onProgress=function(a){
        this.progressBar.css("width",a+"%");
        this.progressPer.html(a+"%")
    };
    
    var i=function(a,b){
        var c=document.createElement("input");
        c.type="file";
        this.hasAjaxUpload="multiple"in c&&"undefined"!= typeof File&&"undefined"!=typeof(new XMLHttpRequest).upload;
        this.hasFlash=!1;
        if(!this.hasAjaxUpload)try{
            new ActiveXObject("ShockwaveFlash.ShockwaveFlash")&&(this.hasFlash=!0)
        }catch(g){
            void 0!=navigator.mimeTypes["application/x-shockwave-flash"]&&(this.hasFlash=!0)
        }
        this.hasHtml4=!this.hasFlash&&!this.hasAjaxUpload;
        this.$this=a;
        this.files=[];
        this.processed=this.slots=0;
        this.settings=b;
        this.fieldSet=d("<fieldset />").append('<legend class="ax-legend">'+f("Select Files")+"</legend>").appendTo(a);
        this.flashObj= this.form=null;
        this.browse_c=d('<a class="ax-browse-c ax-button" title="'+f("Add files")+'" />').append('<span class="ax-plus-icon ax-icon"></span> <span class="ax-text">'+f("Add files")+"</span>").appendTo(this.fieldSet);
        this.browseFiles=d('<input type="file" class="ax-browse" name="ax-files[]" />').appendTo(this.browse_c);
        b.uploadDir&&this.browseFiles.attr({
            directory:"directory",
            webkitdirectory:"webkitdirectory",
            mozdirectory:"mozdirectory"
        });
        if(this.hasFlash){
            this.browse_c.children(".ax-browse").remove();
            var c=a.attr("id")+"_flash",e='<\!--[if !IE]> --\><object style="position:absolute;width:150px;height:100px;left:0px;top:0px;z-index:1000;" id="'+c+'" type="application/x-shockwave-flash" data="'+b.flash+'" width="150" height="100"><\!-- <![endif]--\><\!--[if IE]><object style="position:absolute;width:150px;height:100px;left:0px;top:0px;z-index:1000;" id="'+c+'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="150" height="100"><param name="movie" value="'+ b.flash+'" /><\!--\><\!--dgx--\><param name="flashvars" value="instance_id='+a.attr("id")+'"><param name="allowScriptAccess" value="always" /><param value="transparent" name="wmode"></object><\!-- <![endif]--\>';
            this.browse_c.append('<div style="position:absolute;overflow:hidden;width:150px;height:100px;left:0px;top:0px;z-index:0;">'+e+"</div>");
            this.flashObj=document.getElementById(c)
        }
        this.uploadFiles=d('<a class="ax-upload-all ax-button" title="'+f("Upload all files")+'" />').append('<span class="ax-upload-icon ax-icon"></span> <span>'+ f("Start upload")+"</span>").appendTo(this.fieldSet);
        this.removeFiles=d('<a class="ax-clear ax-button" title="'+f("Remove all")+'" />').append('<span class="ax-clear-icon ax-icon"></span> <span>'+f("Remove all")+"</span>").appendTo(this.fieldSet);
        this.fileList=d('<ul class="ax-file-list" />').appendTo(this.fieldSet);
        this.bindEvents(a)
    };
    
    i.prototype.bindEvents=function(a){
        var b=this.settings;
        this.browseFiles.bind("change",this,function(a){
            a.data.settings.enable&&!a.data.hasFlash&&(a.data.addFiles(a.data.hasAjaxUpload? this.files:Array(this)),a.data.hasAjaxUpload||d(this).clone(!0).val("").appendTo(a.data.browse_c),b.autoStart&&a.data.uploadAll())
        });
        this.uploadFiles.bind("click",this,function(a){
            a.data.settings.enable&&a.data.uploadAll();
            return!1
        });
        this.removeFiles.bind("click",this,function(a){
            a.data.settings.enable&&a.data.clearQueue();
            return!1
        });
        0<d(b.form).length?this.form=d(b.form):"parent"==b.form&&(this.form=a.parents("form:first"));
        null!==this.form&&void 0!==this.form&&this.form.bind("submit.ax",this,function(a){
            if(0< a.data.files.length)return a.data.uploadAll(),!1
        });
        if(this.hasAjaxUpload){
            var c="self"==b.dropArea?a[0]:d(b.dropArea)[0],g=this;
            "self"==b.dropArea&&this.fieldSet.find(".ax-legend").html(f("Select Files or Drag&Drop Files"));
            c.addEventListener("dragenter",function(a){
                a.stopPropagation();
                a.preventDefault()
            },!1);
            c.addEventListener("dragover",function(a){
                a.stopPropagation();
                a.preventDefault();
                g.settings.enable&&(this.style.backgroundColor=b.dropColor)
            },!1);
            c.addEventListener("dragleave",function(a){
                a.stopPropagation();
                a.preventDefault();
                g.settings.enable&&(this.style.backgroundColor="")
            },!1);
            c.addEventListener("drop",function(a){
                g.settings.enable&&(a.stopPropagation(),a.preventDefault(),g.addFiles(a.dataTransfer.files),this.style.backgroundColor="",b.autoStart&&g.uploadAll())
            },!1);
            d(document).unbind(".ax").bind("keyup.ax",function(a){
                27==a.keyCode&&d("#ax-box-shadow, #ax-box").fadeOut(500)
            })
        }
        this.enable(a,this.settings.enable)
    };
    
    i.prototype.finish=function(){
        for(var a=[],b=0;b<this.files.length;b++)a.push(this.files[b].name);
        this.settings.finish(a,this.files);
        if(null!==this.form&&void 0!==this.form){
            for(var c="function"==typeof this.settings.remotePath?this.settings.remotePath():this.settings.remotePath,b=0;b<a.length;b++)this.form.append('<input name="ax-uploaded-files[]" type="hidden" value="'+(c+a[b])+'" />');
            this.form.unbind("submit.ax");
            a=this.form.find('[name="submit"]');
            0<a.length?a.trigger("click"):this.form.submit()
        }
    };

    i.prototype.addFiles=function(a){
        for(var b=0;b<a.length;b++){
            var c,g,e;
            this.hasAjaxUpload|| this.hasFlash?(g=a[b].name,e=a[b].size):(g=a[b].value.replace(/^.*\\/,""),e=0);
            c=g.split(".").pop().toLowerCase();
            if(this.files.length<this.settings.maxFiles&&(0<=d.inArray(c,this.settings.allowExt)||0==this.settings.allowExt.length))c=new h(a[b],g,e,c,this),this.files.push(c)
        }
        this.settings.afterSelect(this.files);
        this.settings.sortable&&jQuery().sortable&&d(this.fileList).sortable({
            items:"li",
            cursor:"move"
        })
    };
    
    i.prototype.uploadAll=function(){
        if(!1!==this.settings.beforeUploadAll(this.files)){
            for(var a= !1,b=0;b<this.files.length;b++)0==this.files[b].status&&(a=!0);
            if(a)if(this.hasAjaxUpload){
                var c=this;
                setTimeout(function(){
                    for(var a=!0,b=0;b<c.files.length;b++)0==c.files[b].status&&(a=!1,c.slots<=c.settings.maxConnections&&c.files[b].startUpload());
                    a||c.uploadAll()
                },300)
            }else 0<this.files.length&&this.files[0].startUpload(!0)
        }
    };

    i.prototype.clearQueue=function(){
        for(;0<this.files.length;)this.removeFile(0)
    };
        
    i.prototype.getUrl=function(a,b){
        var c=this.settings,d="function"==typeof c.remotePath?c.remotePath(): c.remotePath,e=[];
        e.push("ax-file-path="+encodeURIComponent(d));
        e.push("ax-allow-ext="+encodeURIComponent(c.allowExt.join("|")));
        e.push("ax-file-name="+encodeURIComponent(a));
        e.push("ax-thumbHeight="+c.thumbHeight);
        e.push("ax-thumbWidth="+c.thumbWidth);
        e.push("ax-thumbPostfix="+encodeURIComponent(c.thumbPostfix));
        e.push("ax-thumbPath="+encodeURIComponent(c.thumbPath));
        e.push("ax-thumbFormat="+encodeURIComponent(c.thumbFormat));
        e.push("ax-maxFileSize="+encodeURIComponent(c.maxFileSize));
        e.push("ax-fileSize="+ b);
        d="function"==typeof c.data?c.data():c.data;
        if("object"==typeof d)for(var f in d)e.push(f+"="+encodeURIComponent(d[f]));else"string"==typeof d&&""!=d&&e.push(d);
        f=-1==c.url.indexOf("?")?"?":"&";
        return c.url+f+e.join("&")
    };
    
    i.prototype.removeFile=function(a){
        var b=this.files[a];
        b.stopUpload();
        b.li.remove();
        b.file=null;
        this.files.splice(a,1);
        this.hasFlash&&this.flashObj.removeFile(a);
        for(a=0;a<this.files.length;a++)this.files[a].pos=a
    };
        
    i.prototype.options=function(a,b){
        if(void 0!==b&&null!==b)this.settings[a]= b,"enable"==a&&this.enable(b);else return this.settings[a]
    };
        
    i.prototype.enable=function(a){
        (this.settings.enable=a)?this.$this.removeClass("ax-disabled").find("input").attr("disabled",!1):this.$this.addClass("ax-disabled").find("input").attr("disabled",!0)
    };
    
    var q={
        remotePath:"uploads/",
        url:"upload.php",
        flash:"uploader.swf",
        data:"",
        async:!0,
        maxFiles:9999,
        allowExt:[],
        success:function(){},
        finish:function(){},
        error:function(){},
        enable:!0,
        chunkSize:1048576,
        maxConnections:3,
        dropColor:"red",
        dropArea:"self", 
        autoStart:!1,
        thumbHeight:0,
        thumbWidth:0,
        thumbPostfix:"_thumb",
        thumbPath:"",
        thumbFormat:"",
        maxFileSize:"10M",
        form:null,
        editFilename:!1,
        sortable:!1,
        beforeUpload:function(){
            return!0
        },
        beforeUploadAll:function(){
            return!0
        },
        language:"auto",
        uploadDir:!1,
        removeOnSuccess:!1,
        removeOnError:!1,
        afterSelect:function(){}
    },m={
        init:function(a){
            return this.each(function(){
                var b=d.extend({},q,a),c=d(this).html("");
                void 0===c.data("AU")&&("auto"==b.language&&(b.language=(window.navigator.userLanguage||window.navigator.language).replace("-", "_")),l=n[b.language],c.addClass("ax-uploader").data("author","http://www.albanx.com/"),0==d("#ax-main-frame").length&&d('<iframe name="ax-main-frame" id="ax-main-frame" />').hide().appendTo("body"),0==d("#ax-box").length&&d('<div id="ax-box"><div id="ax-box-fn"><span></span></div><img /><a id="ax-box-close" title="'+f("Close")+'"></a></div>').appendTo("body"),0==d("#ax-box-shadow").length&&d('<div id="ax-box-shadow"/>').appendTo("body"),d("#ax-box-close, #ax-box-shadow").click(function(a){
                    a.preventDefault();
                    d("#ax-box").fadeOut(500);
                    d("#ax-box-shadow").hide()
                }),this.id=this.id?this.id:"AX"+Math.floor(10001*Math.random()),b.allowExt=d.map(b.allowExt,function(a){
                    return a.toLowerCase()
                }),c.data("AU",new i(c,b)))
            })
        },
        clear:function(){
            return this.each(function(){
                d(this).data("AU").clearQueue()
            })
        },
        start:function(){
            return this.each(function(){
                d(this).data("AU").uploadAll()
            })
        },
        addFlash:function(a){
            d(this).data("AU").addFiles(a)
        },
        progressFlash:function(a,b){
            d(this).data("AU").files[b].onProgress(a)
        },
        onFinishFlash:function(a, b,c){
            var g=d(this).data("AU");
            try{
                var e=jQuery.parseJSON(a);
                if(-1==parseInt(e.status))throw e.info;
                g.files[b].onFinish(e.name,e.size,e.status,e.info)
            }catch(f){
                g.files[b].onError(f)
            }
            if(c)for(a=!0;a;)b++,void 0!==g.files[b]&&0==g.files[b].status?(a=!1,g.files[b].startUpload(c)):a=void 0!==g.files[b]&&0!=g.files[b].status?!0:!1
        },
        getUrlFlash:function(a,b){
            var c=d(this).data("AU"),f=c.getUrl(a,b),c=c.settings.flash.match(/\//g),e="";
            if(null!==c&&c)for(var h=0;h<c.length;h++)e+="../";
            -1!=navigator.appVersion.indexOf("MSIE")&& (e="");
            return e+f
        },
        getAllowedExt:function(a){
            var b=d(this).data("AU").settings.allowExt;
            return!0===a?b:b.join("|")
        },
        getMaxFileNum:function(){
            return d(this).data("AU").settings.maxFiles
        },
        enable:function(){
            return this.each(function(){
                d(this).data("AU").enable(!0)
            })
        },
        disable:function(){
            return this.each(function(){
                d(this).data("AU").enable(!1)
            })
        },
        destroy:function(){
            return this.each(function(){
                var a=d(this);
                a.data("AU").clearQueue();
                a.removeData("AU").html("")
            })
        },
        option:function(a,b){
            return this.each(function(){
                return d(this).data("AU").options(a, b)
            })
        }
    };

    d.fn.ajaxupload=function(a,b){
        if(m[a])return m[a].apply(this,Array.prototype.slice.call(arguments,1));
        if("object"===typeof a||!a)return m.init.apply(this,arguments);
        d.error("Method "+a+" does not exist on jQuery.AjaxUploader")
    }
})(jQuery);