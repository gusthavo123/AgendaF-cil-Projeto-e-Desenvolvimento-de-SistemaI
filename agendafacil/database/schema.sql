-- AgendaFácil: script de criação do banco de dados
-- Importe este arquivo no phpMyAdmin (aba "Importar") ou rode: mysql -u root < database/schema.sql

CREATE DATABASE IF NOT EXISTS agendafacil
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE agendafacil;

DROP TABLE IF EXISTS agendamentos;
DROP TABLE IF EXISTS servicos;
DROP TABLE IF EXISTS profissionais;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome        VARCHAR(100) NOT NULL,
    email       VARCHAR(120) NOT NULL,
    senha_hash  VARCHAR(255) NOT NULL,
    criado_em   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB;

CREATE TABLE clientes (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome        VARCHAR(100) NOT NULL,
    telefone    VARCHAR(20)  NOT NULL,
    email       VARCHAR(120) NULL,
    criado_em   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE profissionais (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome          VARCHAR(100) NOT NULL,
    telefone      VARCHAR(20)  NULL,
    especialidade VARCHAR(80)  NULL,
    ativo         TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE servicos (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome         VARCHAR(100) NOT NULL,
    descricao    VARCHAR(255) NULL,
    duracao_min  SMALLINT UNSIGNED NOT NULL,
    preco        DECIMAL(10,2) NOT NULL,
    ativo        TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE agendamentos (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    cliente_id       INT UNSIGNED NOT NULL,
    profissional_id  INT UNSIGNED NOT NULL,
    servico_id       INT UNSIGNED NOT NULL,
    usuario_id       INT UNSIGNED NOT NULL,
    data_hora_inicio DATETIME NOT NULL,
    status           ENUM('agendado','concluido','cancelado') NOT NULL DEFAULT 'agendado',
    observacoes      VARCHAR(255) NULL,
    PRIMARY KEY (id),
    KEY idx_agendamentos_inicio (data_hora_inicio),
    KEY idx_agendamentos_profissional (profissional_id, data_hora_inicio),
    -- RESTRICT impede apagar cliente, profissional, serviço ou usuário que tenha agendamentos (RF10)
    CONSTRAINT fk_agd_cliente      FOREIGN KEY (cliente_id)      REFERENCES clientes (id)      ON DELETE RESTRICT,
    CONSTRAINT fk_agd_profissional FOREIGN KEY (profissional_id) REFERENCES profissionais (id) ON DELETE RESTRICT,
    CONSTRAINT fk_agd_servico      FOREIGN KEY (servico_id)      REFERENCES servicos (id)      ON DELETE RESTRICT,
    CONSTRAINT fk_agd_usuario      FOREIGN KEY (usuario_id)      REFERENCES usuarios (id)      ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Dados iniciais
-- Login de teste: admin@agendafacil.com / senha: admin123
-- (troque a senha depois; o hash abaixo é bcrypt gerado com password_hash)
-- ---------------------------------------------------------------
INSERT INTO usuarios (nome, email, senha_hash) VALUES
('Administrador', 'admin@agendafacil.com', '$2y$10$Atvb2BXX14n1.V1sIOk18O8Jj/10XLIVtSL2XfIB6VTsEob56cjZ.');

INSERT INTO clientes (nome, telefone, email) VALUES
('João Silva',   '(43) 99999-0001', 'joao@exemplo.com'),
('Maria Souza',  '(43) 99999-0002', NULL),
('Carlos Lima',  '(43) 99999-0003', 'carlos@exemplo.com');

INSERT INTO profissionais (nome, telefone, especialidade, ativo) VALUES
('Rafael Costa', '(43) 98888-0001', 'Barbeiro', 1),
('Ana Paula',    '(43) 98888-0002', 'Cabeleireira', 1);

INSERT INTO servicos (nome, descricao, duracao_min, preco, ativo) VALUES
('Corte masculino', 'Corte na máquina ou tesoura', 30, 40.00, 1),
('Barba',           'Barba com toalha quente',      20, 25.00, 1),
('Corte feminino',  'Corte e escova',               60, 90.00, 1);
