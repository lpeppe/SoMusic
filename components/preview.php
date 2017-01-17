<?php
class VISUALMELODY_CMP_Preview extends OW_Component
{
    public function __construct($component="data-sevc-controllet")
    {
        $this->assign("component", $component);
        OW::getDocument()->addScript("https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js", 'text/javascript');
        OW::getDocument()->addScript("http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js", 'text/javascript');
        OW::getDocument()->addStyleSheet("http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css", 'text/css');
        OW::getDocument()->addStyleSheet("http://pingendo.github.io/pingendo-bootstrap/themes/default/bootstrap.css", 'text/css');
        OW::getDocument()->addScript("https://www.gstatic.com/firebasejs/3.6.4/firebase.js", 'text/javascript');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('visualmelody')->getStaticJsUrl() . 'firebase_connection.js', 'text/javascript');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('visualmelody')->getStaticJsUrl() . 'vexflow-debug.js', 'text/javascript');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('visualmelody')->getStaticJsUrl() . 'editorData.js', 'text/javascript');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('visualmelody')->getStaticJsUrl() . 'visual-melody.js', 'text/javascript');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('visualmelody')->getStaticJsUrl() . 'measure.js', 'text/javascript');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('visualmelody')->getStaticJsUrl() . 'editor.js', 'text/javascript');
        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('visualmelody')->getStaticJsUrl() . 'init.js', 'text/javascript');
    }
}