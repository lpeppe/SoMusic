VISUALMELODY = {}

OwComments.prototype.initTextarea = function()
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
};

VISUALMELODY.commentSendMessage = function(message, context)
{
    console.log('hi')
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