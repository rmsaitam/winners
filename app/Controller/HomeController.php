﻿<?php
class HomeController extends AppController{

	public function beforeFilter(){
		return true;
   	}

	function index(){
		$this->layout = 'winners';
	}

	function requisicoes(){
		if($this->request->data['login']['acao'] == 'logar'){
			echo 'entrou aqui';
			echo $this->request->data['login']['login_email'];
		}else{
			echo 'ainda não';
			echo $this->request->data['login']['login_email'];
		}
	}

	function login() {
		$this->layout = 'login';
	}

	function tema(){
		$this->layout = 'winners';
	}

	function teste_arrays(){
		$produtos = array();

		for($cont = 0; $cont<9; $cont++){
			$array = array('Produtos' => array('nome' => 'produto'.$cont , 'id_produto' => $cont*2));
			array_push($produtos, $array);
		}
		print_r($produtos);

		foreach($produtos as $valor){
			echo 'Nome produto: '. $valor['Produtos']['nome'].'<br>';
			echo 'ID_Prod'.$valor['Produtos']['id_produto'].'<br>';
		}
	}

	function enviar_email() {
		$dados = $this->request->data('dados');

		App::uses('CakeEmail', 'Network/Email');

		$Email = new CakeEmail();
		$Email->from(array('winnersdevelopers@gmail.com' => 'Winners'));
		$Email->to($dados['email']);
		$Email->subject('Contato Winners Desenvolvimento');
		$Email->send('Muito obrigado');
	}
}