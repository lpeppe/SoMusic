<?php

class VISUALMELODY_BOL_Service
{
    /**
     * Singleton instance.
     *
     * @var VISUALMELODY_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return ODE_BOL_Service
     */
    public static function getInstance()
    {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function addMelodyOnPost($data, $description, $id_owner, $title, $id_post)
    {
        $dt = new VISUALMELODY_BOL_Visualmelody();
        $dt->data = $data;
        $dt->description = $description;
        $dt->id_owner = $id_owner;
        $dt->title = $title;
        VISUALMELODY_BOL_VisualmelodyDao::getInstance()->save($dt);
        $dt2 = new VISUALMELODY_BOL_VisualmelodyPost();
        $dt2->id_melody = $dt->id;
        $dt2->id_post = $id_post;
        VISUALMELODY_BOL_VisualmelodyPostDao::getInstance()->save($dt2);
        return $dt;
    }
}