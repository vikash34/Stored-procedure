CREATE DEFINER=`user_name`@`%` PROCEDURE `table_name`(IN `p_data` JSON)
BEGIN
	 
	#call table_name('{ "Project_Name": "Project1", "Project_Category": "Digital", "ProgramType_ID": "11", "CreatedBy": "2", "ctc_job": "23 CTC", "zulimar_job": "23 Zulimar", "Client_ID": 21, "invoice_status": 1, "total_budget": "234", "include_meetings": "1", "include_faculty": "1", "Pay_Id": "30", "Payment_Amount": "70.2", "Project_Note": "comeent test", "project_status": "Open Started" }');

	DECLARE v_Project_ID       INT DEFAULT NULL;
	#DECLARE v_Phys_ID          INT DEFAULT NULL;
    DECLARE v_Client_Org_ID    INT DEFAULT NULL ;
	DECLARE v_Client_ID        TEXT;
	DECLARE v_ProgramType_ID   TEXT;
	DECLARE v_Project_Code     VARCHAR(500);
	DECLARE v_Zulimar_Code     VARCHAR(500);
	DECLARE v_Project_Name     VARCHAR(500);
	DECLARE v_Project_Category VARCHAR(500);
	#DECLARE v_Project_StDate   DATETIME;
	#DECLARE v_NoofCreditApp    INT DEFAULT NULL;
	#DECLARE v_Project_Loc      VARCHAR(500);
	#DECLARE v_Province         INT DEFAULT NULL;
	#DECLARE v_Project_EDate    DATETIME;
	DECLARE v_ctc_job          VARCHAR(500);
	DECLARE v_zulimar_job      VARCHAR(500);
	DECLARE v_total_budget     DECIMAL(10,0) DEFAULT NULL;
	DECLARE v_invoice_status   INT(5);
	DECLARE v_HST   		   VARCHAR(50);
	DECLARE v_Pay_Id           INT DEFAULT NULL;
	DECLARE v_Payment_Amount   DECIMAL(16,2) DEFAULT NULL;
	DECLARE v_Project_Note     VARCHAR(500);
	DECLARE v_include_meetings INT DEFAULT NULL;
	DECLARE v_include_faculty  INT DEFAULT NULL;
	DECLARE v_project_status   VARCHAR(500);
	DECLARE v_CreatedBy        INT DEFAULT NULL;
	DECLARE v_file_Obj         TEXT;
	DECLARE v_assignteam       TEXT;
	DECLARE v_alldoc_description       TEXT;
    
    DECLARE k 				  INT DEFAULT 0;
    DECLARE j 				  INT DEFAULT 0;
    DECLARE l 				  INT DEFAULT 0;
    DECLARE m 				  INT DEFAULT 0;

	DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			GET DIAGNOSTICS CONDITION 1
			  @p1 = CLASS_ORIGIN        ,
			  @p2 = SUBCLASS_ORIGIN        ,
			  @p3 = RETURNED_SQLSTATE   ,
			  @p4 = MESSAGE_TEXT        ,
			  @p5 = MYSQL_ERRNO         ,
			  @p6 = CONSTRAINT_CATALOG  ,
			  @p7 = CONSTRAINT_SCHEMA   ,
			  @p8 = CONSTRAINT_NAME     ,
			  @p9 = CATALOG_NAME        ,
			  @p10 = SCHEMA_NAME        ,
			  @p11 = TABLE_NAME         ,
			  @p12 = COLUMN_NAME        ,
			  @p13 = CURSOR_NAME        ;
			  
			  ROLLBACK;
			  
			  INSERT INTO ERROR_LOG
				(C_CLASS_ORIGIN, C_SUBCLASS_ORIGIN, C_RETURNED_SQLSTATE, C_MESSAGE_TEXT, C_MYSQL_ERRNO, 
				 C_CONSTRAINT_CATALOG,C_CONSTRAINT_SCHEMA, C_CONSTRAINT_NAME, C_CATALOG_NAME, 
				 C_SCHEMA_NAME, C_TABLE_NAME, C_COLUMN_NAME, C_CURSOR_NAME,C_PROCEDURE_NAME, C_DATA    )
				VALUES( @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8, @p9, @p10, @p11, @p12, @p13,'Insert_Project', p_data);
	 
			SELECT 0 as flag, @p4 as msg;
		END;
	 
	/*
	SET v_Phys_ID		 = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.Phys_ID'));    
	SET v_Client_ID		 = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.selectclientID'));    
	SET v_Project_Category  = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.selectproject_category'));    
	SET v_ProgramType_ID  = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.ProgramType_ID'));    
	SET v_Project_Name   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.Project_Name'));
	SET v_CreatedBy      = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.CreatedBy'));
    SET v_Province_Obj   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.Province_Data'));
	SET v_ctc_job   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.ctc_job'));
	SET v_zulimar_job   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.zulimar_job'));
	SET v_total_budget   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.total_budget'));
	SET v_invoice_status   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.invoice_status'));
	SET v_include_meetings   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.include_meetings'));
	SET v_include_faculty   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.include_faculty'));
	SET v_project_status   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.selectproject_status'));
	*/
	#SET @pProjectID=0;
	#SELECT MAX(@pProjectID:=@pProjectID+1) INTO v_ProjectID from CPD_Project;
	#SELECT IFNULL(MAX(Project_ID),0) INTO v_ProjectID from CPD_Project;
	#SET v_Project_Code   = CONCAT('PJ',date_format(NOW(),'%m%y'),(v_ProjectID+1)) ;
	#SET v_Project_Code   = f_get_init_Project(v_Project_Name);
	#SELECT CONCAT(Clientorg_Id,"/",f_get_init_Project(v_Project_Name)) INTO v_Project_Code
	#FROM CTC_ClientOrganisation
	#WHERE _ID  = v_Client_ID;
    
  	#SET v_Project_ID       = JSON_EXTRACT(p_data,'$.Project_ID');
    SET v_Client_Org_ID	   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.Clientorg_Id'));	
	SET v_Client_ID        = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.Client_ID'));
	SET v_ProgramType_ID   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.ProgramType_ID'));
	#SET v_Project_Code     = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.Project_Code'));
	#SET v_Zulimar_Code     = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.Zulimar_Code'));
	SET v_Project_Name     = TRIM(JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.Project_Name')));
	SET v_Project_Category = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.Project_Category'));
	SET v_ctc_job          = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.ctc_job'));
	SET v_zulimar_job      = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.zulimar_job'));
	SET v_total_budget     = JSON_EXTRACT(p_data,'$.total_budget');
	SET v_invoice_status   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.invoice_status'));
	SET v_HST   		   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.HST'));
	SET v_Pay_Id           = JSON_EXTRACT(p_data,'$.Pay_Id');
	SET v_Payment_Amount   = JSON_EXTRACT(p_data,'$.Payment_Amount');
	SET v_Project_Note     = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.Project_Note'));
	SET v_include_meetings = JSON_EXTRACT(p_data,'$.include_meetings');
	SET v_include_faculty  = JSON_EXTRACT(p_data,'$.include_faculty');
	SET v_project_status   = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.project_status'));
	SET v_CreatedBy        = JSON_EXTRACT(p_data,'$.CreatedBy');
    SET v_file_Obj         = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.userfile'));
    SET v_assignteam       = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.assignteam'));
    SET v_alldoc_description       = JSON_UNQUOTE(JSON_EXTRACT(p_data,'$.alldoc_description'));
    
    IF exists(select 1 from CPD_Project where Project_Name = v_Project_Name) THEN
		select 2 as flag,'Project is already exist' as msg;
	ELSE
		START TRANSACTION; 
		
		INSERT INTO CPD_Project
		( 
			
            Clientorg_Id
			, ProgramType_ID
			, Project_Code
			, Zulimar_Code
			, Project_Name
			, Project_Category
			, ctc_job
			, zulimar_job
			, total_budget
			, invoice_status
            , HST
			, Pay_Id
			, Payment_Amount
			, Project_Note
			, include_meetings
			, include_faculty
			, project_status
			, CreatedBy
		)
		VALUE
		( 
			v_Client_Org_ID
			, ''
			, v_Project_Code
			, v_Zulimar_Code
			, v_Project_Name
			, v_Project_Category
			, v_ctc_job
			, v_zulimar_job
			, v_total_budget
			, v_invoice_status
            , v_HST
			, v_Pay_Id
			, v_Payment_Amount
			, v_Project_Note
			, v_include_meetings
			, v_include_faculty
			, v_project_status
			, v_CreatedBy
		);
        
        
		
		SELECT LAST_INSERT_ID() INTO v_Project_ID;
        
        INSERT INTO `CPD_Accounting` (`project_id`) VALUES ( v_Project_ID);
        
         IF (v_ProgramType_ID IS NOT NULL) THEN  
		   WHILE m < JSON_LENGTH(v_ProgramType_ID) DO
					
	#INSERT INTO CTC_Project_Team(Project_ID,User_ID)value(v_Project_ID,JSON_UNQUOTE(JSON_EXTRACT(v_assignteam,CONCAT('$[',j,']'))));
	 INSERT INTO `project_programtype_mapping`
		(
		`programtype_id`,
		`project_id`)
		VALUES
		(
			JSON_UNQUOTE(JSON_EXTRACT(v_ProgramType_ID,CONCAT('$[',m,']'))),
            v_Project_ID
		);
						
						SELECT m + 1 INTO m;
						
					END WHILE;
        END IF;
        
        
        IF (v_file_Obj IS NOT NULL) THEN  
		   WHILE k < JSON_LENGTH(v_file_Obj) DO
					
						INSERT INTO CTC_Project_File
							(
                              Project_ID,
                              file_name,
                              file_description,
                              file_uploaded_by
							)
						value(
								v_Project_ID,	
								JSON_UNQUOTE(JSON_EXTRACT(v_file_Obj,CONCAT('$[',k,']'))),
								JSON_UNQUOTE(JSON_EXTRACT(v_alldoc_description,CONCAT('$[',k,']'))),
                                v_CreatedBy
							)
							;
						
						SELECT k + 1 INTO k;
						
					END WHILE;
        END IF;
        
        IF (v_assignteam IS NOT NULL) THEN  
		   WHILE j < JSON_LENGTH(v_assignteam) DO
					
	#INSERT INTO CTC_Project_Team(Project_ID,User_ID)value(v_Project_ID,JSON_UNQUOTE(JSON_EXTRACT(v_assignteam,CONCAT('$[',j,']'))));
	 INSERT INTO `ctc_user_projects`
		(
		`user_id`,
		`project_id`)
		VALUES
		(
			JSON_UNQUOTE(JSON_EXTRACT(v_assignteam,CONCAT('$[',j,']'))),
            v_Project_ID
		);
						
						SELECT j + 1 INTO j;
						
					END WHILE;
        END IF;
        
        IF (v_Client_ID IS NOT NULL) THEN  
		   WHILE l < JSON_LENGTH(v_Client_ID) DO
					
	#INSERT INTO project_client_mapping(project_id,cleint_id)value(v_Project_ID,JSON_UNQUOTE(JSON_EXTRACT(v_Client_ID,CONCAT('$[',l,']'))));
	 INSERT INTO `project_client_mapping`
		(
		`client_id`,
		`project_id`)
		VALUES
		(
			JSON_UNQUOTE(JSON_EXTRACT(v_Client_ID,CONCAT('$[',l,']'))),
            v_Project_ID
		);
						
						SELECT l + 1 INTO l;
						
		 END WHILE;
        END IF;
        
        IF (v_Project_ID < 10) THEN
			SET v_Project_Code = CONCAT(CAST(YEAR(now()) AS CHAR(4)), '-', CAST(LPAD(v_Project_ID,2,'0') AS CHAR(10)));
		ELSE
			SET v_Project_Code = CONCAT(CAST(YEAR(now()) AS CHAR(4)), '-', CAST(v_Project_ID AS CHAR(10)));
        END IF;
        
        #IF (UPPER(v_Project_Category) = 'DIGITAL') THEN 
        IF (UPPER(v_Project_Category) = 1) THEN 
			SET v_Zulimar_Code = CONCAT('Z',v_Project_Code);
        END IF;
        
        #SELECT v_Project_ID, v_Project_Code, v_Zulimar_Code;
        
        UPDATE CPD_Project
        SET Project_Code = v_Project_Code, Zulimar_Code = v_Zulimar_Code
        WHERE Project_ID =  v_Project_ID;
		
		SELECT
			1								as flag
			, 'Project saved successfully'	as msg
			, v_Project_ID					as insert_id
		;

	   COMMIT;

	END IF;
END
