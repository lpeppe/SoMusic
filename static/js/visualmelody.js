VISUALMELODY = {}
if(document.getElementsByName("save").length > 0) {
    document.getElementsByName("save")[0].addEventListener("click", function (e) {
        document.getElementById("vm_placeholder").style.display = "none";
    });
}
/*OwComments.prototype.initTextarea = function()
{
    var self = this;
    this.realSubmitHandler = function(){

        self.initialCount++;

        //self.sendMessage(self.$textarea.val());
        VISUALMELODY.commentSendMessage(self.$textarea.val(), self);

        self.attachmentInfo = false;
        self.oembedInfo = false;
        self.$hiddenBtnCont.hide();
        if( this.mediaAllowed ){
            OWLinkObserver.getObserver(self.textAreaId).resetObserver();
        }
        self.$attchCont.empty();
        OW.trigger('base.photo_attachment_reset', {pluginKey:self.pluginKey, uid:self.attchUid});
        OW.trigger('base.comment_add', self.eventParams);

        self.$formWrapper.addClass('ow_preloader');
        self.$commentsInputCont.hide();

    };

    this.submitHandler = this.realSubmitHandler;

    this.$textarea
        .bind('keypress comment.test',
            function(e){
                if( e.isButton || (e.which === 13 && !e.shiftKey) ){
                    e.stopImmediatePropagation();
                    var textBody = $(this).val();

                    if ( $.trim(textBody) == '' && !self.attachmentInfo && !self.oembedInfo ){
                        OW.error(self.labels.emptyCommentMsg);
                        return false;
                    }

                    self.submitHandler();
                    return false;
                }
            }
        )
        .one('focus', function(){$(this).removeClass('invitation').val('').autosize({callback:function(data){OW.trigger('base.comment_textarea_resize', self.eventParams);}});});

    this.$hiddenBtnCont.unbind('click').click(function(){self.submitHandler();});

    if( this.mediaAllowed ){
        OWLinkObserver.observeInput(this.textAreaId, function( link ){
            if( !self.attachmentInfo ){
                self.$attchCont.html('<div class="ow_preloader" style="height: 30px;"></div>');
                this.requestResult( function( r ){
                    self.$attchCont.html(r);
                    self.$hiddenBtnCont.show();

                    OW.trigger('base.comment_attach_media', {})
                });
                this.onResult = function( r ){
                    self.oembedInfo = r;
                    if( $.isEmptyObject(r) ){
                        self.$hiddenBtnCont.hide();
                    }
                };
            }
        });
    }
};*/

VISUALMELODY.commentSendMessage = function(message, context)
{
    var self = context;
    var dataToSend = {
        entityType: self.entityType,
        entityId: self.entityId,
        displayType: self.displayType,
        pluginKey: self.pluginKey,
        ownerId: self.ownerId,
        cid: self.uid,
        attchUid: self.attchUid,
        commentCountOnPage: self.commentCountOnPage,
        commentText: message,
        initialCount: self.initialCount,
        vmJSONData: vmData,
        decription: vmDescription,
        title: vmTitle
    };

    if( self.attachmentInfo ){
        dataToSend.attachmentInfo = JSON.stringify(self.attachmentInfo);
    }
    else if( self.oembedInfo ){
        dataToSend.oembedInfo = JSON.stringify(self.oembedInfo);
    }
    $.ajax({
        type: 'post',
        //url: self.addUrl,
        url: VISUALMELODY.ajax_add_comment,
        data: dataToSend,
        dataType: 'JSON',
        success: function(data){
            self.repaintCommentsList(data);
            OW.trigger('base.photo_attachment_uid_update', {uid:self.attchUid, newUid:data.newAttachUid});
            self.eventParams.commentCount = data.commentCount;
            OW.trigger('base.comment_added', self.eventParams);
            self.attchUid = data.newAttachUid;

            self.$formWrapper.removeClass('ow_preloader');
            self.$commentsInputCont.show();
        },
        error: function( XMLHttpRequest, textStatus, errorThrown ){
            OW.error(textStatus);
        },
        complete: function(){
        }
    });
    self.$textarea.val('').keyup().trigger('input.autosize');
};

VISUALMELODY.loadScore = function (data, id, title) {
    var scoreDiv = document.getElementById(id);
    scoreDiv.parentElement.style.display = "none";
    var titleField = document.createElement("p");
    titleField.style.textAlign = "center";
    titleField.style.fontSize= "large";
    titleField.style.fontWeight = "bold";
    titleField.style.paddingTop = "20px";
    titleField.style.marginBottom = "0px";
    var nodeText = document.createTextNode(title);
    titleField.appendChild(nodeText);
    scoreDiv.parentElement.insertBefore(titleField, scoreDiv);
    var scoreCanvas =  document.createElement('canvas');
    scoreCanvas.height = 600;
    scoreCanvas.id = id + "_sc";
    var vmCanvas = document.createElement('canvas');
    vmCanvas.height = 130;
    vmCanvas.id = id + "_vmc";
    scoreDiv.appendChild(scoreCanvas);
    scoreDiv.appendChild(vmCanvas);
    var renderer = new Renderer(scoreCanvas.id, id, vmCanvas.id);
    renderer.restoreData(data);
    scoreDiv.parentElement.style.display = "block";
}

VISUALMELODY.initEditor = function () {
    var keySig = document.getElementById("ks");
    Object.keys(Vex.Flow.keySignature.keySpecs).forEach(function (key) {
        var option = document.createElement("option");
        option.text = key;
        keySig.add(option);
    });
    var ren = new Renderer("score", "scoreDiv", "vmCanvas");
    document.getElementById("next").addEventListener("click", function () {
        document.getElementById("firstDiv").style.display = "none";
        document.getElementById("secondDiv").style.display = "block";
        ren.init();
    }, false);
    document.getElementById("add").addEventListener("click", function () {
        VISUALMELODY.addListener(ren);
    });
    document.getElementById('feed_scoreDiv').addEventListener("click", VISUALMELODY.modScore);
    document.getElementById("removeScore").addEventListener("click", VISUALMELODY.removeScore, false);
    document.getElementById("ks").addEventListener("change", preview, false);
    document.getElementById('score_title_text').addEventListener("change", function () {
        $('input[name=scoreTitle]').val($('#score_title_text').val());
    });
    var elements = document.getElementsByName("timeLab");
    for (var i = 0; i < elements.length; i++)
        elements[i].addEventListener("click", preview, false);
    var prevDiv = document.getElementById("prev");
    var renderer = new Vex.Flow.Renderer(prevDiv, Vex.Flow.Renderer.Backends.SVG);
    var ctx = renderer.getContext();
    renderer.resize(200, 150);
    preview();

    function preview() {
        ctx.clear();
        var prevStave = new Vex.Flow.Stave(10, 20, 200);
        if (arguments.length == 0 || arguments[0].target.tagName != "LABEL")
            var timeSign = getRadioSelected("time");
        else
            var timeSign = arguments[0].target.childNodes[1].id;
        var keySign = $("#ks :selected").text();
        prevStave.addClef("treble").addTimeSignature(timeSign).addKeySignature(keySign);
        prevStave.setContext(ctx).draw();
    }
}

VISUALMELODY.removeScore = function () {
    $('input[name=vmHidden]').val('');
    document.getElementById("vm_placeholder").style.display = "none";
    previewFloatBox.close();
    delete previewFloatBox;
    $('.floatbox_canvas').each(function(i, obj) {
        obj.style.display = 'block';
    });
    if(document.getElementById('floatbox_overlay') != null)
        document.getElementById('floatbox_overlay').style.display = 'block';
}

VISUALMELODY.modScore = function (vmData) {
    document.getElementById("vm_placeholder").style.display = "none";
    $('.floatbox_canvas').each(function(i, obj) {
        obj.style.display = 'block';
    });
    document.getElementById('floatbox_overlay').style.display = 'block';
    $('input[name=vmHidden]').val('');
}

VISUALMELODY.addListener = function (ren) {
    document.getElementById('score').classList.remove("shake");
    document.getElementById('score').classList.remove("animated");
    $('.floatbox_canvas').each(function(i, obj) {
        obj.style.display = 'none';
    });
    document.getElementById('floatbox_overlay').style.display = 'none';
    var vmData = ren.saveData();
    $('input[name=vmHidden]').val(JSON.stringify(vmData));
    var ren2 = new Renderer("feed_score", "feed_scoreDiv", "feed_vmCanvas");
    document.getElementById("vm_placeholder").style.display = "block";
    ren2.restoreData(vmData);
    document.getElementById('score_title_text').value = "";
    $('input[name=scoreTitle]').val('');
    document.getElementsByTagName("BODY")[0].classList.remove("floatbox_nooverflow");
}
