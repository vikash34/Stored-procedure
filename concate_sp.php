CREATE DEFINER=`user_name`@`%` PROCEDURE `getZoneWiseChartData`(in _CompanyID INT,IN param TEXT,IN _Years INT,IN _UserID INT)
BEGIN

SELECT DISTINCT
    (CASE
        WHEN caluate_report = 1 THEN 1 -- mrp
        WHEN caluate_report = 2 THEN 2 -- 'ptr'
        WHEN caluate_report = 3 THEN 3 -- pts
        ELSE 'mrp'
    END)
INTO @v_col1 FROM
    UserMasters U
WHERE
    U.ClientID = _CompanyID
LIMIT 1;




set @query = ' ';
set @query = concat( @query  , 'SELECT distinct `zm`.`zone_name`,
 CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IFNULL((`z_sale`.`SECONDARY_SALES`),0) * IFNULL((`c_prods`.`mrp`),0) )/100000,2)
 WHEN @v_col1 = 2 THEN ROUND(SUM(IFNULL((`z_sale`.`SECONDARY_SALES`),0) * IFNULL((`c_prods`.`ptr`),0) )/100000,2)
 WHEN @v_col1 = 3 THEN ROUND(SUM(IFNULL((`z_sale`.`SECONDARY_SALES`),0) * IFNULL((`c_prods`.`pts`),0) )/100000,2) 
 END AS `secondary_data`,
 CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IFNULL((`z_sale`.`CLOSING_STOCK`),0) * IFNULL((`c_prods`.`mrp`),0) )/100000,2) 
 WHEN @v_col1 = 2 THEN ROUND(SUM(IFNULL((`z_sale`.`CLOSING_STOCK`),0) * IFNULL((`c_prods`.`ptr`),0) )/100000,2) 
 WHEN @v_col1 = 3 THEN ROUND(SUM(IFNULL((`z_sale`.`CLOSING_STOCK`),0) * IFNULL((`c_prods`.`pts`),0) )/100000,2) 
 END AS `closing_data`
 FROM ZSecondarySales AS `z_sale` LEFT JOIN Zones AS `zm` ON (`zm`.`id`=`z_sale`.`ZCode`) 
 LEFT JOIN CompanyProducts AS `c_prods` ON(`c_prods`.`product_id`=`z_sale`.`CompanyProductCode`) 
 WHERE `z_sale`.`ZCode` IS NOT NULL AND  `z_sale`.`ZCode` != 0 ');
 
         if _CompanyID > 0 then 
      
     set @query = concat(  @query  , ' and `z_sale`.`CompanyID` = ' , _CompanyID ) ;
     
    end if; 
     
	if param  = 'month' then 
      
     set @query = concat( @query  , ' and month(`z_sale`.`Tran_Date`) = ' , MONTH(CURRENT_DATE()) ) ;
     
    end if;
    
    if param  = 'quarter' then 
      
     set @query = concat( @query  , ' and quarter(`z_sale`.`Tran_Date`) = ' , quarter(CURRENT_DATE()) ) ;
     
    end if;
    
    if param  = 'halfyear' then 
      
     set @query = concat( @query  , ' and `z_sale`.`Tran_Date` > ', 'DATE_SUB(now(), INTERVAL 6 MONTH)' ) ;
     
    end if;
    
    if _Years > 0 then 
      
     set @query = concat( @query  , ' and year(`z_sale`.`Tran_Date` ) = ' , _Years ) ;
     
    end if;
    
      
    
    set @query = concat( @query  , ' GROUP BY `z_sale`.`ZCode`; ');
    
 
  #     select @query;
	PREPARE query FROM @query;
	EXECUTE query;
	DEALLOCATE PREPARE query; 


END
