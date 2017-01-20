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

    public function getScoreByPostId($id)
    {
        $dbo = OW::getDbo();
        $query = "SELECT *
                  FROM ow_visual_melody_post JOIN ow_visual_melody ON ow_visual_melody_post.id_melody = ow_visual_melody.id
                  WHERE ow_visual_melody_post.id_post = " . $id . ";";
        return $dbo->queryForRow($query);
    }

    public function deleteScoreById($id)
    {
        $dbo = OW::getDbo();
        $query = "DELETE FROM ow_visual_melody, ow_visual_melody_post 
                  USING ow_visual_melody_post 
                  INNER JOIN ow_visual_melody 
                  ON ow_visual_melody_post.id_melody = ow_visual_melody.id  
                  WHERE ow_visual_melody_post.id_post = ".$id.';';
        $dbo->query($query);
    }
}