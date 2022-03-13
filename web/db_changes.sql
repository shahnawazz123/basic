/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  akram
 * Created: Jul 7, 2021
 */
ALTER TABLE doctor_working_days ADD end_time TIME NOT NULL;
ALTER TABLE doctor_working_days ADD start_time TIME NOT NULL;


/**
* Author: Vasim 
* Created: July 7, 2021
**/
CREATE TABLE `symptoms` (
  `symptom_id` int(11) NOT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `name_ar` varchar(100) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `is_active` int(4) NOT NULL DEFAULT 0,
  `is_deleted` int(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `symptoms`
  ADD PRIMARY KEY (`symptom_id`);

  ALTER TABLE `symptoms`
  MODIFY `symptom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


CREATE TABLE `doctor_symptoms` (
  `doctor_symptom_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `symptom_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `doctor_symptoms`
  ADD PRIMARY KEY (`doctor_symptom_id`),
  ADD KEY `symptom_id` (`symptom_id`);

ALTER TABLE `doctor_symptoms`
  ADD CONSTRAINT `doctor_symptoms_ibfk_1` FOREIGN KEY (`symptom_id`) REFERENCES `symptoms` (`symptom_id`);


ALTER TABLE `doctors` ADD `is_featured` INT(10) NOT NULL DEFAULT '0' AFTER `is_deleted`;
ALTER TABLE `clinics` ADD `is_featured` INT(10) NOT NULL DEFAULT '0' AFTER `is_deleted`;

/*
* Author: Vasim 
* Created : Aug 7,2021 
*/

ALTER TABLE `promotions` CHANGE `promo_for` `promo_for` ENUM('P','B','D','C','L') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

INSERT INTO `auth_module` (`auth_module_id`, `auth_module_name`, `auth_module_url`, `is_active`, `sort_order`) VALUES (NULL, 'Promotions', '/promotions', '1', NULL)

INSERT INTO `auth_item` (`auth_item_id`, `auth_item_url`, `auth_item_name`, `auth_item_description`, `auth_module_id`, `rule_name`, `is_active`, `created_at`) VALUES 
(NULL, '/index', 'Manage', NULL, '24', 'admin', '1', '2021-07-08 14:07:38.000000'), 
(NULL, '/create', 'Create', NULL, '24', 'admin', '1', '2021-07-08 14:07:38.000000'),
(NULL, '/view', 'Views', NULL, '24', 'admin', '1', '2021-06-27-08:07:38.000000'),
(NULL, '/update', 'Update', NULL, '24', 'admin', '1', '2021-07-08 14:07:38.000000'),
(NULL, '/delete', 'Delete', NULL, '24', 'admin', '1', '2021-07-08 14:07:38.000000');

/**
 * Author:  akram
 * Created: Jul 9, 2021
 */
ALTER TABLE clinics MODIFY COLUMN is_deleted tinyint(4) DEFAULT 0 NOT NULL;
ALTER TABLE doctor_symptoms MODIFY COLUMN doctor_symptom_id int(11) auto_increment NOT NULL;
ALTER TABLE doctor_appointments ADD is_cancelled TINYINT DEFAULT 0 NOT NULL;
ALTER TABLE doctor_appointments ADD is_paid TINYINT DEFAULT 0 NOT NULL;
ALTER TABLE eyadat.doctor_appointments ADD discount DECIMAL DEFAULT 0 NOT NULL;
ALTER TABLE eyadat.doctor_appointments ADD sub_total DECIMAL DEFAULT 0 NOT NULL;
ALTER TABLE eyadat.doctor_appointments ADD amount DECIMAL DEFAULT 0 NOT NULL;
ALTER TABLE eyadat.doctor_appointments ADD payment_initiate_time DATETIME NULL;
ALTER TABLE eyadat.doctor_appointments ADD has_gone_payment TINYINT DEFAULT 0 NOT NULL;
ALTER TABLE eyadat.payment ADD tap_charge_id varchar(250) NULL;
ALTER TABLE eyadat.doctor_appointments ADD duration INT DEFAULT 0 NOT NULL;
/**
 * Auther :vasim
 * Creaed :9-7-2021
 */

 ALTER TABLE `country` CHANGE `express_shipping_cost` `express_shipping_cost` FLOAT NULL DEFAULT '0';

 ALTER TABLE `country` CHANGE `is_deleted` `is_deleted` INT(11) NULL DEFAULT '0';

 ALTER TABLE `doctors` CHANGE `type` `type` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'V = Video consultation, I = In person consultation';

 /*
* Author : Vasim
* Created : Jul 12,2021 
*/
 ALTER TABLE `doctors` ADD `registration_number` VARCHAR(100) NULL AFTER `consultation_price_final`;

 /*
* Author : Vasim
* Created : Jul 13,2021 
*/
 ALTER TABLE `users` ADD `height` VARCHAR(10) NULL AFTER `code`, ADD `weight` VARCHAR(10) NULL AFTER `height`, ADD `blood_group` VARCHAR(10) NULL AFTER `weight`;
 ALTER TABLE `doctors` ADD `description_en` TEXT NULL AFTER `registration_number`, ADD `description_ar` TEXT NULL AFTER `description_en`;
 ALTER TABLE `labs` ADD `image_en` VARCHAR(100) NULL AFTER `admin_commission`, ADD `image_ar` VARCHAR(100) NULL AFTER `image_en`;

 /*
* Author : Vasim
* Created : Jul 14,2021 
*/
 ALTER TABLE `users` CHANGE `gender` `gender` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

 /*
* Author : Vasim
* Created : Jul 15,2021 
*/
 ALTER TABLE labs ADD end_time TIME NOT NULL;
ALTER TABLE labs ADD start_time TIME NOT NULL;
ALTER TABLE labs ADD image_en VARCHAR(100) NOT NULL;
ALTER TABLE labs ADD image_ar VARCHAR(100) NOT NULL;

/**
 * Author:  akram
 * Created: Jul 15, 2021
 */
ALTER TABLE lab_appointments ADD appointment_datetime DATETIME NOT NULL;
ALTER TABLE lab_appointments CHANGE appointment_datetime appointment_datetime DATETIME NOT NULL AFTER phone_number;
ALTER TABLE lab_appointments ADD is_cancelled TINYINT DEFAULT 0 NOT NULL;
ALTER TABLE lab_appointments ADD discount DECIMAL DEFAULT 0 NOT NULL;
ALTER TABLE lab_appointments ADD sub_total DECIMAL DEFAULT 0 NOT NULL;
ALTER TABLE lab_appointments ADD amount DECIMAL DEFAULT 0 NOT NULL;
ALTER TABLE lab_appointments ADD payment_initiate_time DATETIME NULL;
ALTER TABLE lab_appointments ADD has_gone_payment TINYINT DEFAULT 0 NOT NULL;
ALTER TABLE lab_appointments ADD duration INT DEFAULT 0 NOT NULL;
ALTER TABLE lab_appointments MODIFY COLUMN sample_collection_time DATETIME NULL;

/*
* Author : Vasim
* Created : jul 15,2021 
*/
ALTER TABLE `tests` CHANGE `price` `price` FLOAT NOT NULL;

/**
 * Author:  akram
 * Created: Jul 17, 2021
 */
ALTER TABLE brands CHANGE image_en image_name varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE brands DROP COLUMN image_ar;
ALTER TABLE brands MODIFY COLUMN image_name varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE attribute_values MODIFY COLUMN sort_order int(11) NULL;

/**
 * Author:  akram
 * Created: Jul 19, 2021
 */
ALTER TABLE doctor_appointments ADD is_completed TINYINT DEFAULT 0 NOT NULL;
ALTER TABLE lab_appointments ADD is_completed TINYINT DEFAULT 0 NOT NULL;

/** Auther : vasim
* Created : Jul 19, 2021
*/
ALTER TABLE `doctor_appointments` 
ADD `promotion_id` INT(10) NULL AFTER `is_cancelled`, 
ADD `promo_for` ENUM('P', 'B', 'D', 'C', 'L') NULL AFTER `promotion_id`, 
ADD `discount_price` DOUBLE NULL DEFAULT '0' AFTER `promo_for`;

/** Auther : vasim
* Created : Jul 20, 2021
*/
ALTER TABLE `lab_appointments` 
ADD `promotion_id` INT(10) NULL AFTER `is_cancelled`, 
ADD `promo_for` ENUM('L') NULL AFTER `promotion_id`, 
ADD `discount_price` DOUBLE NULL DEFAULT '0' AFTER `promo_for`;

/*
* By Vasim
* Created : Jul 22 ,2021
*/

ALTER TABLE `lab_appointments` CHANGE `amount` `amount` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `lab_appointments` CHANGE `lab_amount` `lab_amount` DOUBLE NOT NULL, CHANGE `discount` `discount` DOUBLE NOT NULL DEFAULT '0', CHANGE `sub_total` `sub_total` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `doctor_appointments` CHANGE `discount` `discount` DOUBLE NOT NULL DEFAULT '0', CHANGE `sub_total` `sub_total` DOUBLE NOT NULL DEFAULT '0', CHANGE `amount` `amount` DOUBLE NOT NULL DEFAULT '0';

/*
* By vasim
* Created : Jul 23, 2021
*/
ALTER TABLE `labs` ADD `latlon` VARCHAR(100) NULL AFTER `image_ar`, ADD `governorate_id` INT(10) NULL AFTER `latlon`, ADD `area_id` INT(10) NULL AFTER `governorate_id`, ADD `block` VARCHAR(100) NULL AFTER `area_id`, ADD `street` VARCHAR(150) NULL AFTER `block`;
ALTER TABLE `labs` ADD `building` VARCHAR(100) NULL AFTER `street`;
ALTER TABLE `lab_appointments` ADD `user_address_id` INT(10) NULL AFTER `sample_collection_time`;
ALTER TABLE `lab_appointments` ADD `uploaded_report` VARCHAR(150) NULL AFTER `has_gone_payment`;

/*
* By Akram
* Created : Jul 27, 2021
*/
ALTER TABLE manufacturers CHANGE image_en image_name varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE manufacturers DROP COLUMN image_ar;

INSERT INTO product_status (status_name_en,status_name_ar) VALUES
	 ('Pending','قيد الانتظار'),
	 ('Approved','وافق'),
	 ('Disapproved','مرفوض');

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `price` decimal(10,3) NOT NULL,
  `cost_price` decimal(10,3) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `message` text,
  `pharmacy_order_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`order_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*
* Auther: Vasim
* Created : Jul 28,2021
*/
CREATE TABLE `user_report` ( 
  `report_id` INT(10) NOT NULL AUTO_INCREMENT , 
  `user_id` INT(10) NOT NULL , 
  `title` VARCHAR(100) NOT NULL ,
  `is_deleted` INT NOT NULL DEFAULT '0' , 
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  PRIMARY KEY (`report_id`)) ENGINE = InnoDB;

ALTER TABLE user_report ADD FOREIGN KEY (user_id) REFERENCES users(user_id);

CREATE TABLE `user_reports_images` ( 
  `user_reports_image_id` INT(10) NOT NULL AUTO_INCREMENT , 
  `report_id` INT(10) NOT NULL , `image_url` VARCHAR(100) NULL , 
  PRIMARY KEY (`user_reports_image_id`)) ENGINE = InnoDB;

ALTER TABLE user_reports_images ADD FOREIGN KEY (report_id) REFERENCES user_report(report_id);
ALTER TABLE `user_reports_images` CHANGE `image_url` `image` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

/*
* Author : Vasim
* Created : Jul 29,2021 
*/
CREATE TABLE `labs_working_days` (
  `lab_working_day_id` int(11) NOT NULL,
  `lab_id` int(11) NOT NULL,
  `day` varchar(100) NOT NULL,
  `lab_end_time` time NOT NULL,
  `lab_start_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `labs_working_days`
  ADD PRIMARY KEY (`lab_working_day_id`),
  ADD KEY `doctor_working_days_FK` (`lab_id`);
ALTER TABLE `labs_working_days`
  MODIFY `lab_working_day_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

/*
* Author : Vasim
* Created : Jul 30,2021 
*/ 
CREATE TABLE `doctor_report_request` 
( `doctor_report_request_id` INT(10) NOT NULL AUTO_INCREMENT , 
  `doctor_appointment_id` INT(10) NOT NULL , 
  `doctor_request_for` TEXT NULL , 
  `user_id` INT(10) NOT NULL , 
  `request_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  `status` ENUM("P","A","R") NOT NULL DEFAULT 'P' COMMENT 'P - Pending, A - Accepted , R - Rejected' , 
  PRIMARY KEY (`doctor_report_request_id`)
) ENGINE = InnoDB;

ALTER TABLE doctor_report_request ADD FOREIGN KEY (user_id) REFERENCES users(user_id);
ALTER TABLE doctor_report_request ADD FOREIGN KEY (doctor_appointment_id) REFERENCES doctor_appointments(doctor_appointment_id);

/*
* Author : Vasim
* Created : Jul 31,2021 
*/
CREATE TABLE `doctor_assigned_report_request` ( `request_id` INT(10) NOT NULL AUTO_INCREMENT , `doctor_report_request_id` INT(10) NOT NULL , `report_id` INT(10) NOT NULL , `is_approved` INT NOT NULL DEFAULT '0' , PRIMARY KEY (`request_id`)) ENGINE = InnoDB;
ALTER TABLE `doctor_assigned_report_request` ADD `doctor_appointment_id` INT(10) NOT NULL AFTER `doctor_report_request_id`;
ALTER TABLE `doctor_assigned_report_request` DROP `doctor_report_request_id`;
ALTER TABLE `doctor_assigned_report_request` ADD `doctor_report_request_id` INT NOT NULL AFTER `request_id`;

/*
* By Akram
* Date : 30-7-2021
*/
ALTER TABLE orders MODIFY COLUMN payment_mode enum('C','CC','K','W','M','AE','S','B','NP','MD','KF','AP','AF','STC','UAECC','') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NULL;

/*
* Author : Vasim
* Created : Aug 3,2021 
*/
ALTER TABLE `doctor_report_request` ADD `report_id` INT(10) NULL 
COMMENT 'user report id' AFTER `doctor_request_for`;
DROP TABLE `doctor_assigned_report_request`

/*
* Author : Vasim
* Created : Aug 9,2021 
*/
ALTER TABLE `promotions` CHANGE `promo_for` `promo_for` ENUM('P','B','D','C','L','F') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

CREATE TABLE `promotion_pharmacy` ( `promotion_pharmacy_id` INT(10) NOT NULL AUTO_INCREMENT , `promotion_id` INT(10) NOT NULL , `pharmacy_id` INT(10) NOT NULL , PRIMARY KEY (`promotion_pharmacy_id`)) ENGINE = InnoDB;

ALTER TABLE promotion_pharmacy ADD FOREIGN KEY (promotion_id) REFERENCES promotions(promotion_id);
ALTER TABLE promotion_pharmacy ADD FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(pharmacy_id);
ALTER TABLE `doctors` ADD `sort_order` INT(10) NULL AFTER `is_featured`;
ALTER TABLE `services` ADD `sort_order` INT(10) NULL AFTER `image_ar`;

/*
* Author : Vasim
* Created : Aug 10,2021 
*/
ALTER TABLE `promotions` ADD `is_active` INT(10) NOT NULL DEFAULT '0' AFTER `registration_end_date`;

/*
* By Akram
* Date : 10-08-2021
*/
INSERT INTO auth_module (auth_module_name,auth_module_url,is_active,sort_order)
	VALUES ('Brands','/brand',1,25);
	
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/index','Manage',25,1,'2021-08-10 13:01:15', 'admin');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/view','View',25,1,'2021-08-10 13:01:15', 'admin');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/create','Create',25,1,'2021-08-10 13:01:15', 'admin');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/update','Update',25,1,'2021-08-10 13:01:15', 'admin');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/delete','Delete',25,1,'2021-08-10 13:01:15', 'admin');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/activate','Activate',25,1,'2021-08-10 13:01:15', 'admin');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/send-push','Send Push',25,1,'2021-08-10 13:01:15', 'admin');


INSERT INTO auth_module (auth_module_name,auth_module_url,is_active,sort_order)
	VALUES ('Category','/category',1,26);

INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,rule_name,is_active,created_at)
	VALUES ('/index','Manage',26,'admin',1,'2021-08-10 13:12:14');


INSERT INTO auth_module (auth_module_name,auth_module_url,is_active,sort_order)
	VALUES ('Attribute','/attribute',1,27);

INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/index','Manage',27,1,'2021-08-10 13:56:48', 'admin');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/view','View',27,1,'2021-08-10 13:56:48', 'admin');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/create','Create',27,1,'2021-08-10 13:56:48', 'admin');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/update','Update',27,1,'2021-08-10 13:56:48', 'admin');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,is_active,created_at,rule_name)
	VALUES ('/delete','Delete',27,1,'2021-08-10 13:56:48', 'admin');


INSERT INTO auth_module (auth_module_name,auth_module_url,is_active,sort_order)
	VALUES ('Attribute Set','/attribute-set',1,28);
	
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,rule_name,is_active,created_at)
	VALUES ('/index','Manage',28,'admin',1,'2021-08-10 14:58:15');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,rule_name,is_active,created_at)
	VALUES ('/view','View',28,'admin',1,'2021-08-10 14:58:15');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,rule_name,is_active,created_at)
	VALUES ('/create','Create',28,'admin',1,'2021-08-10 14:58:15');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,rule_name,is_active,created_at)
	VALUES ('/update','Update',28,'admin',1,'2021-08-10 14:58:15');
INSERT INTO auth_item (auth_item_url,auth_item_name,auth_module_id,rule_name,is_active,created_at)
	VALUES ('/delete','Delete',28,'admin',1,'2021-08-10 14:58:15');

  
/*
* Author : Vasim
* Created : Aug 8,2021 
*/
ALTER TABLE `shipping_addresses` ADD `block_name` VARCHAR(100) NULL AFTER `block_id`;

#13-8-2021 by vasim
ALTER TABLE `doctor_appointments` ADD `appointment_number` VARCHAR(50) NULL AFTER `doctor_appointment_id`;
ALTER TABLE `lab_appointments` ADD `appointment_number` VARCHAR(50) NULL AFTER `lab_appointment_id`;
ALTER TABLE `lab_appointments` CHANGE `paymode` `paymode` 
ENUM('C','CC','K','W','') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'P - Promo if 100% off';

/*
* Author : Vasim
* Created : Aug 14,2021 
*/
ALTER TABLE `doctor_appointments` ADD `uploaded_report` VARCHAR(100) NULL AFTER `is_completed`;

/*
* Auther : Vasim
* Created : Aug 16,2021
*/
CREATE TABLE `doctor_prescriptions` 
( `doctor_appointment_prescription_id` INT NOT NULL AUTO_INCREMENT , 
  `doctor_appointment_id` INT(10) NOT NULL , 
  `total_usage` INT(10) NOT NULL , 
  `referred_pharmacy_id` INT(10) NOT NULL , 
  `is_deleted` INT NOT NULL DEFAULT '0' , 
  `is_active` INT NOT NULL DEFAULT '1' , 
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  PRIMARY KEY (`doctor_appointment_prescription_id`)
) ENGINE = InnoDB;

ALTER TABLE `lab_appointments` CHANGE `paymode` `paymode` 
ENUM('C','CC','K','W','') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '';

CREATE TABLE `doctor_appointment_medicines` 
( 
  `doctor_appointment_medicine_id` INT(10) NOT NULL AUTO_INCREMENT , 
  `doctor_appointment_prescription_id` INT(10) NOT NULL , 
  `product_id` INT(10) NOT NULL , 
  `instruction` TEXT NULL , 
  PRIMARY KEY (`doctor_appointment_medicine_id`)
) ENGINE = InnoDB;

ALTER TABLE `doctor_appointment_medicines` ADD `qty` INT(10) NOT NULL DEFAULT '1' AFTER `product_id`;
ALTER TABLE `users` ADD `civil_id` VARCHAR(50) NULL AFTER `weight`;
ALTER TABLE `doctor_prescriptions` ADD `user_id` INT(10) NOT NULL AFTER `doctor_appointment_id`;
ALTER TABLE `orders` ADD `prescription_id` INT NOT NULL DEFAULT '0' COMMENT 'id from doctor prescription' AFTER `tracking_link`;

/*
* Auther : Vasim
* Date   : Aug 19,2021
*/
ALTER TABLE `users` ADD `previous_hospital_visit` VARCHAR(100) NULL AFTER `civil_id`;
ALTER TABLE `doctors` ADD `accepted_payment_method` VARCHAR(20) NULL AFTER `description_ar`;
ALTER TABLE `labs` ADD `accepted_payment_method` VARCHAR(20) NULL AFTER `building`;

/*
* Auther : vasim
* Created : Aug 20, 2021
*/
ALTER TABLE `users` ADD `insurance_id` INT(10) NULL AFTER `civil_id`, ADD `insurance_numbar` VARCHAR(100) NULL AFTER `insurance_id`;

CREATE TABLE `notifications` 
( 
   `notification_id` INT NOT NULL AUTO_INCREMENT ,
   `title` VARCHAR(250) NOT NULL ,
    `message` TEXT NOT NULL ,
    `user_id` INT(10) NULL , 
    `target` VARCHAR(50) NULL , 
    `target_id` INT(10) NULL , 
    `posted_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`notification_id`)
) ENGINE = InnoDB;

/*
* AUTHER : vasim
* CREATED : 23-8-2021
*/
ALTER TABLE `pharmacies` ADD `delivery_charge` FLOAT NULL DEFAULT '0' AFTER `is_free_delivery`;

/*
* AUTHER : VASIM
* DATE : AUG 24,2021
*/
ALTER TABLE `doctor_appointments` ADD `is_call_initiated` INT(10) NOT NULL DEFAULT '0' AFTER `uploaded_report`;

/*
* DATE : 9-8-2021
* AUTHER : VASIM PATHAN
*/
ALTER TABLE `lab_appointments` ADD `report_title_en` VARCHAR(110) NULL AFTER `uploaded_report`, ADD `report_title_ar` VARCHAR(110) NULL AFTER `report_title_en`, ADD `report_upload_date` DATETIME NULL AFTER `report_title_ar`;
ALTER TABLE `doctor_appointments` ADD `report_title_en` VARCHAR(110) NULL AFTER `uploaded_report`, ADD `report_title_ar` VARCHAR(110) NULL AFTER `report_title_en`, ADD `report_upload_date` DATETIME NULL AFTER `report_title_ar`;

/*
* DATE : 9-9-2021
* AUTHER : VASIM
*/
ALTER TABLE `settings` ADD `physical_consultation_image` VARCHAR(100) NULL AFTER `banner_height`, ADD `online_consultation_image` VARCHAR(100) NULL AFTER `physical_consultation_image`, ADD `lab_test_image` VARCHAR(100) NULL AFTER `online_consultation_image`, ADD `pharmacies_image` VARCHAR(100) NULL AFTER `lab_test_image`, ADD `beauty_clinic_image` VARCHAR(100) NULL AFTER `pharmacies_image`, ADD `hospital_image` VARCHAR(100) NULL AFTER `beauty_clinic_image`;
ALTER TABLE `order_status` CHANGE `user_type` `user_type` ENUM('A','V','U','D') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
UPDATE `pharmacy_status` SET `name_en` = 'Ready to pickup' WHERE `pharmacy_status`.`pharmacy_status_id` = 2;
UPDATE `pharmacy_status` SET `name_en` = 'Picked by Driver' WHERE `pharmacy_status`.`pharmacy_status_id` = 4;

CREATE TABLE `driver_suborders` (
  `driver_suborder_id` INT NOT NULL AUTO_INCREMENT ,
  `driver_id` INT(10) NOT NULL ,
  `order_id` INT(10) NOT NULL ,
  `pharmacy_order_id` INT(10) NOT NULL ,
  `pharmacy_id` INT(10) NOT NULL ,
  `assigned_date` DATETIME NOT NULL ,
  PRIMARY KEY (`driver_suborder_id`)
  )
ENGINE = InnoDB;
ALTER TABLE `pharmacy_order_status` CHANGE `user_type` `user_type` ENUM('A','S','D') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

/*
* Date : 11-9-2021
* Auther : Vasim
*/

ALTER TABLE `pharmacy_order_status` ADD `image` VARCHAR(100) NULL AFTER `comment`;
ALTER TABLE `order_status` ADD `delivery_proof` VARCHAR(100) NULL AFTER `notify_customer`;
ALTER TABLE `clinics` ADD `description_en` TEXT NULL AFTER `name_ar`, ADD `description_ar` TEXT NULL AFTER `description_en`;
/**
* Date : 14-9-2021
* Auther : Vasim
**/
DELETE FROM `eyadat`.`status` WHERE (`status_id` = '7');
ALTER TABLE `clinics` ADD `country_id` INT(100) NULL AFTER `type`;

/**
* Date : 17-9-2021
* Author : Vasim
**/
ALTER TABLE `clinics` ADD `admin_commission` DECIMAL(10,0) NULL DEFAULT '0' AFTER `password`;
UPDATE `country` SET delivery_interval="0",express_delivery_interval="0",free_delivery_limit="0",vat="0",standard_delivery_items="0",standard_delivery_charge="0",express_delivery_charge="0",standard_shipping_cost_actual="0",express_shipping_cost_actual="0",min_vat_amount="0",min_vat_amount="0",min_custom_admin_amount="0"
WHERE country_id BETWEEN 1 AND 300

ALTER TABLE `doctor_appointments` ADD `not_show` INT(10) NULL DEFAULT '0'
COMMENT '0 - show , 1 - Not Show' AFTER `is_cancelled`;

ALTER TABLE `lab_appointments` ADD `not_show` INT(10) NULL DEFAULT '0'
COMMENT '0 - show , 1 - Not Show' AFTER `is_cancelled`;

  ALTER TABLE `doctor_appointments` ADD `admin_commission` FLOAT(10,2) NOT NULL DEFAULT '0' COMMENT 'admin_commission from clinic' AFTER `amount`;
  ALTER TABLE `doctor_appointments` CHANGE `admin_commission` `admin_commission` FLOAT(10,2) NULL DEFAULT '0.00' COMMENT 'admin_commission from clinic';
ALTER TABLE `orders` CHANGE `promo_for` `promo_for` ENUM('P','B','F') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'F-pharmacy';
ALTER TABLE `orders` CHANGE `discount_price` `discount_price` VARCHAR(100) NOT NULL DEFAULT '0';

/**
* DATE : 22-9-2021
* AUTHER : VASIM PATHAN
**/
ALTER TABLE `lab_appointments` ADD `admin_commission` VARCHAR(100) NOT NULL DEFAULT '0' AFTER `amount`;

/**
* AUTHER : VASIM
* DATE : 24-9-2021
*/
INSERT INTO `status` (`status_id`, `name_en`, `name_ar`, `color`, `list_order`) VALUES (NULL, 'READY FOR DELIVERY', 'جاهز للتسليم', NULL, NULL);
UPDATE `status` SET `list_order` = '4' WHERE `status`.`status_id` = 8;
UPDATE `status` SET `name_en` = 'ACCEPTED', `name_ar` = 'ACCEPTED' WHERE `status`.`status_id` = 2

/*
Created By Sreejit Manoharan
05-10-2021
*/
ALTER TABLE `settings` ADD `translator_price` DECIMAL(10,3) NULL DEFAULT '0' AFTER `hospital_image`;

ALTER TABLE `doctor_appointments` ADD `need_translator` INT(11) NULL DEFAULT '0' AFTER `is_call_initiated`;

CREATE TABLE `translator` (
 `translator_id` int(11) NOT NULL AUTO_INCREMENT,
 `name_en` varchar(255) CHARACTER SET utf8 NOT NULL,
 `name_ar` varchar(255) CHARACTER SET utf8 NOT NULL,
 `email` varchar(255) CHARACTER SET utf8 NOT NULL,
 `password` varchar(255) CHARACTER SET utf8 NOT NULL,
 `is_active` int(11) NOT NULL DEFAULT 0,
 `is_deleted` int(11) NOT NULL DEFAULT 0,
 `created_at` datetime DEFAULT NULL,
 `updated_at` datetime DEFAULT NULL,
 PRIMARY KEY (`translator_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*
Created By Sreejit Manoharan
23-10-2021
*/
ALTER TABLE `notifications` ADD `is_read` INT(11) NOT NULL DEFAULT '0' AFTER `posted_date`;