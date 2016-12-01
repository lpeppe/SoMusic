<?php
    OW::getRouter()->addRoute(new OW_Route('vm.index', 'vm', 'VISUALMELODY_CTRL_Vm', 'index'));
    OW::getEventManager()->bind('feed.on_item_render', array(SCORES_CLASS_FeedHandler::getInstance(), 'testFunction'));