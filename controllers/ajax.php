<?php

class VISUALMELODY_CTRL_Ajax extends NEWSFEED_CTRL_Ajax
{
    public function addComment()
    {
        $clean = $_REQUEST;

        $errorMessage = false;
        $isMobile = !empty($clean['isMobile']) && (bool)$clean['isMobile'];
        $params = $this->getParamsObject();

        if (empty($clean['commentText']) && empty($clean['attachmentInfo']) && empty($clean['oembedInfo'])) {
            $errorMessage = OW::getLanguage()->text('base', 'comment_required_validator_message');
        } else if (!OW::getUser()->isAuthorized($params->getPluginKey(), 'add_comment')) {
            $status = BOL_AuthorizationService::getInstance()->getActionStatus($params->getPluginKey(), 'add_comment');
            $errorMessage = $status['msg'];
        } else if (BOL_UserService::getInstance()->isBlocked(OW::getUser()->getId(), $params->getOwnerId())) {
            $errorMessage = OW::getLanguage()->text('base', 'user_block_message');
        }

        if ($errorMessage) {
            exit(json_encode(array('error' => $errorMessage)));
        }

        $commentText = empty($clean['commentText']) ? '' : trim($clean['commentText']);
        $attachment = null;

        if (BOL_TextFormatService::getInstance()->isCommentsRichMediaAllowed() && !$isMobile) {
            if (!empty($clean['attachmentInfo'])) {
                $tempArr = json_decode($clean['attachmentInfo'], true);
                OW::getEventManager()->call('base.attachment_save_image', array('uid' => $tempArr['uid'], 'pluginKey' => $tempArr['pluginKey']));
                $tempArr['href'] = $tempArr['url'];
                $tempArr['type'] = 'photo';
                $attachment = json_encode($tempArr);
            } else if (!empty($clean['oembedInfo'])) {
                $tempArr = json_decode($clean['oembedInfo'], true);
                // add some actions
                $attachment = json_encode($tempArr);
            }
        }

        $comment = BOL_CommentService::getInstance()->addComment($params->getEntityType(), $params->getEntityId(), $params->getPluginKey(), OW::getUser()->getId(), $commentText, $attachment);
        VISUALMELODY_BOL_Service::getInstance()->addMelodyOnPost(
            $clean['vmJSONData'],
            $clean['description'],
            OW::getUser()->getId(),
            //$clean['title'],
            "test",
            $comment->getId()
        );

        //OW::getEventManager()->trigger($event);

        BOL_AuthorizationService::getInstance()->trackAction($params->getPluginKey(), 'add_comment');
    }
    public function statusUpdate()
    {
        $clean = $_REQUEST;
        if ( empty($clean['status']) && empty($clean['attachment']) )
        {
            echo json_encode(array(
                "error" => OW::getLanguage()->text('base', 'form_validate_common_error_message')
            ));
            exit;
        }

        if ( !OW::getUser()->isAuthenticated() )
        {
            echo json_encode(false);
            exit;
        }

        $oembed = null;
        $attachId = null;
        $status = empty($clean['status']) ? '' : strip_tags($clean['status']);
        $content = array();

        if ( !empty($clean['attachment']) )
        {
            $content = json_decode($clean['attachment'], true);

            if ( !empty($content) )
            {
                if( $content['type'] == 'photo' && !empty($content['uid']) )
                {
                    $attachmentData = OW::getEventManager()->call('base.attachment_save_image', array(
                        "pluginKey" => "newsfeed",
                        'uid' => $content['uid']
                    ));

                    $content['url'] = $content['href'] = $attachmentData["url"];
                    $attachId = $content['uid'];
                }

                if( $content['type'] == 'video' )
                {
                    $content['html'] = BOL_TextFormatService::getInstance()->validateVideoCode($content['html']);
                }
            }
        }

        $userId = OW::getUser()->getId();

        $event = new OW_Event("feed.before_content_add", array(
            "feedType" => $clean['feedType'],
            "feedId" => $clean['feedId'],
            "visibility" => $clean['visibility'],
            "userId" => $userId,
            "status" => $status,
            "type" => empty($content["type"]) ? "text" : $content["type"],
            "data" => $content
        ));

        OW::getEventManager()->trigger($event);

        $data = $event->getData();

        if ( !empty($data) )
        {
            if ( !empty($attachId) )
            {
                BOL_AttachmentService::getInstance()->deleteAttachmentByBundle("newsfeed", $attachId);
            }

            $item = empty($data["entityType"]) || empty($data["entityId"])
                ? null
                : array(
                    "entityType" => $data["entityType"],
                    "entityId" => $data["entityId"]
                );

            echo json_encode(array(
                "item" => $item,
                "message" => empty($data["message"]) ? null : $data["message"],
                "error" => empty($data["error"]) ? null : $data["error"]
            ));
            exit;
        }

        $status = UTIL_HtmlTag::autoLink($status);
        $out = NEWSFEED_BOL_Service::getInstance()
            ->addStatus(OW::getUser()->getId(), $clean['feedType'], $clean['feedId'], $clean['visibility'], $status, array(
                "content" => $content,
                "attachmentId" => $attachId
            ));

        /* VISUALMELODY */
        if(!empty($clean['vmHidden'])) {
            VISUALMELODY_BOL_Service::getInstance()->addMelodyOnPost(
                $clean['vmHidden'],
                "",//$clean['description'],
                OW::getUser()->getId(),
                //$clean['title'],
                $clean['scoreTitle'],
                $out['entityId']
            );
        }
        /* VISUALMELODY */

        echo json_encode(array(
            "item" => $out
        ));
        exit;
    }
}