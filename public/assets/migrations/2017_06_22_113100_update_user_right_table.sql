UPDATE user_right ur 
INNER JOIN menu m ON ur.menu = m.id
SET ur.active = 0
WHERE lower(m.menu_text) IN ('migration', 'backup')//