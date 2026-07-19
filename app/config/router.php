<?php

$router = $di->getRouter();

$router->add('/pessoa-fisica', [
    'controller' => 'pessoa-fisica',
    'action' => 'index'
]);

$router->add('/pessoa-fisica/novo', [
    'controller' => 'pessoa-fisica',
    'action' => 'novo'
]);

$router->add('/pessoa-fisica/cadastrar', [
    'controller' => 'pessoa-fisica',
    'action' => 'cadastrar'
]);

$router->add('/pessoa-fisica/editar/{id:[0-9]+}', [
    'controller' => 'pessoa-fisica',
    'action' => 'editar'
]);

$router->add('/pessoa-fisica/atualizar', [
    'controller' => 'pessoa-fisica',
    'action' => 'atualizar'
]);

$router->add('/pessoa-fisica/excluir/{id:[0-9]+}', [
    'controller' => 'pessoa-fisica',
    'action' => 'excluir'
]);

$router->handle($_SERVER['REQUEST_URI']);
