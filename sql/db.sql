CREATE TABLE IF NOT EXISTS `mc_vat` (
    `id_vat` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
    `percent_vat` decimal(4,0) NULL DEFAULT '0',
    `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_vat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mc_vat_category` (
    `id_vat_c` int(7) unsigned NOT NULL AUTO_INCREMENT,
    `id_vat` smallint(3) unsigned NOT NULL,
    `id_cat` int(7) unsigned NOT NULL,
    `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_vat_c`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `mc_admin_access` (`id_role`, `id_module`, `view`, `append`, `edit`, `del`, `action`)
SELECT 1, m.id_module, 1, 1, 1, 1, 1 FROM mc_module as m WHERE name = 'vat';