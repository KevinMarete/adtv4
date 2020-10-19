CREATE TABLE IF NOT EXISTS `pv_feedbacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` char(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sadr_id` int(11) DEFAULT NULL,
  `sadr_followup_id` int(11) DEFAULT NULL,
  `pqmp_id` int(11) DEFAULT NULL,
  `feedback` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
)//