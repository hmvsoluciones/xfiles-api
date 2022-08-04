<?php

interface EventActionsDao {

    public function like($idEvent, $idUser);

    public function unlike($iEvent, $idUser);
   
}
