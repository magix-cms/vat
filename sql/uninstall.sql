TRUNCATE TABLE `mc_vat_category`;
DROP TABLE `mc_vat_category`;
TRUNCATE TABLE `mc_vat`;
DROP TABLE `mc_vat`;

DELETE FROM `mc_plugins_module` WHERE `module_name` = 'vat';

DELETE FROM `mc_plugins` WHERE `name` = 'vat';

DELETE FROM `mc_admin_access` WHERE `id_module` IN (
    SELECT `id_module` FROM `mc_module` as m WHERE m.name = 'vat'
);