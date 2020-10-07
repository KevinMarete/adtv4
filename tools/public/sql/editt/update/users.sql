UPDATE users u 
SET u.Facility_Code = {destination_facility_code}
WHERE u.Facility_Code != {destination_facility_code}//