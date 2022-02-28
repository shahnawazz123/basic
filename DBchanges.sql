/**
 Nasar Basha
 09/27/2021
 Update link_type to show pharmacy details
 */
ALTER TABLE
    `eyadat`.`banner` CHANGE COLUMN `link_type` `link_type` ENUM(
        'C',
        'P',
        'BR',
        'L',
        'PH',
        'M',
        'FP',
        'CL',
        'D',
        'LA',
        'TC',
        'T',
        'PC',
        "F"
        /** F => Pharmacy **/
    ) NOT NULL;

/**
 Adding payment method to pharmacy table
 */
ALTER TABLE
    eyadat.pharmacies
ADD
    accepted_payment_method varchar(50);

/**
 * Adding translator to appoints
 * Nasar Basha 10/13/2021
 */
ALTER TABLE
    `eyadat`.`doctor_appointments`
ADD
    COLUMN `translator_id` INT NULL
AFTER
    `need_translator`;

/**
 Add Type H to linktype
 Nasar Basha 10/19/2021
 */
ALTER TABLE
    `eyadatcms`.`banner` CHANGE COLUMN `link_type` `link_type` ENUM(
        'C',
        'P',
        'BR',
        'L',
        'PH',
        'M',
        'FP',
        'CL',
        'D',
        'LA',
        'TC',
        'H',
        'T',
        'PC',
        'F'
    ) NOT NULL;

/**
 /* --------- Updated Banner is_active column to 0 as per instruction -------- */
Nasar Basha 10 / 19 / 2021 * /
ALTER TABLE
    `eyadat`.`banner` CHANGE COLUMN `is_active` `is_active` INT NOT NULL DEFAULT '0';

/**
 /* --------- ADD lab appoinments home service price column -------- */
Nasar Basha 11 / 04 / 2021 * /
ALTER TABLE
    `eyadat`.`lab_appointments`
ADD
    COLUMN `home_service_price` DOUBLE NULL DEFAULT '0'
AFTER
    `is_completed`;

/**
 /* --------- ADD translated column to notification table -------- */
Nasar Basha 11 / 08 / 2021 * / /
ALTER TABLE
    `eyadat`.`notifications`
ADD
    COLUMN `title_ar` TEXT NULL
AFTER
    `title`,
ADD
    COLUMN `message_ar` TEXT NULL
AFTER
    `message`;

/**
 /* ADD notified column to doctor appointment for cron jobs 
 /* Nasar Basha 11-10-2021 
 */
ALTER TABLE
    `eyadatcms`.`doctor_appointments`
ADD
    COLUMN `is_notified` TINYINT NULL DEFAULT 0
AFTER
    `translator_id`;