-- Script to remove duplicate property_db records
-- Keep only one record per id_property (the one with the latest tanggal)

-- Create temporary table with unique records
CREATE TEMPORARY TABLE temp_unique_properties AS
SELECT *
FROM property_db p1
WHERE
    tanggal = (
        SELECT MAX(tanggal)
        FROM property_db p2
        WHERE
            p2.id_property = p1.id_property
    )
GROUP BY
    id_property;

-- Delete all records from original table
DELETE FROM property_db;

-- Insert unique records back
INSERT INTO property_db SELECT * FROM temp_unique_properties;

-- Drop temporary table
DROP TEMPORARY TABLE temp_unique_properties;

-- Add PRIMARY KEY to prevent future duplicates
ALTER TABLE property_db ADD PRIMARY KEY (id_property);

-- Show results
SELECT COUNT(*) as total_after FROM property_db;

SELECT id_property, COUNT(*) as count
FROM property_db
GROUP BY
    id_property
HAVING
    count > 1;