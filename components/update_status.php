<?php
class VISUALMELODY_CMP_UpdateStatus extends NEWSFEED_CMP_UpdateStatus
{
    public function __construct( $feedAutoId, $feedType, $feedId, $actionVisibility = null )
    {
        parent::__construct($feedAutoId, $feedType, $feedId, $actionVisibility = null);
    }
    public function createForm( $feedAutoId, $feedType, $feedId, $actionVisibility )
    {
        $form = parent::createForm($feedAutoId, $feedType, $feedId, $actionVisibility);
        $vmButton = new Button('vm_open_dialog');
        $vmButton->setValue("");
        $form->addElement($vmButton);
        $script = "ODE.pluginPreview = 'newsfeed';
            $('#{$vmButton->getId()}').click(function(e){
                ODE.pluginPreview = 'newsfeed';
                //$('#ode_controllet_placeholder').slideToggle('fast');
                previewFloatBox = OW.ajaxFloatBox('ODE_CMP_Preview', {} , {top:'56px', width:'calc(100vw - 112px)', height:'calc(100vh - 112px)', iconClass: 'ow_ic_add', title: ''});
            });
            $('#{$vmButton->getId()}').click(function(e){
                previewFloatBox = OW.ajaxFloatBox('ODE_CMP_Preview', {component:'map-controllet'} , {top:'56px', width:'calc(100vw - 112px)', height:'calc(100vh - 112px)', iconClass: 'ow_ic_add', title: ''});
            });
        ";

        OW::getDocument()->addOnloadScript($script);

        //$form->setAction( OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlFor('ODE_CTRL_Ajax', 'statusUpdate')) );

        return $form;
    }
}