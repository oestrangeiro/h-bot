<?php
namespace Oestrangeiro\HBot;

class HBot{

	protected $imagePath = '';
	protected $folderPath = '';
	protected $folderName = 'Images/';
	protected $urlToSaveImages = 'https://wimg.rule34.xxx/images/'; //Concatenar com o id do personagem e o nome do arquivo
	protected $arrayPageContents = array();
	protected $arrayWithPrefixAndImagesNames;
	public $countImagesDownloaded = 0;
	

	public function __construct(){
		$this->folderPath = dirname(__DIR__, 3) . '/';
		$this->makeDirIfNotExists();
	}

	public function makeDirIfNotExists(){

		// Checa se a pasta existe
		$dirExists = is_dir($this->folderPath . $this->folderName);

		// Se não existe, tento criar
		if(!$dirExists){
			echo "Pasta de imagens não existe. Criando, aguarde...\n";
			$mkDirStatus = mkdir($this->folderPath . $this->folderName);
			try{
				if(!$mkDirStatus){
					echo "ERRO FATAL: Não foi possível criar a pasta!\n";
					echo "Encerrando o programa...\n";
				}
				echo "Pasta criada com sucesso!\n";
			}catch(Exception $e){
				echo "Error: {$e->getMessage()}\n";
			}
		}else{
			echo "Pasta de imagens encontrada!\n";
		}

		// Por fim defino o caminho absoluto de onde estarão as minhas imagens
		$this->folderPath = $this->folderPath . $this->folderName;

	}

	public function search(string $link){
		$this->urlRule34 = $link;
	}

	public function getPageContents(){
		// Por ora, vou tentar pegar só a primeira imagem
		$pageContents = file_get_contents($this->urlRule34);

		$tag = 'src="https://wimg.rule34.xxx/thumbnails/';
		$explode = explode($tag, $pageContents);
		unset($explode[0]);

		// Cada indice segue esse padrão:
		// 1999/01e4592770550b7ede1d556e4b1a3824.jpg?12616452
		// Então eu removo tudo o que vem depois do '?'

		$charToRemove = '?';
		foreach ($explode as $links) {
			$positionChar = strpos($links, $charToRemove);
			$this->arrayPageContents[] = substr($links, 0, $positionChar);
		}

		// Pronto, agora tenho o id do personagem e o id da foto com extensão
		// var_dump($this->arrayPageContents);
		
	}

	public function filter(){
		$newArray = [];

		foreach ($this->arrayPageContents as $key => $value) {
			if(preg_match('/^(\d+)\/thumbnail_([^\/]+)$/', $value, $matches)){
				$index = $matches[1];
				$imageName = $matches[2];
				$imageName = preg_replace('/\.[^.]+$/', '', $imageName);
				$newArray[$index][] = $imageName;
			}

		}

		$this->arrayWithPrefixAndImagesNames = $newArray;

	}
	public function save(){
		
		$extensions = ['png', 'jpg', 'jpeg'];

		foreach ($this->arrayWithPrefixAndImagesNames as $prefix => $image) {

			foreach ($image as $imageName) {

				foreach ($extensions as $ext) {
					$url = $this->urlToSaveImages . $prefix . '/' . $imageName;
					
					// Tenta salvar a imagem
					$statusFileGetContents = file_get_contents($url . '.' . $ext);

					if(!$statusFileGetContents){
						continue;
					}else{
						@file_put_contents(
							$this->folderPath . $imageName . '.' . $ext, file_get_contents(
								$url . '.' . $ext
							)
						);
					}
				}
			}
		}
	}
}