<?php

interface EventActionsService {
  
    public function like($iEvent, $idUser);

    public function unlike($iEvent, $idUser);
  
}
