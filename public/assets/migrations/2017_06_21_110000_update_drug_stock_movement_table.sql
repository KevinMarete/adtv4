UPDATE drug_stock_movement
SET transaction_date = DATE_FORMAT(transaction_date, '%Y-%m-%d'), expiry_date = DATE_FORMAT(expiry_date, '%Y-%m-%d')//