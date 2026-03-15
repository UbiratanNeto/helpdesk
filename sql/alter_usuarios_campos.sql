-- Adiciona campos de endereço, ativo, empresa e demais ao cadastro de usuários
-- Execute no phpMyAdmin (banco helpdesk) ou via linha de comando.

ALTER TABLE `usuarios`
  ADD COLUMN `endereco`      VARCHAR(255) NULL DEFAULT NULL COMMENT 'Logradouro' AFTER `senha`,
  ADD COLUMN `numero`        VARCHAR(20)  NULL DEFAULT NULL COMMENT 'Número' AFTER `endereco`,
  ADD COLUMN `complemento`   VARCHAR(100) NULL DEFAULT NULL AFTER `numero`,
  ADD COLUMN `bairro`        VARCHAR(100) NULL DEFAULT NULL AFTER `complemento`,
  ADD COLUMN `cidade`        VARCHAR(100) NULL DEFAULT NULL AFTER `bairro`,
  ADD COLUMN `estado`        CHAR(2)     NULL DEFAULT NULL COMMENT 'UF' AFTER `cidade`,
  ADD COLUMN `cep`           VARCHAR(10) NULL DEFAULT NULL AFTER `estado`,
  ADD COLUMN `ativo`         TINYINT(1)  NOT NULL DEFAULT 1 COMMENT '1=ativo, 0=inativo' AFTER `cep`,
  ADD COLUMN `empresa`       INT(11)     NOT NULL DEFAULT 0 COMMENT 'ID empresa (0 = único)' AFTER `ativo`,
  ADD COLUMN `data_cadastro` DATETIME    NULL DEFAULT NULL AFTER `empresa`,
  ADD COLUMN `data_atualizacao` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `data_cadastro`;
