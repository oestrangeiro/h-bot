<?php

require 'vendor/autoload.php';

use Oestrangeiro\HBot\HBot;

$Hbot = new HBot();

echo "[*] Insira url:\n";
$input = readline();

$Hbot->search($input);

// Pegando o conteudo da página
$Hbot->getPageContents();
// Filtrando o html para obter somente o id e o link das fotos
$Hbot->filter();
// Salvando as imagens
$Hbot->save();

system('clear');
echo "[*] Fotos baixadas com sucesso. Verifique a pasta 'Images/' \n";
