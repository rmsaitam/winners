<?php

class ProdutoController extends AppController{	

	public function listar_cadastros() {
		$this->layout = 'wadmin';

		$this->set('produtos', $this->Produto->find('all', 
				array('conditions' => 
					array('ativo' => 1,
						  'id_usuario' => $this->instancia
					)
				)
			)
		);
	}

	public function adicionar_cadastro() {
		$this->layout = 'wadmin';
	}

	public function s_adicionar_cadastro() {
		$dados  = $this->request->data('dados');
		$image  = $_FILES['imagem'];
		
		$retorno = $this->uploadImage($image);

		if (!$retorno['status']) 
			$this->Session->setFlash('Não foi possivel salvar a imagem tente novamente');

		$dados['imagem'] = $retorno['nome'];
		$dados['id_usuario'] = $this->instancia;
		$dados['ativo'] = 1;
		$dados['id_alias'] = $this->id_alias();

		if($this->Produto->save($dados)) {
			$this->Session->setFlash('Produto salvo com sucesso!');
            return $this->redirect('/produto/listar_cadastros');
		} else {
			$this->Session->setFlash('Ocorreu um erro ao salva o produto!');
            return $this->redirect('/produto/listar_cadastros');
		}
	}

	public function editar_cadastro($id) {
		$this->layout = 'wadmin';

		$this->set('produto', $this->Produto->find('all', 
				array('conditions' => 
					array('ativo' => 1,
						  'id' => $id
					)
				)
			)[0]
		);
	}

	public function s_editar_cadastro($id) {
		$dados = $this->request->data('dados');

		$image  = $_FILES['imagem'];
		
		if (!empty($image['name'])) {
			$retorno = $this->uploadImage($image);
			
			if (!$retorno['status']) 
				$this->Session->setFlash('Não foi possivel salvar a imagem tente novamente');
			
			$dados['imagem'] = $retorno['nome'];
		}


		$dados['id_usuario'] = $this->instancia;
		$dados['ativo'] = 1;
		$dados['id_alias'] = $this->id_alias();

		$this->Produto->id = $id;
		
		if ($this->Produto->save($dados)) {
			$this->Session->setFlash('Produto editado com sucesso!','default','good');
            return $this->redirect('/produto/listar_cadastros');
		} else {
			$this->Session->setFlash('Ocorreu um erro ao editar o produto!','default','good');
            return $this->redirect('/produto/listar_cadastros');
		}
	}

	public function excluir_cadastro() {
		$this->layout = 'ajax';

		$id = $this->request->data('id');

		$dados = array ('ativo' => '0');
		$parametros = array ('id' => $id);

		if ($this->Produto->updateAll($dados,$parametros)) {
			echo json_encode(true);
		} else {
			echo json_encode(false);
		}
	}

	public function id_alias() {
		$id_alias = $this->Produto->find('first', array(
				'conditions' => array('Produto.ativo' => 1),
				'order' => array('Produto.id' => 'desc')
			)
		);

		return $id_alias['Produto']['id_alias'] + 1;
	}

	public function carregar_dados_venda_ajax() {
		$this->layout = 'ajax';

		$retorno = $this->Produto->find('first', 
			array('conditions' => 
				array('Produto.ativo' => 1,
					  'Produto.id_usuario' => $this->instancia,
					  'Produto.id' => $this->request->data('id')
				)
			)
		);

		if (!$this->validar_estoque($retorno)) {
			return false;
		}

		$retorno['Produto']['total'] = $this->calcular_preco_produto_venda($retorno['Produto']['preco'], $this->request->data('qnt'));

		$retorno['Produto']['preco'] = number_format($retorno['Produto']['preco'], 2, ',', '.');
		
		echo json_encode($retorno);
	}

	public function validar_estoque($produto) {
		if (empty($produto) && !isset($produto)) {
			return false;
		}

		if ($produto['Produto']['estoque'] <= 0) {
			return false;
		}

		return true;
	}

	public function calcular_preco_produto_venda($preco, $qnt) {
		if (empty($preco) || !isset($preco)) {
			return false;
		}

		if (!is_numeric($qnt)) {
			return false;
		}

		$retorno = $preco * $qnt;

		return number_format($retorno, 2, ',', '.');
	}

	public function uploadImage(&$image) {
		$type = substr($image['name'], -4);
		$nameImage = uniqid() . md5($image['name']) . $type;
		$dir = APP . 'webroot/uploads/produto/imagens/';
		
		$returnUpload = move_uploaded_file($image['tmp_name'], $dir . $nameImage);

		if (!$returnUpload)
			return array('nome' => null, 'status' => false);

		return array('nome' => $nameImage, 'status' => true);
	}

	public function visualizar_cadastro($id) {
		$this->layout = 'wadmin';

		$produto = $this->Produto->find('all', 
			array('conditions' => 
				array('ativo' => 1,
					  'id' => $id
				)
			)
		);

		if (empty($produto)) {
			$this->Session->setFlash("Produto não encotrado, tente novamente");
			$this->redirect("/produto/listar_cadastros");
		}

		$this->set('produto', $produto[0]);
	}

}