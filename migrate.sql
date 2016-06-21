
ALTER TABLE Application ADD applicationType_id INT DEFAULT NULL, ADD deploymentStrategy_id INT DEFAULT NULL, DROP deployment_strategy;


CREATE TABLE `ApplicationType` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `userDirs` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `userFiles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci


INSERT INTO ApplicationType (`title`, `userDirs`, `userFiles`) VALUES ('Drupal', '["sites/default/files"]', '[]');
INSERT INTO ApplicationType (`title`, `userDirs`, `userFiles`) VALUES ('Wordpress', '["wp-content/uploads"]', '[]');
INSERT INTO ApplicationType (`title`, `userDirs`, `userFiles`) VALUES ('OMS', '["media", "cache", "lucene", "tmp"]', '["cms/.htpasswd", "cms/.htaccess"]');
INSERT INTO ApplicationType (`title`, `userDirs`, `userFiles`) VALUES ('Symfony', '[]', '[]');
INSERT INTO ApplicationType (`title`, `userDirs`, `userFiles`) VALUES ('Other', '[]', '[]');

UPDATE Application SET `applicationType_id` = 1
WHERE applicationType IN ('drupal', 'netvlies_publish.type.drupal');

UPDATE Application SET `applicationType_id` = 2
WHERE applicationType IN ('wordpress', 'netvlies_publish.type.wordpress');

UPDATE Application SET `applicationType_id` = 3
WHERE applicationType IN ('oms',  'netvlies_publish.type.oms');

UPDATE Application SET `applicationType_id` = 4
WHERE applicationType IN ('symfony2', 'symfony21', 'symfony23',  'symfony20', 'netvlies_publish.type.symfony27', 'netvlies_publish.type.symfony20');

UPDATE Application SET `applicationType_id` = 5
WHERE applicationType IN ('basissitev1', 'custom phing', 'custom',  'silex');


CREATE TABLE `Strategy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_ED3027BB2B36786B` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci

INSERT INTO `Strategy`(`id`,`title`) values (1,'Capistrano2');

UPDATE Application SET deploymentStrategy_id = 1

INSERT INTO `CommandTemplate`(`id`,`strategy_id`,`title`,`template`,`enabledByDefault`) VALUES
(1,1,'Deploy','source /usr/local/rvm/scripts/rvm &&\r\nrvm use ruby-1.8.7-head &&\r\ncd {{ versioning.getRepositoryPath(application) }} && \\\r\neval `ssh-agent -t 7200` && \\\r\n`ssh-add {{versioning.privateKey}}` && \\\r\n\\ {# Bring repository in desired state #}\r\ngit checkout \'{{ revision }}\' && \\\r\n\\ {# Capistrano deploy #}\r\ncap {{ target.environment.type }} deploy:update \\\r\n-Sproject=\'{{ application.name }}\' \\\r\n-Sapptype=\'{{ application.applicationType.title }}\' \\\r\n-Sappkey=\'{{ application.keyname }}\' \\\r\n-Sgitrepo=\'{{ application.scmUrl }}\' \\\r\n-Srepositorypath=\'{{ versioning.repositoryPath(application) }}\' \\\r\n-Srevision=\'{{ revision }}\' \\\r\n-Susername=\'{{ target.username }}\' \\\r\n-Smysqldb=\'{{ target.mysqldb }}\' \\\r\n-Smysqluser=\'{{ target.mysqluser }}\' \\\r\n-Smysqlpw=\'{{ target.mysqlpw }}\' \\\r\n-Swebroot=\'{{ target.webroot }}\' \\\r\n-Sapproot=\'{{ target.approot }}\' \\\r\n-Scaproot=\'{{ target.caproot }}\' \\\r\n-Sprimarydomain=\'{{ target.primaryDomain }}\' \\\r\n-Shostname=\'{{ target.environment.hostname }}\' \\\r\n-Ssshport=\'{{ target.environment.port }}\' \\\r\n-Sdtap=\'{{ target.environment.type }}\' \\\r\n-Suserfiles=\'{% for userFile in application.userFilesFiles %}{{ userFile.path }}{% if not loop.last %},{% endif %}{% endfor %}\' \\\r\n-Suserdirs=\'{% for userFile in application.userFilesFiles %}{{ userFile.path }}{% if not loop.last %},{% endif %}{% endfor %}\' \\\r\n-Svhostaliases=\'{% for alias in target.domainAliases %}{{ alias.alias }}{% if not loop.last %},{% endif %}{% endfor %}\' && \\\r\n\\ {# Update version script#}\r\necho \"Finished updating process. Retrieving current version ...\" && \\\r\ncd {{ approot }} && \\\r\napp/console organist:updateversion --id={{ target.id }} && \\\r\necho \"\" && \\\r\n\\ {# Kill SSH agent #}\r\nssh-agent -k > /dev/null 2>&1 && \\\r\nunset SSH_AGENT_PID && \\\r\nunset SSH_AUTH_SOCK \\',1),
(2,1,'Rollback','source /usr/local/rvm/scripts/rvm &&\r\nrvm use ruby-1.8.7-head &&\r\ncd {{ versioning.getRepositoryPath(application) }} && \\\r\n\\ {# Capistrano rollback #}\r\ncap {{ target.environment.type }} deploy:rollback \\\r\n-Sproject=\'{{ application.name }}\' \\\r\n-Sapptype=\'{{ application.applicationType.title }}\' \\\r\n-Sappkey=\'{{ application.keyname }}\' \\\r\n-Sgitrepo=\'{{ application.scmUrl }}\' \\\r\n-Srepositorypath=\'{{ versioning.repositoryPath(application) }}\' \\\r\n-Srevision=\'\' \\\r\n-Smysqldb=\'{{ target.mysqldb }}\' \\\r\n-Smysqluser=\'{{ target.mysqluser }}\' \\\r\n-Smysqlpw=\'{{ target.mysqlpw }}\' \\\r\n-Swebroot=\'{{ target.webroot }}\' \\\r\n-Sapproot=\'{{ target.approot }}\' \\\r\n-Scaproot=\'{{ target.caproot }}\' \\\r\n-Sprimarydomain=\'{{ target.primaryDomain }}\' \\\r\n-Shostname=\'{{ target.environment.hostname }}\' \\\r\n-Ssshport=\'{{ target.environment.port }}\' \\\r\n-Sdtap=\'{{ target.environment.type }}\' \\\r\n-Suserfiles=\'{% for userFile in application.userFilesFiles %}{{ userFile.path }}{% if not loop.last %},{% endif %}{% endfor %}\' \\\r\n-Suserdirs=\'{% for userFile in application.userFilesFiles %}{{ userFile.path }}{% if not loop.last %},{% endif %}{% endfor %}\' \\\r\n-Svhostaliases=\'{% for alias in target.domainAliases %}{{ alias.alias }}{% if not loop.last %},{% endif %}{% endfor %}\' && \\\r\n\\ {# Update version script#}\r\necho \"Finished rollback process. Retrieving current version ...\" && \\\r\ncd {{ approot }} && \\\r\napp/console organist:updateversion --id={{ target.id }}',1),
(3,1,'Setup','source /usr/local/rvm/scripts/rvm &&\r\nrvm use ruby-1.8.7-head &&\r\ncd {{ versioning.getRepositoryPath(application) }} && \\\r\n\\ {# Capistrano setup #}\r\ncap {{ target.environment.type }} deploy:setup \\\r\n-Sproject=\'{{ application.name }}\' \\\r\n-Sapptype=\'{{ application.applicationType.title }}\' \\\r\n-Sappkey=\'{{ application.keyname }}\' \\\r\n-Sgitrepo=\'{{ application.scmUrl }}\' \\\r\n-Srepositorypath=\'{{ versioning.repositoryPath(application) }}\' \\\r\n-Srevision=\'\' \\\r\n-Smysqldb=\'{{ target.mysqldb }}\' \\\r\n-Smysqluser=\'{{ target.mysqluser }}\' \\\r\n-Smysqlpw=\'{{ target.mysqlpw }}\' \\\r\n-Swebroot=\'{{ target.webroot }}\' \\\r\n-Sapproot=\'{{ target.approot }}\' \\\r\n-Scaproot=\'{{ target.caproot }}\' \\\r\n-Sprimarydomain=\'{{ target.primaryDomain }}\' \\\r\n-Shostname=\'{{ target.environment.hostname }}\' \\\r\n-Ssshport=\'{{ target.environment.port }}\' \\\r\n-Sdtap=\'{{ target.environment.type }}\' \\\r\n-Suserfiles=\'{% for userFile in application.userFilesFiles %}{{ userFile.path }}{% if not loop.last %},{% endif %}{% endfor %}\' \\\r\n-Suserdirs=\'{% for userFile in application.userFilesFiles %}{{ userFile.path }}{% if not loop.last %},{% endif %}{% endfor %}\' \\\r\n-Svhostaliases=\'{% for alias in target.domainAliases %}{{ alias.alias }}{% if not loop.last %},{% endif %}{% endfor %}\' && \\\r\n\\ {# Update version script#}\r\necho \"Finished setup process\"',1);