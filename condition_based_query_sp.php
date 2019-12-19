CREATE DEFINER=`user_name` PROCEDURE `sp_name`(in _CompanyID INT,IN _Month INT,IN _Years INT,IN _UserID INT)
BEGIN 
declare _FreeQty_ind int(1) default 0;
declare TY_START_DATE DATE ;
declare TY_END_DATE DATE ;
declare LY_START_DATE DATE ;
declare LY_END_DATE DATE ;

SET @TY_START_DATE =CONCAT(_Years,'-01-01');
SET @LY_START_DATE =CONCAT(_Years-1,'-01-01');


Select FreeQty_ind into _FreeQty_ind from UserMasters where ClientID =_CompanyID;

SELECT Tran_Date into @TY_END_DATE  FROM  ZSecondarySales  ORDER BY `_id` DESC LIMIT 1;

SET @LY_END_DATE=DATE_SUB(@TY_END_DATE,INTERVAL 1 YEAR);


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

if _FreeQty_ind = 0 then

set @query = concat( @query  , 'SELECT distinct `bm`.`Brand_Name`,
 CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`mrp`),0) )/100000,2)
 WHEN @v_col1 = 2 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`ptr`),0) )/100000,2)
 WHEN @v_col1 = 3 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`pts`),0) )/100000,2)
 END AS `LY_YTD`,
  CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`mrp`),0) )/100000,2)
 WHEN @v_col1 = 2 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`ptr`),0) )/100000,2)
 WHEN @v_col1 = 3 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`pts`),0) )/100000,2)
 END AS `TY_YTD`,
 Round(((CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`mrp`),0) ),2)
 WHEN @v_col1 = 2 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`ptr`),0) ),2)
 WHEN @v_col1 = 3 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`pts`),0) ),2)
 END-CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`mrp`),0) ),2)
 WHEN @v_col1 = 2 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`ptr`),0) ),2)
 WHEN @v_col1 = 3 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`pts`),0) ),2)
 END)/CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`mrp`),0) ),2)
 WHEN @v_col1 = 2 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`ptr`),0) ),2)
 WHEN @v_col1 = 3 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,IFNULL((`z_sale`.`SECONDARY_SALES`),0) * (`c_prods`.`pts`),0) ),2)
 END)*100,2) as growth
 FROM ZSecondarySales AS `z_sale` LEFT JOIN BrandMasters AS `bm` ON (`bm`.`Brand_ID`=`z_sale`.`Brand_ID`) 
 LEFT JOIN CompanyProducts AS `c_prods` ON(`c_prods`.`product_id`=`z_sale`.`CompanyProductCode`) 
 WHERE `bm`.`Brand_Name` IS NOT NULL  ');

  else
 
set @query = concat( @query  , 'SELECT distinct `bm`.`Brand_Name`,
 CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`mrp`),0) )/100000,2)
 WHEN @v_col1 = 2 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`ptr`),0) )/100000,2)
 WHEN @v_col1 = 3 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`pts`),0) )/100000,2)
 END AS `LY_YTD`,
  CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`mrp`),0) )/100000,2)
 WHEN @v_col1 = 2 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`ptr`),0) )/100000,2)
 WHEN @v_col1 = 3 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`pts`),0) )/100000,2)
 END AS `TY_YTD`,
 Round(((CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`mrp`),0) ),2)
 WHEN @v_col1 = 2 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`ptr`),0) ),2)
 WHEN @v_col1 = 3 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @TY_START_DATE AND `z_sale`.`Tran_Date` <= @TY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`pts`),0) ),2)
 END-CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`mrp`),0) ),2)
 WHEN @v_col1 = 2 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`ptr`),0) ),2)
 WHEN @v_col1 = 3 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`pts`),0) ),2)
 END )/CASE 
 WHEN @v_col1 = 1 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`mrp`),0) ),2)
 WHEN @v_col1 = 2 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`ptr`),0) ),2)
 WHEN @v_col1 = 3 THEN ROUND(SUM(IF(`z_sale`.`Tran_Date` >= @LY_START_DATE AND `z_sale`.`Tran_Date` <= @LY_END_DATE ,(IFNULL((`z_sale`.`SECONDARY_SALES`),0) + IFNULL((`z_sale`.`SECONDARY_SALES_FREE`),0)) * (`c_prods`.`pts`),0) ),2)
 END )*100,2) as growth
 FROM ZSecondarySales AS `z_sale` LEFT JOIN BrandMasters AS `bm` ON (`bm`.`Brand_ID`=`z_sale`.`Brand_ID`) 
 LEFT JOIN CompanyProducts AS `c_prods` ON(`c_prods`.`product_id`=`z_sale`.`CompanyProductCode`) 
 WHERE `bm`.`Brand_Name` IS NOT NULL  ');


 end if;  
 
         if _CompanyID > 0 then 
      
     set @query = concat(  @query  , ' and `z_sale`.`CompanyID` = ' , _CompanyID ) ;
     
    end if; 
     
    
    set @query = concat( @query  , ' GROUP BY `z_sale`.`Brand_ID` ORDER BY `TY_YTD` DESC LIMIT 10 ; ');
    
 
  #     select @query;
  PREPARE query FROM @query;
  EXECUTE query;
  DEALLOCATE PREPARE query; 


END
