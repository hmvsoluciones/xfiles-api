<?php

interface CarteraDao {

    public function getPuntos($idUsuario);

    public function setPuntos($idUsuario, $puntos, $isNew);
}
