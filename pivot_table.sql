CREATE  PROCEDURE `PivotExample`()
BEGIN

SET SESSION group_concat_max_len=150000;

SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

drop temporary Table IF EXISTS T1;
drop temporary Table IF EXISTS T2;
drop temporary Table IF EXISTS T3;
drop temporary Table IF EXISTS T_1;
drop temporary Table IF EXISTS T_2;

Create temporary Table T1
SELECT 
    CPM.Phys_ID,
    CPM.First_Name,
    CPM.Last_Name
FROM CPD_Physician CPM
where CPM.status=1 
order by CPM.CreatedOn desc;

Create temporary Table T2
#SELECT distinct DATE_FORMAT(start_datetime, '%b - %y') as `month_name` from `meeting_master` order by `start_datetime` desc;
select distinct month_name from (SELECT distinct DATE_FORMAT(mm1.start_datetime, '%b - %y') as `month_name`,mm1.start_datetime as date_time from bi_marketing_production.`meeting_master` as mm1
union all 
SELECT  distinct DATE_FORMAT(mm2.start_datetime, '%b - %y') as `month_name`,mm2.start_datetime as date_time from bi_medical_production.`meeting_master` as mm2) as T order by T.`date_time` desc;

Create temporary Table T_1
SELECT T1.Phys_ID,T1.First_Name,T1.Last_Name,concat(`mm`.`meeting_name`,'(',DATE_FORMAT(`mm`.`start_datetime`, "%d - %m - %Y"),')') as meeting_name,T2.month_name as `month_name` from bi_marketing_production.`meeting_master` as `mm` INNER join bi_marketing_production.`meeting_user_mapping` as mum on (`mum`.`meeting_id`=`mm`.`meeting_id`)  left join T1  on (`T1`.`Phys_ID`=`mum`.`user`) left join `T2` on (`T2`.`month_name`=DATE_FORMAT(`mm`.`start_datetime`, '%b - %y'))  order by `mm`.`start_datetime` desc;

Create temporary Table T_2
SELECT T1.Phys_ID,T1.First_Name,T1.Last_Name,concat(`mm`.`meeting_name`,'(',DATE_FORMAT(`mm`.`start_datetime`, "%d - %m - %Y"),')') as meeting_name,T2.month_name as `month_name` from bi_medical_production.`meeting_master` as `mm` INNER join bi_medical_production.`meeting_user_mapping` as mum on (`mum`.`meeting_id`=`mm`.`meeting_id`)  left join T1  on (`T1`.`Phys_ID`=`mum`.`user`) left join `T2` on (`T2`.`month_name`=DATE_FORMAT(`mm`.`start_datetime`, '%b - %y'))  order by `mm`.`start_datetime` desc;

Create temporary Table T3
select * from  T_1
union all
select * from  T_2;

#select * from T2;
#select * from T3;

SET @sql = NULL;
SELECT
  GROUP_CONCAT(DISTINCT
    CONCAT('group_concat(distinct CASE WHEN month_name = ''', month_name,
                ''' THEN meeting_name END) `', month_name, '`')) INTO @sql
FROM T3;

SET @sql = CONCAT('SELECT p.Phys_ID
                    , p.First_Name
                    , p.Last_Name, ', @sql, ' 
                   FROM T1 p
                   LEFT JOIN T3 AS pa 
                    ON p.Phys_ID = pa.Phys_ID
                   GROUP BY p.Phys_ID');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

#select @sql as q;

drop temporary Table IF EXISTS T1;
drop temporary Table IF EXISTS T2;
drop temporary Table IF EXISTS T3;
drop temporary Table IF EXISTS T_1;
drop temporary Table IF EXISTS T_2;


SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ ;
END
