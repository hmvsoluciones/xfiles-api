<?php
class MultimediaDTO{
    private $idMultimedia;
    private $idRemote;
    private $nombreMultimedia;
    private $urlMultimedia;
    private $urlMultimediaGet;
    private $extensionMultimedia;

    function getIdMultimedia() {
        return $this->idMultimedia;
    }
    function setIdMultimedia($idMultimedia) {
        $this->idMultimedia = $idMultimedia;
    }
    function getIdRemote() {
        return $this->idRemote;
    }
    function setIdRemote($idRemote) {
        $this->idRemote = $idRemote;
    }
    function getNombreMultimedia() {
        return $this->nombreMultimedia;
    }
    function setNombreMultimedia($nombreMultimedia) {
        $this->nombreMultimedia = $nombreMultimedia;
    }
    function getUrlMultimedia() {
        return $this->urlMultimedia;
    }
    function setUrlMultimedia($urlMultimedia) {
        $this->urlMultimedia = $urlMultimedia;
    }
    function getUrlMultimediaGet() {
        return $this->urlMultimediaGet;
    }
    function setUrlMultimediaGet($urlMultimediaGet) {
        $this->urlMultimediaGet = $urlMultimediaGet;
    }
    function getExtensionMultimedia() {
        return $this->extensionMultimedia;
    }
    function setExtensionMultimedia($extensionMultimedia) {
        $this->extensionMultimedia = $extensionMultimedia;
    }
    public function expose() {
        return get_object_vars($this);
    }
}
