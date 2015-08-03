-- 
--

--
-- Table structure for table `autodm_log`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_autodm_log` (
  `dm_type` enum('follow_dm') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dm_text` varchar(140) NOT NULL,
  `http_code` int(11) NOT NULL,
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `autotweet_log`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_autotweet_log` (
  `tweet_type` enum('text','rss','ff','leader_retweet','follow_tweet') NOT NULL,
  `scheduled` tinyint(1) NOT NULL,
  `recurring` tinyint(1) NOT NULL,
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tweet_text` varchar(150) NOT NULL,
  `tweet_id` bigint(20) unsigned NOT NULL,
  `http_code` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `autotweet_recurring`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_autotweet_recurring` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tweet_type` enum('text','rss','ff','leader_retweet') NOT NULL,
  `dow` varchar(7) NOT NULL,
  `random_time` tinyint(1) NOT NULL,
  `tweet_hour` int(11) NOT NULL,
  `tweet_minute` int(11) NOT NULL,
  `tweet_text` varchar(140) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `autotweet_rss_feeds`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_autotweet_rss_feeds` (
  `name` varchar(40) NOT NULL,
  `url` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `autotweet_rss_tweets`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_autotweet_rss_tweets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_name` varchar(40) NOT NULL,
  `title` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `guid` varchar(100) NOT NULL,
  `pub_date` datetime NOT NULL,
  `tweet_text` varchar(140) NOT NULL,
  `posted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `feed_name` (`feed_name`),
  KEY `guid` (`guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `autotweet_scheduled`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_autotweet_scheduled` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tweet_date` date NOT NULL,
  `tweet_hour` int(11) NOT NULL,
  `tweet_minute` int(11) NOT NULL,
  `tweet_text` varchar(140) NOT NULL,
  `posted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`tweet_date`),
  KEY `hour` (`tweet_hour`),
  KEY `minute` (`tweet_minute`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dms`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_dms` (
  `dm_id` bigint(20) unsigned NOT NULL,
  `dm_text` varchar(160) NOT NULL,
  `created_at` datetime NOT NULL,
  `sender_user_id` bigint(20) unsigned NOT NULL,
  `recipient_user_id` bigint(20) unsigned NOT NULL,
  `sent` tinyint(1) NOT NULL,
  `received` tinyint(1) NOT NULL,
  PRIMARY KEY (`dm_id`),
  KEY `created_at` (`created_at`),
  KEY `sender_user_id` (`sender_user_id`),
  KEY `recipient_user_id` (`recipient_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engagement_account`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_engagement_account` (
  `user_id` bigint(20) unsigned NOT NULL,
  `screen_name` varchar(20) NOT NULL,
  `old_timeline_collected` datetime NOT NULL,
  `new_timeline_collected` datetime NOT NULL,
  `old_search_collected` datetime NOT NULL,
  `new_search_collected` datetime NOT NULL,
  `search_since_id` bigint(20) unsigned NOT NULL,
  `old_dms_sent_collected` datetime NOT NULL,
  `new_dms_sent_collected` datetime NOT NULL,
  `old_dms_received_collected` datetime NOT NULL,
  `new_dms_received_collected` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `screen_name` (`screen_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_followers` (
  `user_id` bigint(20) unsigned NOT NULL,
  `current` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------

--
-- Table structure for table `follow_log`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_follow_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `event` enum('friend','unfriend','follow','unfollow') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tweet_sent` tinyint(1) NOT NULL,
  `dm_sent` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_friends` (
  `user_id` bigint(20) unsigned NOT NULL,
  `current` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `leaders`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_leaders` (
  `user_id` bigint(20) unsigned NOT NULL,
  `screen_name` varchar(20) NOT NULL,
  `old_timeline_collected` datetime NOT NULL,
  `new_timeline_collected` datetime NOT NULL,
  `old_search_collected` datetime NOT NULL,
  `new_search_collected` datetime NOT NULL,
  `search_since_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `old_timeline_collected` (`old_timeline_collected`),
  KEY `new_timeline_collected` (`new_timeline_collected`),
  KEY `old_search_collected` (`old_search_collected`),
  KEY `new_search_collected` (`new_search_collected`),
  KEY `screen_name` (`screen_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tweets`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_tweets` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `tweet_text` varchar(160) NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `is_rt` tinyint(1) NOT NULL,
  `retweet_count` int(11) NOT NULL,
  PRIMARY KEY (`tweet_id`),
  KEY `created_at` (`created_at`),
  KEY `user_id` (`user_id`),
  KEY `retweet_count` (`retweet_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tweet_mentions`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_tweet_mentions` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `source_user_id` bigint(20) unsigned NOT NULL,
  `target_user_id` bigint(20) unsigned NOT NULL,
  KEY `tweet_id` (`tweet_id`),
  KEY `created_at` (`created_at`),
  KEY `source_user_id` (`source_user_id`),
  KEY `target_user_id` (`target_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tweet_retweets`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_tweet_retweets` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `source_user_id` bigint(20) unsigned NOT NULL,
  `target_user_id` bigint(20) unsigned NOT NULL,
  KEY `tweet_id` (`tweet_id`),
  KEY `created_at` (`created_at`),
  KEY `source_user_id` (`source_user_id`),
  KEY `target_user_id` (`target_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tweet_tags`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_tweet_tags` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `tag` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  KEY `tweet_id` (`tweet_id`),
  KEY `tag` (`tag`),
  KEY `created_at` (`created_at`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tweet_urls`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_tweet_urls` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `url` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  KEY `tweet_id` (`tweet_id`),
  KEY `url` (`url`),
  KEY `created_at` (`created_at`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_users` (
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` bigint(20) unsigned NOT NULL,
  `screen_name` varchar(20) NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  `profile_image_url` varchar(200) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `description` varchar(160) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `followers_count` int(10) unsigned DEFAULT NULL,
  `friends_count` int(10) unsigned DEFAULT NULL,
  `statuses_count` int(10) unsigned DEFAULT NULL,
  `listed_count` int(10) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL,
  `suspended` tinyint(1) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `last_tweet_date` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `screen_name` (`screen_name`),
  KEY `followers_count` (`followers_count`),
  KEY `friends_count` (`friends_count`),
  KEY `last_tweet_date` (`last_tweet_date`),
  KEY `last_updated` (`last_updated`),
  KEY `statuses_count` (`statuses_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_tags`
--

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_user_tags` (
  `user_id` bigint(20) unsigned NOT NULL,
  `tag` varchar(100) NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_Twitter_Settings` (
  `App_ID` varchar(250) NOT NULL,
  `App_Name` varchar(250) NOT NULL,
  `Consumer_key` varchar(250) NOT NULL,
  `Consumer_secret` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `civicrm_civisocial_faceboook_Settings` (
  `App_ID` varchar(250) NOT NULL,
  `App_Name` varchar(250) NOT NULL,
  `App_Secret` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

