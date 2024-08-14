CREATE TABLE `sso_provider_oauth_access_tokens` (
                                                    `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                    `user_id` bigint(20) DEFAULT NULL,
                                                    `client_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                    `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                    `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                    `revoked` tinyint(1) NOT NULL,
                                                    `created_at` bigint(20) DEFAULT NULL,
                                                    `updated_at` bigint(20) DEFAULT NULL,
                                                    `expires_at` bigint(20) DEFAULT NULL,
                                                    PRIMARY KEY (`id`),
                                                    KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sso_provider_oauth_auth_codes` (
                                                 `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                 `user_id` bigint(20) NOT NULL,
                                                 `client_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                 `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                 `revoked` tinyint(1) NOT NULL,
                                                 `expires_at` bigint(20) unsigned DEFAULT NULL,
                                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sso_provider_oauth_refresh_tokens` (
                                                     `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                     `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                     `revoked` tinyint(1) NOT NULL,
                                                     `expires_at` bigint(20) DEFAULT NULL,
                                                     PRIMARY KEY (`id`),
                                                     KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
