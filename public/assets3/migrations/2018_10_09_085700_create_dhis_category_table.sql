DROP TABLE IF EXISTS dhis_category//
CREATE TABLE dhis_category (
  dhiscode varchar(100) NOT NULL,
  name varchar(100) NOT NULL
)//

INSERT INTO dhis_category (dhiscode, name) VALUES
('jWmWT3Nvq1P', 'balance'),
('XmKrTgYAPoi', 'received'),
('yP6vevc91WZ', 'dispensed_packs'),
('b11dZBeBzRE', 'losses'),
('LeyPc0LYjLg', 'adjustments'),
('O9yaDegYywr', 'adjustments_neg'),
('GvjV9gy3OOc', 'count'),
('r9aTy1gRXUC', 'expiry_quant'),
('hOMc7AVsdRk', 'expiry_date'),
('aDZLiIaG8gC', 'out_of_stock'),
('R4B7KIT1mch', 'resupply'),
('NhSoXUMPK2K', 'total')//