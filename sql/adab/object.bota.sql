update adab_object a, wwbota_reference b
set a.`bota` = b.`reference`
where a.`reference` = replace(substring_index(b.`name`, ' ', 1), 'A', 'A0');