<?php
class VISUALMELODY_CMP_Preview extends OW_Component
{
    public function __construct($component="data-sevc-controllet")
    {
        $this->assign("component", $component);
        OW::getDocument()->addScript("http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js", 'text/javascript');
        OW::getDocument()->addStyleSheet("http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css", 'text/css');
        OW::getDocument()->addStyleSheet("http://pingendo.github.io/pingendo-bootstrap/themes/default/bootstrap.css", 'text/css');
        //OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('visualmelody')->getStaticJsUrl() . 'init.js', 'text/javascript');
    }
}