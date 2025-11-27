CREATE DATABASE cadastro_usuarios;

USE cadastro_usuarios;

CREATE TABLE usuario (
	id INT auto_increment PRIMARY KEY,
    nome VARCHAR(100),
    email VARCHAR(100),
    cpf VARCHAR(14) UNIQUE,
    cep VARCHAR(9),
    endereco VARCHAR(100),
    bairro VARCHAR(50),
    cidade VARCHAR(50),
    senha VARCHAR(255) NOT NULL,
    criado_em timestamp default current_timestamp
);    



select * from usuario;





