<?php

class CargoDTO {
    private $id;
    private $method;
    private $status;
    private $conciliated;
	private $source_id;
    private $creation_date;
    private $operation_date;
	private $due_date;
    private $description;
    private $error_message;
    private $amount;
    private $currency;
    private $payment_method;
    private $customer;


    public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getSource_id(){
		return $this->source_id;
	}

	public function setSource_id($source_id){
		$this->source_id = $source_id;
	}

	

	public function getMethod(){
		return $this->method;
	}

	public function setMethod($method){
		$this->method = $method;
	}

	public function getStatus(){
		return $this->status;
	}

	public function setStatus($status){
		$this->status = $status;
	}

	public function getConciliated(){
		return $this->conciliated;
	}

	public function setConciliated($conciliated){
		$this->conciliated = $conciliated;
	}

	public function getCreation_date(){
		return $this->creation_date;
	}

	public function setCreation_date($creation_date){
		$this->creation_date = $creation_date;
	}

	public function getDue_date(){
		return $this->due_date;
	}

	public function setDue_date($due_date){
		$this->due_date = $due_date;
	}

	public function getOperation_date(){
		return $this->operation_date;
	}

	public function setOperation_date($operation_date){
		$this->operation_date = $operation_date;
	}

	public function getDescription(){
		return $this->description;
	}

	public function setDescription($description){
		$this->description = $description;
	}

	public function getError_message(){
		return $this->error_message;
	}

	public function setError_message($error_message){
		$this->error_message = $error_message;
	}

	public function getAmount(){
		return $this->amount;
	}

	public function setAmount($amount){
		$this->amount = $amount;
	}

	public function getCurrency(){
		return $this->currency;
	}

	public function setCurrency($currency){
		$this->currency = $currency;
	}

    public function getPayment_method(){
		return $this->payment_method;
	}

	public function setPayment_method($payment_method){
		$this->payment_method = $payment_method;
	}

    public function getCustomer(){
		return $this->customer;
	}

	public function setCustomer($customer){
		$this->customer = $customer;
	}


    public function expose() {
        return get_object_vars($this);
    }

}