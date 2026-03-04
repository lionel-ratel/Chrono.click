
UPDATE `#__extensions` SET `enabled` = '1' WHERE `folder` = 'cck_field' AND `element` IN ('cck_processing');

UPDATE `#__cck_core_fields` SET `type` = 'cck_processing' WHERE `type` = 'code_processing';

DELETE FROM `#__extensions` WHERE `folder` = 'cck_field' AND `element` = 'code_processing';