<?php

function atualizarEstoque($jsonProdutos) {
    $dsn = 'mysql:host=localhost;dbname=geovendas_estoque;charset=utf8mb4';
    $usuario = 'root';
    $senha = '';
    
    try {
        $pdo = new PDO($dsn, $usuario, $senha);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        foreach ($jsonProdutos as $produto) {
            
            $sql = "SELECT id FROM produtos
                    WHERE produto = :produto 
                      AND cor = :cor 
                      AND tamanho = :tamanho 
                      AND deposito = :deposito 
                      AND data_disponibilidade = :data_disponibilidade";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':produto', $produto['produto']);
            $stmt->bindParam(':cor', $produto['cor']);
            $stmt->bindParam(':tamanho', $produto['tamanho']);
            $stmt->bindParam(':deposito', $produto['deposito']);
            $stmt->bindParam(':data_disponibilidade', $produto['data_disponibilidade']);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $id = $resultado['id'];
                $quantidade = $produto['quantidade'];
                
                $sql = "UPDATE produtos SET quantidade = quantidade + :quantidade WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':quantidade', $quantidade, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $sql = "INSERT INTO produtos (produto, cor, tamanho, deposito, data_disponibilidade, quantidade) 
                        VALUES (:produto, :cor, :tamanho, :deposito, :data_disponibilidade, :quantidade)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':produto', $produto['produto']);
                $stmt->bindParam(':cor', $produto['cor']);
                $stmt->bindParam(':tamanho', $produto['tamanho']);
                $stmt->bindParam(':deposito', $produto['deposito']);
                $stmt->bindParam(':data_disponibilidade', $produto['data_disponibilidade']);
                $stmt->bindParam(':quantidade', $produto['quantidade'], PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        
        echo "Atualização de estoque concluída.";
        
    } catch (PDOException $e) {
        echo "Erro ao atualizar estoque: " . $e->getMessage();
    }
}

$jsonExemplo = '[
    {
        "produto": "10.01.0419",
        "cor": "00",
        "tamanho": "P",
        "deposito": "DEP1",
        "data_disponibilidade": "2023-05-01",
        "quantidade": 15
    },
    {
        "produto": "11.01.0568",
        "cor": "08",
        "tamanho": "P",
        "deposito": "DEP1",
        "data_disponibilidade": "2023-05-01",
        "quantidade": 2
    },
    {
        "produto": "11.01.0568",
        "cor": "08",
        "tamanho": "M",
        "deposito": "DEP1",
        "data_disponibilidade": "2023-05-01",
        "quantidade": 4
    },
    {
        "produto": "11.01.0568",
        "cor": "08",
        "tamanho": "G",
        "deposito": "1",
        "data_disponibilidade": "2023-05-01",
        "quantidade": 6
    },
    {
        "produto": "11.01.0568",
        "cor": "08",
        "tamanho": "P",
        "deposito": "DEP1",
        "data_disponibilidade": "2023-06-01",
        "quantidade": 8
    }
]';

$arrayProdutos = json_decode($jsonExemplo, true);

atualizarEstoque($arrayProdutos);
