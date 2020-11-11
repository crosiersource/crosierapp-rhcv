START TRANSACTION;

SET FOREIGN_KEY_CHECKS=0;

DELETE FROM cfg_program WHERE uuid = '4f1e239d-156a-4398-b552-f64bb1852d36';
DELETE FROM cfg_entmenu WHERE uuid = '84807b2f-c971-4e03-b3da-5ae31f204060';





INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('4f1e239d-156a-4398-b552-f64bb1852d36','COISAS [LIST]', '/coisa/list', '05042f6e-31ad-4243-9a09-91524a762300', null, now(), now(), 1, 1, 1);

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, program_uuid, pai_uuid, ordem, css_style, inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('84807b2f-c971-4e03-b3da-5ae31f204060', 'Coisas', 'fas fa-drumstick-bite', 'ENT', '4f1e239d-156a-4398-b552-f64bb1852d36', '92f4e43c-cdd9-45fd-9077-cba05cbcfbf3', 1 , null, now(), now(), 1, 1, 1);



COMMIT;
