<?php

class VISUALMELODY_CLASS_EventHandler
{
    private static $classInstance;

    public static function getInstance()
    {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    // Handle event and route
    public function init()
    {
        // event that allows returning a component to replace the standard status update form
        OW::getEventManager()->bind('feed.get_status_update_cmp', array($this, 'onStatusUpdateCreate'));
        OW::getEventManager()->bind(OW_EventManager::ON_APPLICATION_INIT, array($this, 'onApplicationInit'));
    }

    // Replace the newsfeed form
    public function onStatusUpdateCreate(OW_Event $event)
    {
        $params = $event->getParams();

        if (OW::getApplication()->isMobile()) {
            //TODO MOBILE PAGE REQUEST
        } else {
            $ret = new VISUALMELODY_CMP_UpdateStatus($params['feedAutoId'], $params['entityType'], $params['entityId'], $params['visibility']);
        }

        $event->setData($ret);
        return $ret;
    }

    public function onApplicationInit(OW_Event $event)
    {
        // if request is Ajax, we don't need to re-execute the same code again!
        if (!OW::getRequest()->isAjax()) {
            //Add ODE.JS script to all the Oxwall pages and set THEME_IMAGES_URL variable with theme image url
            OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('visualmelody')->getStaticJsUrl() . 'visualmelody.js', 'text/javascript');
        }
        $js = UTIL_JsGenerator::composeJsString('
                VISUALMELODY.ajax_add_comment = {$ajax_add_comment}
            ', array(
            'ajax_add_comment' => OW::getRouter()->urlFor('VISUALMELODY_CTRL_Ajax', 'addComment'),
        ));

        OW::getDocument()->addOnloadScript($js);
    }
}
