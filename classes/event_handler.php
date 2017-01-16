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
    }

    // Replace the newsfeed form
    public function onStatusUpdateCreate(OW_Event $event)
    {
        $params = $event->getParams();

        if (OW::getApplication()->isMobile())
        {
            //TODO MOBILE PAGE REQUEST
        }
        else
        {
            $ret = new VISUALMELODY_CMP_UpdateStatus($params['feedAutoId'], $params['entityType'], $params['entityId'], $params['visibility']);
        }

        $event->setData($ret);
        return $ret;
    }
}
