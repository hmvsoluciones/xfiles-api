<?php

class OrdenCompraDTO {
    
    private $idordercompra;
    private $numboletoscompra;
    private $totalcompra;
    private $idusuario;
    private $idmetodopago;
    private $idevento;
    private $idusualta;
    private $fechaalta;
    private $idusumodifica;
    private $fechamodifica;
    private $estatus;
    
    public function getIdordercompra(){
		return $this->idordercompra;
	}

	public function setIdordercompra($idordercompra){
		$this->idordercompra = $idordercompra;
	}

	public function getNumboletoscompra(){
		return $this->numboletoscompra;
	}

	public function setNumboletoscompra($numboletoscompra){
		$this->numboletoscompra = $numboletoscompra;
	}

	public function getTotalcompra(){
		return $this->totalcompra;
	}

	public function setTotalcompra($totalcompra){
		$this->totalcompra = $totalcompra;
	}

	public function getIdusuario(){
		return $this->idusuario;
	}

	public function setIdusuario($idusuario){
		$this->idusuario = $idusuario;
	}

	public function getIdmetodopago(){
		return $this->idmetodopago;
	}

	public function setIdmetodopago($idmetodopago){
		$this->idmetodopago = $idmetodopago;
	}

	public function getIdevento(){
		return $this->idevento;
	}

	public function setIdevento($idevento){
		$this->idevento = $idevento;
	}

	public function getIdusualta(){
		return $this->idusualta;
	}

	public function setIdusualta($idusualta){
		$this->idusualta = $idusualta;
	}

	public function getFechaalta(){
		return $this->fechaalta;
	}

	public function setFechaalta($fechaalta){
		$this->fechaalta = $fechaalta;
	}

	public function getIdusumodifica(){
		return $this->idusumodifica;
	}

	public function setIdusumodifica($idusumodifica){
		$this->idusumodifica = $idusumodifica;
	}

	public function getFechamodifica(){
		return $this->fechamodifica;
	}

	public function setFechamodifica($fechamodifica){
		$this->fechamodifica = $fechamodifica;
	}

	public function getEstatus(){
		return $this->estatus;
	}

	public function setEstatus($estatus){
		$this->estatus = $estatus;
	}

    public function expose() {
        return get_object_vars($this);
    }

}