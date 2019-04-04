START TRANSACTION;

-- app                   05042f6e-31ad-4243-9a09-91524a762300
-- program               27cfaa1b-8f08-4577-a9fb-e325b943bb63
-- entMenu (CrosierCore) 27a3f478-027c-4a4b-917d-cd3f88a36de5
-- entMenu (Raíz do App) 92f4e43c-cdd9-45fd-9077-cba05cbcfbf3
-- entMenu (Dashboard)   983f5444-b146-4541-a1c6-d05c2a26d6c3

SET FOREIGN_KEY_CHECKS=0;

DELETE FROM cfg_app WHERE uuid = '05042f6e-31ad-4243-9a09-91524a762300';
DELETE FROM cfg_program WHERE uuid = '27cfaa1b-8f08-4577-a9fb-e325b943bb63';
DELETE FROM cfg_entmenu WHERE uuid = '27a3f478-027c-4a4b-917d-cd3f88a36de5';
DELETE FROM cfg_entmenu WHERE uuid = '92f4e43c-cdd9-45fd-9077-cba05cbcfbf3';
DELETE FROM cfg_entmenu WHERE uuid = '983f5444-b146-4541-a1c6-d05c2a26d6c3';


INSERT INTO cfg_app(uuid,nome,obs,default_entmenu_uuid,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id) 
VALUES('05042f6e-31ad-4243-9a09-91524a762300','CV','Cadastro de Currículos','92f4e43c-cdd9-45fd-9077-cba05cbcfbf3',now(),now(),1,1,1);

INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('27cfaa1b-8f08-4577-a9fb-e325b943bb63','Dashboard - CV', '/', '05042f6e-31ad-4243-9a09-91524a762300', null, now(), now(), 1, 1, 1);



-- Entrada de menu para o MainMenu do Crosier com apontamento para o Dashboard deste CrosierApp (É EXIBIDO NO MENU DO CROSIER-CORE)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('27a3f478-027c-4a4b-917d-cd3f88a36de5','Dashboard - CV','fas fa-columns','ENT','27cfaa1b-8f08-4577-a9fb-e325b943bb63',null,0,null,now(),now(),1,1,1);

-- Entrada de menu raíz para este CrosierApp (NÃO É EXIBIDO)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('92f4e43c-cdd9-45fd-9077-cba05cbcfbf3','CV - MainMenu','','PAI','',null,0,null,now(),now(),1,1,1);

-- Entrada de menu para o menu raíz deste CrosierApp com apontamento para o Dashboard deste CrosierApp TAMBÉM! (É EXIBIDO COMO PRIMEIRO ITEM DO MENU DESTE CROSIERAPP)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('983f5444-b146-4541-a1c6-d05c2a26d6c3','Dashboard','fas fa-columns','ENT','27cfaa1b-8f08-4577-a9fb-e325b943bb63','27a3f478-027c-4a4b-917d-cd3f88a36de5',0,null,now(),now(),1,1,1);



COMMIT;
