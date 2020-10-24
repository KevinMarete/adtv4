UPDATE `sync_facility` SET `Active` = '1' WHERE `Active` = ''//

UPDATE sync_drug SET Active ='0' WHERE id IN (
	SELECT *
	FROM (
		SELECT id
		FROM sync_drug
		WHERE id NOT IN (
			SELECT  min(id)
	        FROM sync_drug
	        WHERE Active = '1'
	        GROUP BY CONCAT_WS(') (',CONCAT_WS(' (', name, abbreviation),CONCAT_WS(') ', strength, formulation))
	        HAVING COUNT(id) > 1
	   	)
		AND CONCAT_WS(') (',CONCAT_WS(' (', name, abbreviation),CONCAT_WS(') ', strength, formulation)) IN (
			SELECT 
	            CONCAT_WS(') (',CONCAT_WS(' (', name, abbreviation),CONCAT_WS(') ', strength, formulation)) AS drug
	        FROM sync_drug
	        WHERE Active = '1'
	        GROUP BY drug
	        HAVING COUNT(id) > 1
	        ORDER BY drug
	    )
	) t
)//

UPDATE sync_drug SET Active = '0' WHERE id IN (
	SELECT * 
	FROM (
		SELECT id
		FROM sync_drug
		WHERE CONCAT_WS(')(',CONCAT_WS('(',name,abbreviation),CONCAT(strength, CONCAT(')',packsize))) IN ('Stavudine/Lamivudine/Nevirapine(d4T/3TC/NVP)(30/150/200mg)60', 'Stavudine/Lamivudine(d4T/3TC)(30/150mg)60','Zidovudine(AZT)(300mg)60','Darunavir(DRV)(300mg)120','Saquinavir(SQV)(200mg)270','Nevirapine(NVP)(10mg/ml)240','Raltegravir Susp(RAL)(100mg/5ml)60','Diflucan()(2mg/ml)100','Diflucan()(50mg/5ml)35','Diflucan()(200mg)28','Co-trimoxazole()(480mg)1000','Co-trimoxazole (500s) blister pack Tabs()(960mg)500')
	) t
)//