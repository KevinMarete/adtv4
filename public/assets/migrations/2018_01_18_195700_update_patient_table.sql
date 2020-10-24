UPDATE patient set start_bmi = start_weight/(start_height/100 * start_height/100)  WHERE start_height > 0 AND start_weight > 0 //
UPDATE patient set bmi = weight/(height/100 * height/100) WHERE height > 0 AND weight > 0 //
