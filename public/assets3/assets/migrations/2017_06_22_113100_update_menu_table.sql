UPDATE menu set menu_text = 'Tools', menu_url = 'tools'
WHERE lower(menu_text) = 'update'//

UPDATE menu set active = 0
WHERE lower(menu_text) in ('backup','migration')//