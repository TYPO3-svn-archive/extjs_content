#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_extjscontent_mode int(11) DEFAULT '0' NOT NULL,
	tx_extjscontent_selector int(11) DEFAULT '0' NOT NULL,
	tx_extjscontent_lightbox tinyint(3) DEFAULT '0' NOT NULL
	tx_extjscontent_description tinyint(3) DEFAULT '0' NOT NULL
	tx_extjscontent_link tinytext
	tx_extjscontent_interval tinytext
	tx_extjscontent_duration tinytext
);